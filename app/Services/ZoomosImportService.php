<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Characteristic;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ZoomosImportService
{
    private int $productsCreated = 0;

    private int $productsUpdated = 0;

    private int $productsSkipped = 0;

    private int $productsDeactivated = 0;

    private array $processedZoomosIds = [];

    private array $categoryCache = [];

    private array $brandCache = [];

    private int $categoriesCreated = 0;

    private int $categoriesUpdated = 0;

    private int $categoriesReparented = 0;

    private int $brandsCreated = 0;

    private int $brandsUpdated = 0;

    private int $brandsSkipped = 0;

    private int $characteristicsCreated = 0;

    private int $characteristicsUpdated = 0;

    private int $characteristicsLinked = 0;

    private int $characteristicsSkipped = 0;

    private int $imagesDownloaded = 0;

    private int $imagesSkipped = 0;

    public function import(string $apiKey, ?callable $progressCallback = null): array
    {
        $this->resetCounters();
        $this->resetCache();

        $products = $this->fetchProducts($apiKey);

        if (empty($products)) {
            Log::warning('Zoomos API returned empty products list');

            return $this->getStats();
        }

        $totalProducts = count($products);
        $processed = 0;

        foreach ($products as $productData) {
            $this->processProduct($productData);
            $processed++;

            if ($progressCallback) {
                $progressCallback($processed, $totalProducts);
            }
        }

        $this->deactivateProducts();

        Log::info('Products imported', $this->getStats());

        return $this->getStats();
    }

    public function createMissingProducts(string $apiKey, ?callable $progressCallback = null): array
    {
        $this->resetCounters();
        $this->resetCache();

        $products = $this->fetchProducts($apiKey);

        if (empty($products)) {
            Log::warning('Zoomos API returned empty products list');

            return [
                'created' => 0,
                'skipped' => 0,
                'total' => 0,
            ];
        }

        $totalProducts = count($products);
        $processed = 0;

        foreach ($products as $productData) {
            if ($this->shouldSkipProduct($productData)) {
                $this->productsSkipped++;
            } else {
                $zoomosId = $productData['id'];
                $existing = Product::where('zoomos_id', $zoomosId)->first();
                if (! $existing) {
                    $this->createProduct($productData);
                } else {
                    $this->updateProductDescAndCharacteristics($existing, (int) $zoomosId);
                    $this->productsSkipped++;
                }
            }

            $processed++;
            if ($progressCallback) {
                $progressCallback($processed, $totalProducts);
            }
        }

        return [
            'created' => $this->productsCreated,
            'skipped' => $this->productsSkipped,
            'total' => $this->productsCreated + $this->productsSkipped,
        ];
    }

    public function updateExistingProducts(string $apiKey, ?callable $progressCallback = null): array
    {
        $this->resetCounters();
        $this->resetCache();

        $products = $this->fetchProducts($apiKey);

        if (empty($products)) {
            Log::warning('Zoomos API returned empty products list');

            return $this->getStats();
        }

        $totalProducts = count($products);
        $processed = 0;

        foreach ($products as $productData) {
            if ($this->shouldSkipProduct($productData)) {
                $this->productsSkipped++;
            } else {
                $zoomosId = $productData['id'];
                $product = Product::where('zoomos_id', $zoomosId)->first();
                if ($product) {
                    $this->updateProductWithDetails($product, $productData);
                } else {
                    $this->productsSkipped++;
                }
                $this->processedZoomosIds[] = $zoomosId;
            }

            $processed++;
            if ($progressCallback) {
                $progressCallback($processed, $totalProducts);
            }
        }

        $this->deactivateProducts();

        return $this->getStats();
    }

    public function importCategories(string $apiKey): array
    {
        $this->categoriesCreated = 0;
        $this->categoriesUpdated = 0;
        $this->categoriesReparented = 0;

        $payload = $this->fetchCategories($apiKey);

        if (empty($payload)) {
            Log::warning('Zoomos API returned empty categories list');

            return [
                'created' => 0,
                'updated' => 0,
                'reparented' => 0,
                'processed' => 0,
            ];
        }

        $processed = 0;

        // Map: api L2 zoomos_id => local category id
        $l2IdMap = [];

        foreach ($payload as $level1) {
            $childrenL2 = $level1['children'] ?? [];

            foreach ($childrenL2 as $level2) {
                $localId = $this->upsertCategory(
                    zoomosId: (int) ($level2['id'] ?? 0),
                    title: (string) ($level2['name'] ?? ''),
                    slugCandidate: (string) ($level2['linkRewrite'] ?? ''),
                    parentId: null,
                );

                if ($localId) {
                    $l2IdMap[(int) $level2['id']] = $localId;
                    $processed++;
                }

                $childrenL3 = $level2['children'] ?? [];

                foreach ($childrenL3 as $level3) {
                    $parentLocalId = $l2IdMap[(int) ($level2['id'] ?? 0)] ?? null;

                    $this->upsertCategory(
                        zoomosId: (int) ($level3['id'] ?? 0),
                        title: (string) ($level3['name'] ?? ''),
                        slugCandidate: (string) ($level3['linkRewrite'] ?? ''),
                        parentId: $parentLocalId
                    );

                    $processed++;
                }
            }
        }

        return [
            'created' => $this->categoriesCreated,
            'updated' => $this->categoriesUpdated,
            'reparented' => $this->categoriesReparented,
            'processed' => $processed,
        ];
    }

    public function importBrands(string $apiKey): array
    {
        $this->brandsCreated = 0;
        $this->brandsUpdated = 0;
        $this->brandsSkipped = 0;

        $brands = $this->fetchBrands($apiKey);

        if (empty($brands)) {
            Log::warning('Zoomos API returned empty brands list');

            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'processed' => 0,
            ];
        }

        $processed = 0;

        foreach ($brands as $brandData) {
            if ($this->shouldSkipBrand($brandData)) {
                $this->brandsSkipped++;

                continue;
            }

            $this->upsertBrand($brandData);
            $processed++;
        }

        return [
            'created' => $this->brandsCreated,
            'updated' => $this->brandsUpdated,
            'skipped' => $this->brandsSkipped,
            'processed' => $processed,
        ];
    }

    public function importCharacteristics(string $apiKey): array
    {
        $this->characteristicsCreated = 0;
        $this->characteristicsUpdated = 0;
        $this->characteristicsLinked = 0;
        $this->characteristicsSkipped = 0;

        $characteristics = $this->fetchCharacteristics($apiKey);

        if (empty($characteristics)) {
            Log::warning('Zoomos API returned empty characteristics list');

            return [
                'created' => 0,
                'updated' => 0,
                'linked' => 0,
                'skipped' => 0,
                'processed' => 0,
            ];
        }

        $processed = 0;

        foreach ($characteristics as $characteristicData) {
            $this->processCharacteristic($characteristicData);
            $processed++;
        }

        return [
            'created' => $this->characteristicsCreated,
            'updated' => $this->characteristicsUpdated,
            'linked' => $this->characteristicsLinked,
            'skipped' => $this->characteristicsSkipped,
            'processed' => $processed,
        ];
    }

    public function downloadMissingImages(string $apiKey, ?callable $progressCallback = null): array
    {
        $this->imagesDownloaded = 0;
        $this->imagesSkipped = 0;

        $productsFromApi = $this->fetchProducts($apiKey);

        if (empty($productsFromApi)) {
            Log::warning('Zoomos API returned empty products list for missing images import');

            return [
                'downloaded' => 0,
                'skipped' => 0,
                'total' => 0,
            ];
        }

        $productsIndex = [];
        foreach ($productsFromApi as $productData) {
            if (! isset($productData['id'])) {
                continue;
            }

            $productsIndex[(int) $productData['id']] = $productData;
        }

        $query = Product::whereNull('image')
            ->whereNotNull('zoomos_id');

        $totalProducts = (int) $query->count();
        $processed = 0;

        $query->chunkById(100, function ($products) use (&$processed, $totalProducts, $productsIndex, $progressCallback) {
            foreach ($products as $product) {
                $zoomosId = (int) $product->zoomos_id;

                if (! $zoomosId || ! isset($productsIndex[$zoomosId])) {
                    $this->imagesSkipped++;
                    $processed++;

                    if ($progressCallback) {
                        $progressCallback($processed, $totalProducts);
                    }

                    continue;
                }

                $productData = $productsIndex[$zoomosId];
                $imageUrl = $productData['image'] ?? null;

                if (empty($imageUrl)) {
                    $this->imagesSkipped++;
                    $processed++;

                    if ($progressCallback) {
                        $progressCallback($processed, $totalProducts);
                    }

                    continue;
                }

                $imagePath = $this->downloadImage($imageUrl, $zoomosId);

                if ($imagePath) {
                    $product->update(['image' => $imagePath]);
                    $this->imagesDownloaded++;
                } else {
                    $this->imagesSkipped++;
                }

                $processed++;

                if ($progressCallback) {
                    $progressCallback($processed, $totalProducts);
                }
            }
        });

        Log::info('Zoomos missing images import finished', [
            'downloaded' => $this->imagesDownloaded,
            'skipped' => $this->imagesSkipped,
            'total' => $this->imagesDownloaded + $this->imagesSkipped,
        ]);

        return [
            'downloaded' => $this->imagesDownloaded,
            'skipped' => $this->imagesSkipped,
            'total' => $this->imagesDownloaded + $this->imagesSkipped,
        ];
    }

    private function fetchProducts(string $apiKey): array
    {
        try {
            $response = Http::timeout(60)->get('https://api.zoomos.by/pricelist', [
                'key' => $apiKey,
            ]);

            if (! $response->successful()) {
                Log::error('Zoomos API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('Zoomos API exception', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function fetchCategories(string $apiKey): array
    {
        try {
            $response = Http::timeout(60)->get('https://api.zoomos.by/categories', [
                'key' => $apiKey,
            ]);

            if (! $response->successful()) {
                Log::error('Zoomos categories request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('Zoomos categories exception', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function fetchBrands(string $apiKey): array
    {
        try {
            $response = Http::timeout(60)->get('https://api.zoomos.by/dict/vendors/json', [
                'key' => $apiKey,
            ]);

            if (! $response->successful()) {
                Log::error('Zoomos brands request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('Zoomos brands exception', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function fetchCharacteristics(string $apiKey): array
    {
        try {
            $response = Http::timeout(60)->get('https://api.zoomos.by/dict/features/json', [
                'key' => $apiKey,
            ]);

            if (! $response->successful()) {
                Log::error('Zoomos characteristics request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('Zoomos characteristics exception', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function processProduct(array $productData): void
    {
        // Пропускаем товары где vendor[id] = 0 или model = '?'
        if ($this->shouldSkipProduct($productData)) {
            $this->productsSkipped++;

            return;
        }

        $zoomosId = $productData['id'];
        $this->processedZoomosIds[] = $zoomosId;

        $product = Product::where('zoomos_id', $zoomosId)->first();

        if ($product) {
            $this->updateProduct($product, $productData);
        } else {
            $this->createProduct($productData);
        }
    }

    private function shouldSkipProduct(array $productData): bool
    {
        return ($productData['vendor']['id'] ?? 0) === 0
            || ($productData['model'] ?? '') === '?';
    }

    private function resolveProductTitle(array $productData): string
    {
        $supplierName = $productData['supplierInfo']['name']
            ?? $productData['supplierinfo']['name']
            ?? null;

        if ($supplierName === 'Сиб-инструмент') {
            $model = $productData['model'] ?? '';
            $vendorName = $productData['vendor']['name'] ?? '';
            $modelCode = $productData['modelCode'] ?? '';

            preg_match('/^[а-яА-ЯёЁ\s\.]+/u', $model, $matches);
            $russianPrefix = isset($matches[0]) ? trim($matches[0]) : '';

            $parts = array_filter([$russianPrefix, $vendorName, $modelCode], 'strlen');
            return implode(' ', $parts);
        }

        $supplierModel = $productData['supplierInfo']['model']
            ?? $productData['supplierinfo']['model']
            ?? null;

        if (is_string($supplierModel) && trim($supplierModel) !== '') {
            return trim($supplierModel);
        }

        $prefix = $productData['typePrefix'] ?? null;

        return $prefix
            ? $prefix.' '.$productData['model']
            : $productData['model'];
    }

    private function updateProduct(Product $product, array $productData): void
    {
        $title = $this->resolveProductTitle($productData);

        if (
            str_contains($title, 'Startul Auto') ||
            str_contains($title, 'Домкрат гидравлический')
        ) {
            Log::info('Zoomos import matched special product title', [
                'zoomos_id' => $productData['id'] ?? null,
                'title' => $title,
            ]);
        }
        $brandId = null;
        if (! empty($productData['vendor']['id'])) {
            $brandId = $this->getOrCreateBrand($productData['vendor']);
        }

        try {
            $updates = [
                'h1' => $title,
                'price' => $productData['price'] ?? null,
                'brand_id' => $brandId,
                'balance' => 99,
                'is_active' => $productData['status'] ?? 0,
            ];

            if ($title !== $product->title) {
                $updates['title'] = $title;
            }

            $product->update($updates);

            $this->productsUpdated++;
        } catch (\Exception $e) {
            Log::error('Failed to update product', [
                'zoomos_id' => $productData['id'],
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function updateProductWithDetails(Product $product, array $productData): void
    {
        $this->updateProduct($product, $productData);
    }

    private function createProduct(array $productData): void
    {
        try {
            $categoryId = null;
            if (! empty($productData['category']['id'])) {
                $categoryId = $this->getOrCreateCategory($productData['category']);
            }

            $brandId = null;
            if (! empty($productData['vendor']['id'])) {
                $brandId = $this->getOrCreateBrand($productData['vendor']);
            }

            $imagePath = null;
            if (! empty($productData['image'])) {
                $imagePath = $this->downloadImage($productData['image'], $productData['id']);
            }

            $title = $this->resolveProductTitle($productData);

            if (
                str_contains($title, 'Startul Auto') ||
                str_contains($title, 'Домкрат гидравлический')
            ) {
                Log::info('Zoomos import matched special product title', [
                    'zoomos_id' => $productData['id'] ?? null,
                    'title' => $title,
                ]);
            }

            $slug = Str::slug($title);

            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug.'-'.$counter;
                $counter++;
            }

            $detailedProductData = $this->fetchProductDetails($productData['id']);
            $description = $detailedProductData['fullDescriptionHTML'] ?? null;

            Log::info('Creating product with details', [
                'zoomos_id' => $productData['id'],
                'title' => $title,
                'has_detailed_data' => ! is_null($detailedProductData),
                'has_description' => ! is_null($description),
                'has_features_blocks' => isset($detailedProductData['details']['featuresBlocks']),
            ]);

            $product = Product::create([
                'zoomos_id' => $productData['id'],
                'title' => $title,
                'slug' => $slug,
                'price' => $productData['price'] ?? null,
                'is_active' => $productData['status'] ?? 0,
                'balance' => 99,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'image' => $imagePath,
                'desc' => $description,
            ]);

            Log::info('Product created successfully', [
                'product_id' => $product->id,
                'zoomos_id' => $product->zoomos_id,
            ]);

            if ($detailedProductData && isset($detailedProductData['details']['featuresBlocks'])) {
                Log::info('Linking characteristics to product', [
                    'product_id' => $product->id,
                    'features_blocks_count' => count($detailedProductData['details']['featuresBlocks']),
                ]);
                $this->linkProductCharacteristics($product, $detailedProductData['details']['featuresBlocks']);
            } else {
                Log::warning('No detailed data or features blocks for product', [
                    'product_id' => $product->id,
                    'has_detailed_data' => ! is_null($detailedProductData),
                    'has_features_blocks' => isset($detailedProductData['details']['featuresBlocks']),
                ]);
            }

            $this->productsCreated++;
        } catch (\Exception $e) {
            Log::error('Failed to create product', [
                'zoomos_id' => $productData['id'],
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function getOrCreateCategory(array $categoryData): ?int
    {
        try {
            $zoomosId = $categoryData['id'];

            if (isset($this->categoryCache[$zoomosId])) {
                return $this->categoryCache[$zoomosId];
            }

            $category = Category::where('zoomos_id', $zoomosId)->first();

            if ($category) {
                $this->categoryCache[$zoomosId] = $category->id;

                return $category->id;
            }

            $title = $categoryData['name'] ?? 'Unknown Category';
            $slug = Str::slug($title);

            $originalSlug = $slug;
            $counter = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $originalSlug.'-'.$counter;
                $counter++;
            }

            $category = Category::create([
                'zoomos_id' => $zoomosId,
                'title' => $title,
                'slug' => $slug,
                'is_active' => false,
            ]);

            $this->categoryCache[$zoomosId] = $category->id;

            return $category->id;
        } catch (\Exception $e) {
            Log::error('Failed to get or create category', [
                'category_data' => $categoryData,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    private function getOrCreateBrand(array $vendorData): ?int
    {
        try {
            $zoomosId = $vendorData['id'];

            if (isset($this->brandCache[$zoomosId])) {
                return $this->brandCache[$zoomosId];
            }

            $brand = Brand::where('zoomos_id', $zoomosId)->first();

            if ($brand) {
                $this->brandCache[$zoomosId] = $brand->id;

                return $brand->id;
            }

            $title = $vendorData['name'] ?? 'Unknown Brand';

            $brand = Brand::create([
                'zoomos_id' => $zoomosId,
                'title' => $title,
                'is_active' => 1,
            ]);

            $this->brandCache[$zoomosId] = $brand->id;

            return $brand->id;
        } catch (\Exception $e) {
            Log::error('Failed to get or create brand', [
                'vendor_data' => $vendorData,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    private function downloadImage(string $imageUrl, int $productId): ?string
    {
        try {
            $response = Http::timeout(60)->get($imageUrl);

            if (! $response->successful()) {
                Log::warning('Failed to download image', [
                    'url' => $imageUrl,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $extension = 'jpg';
            $contentType = $response->header('Content-Type');
            if (str_contains($contentType, 'png')) {
                $extension = 'png';
            } elseif (str_contains($contentType, 'gif')) {
                $extension = 'gif';
            } elseif (str_contains($contentType, 'webp')) {
                $extension = 'webp';
            }

            $filename = 'product-'.$productId.'-'.time().'.'.$extension;
            $path = 'products/'.$filename;

            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to download image', [
                'url' => $imageUrl,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function deactivateProducts(): void
    {
        try {
            $deactivatedNull = Product::whereNull('zoomos_id')
                ->where('is_active', 1)
                ->update(['is_active' => 0, 'balance' => 0]);

            $deactivatedMissing = Product::whereNotNull('zoomos_id')
                ->whereNotIn('zoomos_id', $this->processedZoomosIds)
                ->update(['is_active' => 0, 'balance' => 0]);

            $this->productsDeactivated = $deactivatedNull + $deactivatedMissing;

            Log::info('Deactivated products', [
                'null_zoomos_id' => $deactivatedNull,
                'missing_from_api' => $deactivatedMissing,
                'total' => $this->productsDeactivated,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to deactivate products', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function resetCounters(): void
    {
        $this->productsCreated = 0;
        $this->productsUpdated = 0;
        $this->productsSkipped = 0;
        $this->productsDeactivated = 0;
        $this->processedZoomosIds = [];
    }

    private function resetCache(): void
    {
        $this->categoryCache = [];
        $this->brandCache = [];
    }

    private function getStats(): array
    {
        return [
            'created' => $this->productsCreated,
            'updated' => $this->productsUpdated,
            'skipped' => $this->productsSkipped,
            'deactivated' => $this->productsDeactivated,
            'total' => $this->productsCreated + $this->productsUpdated + $this->productsSkipped,
        ];
    }

    private function upsertCategory(int $zoomosId, string $title, string $slugCandidate, ?int $parentId): ?int
    {
        try {
            if ($zoomosId <= 0) {
                return null;
            }

            $title = trim($title) !== '' ? $title : 'Unknown Category';
            $baseSlug = trim($slugCandidate) !== '' ? Str::slug($slugCandidate) : Str::slug($title);

            $category = Category::where('zoomos_id', $zoomosId)->first();

            if ($category) {
                $updates = [];

                if ($category->title !== $title) {
                    $updates['title'] = $title;
                }

                $desiredSlug = $baseSlug;
                if ($category->slug !== $desiredSlug) {
                    $updates['slug'] = $this->ensureUniqueSlug($desiredSlug, $category->id);
                }

                if ($category->parent_id !== $parentId) {
                    $updates['parent_id'] = $parentId;
                    $this->categoriesReparented++;
                }

                if (! empty($updates)) {
                    $category->update($updates);
                    $this->categoriesUpdated++;
                }

                return $category->id;
            }

            $slug = $this->ensureUniqueSlug($baseSlug, null);

            $created = Category::create([
                'zoomos_id' => $zoomosId,
                'title' => $title,
                'slug' => $slug,
                'parent_id' => $parentId,
                'is_active' => false,
            ]);

            $this->categoriesCreated++;

            return $created->id;
        } catch (\Exception $e) {
            Log::error('Failed to upsert category', [
                'zoomos_id' => $zoomosId,
                'title' => $title,
                'slug_candidate' => $slugCandidate,
                'parent_id' => $parentId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    private function ensureUniqueSlug(string $desiredSlug, ?int $ignoreId): string
    {
        try {
            $slug = $desiredSlug !== '' ? $desiredSlug : 'category';
            $original = $slug;
            $counter = 1;

            $exists = function (string $s) use ($ignoreId): bool {
                $query = Category::where('slug', $s);
                if ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                }

                return $query->exists();
            };

            while ($exists($slug)) {
                $slug = $original.'-'.$counter;
                $counter++;
            }

            return $slug;
        } catch (\Exception $e) {
            Log::error('Failed to ensure unique slug', [
                'desired_slug' => $desiredSlug,
                'ignore_id' => $ignoreId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $desiredSlug.'-'.time();
        }
    }

    private function shouldSkipBrand(array $brandData): bool
    {
        $name = $brandData['name'] ?? '';

        return $name === '-' || $name === '?' || $name === '';
    }

    private function upsertBrand(array $brandData): void
    {
        try {
            $zoomosId = $brandData['id'];
            $name = $brandData['name'] ?? 'Unknown Brand';

            $title = json_decode('"'.$name.'"');

            $brand = Brand::where('zoomos_id', $zoomosId)->first();

            if ($brand) {
                $updates = [];

                if ($brand->title !== $title) {
                    $updates['title'] = $title;
                }

                if (! empty($updates)) {
                    $brand->update($updates + ['is_active' => 1]);
                    $this->brandsUpdated++;
                }
            } else {
                Brand::create([
                    'zoomos_id' => $zoomosId,
                    'title' => $title,
                    'pos' => 1000,
                    'is_active' => 1,
                ]);

                $this->brandsCreated++;
            }
        } catch (\Exception $e) {
            Log::error('Failed to upsert brand', [
                'brand_data' => $brandData,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->brandsSkipped++;
        }
    }

    private function processCharacteristic(array $characteristicData): void
    {
        try {
            $name = $characteristicData['name'] ?? '';
            $unit = $characteristicData['unit'] ?? null;
            $categoryName = $characteristicData['category_name'] ?? '';

            if (empty($name) || empty($categoryName)) {
                $this->characteristicsSkipped++;

                return;
            }

            $characteristic = Characteristic::where('title', $name)->first();

            if (! $characteristic) {
                $characteristic = Characteristic::create([
                    'title' => $name,
                    'type' => 1,
                    'pos' => 1000,
                    'is_active' => 1,
                    'measure' => $unit,
                ]);

                $this->characteristicsCreated++;
            } else {
                if ($characteristic->measure !== $unit) {
                    $characteristic->update(['measure' => $unit]);
                    $this->characteristicsUpdated++;
                }
            }

            $category = Category::where('title', $categoryName)->first();

            if (! $category) {
                Log::warning('Category not found for characteristic', [
                    'characteristic' => $name,
                    'category_name' => $categoryName,
                ]);
                $this->characteristicsSkipped++;

                return;
            }

            $isLinked = DB::table('category_characteristic')
                ->where('category_id', $category->id)
                ->where('characteristic_id', $characteristic->id)
                ->exists();

            if (! $isLinked) {
                DB::table('category_characteristic')->insert([
                    'category_id' => $category->id,
                    'characteristic_id' => $characteristic->id,
                    'in_filter' => 0,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->characteristicsLinked++;
            }
        } catch (\Exception $e) {
            Log::error('Failed to process characteristic', [
                'characteristic_data' => $characteristicData,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->characteristicsSkipped++;
        }
    }

    private function fetchProductDetails(int $zoomosId): ?array
    {
        try {
            $apiKey = config('services.zoomos.api_key');
            $url = "https://api.zoomos.by/item/{$zoomosId}";

            $response = Http::timeout(60)->get($url, [
                'key' => $apiKey,
            ]);

            if (! $response->successful()) {
                Log::warning('Failed to fetch product details', [
                    'zoomos_id' => $zoomosId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Failed to fetch product details', [
                'zoomos_id' => $zoomosId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    private function linkProductCharacteristics(Product $product, array $featuresBlocks): void
    {
        try {
            $this->syncProductCharacteristics($product, $featuresBlocks);
        } catch (\Exception $e) {
            Log::error('Failed to link product characteristics', [
                'product_id' => $product->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function syncProductCharacteristics(Product $product, array $featuresBlocks): void
    {
        $pivotData = [];

        foreach ($featuresBlocks as $block) {
            if (! isset($block['features']) || ! is_array($block['features'])) {
                continue;
            }

            foreach ($block['features'] as $feature) {
                $characteristicName = $feature['name'] ?? '';
                $values = $feature['values'] ?? [];

                if ($characteristicName === '' || empty($values)) {
                    continue;
                }

                $characteristic = Characteristic::where('title', $characteristicName)->first();
                if (! $characteristic) {
                    continue;
                }

                $cleanValues = [];
                foreach ($values as $value) {
                    $cleanValue = $this->cleanCharacteristicValue((string) $value);
                    if ($cleanValue !== '') {
                        $cleanValues[] = $cleanValue;
                    }
                }

                if (! empty($cleanValues)) {
                    $pivotData[$characteristic->id] = ['value' => implode(', ', $cleanValues)];
                }
            }
        }

        $product->characteristics()->sync($pivotData);
    }

    private function updateProductDescAndCharacteristics(Product $product, int $zoomosId): void
    {
        try {
            $detailedProductData = $this->fetchProductDetails($zoomosId);

            if (! $detailedProductData) {
                return;
            }

            $newDesc = (string) ($detailedProductData['fullDescriptionHTML'] ?? '');
            $currentDesc = (string) ($product->desc ?? '');
            if ($newDesc !== '' && $newDesc !== $currentDesc) {
                $product->update(['desc' => $newDesc]);
            }

            $featuresBlocks = $detailedProductData['details']['featuresBlocks'] ?? [];
            if (is_array($featuresBlocks)) {
                $this->syncProductCharacteristics($product, $featuresBlocks);
            } else {
                $product->characteristics()->sync([]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update product desc and characteristics', [
                'product_id' => $product->id,
                'zoomos_id' => $zoomosId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function cleanCharacteristicValue(string $value): string
    {
        $units = Characteristic::whereNotNull('measure')
            ->where('measure', '!=', '')
            ->distinct()
            ->pluck('measure')
            ->toArray();

        $additionalUnits = ['шт', 'шт.', 'ат', 'атм'];
        $allUnits = array_merge($units, $additionalUnits);

        $cleanValue = trim($value);

        usort($allUnits, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($allUnits as $unit) {
            $unit = trim($unit);
            if (empty($unit)) {
                continue;
            }

            if (str_ends_with($cleanValue, $unit)) {
                $beforeUnit = substr($cleanValue, 0, -strlen($unit));

                if (empty($beforeUnit) ||
                    substr($beforeUnit, -1) === ' ' ||
                    is_numeric(substr($beforeUnit, -1))) {
                    $cleanValue = rtrim($beforeUnit);
                }
            }
        }

        return trim($cleanValue);
    }
}
