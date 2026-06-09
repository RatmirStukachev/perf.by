<?php

namespace App\Services\Support;

use App\Models\Category;
use App\Models\CategoryMapping;
use App\Models\Page;
use App\Models\Point;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\View;

class BreadcrumbsService
{
    private $bread = [
        'Главная' => '/',
    ];

    /**
     * Генерация для отдельных страниц
     *
     * @return $this
     */
    public function page($page)
    {
        $this->bread[$page->title] = '/'.$page->slug;

        return $this;
    }

    public function pageAccount($page)
    {
        $this->bread['Личный кабинет'] = '/account';
        $this->bread[$page->title] = end($this->bread).'/'.$page->slug;

        return $this;
    }

    public function pageArticle($page)
    {
        $this->bread['Статьи'] = '/articles';
        $this->bread[$page->title] = end($this->bread).'/'.$page->slug;

        return $this;
    }

    public function pageBrand($page)
    {
        $this->page(Page::where('slug', 'brands-list')->first());
        $this->bread[$page->title] = end($this->bread).'/'.$page->slug;

        return $this;
    }

    public function pageCategory(Category $category)
    {
        $rawParent = $category->parent_id ? Category::find($category->parent_id) : null;
        if ($rawParent) {
            $this->pageCategory($rawParent);
        }
        $this->bread['Каталог'] = '/catalog';
        $this->bread[($category->h1 ?: $category->title)] = '/catalog/'.$category->slug;

        return $this;
    }

    public function pageSubcategory(Category $category)
    {
        $rawParent = $category->parent_id ? Category::find($category->parent_id) : null;
        if ($rawParent) {
            $this->pageCategory($rawParent);
        }
        $this->bread['Каталог'] = '/catalog';
        $this->bread[($category->h1 ?: $category->title)] = end($this->bread).'/'.$category->slug;

        return $this;
    }

    public function pageLastCategory(Category $category)
    {
        $rawParent = $category->parent_id ? Category::find($category->parent_id) : null;
        if ($rawParent) {
            $this->pageSubcategory($rawParent);
        }
        $this->bread[$category->title] = end($this->bread).'/'.$category->slug;

        return $this;
    }

    public function pageNews($page)
    {
        $this->page(Page::where('slug', 'news-list')->first());
        $this->bread[$page->title] = end($this->bread).'/'.$page->slug;

        return $this;
    }

    public function pagePoint(Point $point)
    {
        match ($point->category->level) {
            3 => $this->pageLastCategory($point->category),
            2 => $this->pageSubcategory($point->category),
            1 => $this->pageCategory($point->category),
            default => null,
        };
        $this->bread[$point->title] = end($this->bread).'/'.$point->slug;

        return $this;
    }

    public function pageService(Service $service)
    {
        $this->bread['Услуги'] = '/services';
        $this->bread[$service->title] = end($this->bread).'/'.$service->slug;

        return $this;
    }

    public function pageLastService(Service $service)
    {
        $this->bread['Услуги'] = '/services';
        $this->bread[$service->parent?->title] = end($this->bread).'/'.$service->parent?->slug;
        $this->bread[$service->title] = end($this->bread).'/'.$service->slug;

        return $this;
    }

    public function pageProduct(Product $product)
    {
        $category = $this->resolveVisibleCategory($product->category, $product) ?? $product->category;

        // #region agent log
        @file_put_contents(base_path('.cursor/debug-a60b13.log'), json_encode(['sessionId' => 'a60b13', 'hypothesisId' => 'H2', 'location' => 'BreadcrumbsService.php:pageProduct', 'message' => 'resolved category for breadcrumbs', 'data' => ['product_id' => $product->id, 'original_cat' => $product->category_id, 'resolved_cat' => $category->id, 'resolved_title' => $category->title, 'level' => $category->level], 'timestamp' => round(microtime(true) * 1000)])."\n", FILE_APPEND);
        // #endregion

        match ((int) $category->level) {
            3 => $this->pageLastCategory($category),
            2 => $this->pageSubcategory($category),
            1 => $this->pageCategory($category),
            default => null,
        };

        $this->bread[$product->title.' '.$product->h1] = end($this->bread).'/'.$product->slug;

        return $this;
    }

    private function resolveVisibleCategory(?Category $sourceCategory, ?Product $product = null): ?Category
    {
        if (! $sourceCategory) {
            return null;
        }

        if ($sourceCategory->id === 3 && $product) {
            $isToolCharger = $product->characteristics()
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('characteristics.id', 476)
                            ->whereRaw('TRIM(product_characteristic.value) IN (?, ?)', [
                                'для строительного инструмента',
                                'для строительного инструмента, для садового инструмента',
                            ]);
                    })->orWhere(function ($q) {
                        $q->where('characteristics.id', 852)
                            ->whereRaw('TRIM(product_characteristic.value) IN (?, ?, ?, ?)', ['18 В', '12 В', '18', '12']);
                    });
                })->exists();

            $isCarJumpStarter = $product->characteristics()
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('characteristics.id', 5)
                            ->whereRaw('TRIM(product_characteristic.value) IN (?, ?)', ['стартовые провода', 'пуско-зарядное']);
                    })->orWhere(function ($q) {
                        $q->where('characteristics.id', 66)
                            ->whereRaw('TRIM(product_characteristic.value) IN (?, ?, ?, ?)', ['220 В', '230 В', '220', '230']);
                    })->orWhere(function ($q) {
                        $q->where('characteristics.id', 850)
                            ->whereRaw('TRIM(product_characteristic.value) IN (?, ?, ?, ?, ?, ?)', ['100 А', '500 А', '700 А', '100', '500', '700']);
                    });
                })->exists();

            if ($isToolCharger) { return Category::find(193); }
            if ($isCarJumpStarter) { return Category::find(257); }
        }

        if ($sourceCategory->is_active) {
            return $sourceCategory;
        }

        $mapping = CategoryMapping::query()
            ->where('source_category_id', $sourceCategory->id)
            ->with('visibleCategory')
            ->first();

        return $mapping?->visibleCategory;
    }

    public function generate()
    {
        $lastKeyBread = array_keys($this->bread)[count($this->bread) - 1];
        $this->bread[$lastKeyBread] = '';

        View::share([
            'breadcrumbs' => $this->bread,
        ]);
    }
}
