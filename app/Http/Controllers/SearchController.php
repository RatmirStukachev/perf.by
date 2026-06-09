<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PageService;
use App\Services\SearchService;

class SearchController extends Controller
{
    public function __construct(
        private PageService $pageService,
        private SearchService $searchService,
    ){}
    
    public function search(Request $request)
    {
        $page = $this->pageService->getPage('search');
        $query = trim($request->input('query'));
        $products = $this->searchService->searchProducts($query);

        return view('search', compact('page', 'products', 'query'));
    }
}
