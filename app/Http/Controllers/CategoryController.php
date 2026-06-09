<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use App\Services\ItemService;
use App\Services\PageService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function __construct(
        private PageService $pageService,
        private CategoryService $categoryService,
        private ItemService $itemService,
    ) {}

    public function getCatalog(Request $request)
    {
        $page = $this->pageService->getPage('catalog');
        $categories = $this->categoryService->getCatalog();

        return view('catalog', compact('page', 'categories'));
    }

    public function showLevel1(Request $request, Category $category)
    {
        // abort_if(! $category->isActiveThreeLevels(), Response::HTTP_NOT_FOUND);

        $page = $this->pageService->setCategoryPage($category);
        $page->load(['children', 'children.parent']);

        $products = $this->itemService->getProductsForCatalog($category, $request);
        $categories = $this->itemService->getCategoriesForCatalog();
        $filters = $this->categoryService->getFilters($category);

        return view('category', compact('page', 'category', 'products', 'categories', 'filters'));
    }

    public function showLevel2(Request $request, Category $parent, Category $category)
    {
        abort_if(! $category->isActiveThreeLevels(), Response::HTTP_NOT_FOUND);

        $page = $this->pageService->setCategoryPage($category);
        $page->load(['children', 'children.parent']);

        $products = $this->itemService->getProductsForCatalog($category, $request);
        $categories = $this->itemService->getCategoriesForCatalog();
        $filters = $this->categoryService->getFilters($category);

        return view('category', compact('page', 'category', 'products', 'categories', 'filters'));
    }
}
