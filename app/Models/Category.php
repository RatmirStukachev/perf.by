<?php

namespace App\Models;

use App\Models\Traits\UsedFunctions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use UsedFunctions;

    public const MAX_LEVEL = 3;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (Category $category) {
            if ($category->parent_id) {
                $parent = self::find($category->parent_id);
                $category->level = $parent ? $parent->level + 1 : 0;
            } else {
                $category->level = 1;
            }
        });
    }

    public function getLink(): string
    {
        return match ((int) $this->level) {
            1 => route('catalog.level1', $this),
            2 => route('catalog.level2', [($this->parent_id ? self::find($this->parent_id) : null) ?? $this, $this]),
            default => route('catalog.index'),
        };
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id')->where('is_active', true);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->where('is_active', true);
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->where('is_active', true);
    }

    public function parent(): hasOne
    {
        return $this->hasOne(self::class, 'id', 'parent_id')->where('is_active', true);
    }

    public function parentRaw(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function getRawSelfAndAllParentIds(): array
    {
        $ids = [$this->id];
        $current = $this;

        while ($current->parent_id) {
            $current = self::find($current->parent_id);
            if (! $current) {
                break;
            }
            $ids[] = $current->id;
        }

        return $ids;
    }

    public function getAllParentIds(): array
    {
        $parentIds = [];

        if ($this->parent) {
            $parentIds[] = $this->parent->id;
            $parentIds = array_merge($parentIds, $this->parent->getAllParentIds());
        }

        return $parentIds;
    }

    public function getSelfAndAllParentIds(): array
    {
        $ids = [$this->id];

        if ($this->parent) {
            $ids = array_merge($ids, $this->parent->getSelfAndAllParentIds());
        }

        return $ids;
    }

    public function child(): hasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->where('is_active', true)->orderByPos();
    }

    public function children(): hasMany
    {
        return $this->child()->with('children')->orderByPos();
    }

    public function getFirstLevel(): ?Category
    {
        if ($this->isFirstLevel()) {
            return $this;
        }

        $parent = $this->parent;

        if ($parent->parent_id) {
            $parent = $parent->parent;
        }

        return $parent;
    }

    public function hasChildren(): bool
    {
        if ($this->relationLoaded('children')) {
            return $this->children->isNotEmpty();
        }

        if ($this->relationLoaded('child')) {
            return $this->child->isNotEmpty();
        }

        return $this->child()->exists();
    }

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(Characteristic::class, 'category_characteristic', 'category_id', 'characteristic_id')
            ->withPivot('in_filter', 'is_active');
    }

    public function isNotActive(): bool
    {
        return $this->is_active === false;
    }

    public function isNotParent(Category $parent): bool
    {
        return $this->parent_id !== $parent->id;
    }

    public function isFirstLevel(): bool
    {
        return (int) $this->level === 1;
    }

    public function isSecondLevel(): bool
    {
        return (int) $this->level === 2;
    }

    public function isThirdLevel(): bool
    {
        return (int) $this->level === 3;
    }

    public function neighbors(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'parent_id')
            ->where('is_active', true)
            ->orderByPos();
    }

    public function childrenIds(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->select(['id', 'parent_id'])
            ->with('childrenIds');
    }

    protected function getOnlyChildrenIds(): array
    {
        $ids = [];
        foreach ($this->childrenIds as $child) {
            $ids[] = $child->id;
            if ($child->childrenIds->isNotEmpty()) {
                $ids = array_merge($ids, $child->getOnlyChildrenIds());
            }
        }

        return $ids;
    }

    public function getAllChildrenIds(): array
    {
        return array_merge([$this->id], $this->getOnlyChildrenIds());
    }

    public function hasActiveChild($pageId): bool
    {
        if ($this->relationLoaded('children')) {
            foreach ($this->children as $child) {
                if ($child->id === $pageId) {
                    return true;
                }

                if ($child->children->contains('id', $pageId)) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function getCategoryTree(): array
    {
        $categories = self::query()
            ->where('level', 1)
            ->with(['child', 'child.child'])
            ->orderBy('pos')
            ->get();

        $options = [null => 'Категория первого уровня'];

        foreach ($categories as $category) {
            $options[$category->id] = $category->title;

            foreach ($category->child as $childCategory) {
                $options[$childCategory->id] = '⤷ '.$childCategory->title;

                foreach ($childCategory->child as $grandChildCategory) {
                    $options[$grandChildCategory->id] = ' ⤷⤷ '.$grandChildCategory->title;
                }
            }
        }

        return $options;
    }

    public static function getProductCategoryTree(): array
    {
        $categories = self::query()
            ->where('level', 1)
            ->with(['child', 'child.child'])
            ->orderBy('pos')
            ->get();

        $options = [];

        foreach ($categories as $category) {
            $options[$category->id] = $category->title;

            foreach ($category->child as $childCategory) {
                $options[$childCategory->id] = '⤷ '.$childCategory->title;

                foreach ($childCategory->child as $grandChildCategory) {
                    $options[$grandChildCategory->id] = ' ⤷⤷ '.$grandChildCategory->title;
                }
            }
        }

        return $options;
    }

    public function isActiveThreeLevels(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $rawParent = $this->parent_id ? self::find($this->parent_id) : null;

        if ($this->isFirstLevel()) {
            return true;
        }

        if ($this->isSecondLevel()) {
            return (bool) $rawParent?->is_active;
        }

        if ($this->isThirdLevel()) {
            $grandParent = $rawParent?->parent_id ? self::find($rawParent->parent_id) : null;

            return (bool) ($rawParent?->is_active && $grandParent?->is_active);
        }

        return false;
    }
}
