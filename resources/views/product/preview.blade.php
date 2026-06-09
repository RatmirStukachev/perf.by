@php
    $cartService = app(App\Services\CartService::class);
    $inCart = $cartService->isProductInCart($product->id);
    $currentCount = $inCart ? $cartService->getProductCount($product->id) : 1;
@endphp

<div class="w-catalog-list-item">
    <div class="frame">
        <div class="row flex-column justify-content-between">
            <div class="col-auto col">
                <div class="w-image">
                    <div class="w-stickers">
                        <div class="row">
                            @if ($product->old_price)
                                <div class="col-auto col">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 1.60786C19.8242 2.66108 21.3391 4.17593 22.3923 6.00016C23.4455 7.8244 24 9.89373 24 12.0002C24 14.1066 23.4455 16.1759 22.3922 18.0001C21.339 19.8244 19.8241 21.3392 17.9999 22.3924C16.1756 23.4456 14.1063 24 11.9998 24C9.89336 24 7.82402 23.4455 5.9998 22.3922C4.17558 21.339 2.66075 19.8241 1.60756 17.9998C0.554376 16.1756 -5.35076e-05 14.1062 3.87318e-09 11.9998L0.00600014 11.611C0.0732039 9.53859 0.676257 7.51897 1.75637 5.74902C2.83648 3.97907 4.35678 2.51919 6.16907 1.51172C7.98136 0.504243 10.0238 -0.0164528 12.0972 0.000396292C14.1706 0.0172454 16.2043 0.571064 18 1.60786ZM15 13.1998C14.5226 13.1998 14.0648 13.3894 13.7272 13.727C13.3896 14.0645 13.2 14.5224 13.2 14.9998C13.2 15.4771 13.3896 15.935 13.7272 16.2725C14.0648 16.6101 14.5226 16.7997 15 16.7997C15.4774 16.7997 15.9352 16.6101 16.2728 16.2725C16.6104 15.935 16.8 15.4771 16.8 14.9998C16.8 14.5224 16.6104 14.0645 16.2728 13.727C15.9352 13.3894 15.4774 13.1998 15 13.1998ZM16.4484 7.55142C16.2234 7.32645 15.9182 7.20008 15.6 7.20008C15.2818 7.20008 14.9766 7.32645 14.7516 7.55142L7.5516 14.7514C7.33301 14.9777 7.21206 15.2808 7.21479 15.5954C7.21753 15.9101 7.34373 16.211 7.56622 16.4335C7.78871 16.656 8.08968 16.7822 8.40432 16.785C8.71896 16.7877 9.02208 16.6667 9.2484 16.4482L12.8484 12.8482L16.4484 9.2482C16.6734 9.02317 16.7997 8.718 16.7997 8.39981C16.7997 8.08162 16.6734 7.77645 16.4484 7.55142ZM9 7.19982C8.52261 7.19982 8.06477 7.38946 7.72721 7.72702C7.38964 8.06459 7.2 8.52242 7.2 8.99981C7.2 9.47719 7.38964 9.93502 7.72721 10.2726C8.06477 10.6102 8.52261 10.7998 9 10.7998C9.47739 10.7998 9.93523 10.6102 10.2728 10.2726C10.6104 9.93502 10.8 9.47719 10.8 8.99981C10.8 8.52242 10.6104 8.06459 10.2728 7.72702C9.93523 7.38946 9.47739 7.19982 9 7.19982Z" fill="#FF5F00"/></svg>
                                </div>
                            @endif
                            @if ($product->is_new)
                                <div class="col-auto col">
                                    <div class="product-color-sticker color001">
                                        NEW
                                    </div>
                                </div>
                            @endif
                            @if ($product->is_hit)
                                <div class="col-auto col">
                                    <div class="product-color-sticker color002">
                                        ХИТ
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="image">
                        <a href="{{ route('product', $product) }}" class="block__link">
                            <picture>
                                <img src="{{(new zImage($product->image, [283, 283], ['contain']))->resize()}}" alt="{{ $product->title }}" title="{{ $product->title }}" class="img block" loading="lazy"
                                @if (!$product->image) style="aspect-ratio: 1/1; object-fit: cover; object-position: center;" @endif
                                >
                                @if(env('WEBP'))
                                    <source
                                        srcset="{{(new zImage($product->image, [283, 283], ['contain'], true))->resize()}}">
                                @endif
                            </picture>
                        </a>
                    </div>
                </div>
                <div class="w-name md-pt-10 pt-5 pb-10">
                    <div class="w-price-group">
                        <div class="w-price">
                            <div class="w-old-price">
                                @if ($product->old_price)
                                    <div class="row align-items-center sm-gutters">
                                        <div class="col-auto">
                                            <div class="color-orange semibold old-price">{{ format_price($product->old_price) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="old-price-sticker">-{{ discount($product) }}%</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="w-price">
                                @if ($product->price > 0 && $product->balance > 0)
                                    <div class="_h5 bold">{{ $product->price }} <span class="_h7">{{ $product->category?->measure ? 'BYN/' . $page->category->measure : 'BYN' }}</span></div>
                                @else
                                    <div class="_h5 bold">По запросу</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="name bold mt-5">
                        <a href="{{ route('product', $product) }}" class="name__link block color-black nul">
                            {{ $product->h1 ?: $product->title }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-auto col">
                <div class="w-controlls pb-5">
                    @if ($product->price > 0 && $product->balance > 0)
                        <div class="row row-controlls sm-gutters cart-block">
                            <div class="col-pcs col-12 col pb-5">
                                <div class="_js-pcscontrolls pcscontrolls">
                                    <a class="btn fcm left minus _js-b-minus">
                                        <svg width="15" height="15" viewBox="0 0 34 5" xmlns="http://www.w3.org/2000/svg"><path d="M0.333496 2.75008C0.333496 2.19755 0.55299 1.66764 0.943691 1.27694C1.33439 0.886241 1.8643 0.666748 2.41683 0.666748H31.5835C32.136 0.666748 32.6659 0.886241 33.0566 1.27694C33.4473 1.66764 33.6668 2.19755 33.6668 2.75008C33.6668 3.30262 33.4473 3.83252 33.0566 4.22322C32.6659 4.61392 32.136 4.83341 31.5835 4.83341H2.41683C1.8643 4.83341 1.33439 4.61392 0.943691 4.22322C0.55299 3.83252 0.333496 3.30262 0.333496 2.75008Z"></path></svg>
                                    </a>
                                    <input type="text" value="{{ $currentCount }}" inputmode="numeric" class="input__default _js-product-count" data-max="{{ $product->balance }}">
                                    <a class="btn fcm right plus _js-b-plus">
                                        <svg width="15" height="15" viewBox="0 0 34 34" xmlns="http://www.w3.org/2000/svg"><path d="M0 17.0833C0 16.5308 0.219494 16.0009 0.610195 15.6102C1.0009 15.2195 1.5308 15 2.08333 15H31.25C31.8025 15 32.3324 15.2195 32.7231 15.6102C33.1138 16.0009 33.3333 16.5308 33.3333 17.0833C33.3333 17.6359 33.1138 18.1658 32.7231 18.5565C32.3324 18.9472 31.8025 19.1667 31.25 19.1667H2.08333C1.5308 19.1667 1.0009 18.9472 0.610195 18.5565C0.219494 18.1658 0 17.6359 0 17.0833Z"></path><path d="M17.0833 0C17.6359 0 18.1658 0.219493 18.5565 0.610194C18.9472 1.00089 19.1667 1.5308 19.1667 2.08333V31.25C19.1667 31.8025 18.9472 32.3324 18.5565 32.7231C18.1658 33.1138 17.6359 33.3333 17.0833 33.3333C16.5308 33.3333 16.0009 33.1138 15.6102 32.7231C15.2195 32.3324 15 31.8025 15 31.25V2.08333C15 1.5308 15.2195 1.00089 15.6102 0.610194C16.0009 0.219493 16.5308 0 17.0833 0Z"></path></svg>
                                    </a>
                                </div>
                            </div>
                            <div class="col-btn col-12 col pb-5">
                                <button class="button to-cart-btn {{ $inCart ? 'btn-to-cart _active' : '_js-add-to-cart' }} block row align-items-center justify-content-center sm-gutters" data-product-id="{{ $product->id }}">
                                    <div class="col-auto col">
                                        <svg viewBox="0 0 23 22" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 1C0 0.447715 0.447715 0 1 0H3C3.47158 0 3.87907 0.329457 3.97783 0.790578L4.87936 5H21.04C21.3433 5 21.6302 5.13765 21.82 5.37422C22.0098 5.61079 22.082 5.92071 22.0162 6.21679L20.3666 13.645C20.3666 13.6453 20.3667 13.6447 20.3666 13.645C20.2197 14.3115 19.8498 14.9087 19.3182 15.3368C18.7863 15.7649 18.1244 15.9989 17.4416 16L7.67003 16C6.97666 16.0126 6.30018 15.7846 5.75581 15.3545C5.20825 14.922 4.82861 14.312 4.68225 13.6298L3.10272 6.25468C3.09506 6.22552 3.08869 6.19584 3.08367 6.16571L2.19149 2H1C0.447715 2 0 1.55228 0 1ZM5.3077 7L6.63775 13.2102C6.63778 13.2104 6.63773 13.2101 6.63775 13.2102C6.6866 13.4375 6.81318 13.6411 6.99561 13.7852C7.17813 13.9294 7.40521 14.0054 7.63775 14.0002L7.66 14L17.4384 14C17.4386 14 17.4382 14 17.4384 14C17.6658 13.9995 17.8868 13.9215 18.0639 13.7789C18.2412 13.6362 18.3645 13.4373 18.4134 13.215L19.7936 7H5.3077Z"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M15.95 19.95C15.95 18.8454 16.8454 17.95 17.95 17.95C19.0545 17.95 19.95 18.8454 19.95 19.95C19.95 21.0546 19.0545 21.95 17.95 21.95C16.8454 21.95 15.95 21.0546 15.95 19.95Z"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M4.94995 19.95C4.94995 18.8454 5.84538 17.95 6.94995 17.95C8.05452 17.95 8.94995 18.8454 8.94995 19.95C8.94995 21.0546 8.05452 21.95 6.94995 21.95C5.84538 21.95 4.94995 21.0546 4.94995 19.95Z"></path></svg>
                                    </div>
                                    <div class="col-auto col cart-button">{{ $inCart ? 'В корзине' : 'В корзину' }}</div>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>