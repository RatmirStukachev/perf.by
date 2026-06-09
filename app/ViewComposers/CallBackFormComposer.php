<?php

namespace App\ViewComposers;

use App\Models\PageContent;
use App\Models\Setting;
use App\Services\ItemService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CallBackFormComposer
{
    const ONE_MINUTE = 60;

    protected $callback_form;

    public function __construct(private ItemService $itemService)
    {
        Cache::forget('callback_form');
        $this->callback_form = Cache::flexible(
            key: 'callback_form',
            ttl: [
                self::ONE_MINUTE,
                config('cache.stores.contacts'),
            ],
            callback: function () {
                return $this->itemService->getPageBlock(key: 'callback_form');
            });
    }

    public function compose(View $view): View
    {
        return $view->with([
            'callback_form' => $this->callback_form,
        ]);
    }
}
