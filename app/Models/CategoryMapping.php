<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryMapping extends Model
{
    protected $fillable = [
        'visible_category_id',
        'source_category_id',
    ];

    public function visibleCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'visible_category_id');
    }

    public function sourceCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'source_category_id');
    }
}
