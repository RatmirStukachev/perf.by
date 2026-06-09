<?php

namespace App\Services;

use App\Enums\ChTypeEnum;
use App\Models\Category;
use App\Models\CategoryMapping;
use App\Models\Product;
use App\Services\Support\TextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public const FIRST_LEVEL = 1;

    public const SECOND_LEVEL = 2;

    public function getCatalog()
    {
        return Category::query()
            ->where('level', self::FIRST_LEVEL)
            ->where('is_active', true)
            ->orderByPos()
            ->get();
    }

    public function getCategories()
    {
        return Category::query()
            ->where('level', self::SECOND_LEVEL)
            ->where('is_active', true)
            ->with(['child', 'parent'])
            ->orderBy('title')
            ->orderByPos()
            ->get();
    }

    public function getPopularCategories()
    {
        return Category::query()
            ->where('is_active', true)
            ->with(['child', 'parent'])
            ->where('is_popular', true)
            ->get();
    }

    public function loadRelations(Category $category)
    {
        return match (true) {
            $category->isFirstLevel() => $category->load('children'),
            $category->isSecondLevel() => $category->load(['children.parent']),
            $category->isThirdLevel() => $category->load(['parent.parent', 'neighbors.parent']),
            default => $category
        };
    }

    public function getFilters(Category $category): array
    {
        $sourceCategoryIds = $this->getSourceCategoryIdsForVisibleCategory($category);
        $categoryIdsForProducts = $sourceCategoryIds !== [] ? $sourceCategoryIds : $category->getAllChildrenIds();
        $categoryIdsForCharacteristics = $sourceCategoryIds !== [] ? $this->getSourceSelfAndParentCategoryIds($sourceCategoryIds) : $category->getSelfAndAllParentIds();

        $brands = Product::query()
            ->select('brands.id', 'brands.title')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->where('products.is_active', true)
            ->whereIn('category_id', $categoryIdsForProducts)
            ->groupBy('brands.id', 'brands.title')
            ->orderBy('brands.pos')
            ->orderBy('brands.title')
            ->get();

        $characteristics_checkbox = DB::table('characteristics')
            ->select([
                'characteristics.id',
                'characteristics.title',
                'characteristics.measure',
                DB::raw('JSON_ARRAYAGG(product_characteristic.value) as value'),
            ])
            ->join('category_characteristic', function ($join) {
                $join->on('characteristics.id', '=', 'category_characteristic.characteristic_id')
                    ->where('category_characteristic.in_filter', true)
                    ->where('category_characteristic.is_active', true);
            })
            ->join('product_characteristic', 'characteristics.id', '=', 'product_characteristic.characteristic_id')
            ->join('products', function ($join) {
                $join->on('products.id', '=', 'product_characteristic.product_id')
                    ->where('products.is_active', true);
            })
            ->whereIn('category_characteristic.category_id', $categoryIdsForCharacteristics)
            ->whereIn('products.category_id', $categoryIdsForProducts)
            ->where('characteristics.type', ChTypeEnum::CHECKBOX)
            ->groupBy('characteristics.id', 'characteristics.title', 'characteristics.measure')
            ->orderBy('characteristics.pos')
            ->orderBy('characteristics.title')
            ->get()
            ->map(function ($characteristic) {
                $values = json_decode($characteristic->value, true);
                $characteristic->value = array_values(array_unique($values));
                sort($characteristic->value);

                return $characteristic;
            });

        $characteristics_range = DB::table('characteristics')
            ->select([
                'characteristics.id',
                'characteristics.title',
                'characteristics.measure',
                DB::raw('MIN(CAST(REPLACE(product_characteristic.value, ",", ".") AS DECIMAL(10,2))) as min_value'),
                DB::raw('MAX(CAST(REPLACE(product_characteristic.value, ",", ".") AS DECIMAL(10,2))) as max_value'),
            ])
            ->join('category_characteristic', function ($join) {
                $join->on('characteristics.id', '=', 'category_characteristic.characteristic_id')
                    ->where('category_characteristic.in_filter', true)
                    ->where('category_characteristic.is_active', true);
            })
            ->join('product_characteristic', 'characteristics.id', '=', 'product_characteristic.characteristic_id')
            ->join('products', function ($join) {
                $join->on('products.id', '=', 'product_characteristic.product_id')
                    ->where('products.is_active', true);
            })
            ->whereIn('category_characteristic.category_id', $categoryIdsForCharacteristics)
            // ->whereIn('products.category_id', function($query) {
            //     $query->select('category_id')
            //         ->from('category_characteristic')
            //         ->where('in_filter', true)
            //         ->where('is_active', true)
            //         ->whereColumn('characteristic_id', 'characteristics.id');
            // })
            ->whereIn('products.category_id', $categoryIdsForProducts)
            ->where('characteristics.type', ChTypeEnum::RANGE)
            ->groupBy('characteristics.id', 'characteristics.title', 'characteristics.measure')
            ->orderBy('characteristics.title')
            ->get();

        $prices = Product::query()
            ->selectRaw('min(products.price) as min_price, max(products.price) as max_price')
            ->where('is_active', true)
            ->whereIn('category_id', $categoryIdsForProducts)
            ->first();

        return [
            'brands' => $brands,
            'characteristics_checkbox' => $characteristics_checkbox,
            'characteristics_range' => $characteristics_range,
            'prices' => $prices,
        ];
    }

    /**
     * @return array<int, int>
     */
    private function getSourceCategoryIdsForVisibleCategory(Category $category): array
    {
        $visibleIds = $category->getAllChildrenIds();

        return CategoryMapping::query()
            ->whereIn('visible_category_id', $visibleIds)
            ->pluck('source_category_id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $sourceCategoryIds
     * @return array<int, int>
     */
    private function getSourceSelfAndParentCategoryIds(array $sourceCategoryIds): array
    {
        $categories = Category::query()
            ->with(['parent.parent'])
            ->whereIn('id', $sourceCategoryIds)
            ->get();

        $ids = [];
        foreach ($categories as $category) {
            $ids = array_merge($ids, $category->getSelfAndAllParentIds());
        }

        return array_values(array_unique($ids));
    }

    public function filterProducts(Request $request)
    {
        $count = TextService::getSettingValue('content', 'products_count');
        $category = Category::findOrFail($request->category_id);

        return Product::query()
            ->where('is_active', true)
            ->whereIn('category_id', $category->getAllChildrenIds())
            ->when($request->min_price, function ($query) use ($request) {
                $query->where('price', '>=', $request->min_price);
            })
            ->when($request->max_price, function ($query) use ($request) {
                $query->where('price', '<=', $request->max_price);
            })
            ->when($request->brands, function ($query) use ($request) {
                $query->whereIn('brand_id', $request->brands);
            })
            ->when($request->has('filters'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    foreach ($request->filters as $characteristicId => $values) {
                        $query->whereHas('characteristics', function ($query) use ($characteristicId, $values) {
                            $query->where('characteristics.id', $characteristicId)
                                ->whereIn('product_characteristic.value', $values);
                        });
                    }
                });
            })
            ->when($request->has('filters_range'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    foreach ($request->filters_range as $characteristicId => $range) {
                        if (! empty($range['min']) || ! empty($range['max'])) {
                            $query->whereHas('characteristics', function ($query) use ($characteristicId, $range) {
                                $query->where('characteristics.id', $characteristicId);

                                if (! empty($range['min'])) {
                                    $query->where(DB::raw('CAST(REPLACE(product_characteristic.value, ",", ".") AS DECIMAL(10,2))'), '>=', $range['min']);
                                }

                                if (! empty($range['max'])) {
                                    $query->where(DB::raw('CAST(REPLACE(product_characteristic.value, ",", ".") AS DECIMAL(10,2))'), '<=', $range['max']);
                                }
                            });
                        }
                    }
                });
            })
            ->orderByRaw('
                CASE
                    WHEN availability = 1 THEN 0
                    ELSE 1
                END
            ')
            ->when($request->sort, function ($query) use ($request) {
                return match ($request->sort) {
                    'price_asc' => $query->orderBy('products.price', 'asc'),
                    'price_desc' => $query->orderBy('products.price', 'desc'),
                    'new' => $query->orderBy('products.updated_mc', 'desc'),
                    'old' => $query->orderBy('products.updated_mc', 'asc'),
                    default => $query
                };
            })
            ->paginate((int) $count);
    }
}
