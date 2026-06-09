<?php

namespace App\Models;

use App\Models\Traits\UsedFunctions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use UsedFunctions;

    protected $guarded = [];

    protected $casts = [
        'add_info' => 'array',
        'add_images' => 'array',
    ];

    public function getAllImages()
    {
        return array_merge([$this->image], is_null($this->add_images) ? [] : $this->add_images);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(Characteristic::class, 'product_characteristic', 'product_id', 'characteristic_id')
            ->withPivot('value');
    }

    public function activeCharacteristics(): BelongsToMany
    {
        $categoryIds = $this->category?->getRawSelfAndAllParentIds() ?? [];

        return $this->belongsToMany(Characteristic::class, 'product_characteristic', 'product_id', 'characteristic_id')
            ->withPivot('value')
            ->whereIn('characteristic_id', function ($query) use ($categoryIds) {
                $query->select('characteristic_id')
                    ->from('category_characteristic')
                    ->whereIn('category_id', $categoryIds)
                    ->where('is_active', true);
            });
    }

    public function getAllCategoryParentIds(): array
    {
        if (! $this->category) {
            return [];
        }

        $categoryIds = [];
        $currentCategory = $this->category;

        $categoryIds[] = $currentCategory->id;

        while ($currentCategory->parent) {
            $currentCategory = $currentCategory->parent;
            $categoryIds[] = $currentCategory->id;
        }

        return $categoryIds;
    }

    public function similars(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_similars',
            'product_id',
            'similar_id'
        )->where('is_active', true);
    }

    public function getSimilars()
    {
        $similars = $this->similars;

        if ($similars->isEmpty()) {

            $similars = self::query()
                ->with('category')
                ->where('category_id', $this->category_id)
                ->where('id', '!=', $this->id)
                ->where('is_active', true)
                ->where('price', '>', 0)
                ->inRandomOrder()
                ->limit(6)
                ->get();
        }

        return $similars;
    }

    public function hasDiscount(): bool
    {
        if (empty($this->old_price)) {
            return false;
        }

        return ($this->old_price - $this->price) > 0;
    }

    public function getCategoryIds(): array
    {
        if ($this->category->isFirstLevel()) {
            return [$this->category_id];
        }

        return $this->category->getSelfAndAllParentIds();
    }

    public function isCategoriesActive(): bool
    {
        if (! $this->category) {
            // #region agent log
            @file_put_contents(base_path('.cursor/debug-a60b13.log'), json_encode(['sessionId' => 'a60b13', 'hypothesisId' => 'H1', 'location' => 'Product.php:isCategoriesActive', 'message' => 'no category', 'data' => ['product_id' => $this->id], 'timestamp' => round(microtime(true) * 1000)])."\n", FILE_APPEND);

            // #endregion
            return false;
        }

        $category = $this->category;

        if ($category->is_active) {
            $result = $this->isRawCategoryChainActive($category);
            // #region agent log
            @file_put_contents(base_path('.cursor/debug-a60b13.log'), json_encode(['sessionId' => 'a60b13', 'hypothesisId' => 'H1', 'location' => 'Product.php:isCategoriesActive:activeChain', 'message' => 'direct chain check', 'data' => ['product_id' => $this->id, 'category_id' => $category->id, 'result' => $result], 'timestamp' => round(microtime(true) * 1000)])."\n", FILE_APPEND);

            // #endregion
            return $result;
        }

        $mappingExists = CategoryMapping::query()
            ->where('source_category_id', $category->id)
            ->whereHas('visibleCategory', fn ($q) => $q->where('is_active', true))
            ->exists();

        // #region agent log
        @file_put_contents(base_path('.cursor/debug-a60b13.log'), json_encode(['sessionId' => 'a60b13', 'hypothesisId' => 'H1', 'location' => 'Product.php:isCategoriesActive:mapping', 'message' => 'mapping check for inactive source', 'data' => ['product_id' => $this->id, 'category_id' => $category->id, 'cat_title' => $category->title, 'mapping_exists' => $mappingExists], 'timestamp' => round(microtime(true) * 1000)])."\n", FILE_APPEND);
        // #endregion

        return $mappingExists;
    }

    private function isRawCategoryChainActive(Category $category): bool
    {
        $current = $category;

        while ($current) {
            if (! $current->is_active) {
                return false;
            }
            $current = $current->parent_id ? Category::find($current->parent_id) : null;
        }

        return true;
    }
}
