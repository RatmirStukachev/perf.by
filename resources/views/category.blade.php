@extends('layouts.main')

@section('content')
    <section class="s-line s-page-branding md-pt-20 pt-10">
        <div class="container">
            <div class="w-breadcrumbs-mobile-scroll-shadow pb-10">
                @include('general.breadcrumbs')
            </div>
            <h1 class="_h1 pagetitle bold mb-20">{{ $page->h1 ?: $page->title }}</h1>
        </div>
    </section>

    <section class="s-line  _js-mobile-menu catalog-aside-filter">
        <div class="container pb-60">
            <div class="row lg-md-gutters sm-gutters">
                <div class="col-xl-3 col-12 col">
                    <div class="navigation-menu left-side _js-navigation-menu catalog-aside-filter">
                        <div class="menu-layout body-layout _js-b-toggle-navigation-menu catalog-aside-filter" data-nav-id="catalog-aside-filter"></div>
                        <div class="mobile-menu-header">
                            Подобрать по параметрам
                            <a href="" class="close white _js-b-toggle-navigation-menu catalog-aside-filter" data-nav-id="catalog-aside-filter"></a>
                        </div>
                        <div class="navigation-menu-body">
                            @if ($categories?->isNotEmpty())
                                <ul class="ul-catalog-page-aside-nav mb-20 col-xl-hide">
                                    @foreach($categories as $category)
                                        <li class="li @if ((request()->route('category')?->id == $category->id) || (request()->route('parent')?->id == $category->id)) _active @endif">
                                            <a href="{{ $category->getLink()  }}" class="__link">{{ $category->h1 ?: $category->title }}</a></li>
                                            @if ((request()->route('category')?->id == $category->id && $category->hasChildren()) || (request()->route('parent')?->id == $category->id && $category->hasChildren()))
                                                <div class="inset pt-15 pb-20">
                                                    <ul class="ul-inset">
                                                        @foreach($category->children->sortBy('title') as $child)
                                                            <li class="li @if (request()->route('category')?->id == $child->id) _active @endif"><a href="{{ $child->getLink() }}" class="__link">{{ $child->h1 ?: $child->title }}</a></li>
                                                        @endforeach                                        
                                                    </ul>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach							
                                </ul>
                            @endif

                            <form action="{{ $page->getLink() }}" id="filters-form">
                                @if(isset($filters['brands']) && $filters['brands']->isNotEmpty())
                                    <div class="mb-20">
                                        <div class="s-name _h4 semibiold mb-10">Бренд</div>
                                        <div class="w-checkboxes-group _js-show-more-filters-group">
                                            @foreach($filters['brands'] as $oneBrand)
                                                <div class="custom-selector check mb-10">
                                                    <label class="block">
                                                        <div class="input">
                                                            <input type="checkbox" 
                                                                name="brands[]"
                                                                value="{{ $oneBrand->id }}" 
                                                                class="selector hidden" 
                                                                @if(request()->input('brands') && in_array($oneBrand->id, request()->input('brands')))
                                                                    checked
                                                                @endif
                                                                >
                                                            <div class="styled-figure">
                                                                <div class="border">
                                                                    <div class="inset-figure"></div>
                                                                </div>
                                                            </div>
                                                            <div class="label label-inner">{{ $oneBrand->title }}</div>
                                                        </div> 
                                                    </label>
                                                </div>
                                            @endforeach
                                            <div class="w-more mb-10">
                                                <a href="" class="nul color-orange more _js-b-double-changed _js-b-show-more">
                                                    <div class="info _active"><span class="dashed dott">Посмотреть все</span></div>
                                                    <div class="info"><span class="dashed dott">Свернуть</span></div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($filters['characteristics_checkbox']) && $filters['characteristics_checkbox']->isNotEmpty())
                                    @foreach($filters['characteristics_checkbox'] as $characteristic)
                                        @if(collect($characteristic->value)->filter()->isNotEmpty())
                                            <div class="mb-20">
                                                <div class="s-name _h4 semibiold mb-10">{{ $characteristic->title }}@if($characteristic->measure), {{ $characteristic->measure }}@endif</div>
                                                <div class="w-checkboxes-group _js-show-more-filters-group">
                                                    @foreach($characteristic->value as $value)
                                                        <div class="custom-selector check mb-10">
                                                            <label class="block">
                                                                <div class="input">
                                                                    <input type="checkbox" 
                                                                        name="filters[{{ $characteristic->id }}][]" 
                                                                        value="{{ $value }}" 
                                                                        class="selector hidden"
                                                                        @if(request()->input('filters.' . $characteristic->id) && in_array($value, request()->input('filters.' . $characteristic->id))) 
                                                                            checked 
                                                                        @endif
                                                                        >
                                                                    <div class="styled-figure">
                                                                        <div class="border">
                                                                            <div class="inset-figure"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="label label-inner">{{ $value }}</div>
                                                                </div> 
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    <div class="w-more mb-10">
                                                        <a href="" class="nul color-orange more _js-b-double-changed _js-b-show-more">
                                                            <div class="info _active"><span class="dashed dott">Посмотреть все</span></div>
                                                            <div class="info"><span class="dashed dott">Свернуть</span></div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                @if ($filters['characteristics_checkbox']?->isNotEmpty() || $filters['brands']?->isNotEmpty() )
                                    <div class="row row-submit sm-gutters">
                                        <div class="col-submit col">
                                            <button type="submit" class="button block">Найти</button>
                                        </div>
                                        <div class="col-del col">
                                            <a href="{{ url()->current() }}" class="button block">
                                                <svg width="14" height="19" viewBox="0 0 14 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.6665 4.45076V2.7841C3.6665 2.34207 3.8421 1.91815 4.15466 1.60559C4.46722 1.29303 4.89114 1.11743 5.33317 1.11743H8.6665C9.10853 1.11743 9.53245 1.29303 9.84502 1.60559C10.1576 1.91815 10.3332 2.34207 10.3332 2.7841V4.45076M12.8332 4.45076V16.1174C12.8332 16.5595 12.6576 16.9834 12.345 17.2959C12.0325 17.6085 11.6085 17.7841 11.1665 17.7841H2.83317C2.39114 17.7841 1.96722 17.6085 1.65466 17.2959C1.3421 16.9834 1.1665 16.5595 1.1665 16.1174V4.45076H12.8332Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            
                <div class="col-xl-9 col-12 col">
                    <div class="col-lg-show mb-20">
                        <a href="" class="button block _js-b-toggle-navigation-menu" data-nav-id="catalog-aside-filter">
                            <div class="row align-items-center justify-content-center sm-gutters">
                                <div class="col-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="22" height="22" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve"><path fill="#fff" d="m16 90.259h243.605c7.342 33.419 37.186 58.508 72.778 58.508s65.436-25.088 72.778-58.508h90.839c8.836 0 16-7.164 16-16s-7.164-16-16-16h-90.847c-7.356-33.402-37.241-58.507-72.77-58.507-35.548 0-65.419 25.101-72.772 58.507h-243.611c-8.836 0-16 7.164-16 16s7.164 16 16 16zm273.877-15.958c0-.057.001-.115.001-.172.07-23.367 19.137-42.376 42.505-42.376 23.335 0 42.403 18.983 42.504 42.339l.003.235c-.037 23.407-19.091 42.441-42.507 42.441-23.406 0-42.454-19.015-42.507-42.408zm206.123 347.439h-90.847c-7.357-33.401-37.241-58.507-72.77-58.507-35.548 0-65.419 25.102-72.772 58.507h-243.611c-8.836 0-16 7.163-16 16s7.164 16 16 16h243.605c7.342 33.419 37.186 58.508 72.778 58.508s65.436-25.089 72.778-58.508h90.839c8.836 0 16-7.163 16-16s-7.164-16-16-16zm-163.617 58.508c-23.406 0-42.454-19.015-42.507-42.408l.001-.058c0-.058.001-.115.001-.172.07-23.367 19.137-42.377 42.505-42.377 23.335 0 42.403 18.983 42.504 42.338l.003.235c-.034 23.41-19.089 42.442-42.507 42.442zm163.617-240.248h-243.605c-7.342-33.419-37.186-58.507-72.778-58.507s-65.436 25.088-72.778 58.507h-90.839c-8.836 0-16 7.164-16 16 0 8.837 7.164 16 16 16h90.847c7.357 33.401 37.241 58.507 72.77 58.507 35.548 0 65.419-25.102 72.772-58.507h243.611c8.836 0 16-7.163 16-16 0-8.836-7.164-16-16-16zm-273.877 15.958c0 .058-.001.115-.001.172-.07 23.367-19.137 42.376-42.505 42.376-23.335 0-42.403-18.983-42.504-42.338l-.003-.234c.035-23.41 19.09-42.441 42.507-42.441 23.406 0 42.454 19.014 42.507 42.408z"></path></svg>
                                </div>
                                <div class="col-auto">Подобрать по параметрам</div>
                            </div>
                        </a>
                    </div>
                    <div class="w-catalog-list">
                        <div class="row row-catalog-list lg-md-gutters sm-gutters">
                            @if ($products?->isNotEmpty())
                                @foreach($products as $product)
                                    <div class="col-xl-4 col-md-4 col-xxs-6 col-12 col md-mb-20 mb-10">
                                        @include('product.preview')
                                    </div>
                                @endforeach
                            @else
                                <div class="col-xl-4 col-md-4 col-xxs-6 col-12 col md-mb-20 mb-10">
                                    <p>Товаров не найдено</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if ($products?->isNotEmpty())
                        {{ $products->appends(request()->query())->onEachSide(1)->links('vendor.pagination') }}
                    @endif
                </div>
            </div>
            @if(!request()->has('page') || request()->input('page') <= 1)
                <div class="seo-content container pb-20 pt-20">
                    @include('seo.content')
                </div>
            @endif
        </div>
    </section>
@endsection