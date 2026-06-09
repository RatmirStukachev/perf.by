<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use App\Services\Support\TextService;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    public function searchProducts(string $searchTerm, ?int $limit = null): Collection|LengthAwarePaginator
    {
        if (empty(trim($searchTerm))) {
            return collect();
        }

       return $this->regularSearch($searchTerm, $limit);        
    }

    private function regularSearch(string $searchTerm, ?int $limit = null): Collection|LengthAwarePaginator
    {       
        $count = TextService::getSettingValue('content', 'products_count'); 
        $words = array_filter(explode(' ', trim($searchTerm)));
        
        $query = Product::isActive()->with('category');

        foreach ($words as $word) {
            $query->where(function($q) use ($word) {
                $q->where('title', 'like', '%' . $word . '%')
                  ->orWhere('h1', 'like', '%' . $word . '%')
                  ->orWhere('article', 'like', '%' . $word . '%');
            });
        }

        $query->orderByRaw('
            CASE 
                WHEN h1 LIKE ? THEN 1 
                WHEN title LIKE ? THEN 2 
                WHEN article LIKE ? THEN 3 
                ELSE 4 
            END
        ', ["%{$searchTerm}%", "%{$searchTerm}%", "%{$searchTerm}%"]);

        return $limit ? $query->limit($limit)->get() : $query->paginate((int)$count);
    }
}
