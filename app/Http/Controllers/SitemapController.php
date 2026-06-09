<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::isActive()->get();
        $categories = Category::isActive()->get();

        return response()->view('sitemap', [
            'products' => $products,
            'categories' => $categories,
        ])->header('Content-Type', 'text/xml');
    }
}