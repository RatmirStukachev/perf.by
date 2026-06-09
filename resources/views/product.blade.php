@extends('layouts.main')

@php
    $cartService = app(App\Services\CartService::class);
    $inCart = $cartService->isProductInCart($page->id);
    $currentCount = $inCart ? $cartService->getProductCount($page->id) : 1;
@endphp

@section('content')
    <section class="s-line s-page-branding md-pt-20 pt-10">
        <div class="container">
            <div class="w-breadcrumbs-mobile-scroll-shadow pb-10">
                @include('general.breadcrumbs')
            </div>
            <h1 class="_h1 pagetitle bold mb-20">{{ $page->h1 ?: $page->title }}</h1>
        </div>
    </section>

    <section class="s-line">
        <div class="container pb-60">
            <div class="row row-product-main-info lg-md-gutters sm-gutters">
                <div class="col-images col-12 col pb-30 order-xl-1 order-1">
                    <div class="w-product-main-image _js-w-fancy">
                        <div class="w-top-slider">
                            <div class="owl-carousel owl-product-slider">
                                @foreach ($page->getAllImages() as $image)
                                    <div class="slide">
                                        <a class="block__link grouped_elements" data-fancybox="gallery" rel="group1" href="{{ asset('storage/' . $image) }}">
                                            <picture>
                                                <img src="{{(new zImage($image, [435, 435], ['contain']))->resize()}}" alt="{{ $page->title }}" title="{{ $page->title }}" class="img block" @if (! $loop->first) loading="lazy" @endif>
                                                @if(env('WEBP'))
                                                    <source
                                                        srcset="{{(new zImage($image['image'], [435, 435], ['contain'], true))->resize()}}">
                                                @endif
                                            </picture>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if (count($page->getAllImages()) > 1)
                            <div class="w-bottom-slider pt-10">
                                <div class="owl-carousel owl-product-slider-thumb">
                                    @foreach ($page->getAllImages() as $image)
                                        <div class="slide">
                                            <div class="w-frame">
                                                <picture>
                                                    <img src="{{(new zImage($image, [71, 71], ['contain']))->resize()}}" alt="{{ $page->title }}" title="{{ $page->title }}" class="img block" @if (! $loop->first) loading="lazy" @endif/>
                                                    @if(env('WEBP'))
                                                        <source
                                                            srcset="{{(new zImage($image, [71, 71], ['contain'], true))->resize()}}">
                                                    @endif
                                                </picture>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-description col-12 col pb-30 order-xl-2 order-3">
                    @if ($page->desc)
                        <article class="article _h6 _js-article-fancy-images _js-article-table-mobile-scroll" data-images-fancy="fancy125">
                            {!! $page->desc !!}
                        </article>
                    @endif
                </div>
                <div class="col-price col-12 col order-xl-3 order-2 pb-10">
                    <div class="w-product-page-aside-price-frame mb-20">
                        <div class="frame align-md-left align-center">
                            <div class="row align-items-center">
                                <div class="col-md-12 col-md-auto col-12 col order-xl-1 order-1">
                                    <div class="w-price-group pt-15">
                                        <div class="w-price">
                                            <div class="w-old-price _h6">
                                                <div class="row align-items-center sm-gutters justify-content-md-start justify-content-center">
                                                    @if ($page->old_price)
                                                        <div class="col-auto">
                                                            <div class="color-orange semibold old-price">{{ format_price($page->old_price) }}</div>
                                                        </div>
                                                        
                                                        <div class="col-auto">
                                                            <div class="old-price-sticker">-{{ discount($page) }}%</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="w-price">
                                                @if ($page->price > 0)
                                                    <div class="_h3 bold">{{ $page->price }} <span class="_h7">{{ $page->category?->measure ? 'BYN/' . $page->category->measure : 'BYN' }}</span></div>
                                                @else
                                                    <div class="_h3 bold">По запросу</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-md-auto col-12 col order-xl-2 order-3">
                                    <div class="w-controlls pt-15">
                                        @if ($page->price > 0 && $page->balance > 0)
                                            <div class="row row-controlls sm-gutters cart-block">
                                                <div class="col-pcs col-12 col pt-5">
                                                    <div class="_js-pcscontrolls pcscontrolls big">
                                                        <a class="btn fcm left minus _js-b-minus">
                                                            <svg width="15" height="15" viewBox="0 0 34 5" xmlns="http://www.w3.org/2000/svg"><path d="M0.333496 2.75008C0.333496 2.19755 0.55299 1.66764 0.943691 1.27694C1.33439 0.886241 1.8643 0.666748 2.41683 0.666748H31.5835C32.136 0.666748 32.6659 0.886241 33.0566 1.27694C33.4473 1.66764 33.6668 2.19755 33.6668 2.75008C33.6668 3.30262 33.4473 3.83252 33.0566 4.22322C32.6659 4.61392 32.136 4.83341 31.5835 4.83341H2.41683C1.8643 4.83341 1.33439 4.61392 0.943691 4.22322C0.55299 3.83252 0.333496 3.30262 0.333496 2.75008Z"></path></svg>
                                                        </a>
                                                        <input type="text" value="{{ $currentCount }}" inputmode="numeric"  class="input__default _js-product-count" data-max="{{ $page->balance }}">
                                                        <a class="btn fcm right plus _js-b-plus">
                                                            <svg width="15" height="15" viewBox="0 0 34 34" xmlns="http://www.w3.org/2000/svg"><path d="M0 17.0833C0 16.5308 0.219494 16.0009 0.610195 15.6102C1.0009 15.2195 1.5308 15 2.08333 15H31.25C31.8025 15 32.3324 15.2195 32.7231 15.6102C33.1138 16.0009 33.3333 16.5308 33.3333 17.0833C33.3333 17.6359 33.1138 18.1658 32.7231 18.5565C32.3324 18.9472 31.8025 19.1667 31.25 19.1667H2.08333C1.5308 19.1667 1.0009 18.9472 0.610195 18.5565C0.219494 18.1658 0 17.6359 0 17.0833Z"></path><path d="M17.0833 0C17.6359 0 18.1658 0.219493 18.5565 0.610194C18.9472 1.00089 19.1667 1.5308 19.1667 2.08333V31.25C19.1667 31.8025 18.9472 32.3324 18.5565 32.7231C18.1658 33.1138 17.6359 33.3333 17.0833 33.3333C16.5308 33.3333 16.0009 33.1138 15.6102 32.7231C15.2195 32.3324 15 31.8025 15 31.25V2.08333C15 1.5308 15.2195 1.00089 15.6102 0.610194C16.0009 0.219493 16.5308 0 17.0833 0Z"></path></svg>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-btn col-12 col pt-5">
                                                    <button class="button to-cart-btn {{ $inCart ? 'btn-to-cart _active' : '_js-add-to-cart' }} block row align-items-center justify-content-center sm-gutters" data-product-id="{{ $page->id }}">
                                                        <div class="col-auto col">
                                                            <svg viewBox="0 0 23 22" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 1C0 0.447715 0.447715 0 1 0H3C3.47158 0 3.87907 0.329457 3.97783 0.790578L4.87936 5H21.04C21.3433 5 21.6302 5.13765 21.82 5.37422C22.0098 5.61079 22.082 5.92071 22.0162 6.21679L20.3666 13.645C20.3666 13.6453 20.3667 13.6447 20.3666 13.645C20.2197 14.3115 19.8498 14.9087 19.3182 15.3368C18.7863 15.7649 18.1244 15.9989 17.4416 16L7.67003 16C6.97666 16.0126 6.30018 15.7846 5.75581 15.3545C5.20825 14.922 4.82861 14.312 4.68225 13.6298L3.10272 6.25468C3.09506 6.22552 3.08869 6.19584 3.08367 6.16571L2.19149 2H1C0.447715 2 0 1.55228 0 1ZM5.3077 7L6.63775 13.2102C6.63778 13.2104 6.63773 13.2101 6.63775 13.2102C6.6866 13.4375 6.81318 13.6411 6.99561 13.7852C7.17813 13.9294 7.40521 14.0054 7.63775 14.0002L7.66 14L17.4384 14C17.4386 14 17.4382 14 17.4384 14C17.6658 13.9995 17.8868 13.9215 18.0639 13.7789C18.2412 13.6362 18.3645 13.4373 18.4134 13.215L19.7936 7H5.3077Z"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M15.95 19.95C15.95 18.8454 16.8454 17.95 17.95 17.95C19.0545 17.95 19.95 18.8454 19.95 19.95C19.95 21.0546 19.0545 21.95 17.95 21.95C16.8454 21.95 15.95 21.0546 15.95 19.95Z"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M4.94995 19.95C4.94995 18.8454 5.84538 17.95 6.94995 17.95C8.05452 17.95 8.94995 18.8454 8.94995 19.95C8.94995 21.0546 8.05452 21.95 6.94995 21.95C5.84538 21.95 4.94995 21.0546 4.94995 19.95Z"></path></svg>
                                                        </div>
                                                        <div class="col-auto col cart-button">
                                                            {{ $inCart ? 'В корзине' : 'В корзину' }}
                                                        </div>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12 col-md-auto col-12 col order-xl-3 order-2">
                                    <div class="row justify-content-md-start justify-content-center">
                                        <div class="col-auto">
                                            <div class="w-counter pt-15">
                                                @if ($page->balance > 0)
                                                    <div class="w-icon-left w-counter-icon color003">
                                                        <div class="icon fcm "><svg viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.84536 9C3.53924 9 3.24542 8.86459 3.03006 8.6229L0.338041 5.62325C-0.11268 5.12102 -0.11268 4.30682 0.338041 3.8046C0.788762 3.30237 1.51945 3.30237 1.97018 3.8046L3.78691 5.82722L7.97415 0.443269C8.39103 -0.0915267 9.11865 -0.15152 9.60167 0.312998C10.0832 0.777516 10.1355 1.58999 9.71858 2.1265L4.71912 8.55434C4.50837 8.82345 4.2084 8.98457 3.88843 8.99657C3.87305 9 3.85921 9 3.84536 9Z" fill="white"/></svg></div>
                                                        <div class="text bold">В наличии</div>
                                                    </div>	
                                                @endif										
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (isset($delivery_block['delivery']) && (!empty($delivery_block['delivery']) || !empty($delivery_block['pickup'])) && $page->balance > 0)
                        <div class="w-product-page-aside-price-frame mb-20">
                            <div class="frame">
                                @if ($delivery_block['title'])
                                    <div class="_h3 bold mb-5 pt-15">{{ $delivery_block['title'] }}</div>
                                @endif
                                <div class="pt-5">
                                    @if ($delivery_block['delivery'])
                                        <div class="w-icon-left w-delivery-type-aside-icon mt-10">
                                            <div class="icon"><svg viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.0298 8.3678H17.0298V4.3678H3.02979C1.92979 4.3678 1.02979 5.2678 1.02979 6.3678V17.3678H3.02979C3.02979 19.0278 4.36979 20.3678 6.02979 20.3678C7.68979 20.3678 9.02979 19.0278 9.02979 17.3678H15.0298C15.0298 19.0278 16.3698 20.3678 18.0298 20.3678C19.6898 20.3678 21.0298 19.0278 21.0298 17.3678H23.0298V12.3678L20.0298 8.3678ZM19.5298 9.8678L21.4898 12.3678H17.0298V9.8678H19.5298ZM6.02979 18.3678C5.47978 18.3678 5.02979 17.9178 5.02979 17.3678C5.02979 16.8178 5.47978 16.3678 6.02979 16.3678C6.57979 16.3678 7.02979 16.8178 7.02979 17.3678C7.02979 17.9178 6.57979 18.3678 6.02979 18.3678ZM8.24979 15.3678C7.69979 14.7578 6.91979 14.3678 6.02979 14.3678C5.13979 14.3678 4.35979 14.7578 3.80979 15.3678H3.02979V6.3678H15.0298V15.3678H8.24979ZM18.0298 18.3678C17.4798 18.3678 17.0298 17.9178 17.0298 17.3678C17.0298 16.8178 17.4798 16.3678 18.0298 16.3678C18.5798 16.3678 19.0298 16.8178 19.0298 17.3678C19.0298 17.9178 18.5798 18.3678 18.0298 18.3678Z" fill="#CBCBCB"/></svg></div>
                                            <div class="text">{{$delivery_block['delivery']  }}</div>
                                        </div>
                                    @endif
                                    @if ($delivery_block['pickup'])
                                        <div class="w-icon-left w-delivery-type-aside-icon mt-10">
                                            <div class="icon"><svg viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.5 12.5C11.4 12.5 10.5 11.6 10.5 10.5C10.5 9.4 11.4 8.5 12.5 8.5C13.6 8.5 14.5 9.4 14.5 10.5C14.5 11.6 13.6 12.5 12.5 12.5ZM18.5 10.7C18.5 7.07 15.85 4.5 12.5 4.5C9.15 4.5 6.5 7.07 6.5 10.7C6.5 13.04 8.45 16.14 12.5 19.84C16.55 16.14 18.5 13.04 18.5 10.7ZM12.5 2.5C16.7 2.5 20.5 5.72 20.5 10.7C20.5 14.02 17.83 17.95 12.5 22.5C7.17 17.95 4.5 14.02 4.5 10.7C4.5 5.72 8.3 2.5 12.5 2.5Z" fill="#CBCBCB"/></svg></div>
                                            <div class="text">{{ $delivery_block['pickup']}}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row lg-md-gutters sm-gutters">
                <div class="col-xl-6 col-12 col mb-20">
                    @if ($page->activeCharacteristics)
                        <div class="w-default-dotts-features _h6">
                            @foreach ($page->activeCharacteristics as $characteristic)
                                @if ($characteristic->pivot->value)
                                    <div class="w-default-dotts-features-item pb-10">
                                        <div class="row sm-gutters align-items-end">
                                            <div class="col-6 col">
                                                <div class="row no-gutters align-items-end">
                                                    <div class="col-auto col-feature-name">
                                                        {{ $characteristic->title }}{{ $characteristic->measure ? ',' . $characteristic->measure : '' }}
                                                    </div>
                                                    <div class="col">
                                                        <div class="dotts"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                {{ $characteristic->pivot->value }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            @if ($page->article)
                                <div class="w-default-dotts-features-item pb-10">
                                    <div class="row sm-gutters align-items-end">
                                        <div class="col-6 col">
                                            <div class="row no-gutters align-items-end">
                                                <div class="col-auto col-feature-name">
                                                    Артикул
                                                </div>
                                                <div class="col">
                                                    <div class="dotts"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            {{ $page->article}}
                                        </div>
                                    </div>
                                </div>
                            @endif     
                        </div>
                    @endif                                    
                </div>
                <div class="col-xl-6 col-12 col mb-30">
                    @if ($page->content)
                        <article class="article _h6 _js-article-fancy-images _js-article-table-mobile-scroll" data-images-fancy="fancy125">
                            {!! $page->content !!}
                        </article>
                    @endif
                </div>
            </div>				
        </div>
    </section>

    @if ($page->getSimilars()->isNotEmpty())
        <section class="s-line s-items-slider">
            <div class="container pb-60">
                <div class="s-name _h2 bold align-sm-left align-center mb-30">Похожие товары</div>
                <div class="w-catalog-list">
                    <div class="owl-carousel owl-catalog-list-slider">
                        @foreach ($page->getSimilars() as $product)
                            <div class="slide">
                                @include('product.preview')
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
    
    @php
        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $page->title,
            'offers' => [
                '@type' => 'Offer',
                'url' => request()->url(),
                'priceCurrency' => 'BYN',
                'availability' => $page->balance > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'perf.by'
                ]
            ]
        ];

        if ($page->getAllImages() && count($page->getAllImages()) > 0) {
            $schema['image'] = [asset('storage/' . $page->getAllImages()[0])];
        }

        if ($page->desc || $page->content) {
            $schema['description'] = strip_tags($page->desc ?: $page->content);
        }

        if ($page->article) {
            $schema['sku'] = $page->article;
        }

        if ($page->brand) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $page->brand->title
            ];
        }

        if ($page->price > 0) {
            $schema['offers']['price'] = $page->price;
        }
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    
@endsection