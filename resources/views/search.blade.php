@extends('layouts.main')

@section('content')
    <section class="s-line s-page-branding md-pt-20 pt-10">
        <div class="container">
            <div class="w-breadcrumbs-mobile-scroll-shadow pb-10">
                @include('general.breadcrumbs')
            </div>
            <h1 class="_h1 pagetitle bold mb-20">
                {{ $page->h1 
                    ? ($page->h1 . (isset($query) ? ': ' . $query : ''))
                    : ($page->title . (isset($query) ? ': ' . $query : '')) }}
            </h1>
        </div>
    </section>
    <section class="s-line">
        <div class="container md-pb-10 pb-20">
            <div class="row xl-lg-gutters md-md-gutters sm-gutters">
                @if ($products?->isNotEmpty())
                    @foreach ($products as $product)
                        <div class="col-xl-3 col-md-4 col-sm-6 col-xxs-6 col-12 col md-mb-30 mb-20">
                            @include('product.preview')
                        </div>
                    @endforeach
                @else
                    <p>Товары не найдены</p>
                @endif
            </div>
            <div class="row justify-content-between sm-gutters">
                <div class="col-md-auto col-12 col pt-10">
                    @if ($products?->isNotEmpty())
                        {{ $products->appends(request()->query())->onEachSide(1)->links('vendor.pagination') }}
                    @endif
                </div>
            </div>      
        </div>
    </section>
@endsection