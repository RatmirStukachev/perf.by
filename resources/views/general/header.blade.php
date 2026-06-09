@php
    $cartCount = app(App\Services\CartService::class)->getCartCount();
@endphp


<section class="s-header-mobile w-mobile-header-btns">
    <div class="container-fluid">
        <div class="row row-h-mobile align-items-center justify-content-between">
            <div class="col-logo col">
                <a {{ request()->is('/') ? '' : 'href=/' }}  class="logo__link block__link">
                    <img src="{{ asset('assets/i/perf-by-logo.png') }}" alt="" class="img block">
                </a>
            </div>
            <div class="col-right col col-auto">
                <div class="row lg-lg-gutters md-md-gutters sm-sm-gutters xxxs-no-gutters align-items-center justify-content-end">
                    <div class="col-auto">
                        <a href="" class="mobile-btn__link fcm search _js-b-mobile-search">
                            <svg version="1.1" width="22" height="22" enable-background="new 0 0 515.558 515.558" viewBox="0 0 515.558 515.558" xmlns="http://www.w3.org/2000/svg"><path d="m378.344 332.78c25.37-34.645 40.545-77.2 40.545-123.333 0-115.484-93.961-209.445-209.445-209.445s-209.444 93.961-209.444 209.445 93.961 209.445 209.445 209.445c46.133 0 88.692-15.177 123.337-40.547l137.212 137.212 45.564-45.564c0-.001-137.214-137.213-137.214-137.213zm-168.899 21.667c-79.958 0-145-65.042-145-145s65.042-145 145-145 145 65.042 145 145-65.043 145-145 145z"></path></svg>
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('cart.index') }}" class="mobile-btn__link fcm cart @if ($cartCount > 0) _active @endif">
                            <div class="count">{{ $cartCount }}</div>
                            <svg width="24" height="24" viewBox="0 0 23 22" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 1C0 0.447715 0.447715 0 1 0H3C3.47158 0 3.87907 0.329457 3.97783 0.790578L4.87936 5H21.04C21.3433 5 21.6302 5.13765 21.82 5.37422C22.0098 5.61079 22.082 5.92071 22.0162 6.21679L20.3666 13.645C20.3666 13.6453 20.3667 13.6447 20.3666 13.645C20.2197 14.3115 19.8498 14.9087 19.3182 15.3368C18.7863 15.7649 18.1244 15.9989 17.4416 16L7.67003 16C6.97666 16.0126 6.30018 15.7846 5.75581 15.3545C5.20825 14.922 4.82861 14.312 4.68225 13.6298L3.10272 6.25468C3.09506 6.22552 3.08869 6.19584 3.08367 6.16571L2.19149 2H1C0.447715 2 0 1.55228 0 1ZM5.3077 7L6.63775 13.2102C6.63778 13.2104 6.63773 13.2101 6.63775 13.2102C6.6866 13.4375 6.81318 13.6411 6.99561 13.7852C7.17813 13.9294 7.40521 14.0054 7.63775 14.0002L7.66 14L17.4384 14C17.4386 14 17.4382 14 17.4384 14C17.6658 13.9995 17.8868 13.9215 18.0639 13.7789C18.2412 13.6362 18.3645 13.4373 18.4134 13.215L19.7936 7H5.3077Z"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M15.95 19.95C15.95 18.8454 16.8454 17.95 17.95 17.95C19.0545 17.95 19.95 18.8454 19.95 19.95C19.95 21.0546 19.0545 21.95 17.95 21.95C16.8454 21.95 15.95 21.0546 15.95 19.95Z"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M4.94995 19.95C4.94995 18.8454 5.84538 17.95 6.94995 17.95C8.05452 17.95 8.94995 18.8454 8.94995 19.95C8.94995 21.0546 8.05452 21.95 6.94995 21.95C5.84538 21.95 4.94995 21.0546 4.94995 19.95Z"></path></svg>
                        </a>
                    </div>
                    <div class="col-auto">
                        <div class="w-cloud-dropper js _js-click-dropper">
                            <div class="parent">
                                <a href="" class="mobile-btn__link fcm contacts _js-b-click-dropper">
                                    <svg width="24" height="24" viewBox="0 0 22 22"  xmlns="http://www.w3.org/2000/svg"><path d="M15.9204 21.4999C15.405 21.4984 14.895 21.3938 14.4204 21.1924C8.29678 18.5706 3.41886 13.69 0.800442 7.56493C0.504391 6.87562 0.421896 6.11335 0.563619 5.37666C0.705343 4.63996 1.06477 3.96271 1.59544 3.43243L3.84544 1.18243C4.26732 0.761083 4.83919 0.524414 5.43544 0.524414C6.03169 0.524414 6.60357 0.761083 7.02544 1.18243L9.67294 3.82993C10.0943 4.25181 10.331 4.82368 10.331 5.41993C10.331 6.01619 10.0943 6.58806 9.67294 7.00993L8.45044 8.24743C8.92336 9.44768 9.63815 10.5378 10.5504 11.45C11.4626 12.3622 12.5527 13.077 13.7529 13.5499L15.0129 12.2974C15.4348 11.8761 16.0067 11.6394 16.6029 11.6394C17.1992 11.6394 17.7711 11.8761 18.1929 12.2974L20.8404 14.9449C21.2618 15.3668 21.4985 15.9387 21.4985 16.5349C21.4985 17.1312 21.2618 17.7031 20.8404 18.1249L18.5904 20.3749C18.2423 20.7302 17.8269 21.0126 17.3685 21.2057C16.9101 21.3989 16.4179 21.4989 15.9204 21.4999ZM5.46544 1.99993C5.36674 1.99936 5.26889 2.01828 5.17751 2.05561C5.08613 2.09293 5.00302 2.14792 4.93294 2.21743L2.68294 4.46743C2.36343 4.78551 2.14683 5.19224 2.06125 5.63488C1.97566 6.07753 2.02502 6.53568 2.20294 6.94993C4.65758 12.7338 9.25273 17.3451 15.0279 19.8199C15.4422 19.9979 15.9003 20.0472 16.343 19.9616C16.7856 19.876 17.1924 19.6595 17.5104 19.3399L19.7604 17.0899C19.8307 17.0202 19.8865 16.9373 19.9246 16.8459C19.9627 16.7545 19.9823 16.6564 19.9823 16.5574C19.9823 16.4584 19.9627 16.3604 19.9246 16.269C19.8865 16.1776 19.8307 16.0947 19.7604 16.0249L17.1054 13.3774C16.9649 13.2377 16.7748 13.1593 16.5767 13.1593C16.3786 13.1593 16.1885 13.2377 16.0479 13.3774L14.4579 14.9674C14.3609 15.0625 14.2399 15.1295 14.1079 15.1612C13.9758 15.1929 13.8375 15.1882 13.7079 15.1474C12.1151 14.6165 10.6678 13.722 9.48059 12.5348C8.29338 11.3476 7.39888 9.90025 6.86794 8.30743C6.82721 8.17784 6.82244 8.03962 6.85414 7.90752C6.88585 7.77543 6.95283 7.65443 7.04794 7.55743L8.63794 5.96743C8.70745 5.89735 8.76245 5.81424 8.79977 5.72286C8.83709 5.63149 8.85601 5.53364 8.85544 5.43493C8.85461 5.23818 8.7765 5.04963 8.63794 4.90993L5.99794 2.21743C5.92786 2.14792 5.84475 2.09293 5.75337 2.05561C5.66199 2.01828 5.56415 1.99936 5.46544 1.99993Z"/></svg>
                                </a>
                                <div class="inset _js-inset">
                                    <div class="frame">
                                        <div class="corner"></div>
                                        @if(isset($contacts->contacts_phones) && count($contacts->contacts_phones) > 0)
                                            @foreach($contacts->contacts_phones as $phone)
                                                @if($phone['is_main'] === true)
                                                    <div class="pt-10">
                                                        <div class="row align-items-center sm-gutters">
                                                            @if($phone['phone'])
                                                                <div class="col-auto col">
                                                                    <a href="{{ zContactsService::getPhoneLink($phone['phone']) }}" class="color-black nul semibold"><span class="dashed dash">{{ $phone['phone'] }}</span></a>
                                                                </div>
                                                                <div class="col-auto col">
                                                                    <div class="row row-social-icons-list">
                                                                        @if($phone['is_viber'] === true)
                                                                            <div class="col-auto col">
                                                                                <a href="{{ zContactsService::getPhoneViberLink($phone['phone']) }}" class="social-colored-icon__link colored vi" target="_blank" rel="nofollow">
                                                                                    <svg x="0" y="0" viewBox="0 0 100 100"><path d="m58 10h-16c-15.4 0-28 12.6-28 28v12c0 10.8 6.3 20.7 16 25.3v13.4c0 1.2 1.5 1.8 2.3.9l11.6-11.6h14.1c15.4 0 28-12.6 28-28v-12c0-15.4-12.6-28-28-28zm10.5 52.5-4.1 4c-4.3 4.2-15.4-.6-25.2-10.6s-14.1-21.2-10-25.4l4-4c1.5-1.5 4-1.4 5.7.1l5.8 6c2.1 2.1 1.2 5.7-1.5 6.5-1.9.6-3.2 2.7-2.6 4.6 1 4.4 6.6 10 10.8 11.1 1.9.4 4-.6 4.7-2.5.9-2.7 4.5-3.5 6.5-1.4l5.8 6c1.6 1.4 1.6 3.9.1 5.6zm-14.9-33.5c-.4 0-.8 0-1.2.1-.7.1-1.4-.5-1.5-1.2s.5-1.4 1.2-1.5c.5-.1 1-.1 1.5-.1 7.3 0 13.3 6 13.3 13.3 0 .5 0 1-.1 1.5-.1.7-.8 1.3-1.5 1.2s-1.3-.8-1.2-1.5c0-.4.1-.8.1-1.2.1-5.8-4.7-10.6-10.6-10.6zm8 10.7c0 .7-.6 1.3-1.3 1.3s-1.3-.6-1.3-1.3c0-2.9-2.4-5.3-5.3-5.3-.7 0-1.3-.6-1.3-1.3s.6-1.3 1.3-1.3c4.3-.1 7.9 3.5 7.9 7.9zm10.2 4.3c-.2.7-.9 1.2-1.7 1-.7-.2-1.1-.9-.9-1.6.3-1.2.4-2.4.4-3.7 0-8.8-7.2-16-16-16-.4 0-.8 0-1.2 0-.7 0-1.4-.5-1.4-1.2s.5-1.4 1.2-1.4c.5 0 1-.1 1.4-.1 10.3 0 18.7 8.4 18.7 18.7 0 1.4-.2 2.9-.5 4.3z"></path></svg>
                                                                                </a>
                                                                            </div>			
                                                                        @endif
                                                                        @if($phone['is_whatsapp'] === true)
                                                                            <div class="col-auto col">
                                                                                <a href="{{ zContactsService::getPhoneWhatsappLink($phone['phone']) }}" class="social-colored-icon__link colored wh" target="_blank" rel="nofollow">
                                                                                    <svg viewBox="0 0 14 15">
                                                                                        <path d="M11.9512 2.70841C10.6341 1.39591 8.87805 0.666748 7.02439 0.666748C3.17073 0.666748 0.0487797 3.77786 0.0487797 7.61814C0.0487797 8.83342 0.390244 10.0487 0.97561 11.0695L0 14.6667L3.70732 13.6945C4.73171 14.2292 5.85366 14.5209 7.02439 14.5209C10.878 14.5209 14 11.4098 14 7.56953C13.9512 5.77092 13.2683 4.02091 11.9512 2.70841ZM10.3902 10.0973C10.2439 10.4862 9.56097 10.8751 9.21951 10.9237C8.92683 10.9723 8.53659 10.9723 8.14634 10.8751C7.90244 10.7779 7.56098 10.6806 7.17073 10.4862C5.41463 9.75703 4.29268 8.00703 4.19512 7.86119C4.09756 7.76397 3.46342 6.93758 3.46342 6.06258C3.46342 5.18758 3.90244 4.79869 4.04878 4.60425C4.19512 4.4098 4.39024 4.4098 4.53658 4.4098C4.63415 4.4098 4.78049 4.4098 4.87805 4.4098C4.97561 4.4098 5.12195 4.36119 5.26829 4.70147C5.41463 5.04175 5.7561 5.91675 5.80488 5.96536C5.85366 6.06258 5.85366 6.1598 5.80488 6.25703C5.7561 6.35425 5.70731 6.45147 5.60975 6.54869C5.51219 6.64591 5.41463 6.79175 5.36585 6.84036C5.26829 6.93758 5.17073 7.0348 5.26829 7.18064C5.36585 7.37508 5.70732 7.9098 6.2439 8.39591C6.92683 8.97925 7.46341 9.17369 7.65854 9.27092C7.85366 9.36814 7.95122 9.31953 8.04878 9.2223C8.14634 9.12508 8.48781 8.73619 8.58537 8.54175C8.68293 8.3473 8.82927 8.39592 8.97561 8.44453C9.12195 8.49314 10 8.93064 10.1463 9.02786C10.3415 9.12508 10.439 9.17369 10.4878 9.2223C10.5366 9.36814 10.5366 9.70841 10.3902 10.0973Z"></path>
                                                                                    </svg>
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                        @if($phone['is_telegram'] === true)
                                                                            <div class="col-auto col">
                                                                                <a href="{{ zContactsService::getTelegramLinkViaPhone($phone['phone']) }}" class="social-colored-icon__link colored tg" target="_blank" rel="nofollow">
                                                                                    <svg viewBox="0 0 14 13">
                                                                                        <path d="M14 0.847309L11.7855 12.4084C11.7855 12.4084 11.4756 13.21 10.6244 12.8255L5.51495 8.76862L5.49126 8.75667C6.18143 8.11491 11.5333 3.13184 11.7673 2.90597C12.1294 2.55615 11.9046 2.34789 11.4841 2.61214L3.57869 7.81102L0.528786 6.74834C0.528786 6.74834 0.0488212 6.57154 0.00264736 6.18712C-0.044134 5.80206 0.544582 5.5938 0.544582 5.5938L12.9781 0.542789C12.9781 0.542789 14 0.0778286 14 0.847309Z"></path>
                                                                                    </svg>
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if(isset($contacts->contacts_phones) && count($contacts->contacts_phones) > 0)
                                            @foreach($contacts->contacts_phones as $phone)
                                                @if($phone['is_dropdown'] === true)
                                                    <div class="pt-10">
                                                        @if ($phone['phone_name'])
                                                            <div class="color-gray semibold">{{ $phone['phone_name'] }}</div>
                                                        @endif
                                                        <div class="row align-items-center sm-gutters">
                                                            @if ($phone['phone'])
                                                                <div class="col-auto col">
                                                                    <a href="{{ zContactsService::getPhoneLink($phone['phone']) }}" class="color-black nul semibold"><span class="dashed dash">{{ $phone['phone'] }}</span></a>
                                                                </div>
                                                            @endif
                                                            <div class="col-auto col">
                                                                <div class="row row-social-icons-list">
                                                                    @if($phone['is_viber'] === true)
                                                                        <div class="col-auto col">
                                                                            <a href="{{ zContactsService::getPhoneViberLink($phone['phone']) }}" class="social-colored-icon__link colored vi" target="_blank" rel="nofollow">
                                                                                <svg x="0" y="0" viewBox="0 0 100 100"><path d="m58 10h-16c-15.4 0-28 12.6-28 28v12c0 10.8 6.3 20.7 16 25.3v13.4c0 1.2 1.5 1.8 2.3.9l11.6-11.6h14.1c15.4 0 28-12.6 28-28v-12c0-15.4-12.6-28-28-28zm10.5 52.5-4.1 4c-4.3 4.2-15.4-.6-25.2-10.6s-14.1-21.2-10-25.4l4-4c1.5-1.5 4-1.4 5.7.1l5.8 6c2.1 2.1 1.2 5.7-1.5 6.5-1.9.6-3.2 2.7-2.6 4.6 1 4.4 6.6 10 10.8 11.1 1.9.4 4-.6 4.7-2.5.9-2.7 4.5-3.5 6.5-1.4l5.8 6c1.6 1.4 1.6 3.9.1 5.6zm-14.9-33.5c-.4 0-.8 0-1.2.1-.7.1-1.4-.5-1.5-1.2s.5-1.4 1.2-1.5c.5-.1 1-.1 1.5-.1 7.3 0 13.3 6 13.3 13.3 0 .5 0 1-.1 1.5-.1.7-.8 1.3-1.5 1.2s-1.3-.8-1.2-1.5c0-.4.1-.8.1-1.2.1-5.8-4.7-10.6-10.6-10.6zm8 10.7c0 .7-.6 1.3-1.3 1.3s-1.3-.6-1.3-1.3c0-2.9-2.4-5.3-5.3-5.3-.7 0-1.3-.6-1.3-1.3s.6-1.3 1.3-1.3c4.3-.1 7.9 3.5 7.9 7.9zm10.2 4.3c-.2.7-.9 1.2-1.7 1-.7-.2-1.1-.9-.9-1.6.3-1.2.4-2.4.4-3.7 0-8.8-7.2-16-16-16-.4 0-.8 0-1.2 0-.7 0-1.4-.5-1.4-1.2s.5-1.4 1.2-1.4c.5 0 1-.1 1.4-.1 10.3 0 18.7 8.4 18.7 18.7 0 1.4-.2 2.9-.5 4.3z"></path></svg>
                                                                            </a>
                                                                        </div>			
                                                                    @endif
                                                                    @if($phone['is_whatsapp'] === true)
                                                                        <div class="col-auto col">
                                                                            <a href="{{ zContactsService::getPhoneWhatsappLink($phone['phone']) }}" class="social-colored-icon__link colored wh" target="_blank" rel="nofollow">
                                                                                <svg viewBox="0 0 14 15">
                                                                                    <path d="M11.9512 2.70841C10.6341 1.39591 8.87805 0.666748 7.02439 0.666748C3.17073 0.666748 0.0487797 3.77786 0.0487797 7.61814C0.0487797 8.83342 0.390244 10.0487 0.97561 11.0695L0 14.6667L3.70732 13.6945C4.73171 14.2292 5.85366 14.5209 7.02439 14.5209C10.878 14.5209 14 11.4098 14 7.56953C13.9512 5.77092 13.2683 4.02091 11.9512 2.70841ZM10.3902 10.0973C10.2439 10.4862 9.56097 10.8751 9.21951 10.9237C8.92683 10.9723 8.53659 10.9723 8.14634 10.8751C7.90244 10.7779 7.56098 10.6806 7.17073 10.4862C5.41463 9.75703 4.29268 8.00703 4.19512 7.86119C4.09756 7.76397 3.46342 6.93758 3.46342 6.06258C3.46342 5.18758 3.90244 4.79869 4.04878 4.60425C4.19512 4.4098 4.39024 4.4098 4.53658 4.4098C4.63415 4.4098 4.78049 4.4098 4.87805 4.4098C4.97561 4.4098 5.12195 4.36119 5.26829 4.70147C5.41463 5.04175 5.7561 5.91675 5.80488 5.96536C5.85366 6.06258 5.85366 6.1598 5.80488 6.25703C5.7561 6.35425 5.70731 6.45147 5.60975 6.54869C5.51219 6.64591 5.41463 6.79175 5.36585 6.84036C5.26829 6.93758 5.17073 7.0348 5.26829 7.18064C5.36585 7.37508 5.70732 7.9098 6.2439 8.39591C6.92683 8.97925 7.46341 9.17369 7.65854 9.27092C7.85366 9.36814 7.95122 9.31953 8.04878 9.2223C8.14634 9.12508 8.48781 8.73619 8.58537 8.54175C8.68293 8.3473 8.82927 8.39592 8.97561 8.44453C9.12195 8.49314 10 8.93064 10.1463 9.02786C10.3415 9.12508 10.439 9.17369 10.4878 9.2223C10.5366 9.36814 10.5366 9.70841 10.3902 10.0973Z"></path>
                                                                                </svg>
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                    @if($phone['is_telegram'] === true)
                                                                        <div class="col-auto col">
                                                                            <a href="{{ zContactsService::getTelegramLinkViaPhone($phone['phone']) }}" class="social-colored-icon__link colored tg" target="_blank" rel="nofollow">
                                                                                <svg viewBox="0 0 14 13">
                                                                                    <path d="M14 0.847309L11.7855 12.4084C11.7855 12.4084 11.4756 13.21 10.6244 12.8255L5.51495 8.76862L5.49126 8.75667C6.18143 8.11491 11.5333 3.13184 11.7673 2.90597C12.1294 2.55615 11.9046 2.34789 11.4841 2.61214L3.57869 7.81102L0.528786 6.74834C0.528786 6.74834 0.0488212 6.57154 0.00264736 6.18712C-0.044134 5.80206 0.544582 5.5938 0.544582 5.5938L12.9781 0.542789C12.9781 0.542789 14 0.0778286 14 0.847309Z"></path>
                                                                                </svg>
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if(isset($contacts->company_address_pickup_header) && $contacts->company_address_pickup_header)
                                            <div class="pt-10">
                                                <div class="w-icon-left location">
                                                    <div class="icon top"><svg viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 12.3498L5.98675 12.8087C5.92287 12.8693 5.84696 12.9174 5.76338 12.9503C5.67979 12.9831 5.59017 13 5.49966 13C5.40914 13 5.31952 12.9831 5.23594 12.9503C5.15235 12.9174 5.07644 12.8693 5.01256 12.8087L5.00844 12.8042L4.99675 12.7931L4.95413 12.7522C4.71177 12.5161 4.47341 12.2765 4.23913 12.0333C3.6509 11.4239 3.08765 10.7934 2.55063 10.1431C1.93738 9.39561 1.31037 8.55063 0.833937 7.71604C0.367812 6.8977 0 6.01111 0 5.19992C0 2.24962 2.4695 0 5.5 0C8.5305 0 11 2.24962 11 5.19992C11 6.01111 10.6322 6.8977 10.1661 7.71539C9.68963 8.55128 9.06331 9.39561 8.44938 10.1431C7.6982 11.0528 6.89588 11.9238 6.04587 12.7522L6.00325 12.7931L5.99156 12.8042L5.98744 12.8081L5.5 12.3498ZM5.5 7.1499C6.04701 7.1499 6.57161 6.94445 6.95841 6.57876C7.3452 6.21307 7.5625 5.71709 7.5625 5.19992C7.5625 4.68276 7.3452 4.18678 6.95841 3.82109C6.57161 3.4554 6.04701 3.24995 5.5 3.24995C4.95299 3.24995 4.42839 3.4554 4.04159 3.82109C3.6548 4.18678 3.4375 4.68276 3.4375 5.19992C3.4375 5.71709 3.6548 6.21307 4.04159 6.57876C4.42839 6.94445 4.95299 7.1499 5.5 7.1499Z" fill="#FF5F00"></path></svg></div>
                                                    <div class="text">Самовывоз: {{ $contacts->company_address_pickup_header }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($contacts->work_time_header) && $contacts->work_time_header)
                                            <div class="pt-10">
                                                <div class="w-icon-left time">
                                                    <div class="icon top"><svg viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.5 0C5.64641 0 4.80117 0.168127 4.01256 0.494783C3.22394 0.821439 2.50739 1.30023 1.90381 1.90381C0.684819 3.12279 0 4.77609 0 6.5C0 8.22391 0.684819 9.87721 1.90381 11.0962C2.50739 11.6998 3.22394 12.1786 4.01256 12.5052C4.80117 12.8319 5.64641 13 6.5 13C8.22391 13 9.87721 12.3152 11.0962 11.0962C12.3152 9.87721 13 8.22391 13 6.5C13 5.64641 12.8319 4.80117 12.5052 4.01256C12.1786 3.22394 11.6998 2.50739 11.0962 1.90381C10.4926 1.30023 9.77606 0.821439 8.98744 0.494783C8.19883 0.168127 7.35359 0 6.5 0ZM9.23 9.23L5.85 7.15V3.25H6.825V6.63L9.75 8.385L9.23 9.23Z" fill="#FF5F00"></path></svg></div>
                                                    <div class="text">{{ $contacts->work_time_header }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($contacts->contacts_emails) && count($contacts->contacts_emails) > 0)
                                            @foreach($contacts->contacts_emails as $mail)
                                                @if($mail['is_main'] === true)
                                                    <div class="pt-10">
                                                        @if ($mail['email'])
                                                            <a href="{{ zContactsService::getEmailLink($mail['email']) }}" class="color-orange nul">
                                                                <span class="dashed dash">
                                                                    {{ $mail['email'] }}
                                                                </span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="" class="mobile-btn__link fcm menu _js-b-toggle-mobile-menu">
                            <div class="burger black">
                                <div class="line"></div>
                                <div class="line"></div>
                                <div class="line"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="header-mobile-empty"></div>

<header class="s-header">
    <div class="header-top">
        <div class="container pt-5">
            <div class="w-bordered">
                <div class="row row-header-top align-items-center justify-content-between">
                    <div class="col-left col">
                        <div class="w-heder-adress color-orange">
                            <div class="row md-gutters align-items-center">
                                @if(isset($contacts->company_address_pickup_header) && $contacts->company_address_pickup_header)
                                    <div class="col-auto col pb-5">
                                        <div class="w-icon-left location">
                                            <div class="icon"><svg viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 12.3498L5.98675 12.8087C5.92287 12.8693 5.84696 12.9174 5.76338 12.9503C5.67979 12.9831 5.59017 13 5.49966 13C5.40914 13 5.31952 12.9831 5.23594 12.9503C5.15235 12.9174 5.07644 12.8693 5.01256 12.8087L5.00844 12.8042L4.99675 12.7931L4.95413 12.7522C4.71177 12.5161 4.47341 12.2765 4.23913 12.0333C3.6509 11.4239 3.08765 10.7934 2.55063 10.1431C1.93738 9.39561 1.31037 8.55063 0.833937 7.71604C0.367812 6.8977 0 6.01111 0 5.19992C0 2.24962 2.4695 0 5.5 0C8.5305 0 11 2.24962 11 5.19992C11 6.01111 10.6322 6.8977 10.1661 7.71539C9.68963 8.55128 9.06331 9.39561 8.44938 10.1431C7.6982 11.0528 6.89588 11.9238 6.04587 12.7522L6.00325 12.7931L5.99156 12.8042L5.98744 12.8081L5.5 12.3498ZM5.5 7.1499C6.04701 7.1499 6.57161 6.94445 6.95841 6.57876C7.3452 6.21307 7.5625 5.71709 7.5625 5.19992C7.5625 4.68276 7.3452 4.18678 6.95841 3.82109C6.57161 3.4554 6.04701 3.24995 5.5 3.24995C4.95299 3.24995 4.42839 3.4554 4.04159 3.82109C3.6548 4.18678 3.4375 4.68276 3.4375 5.19992C3.4375 5.71709 3.6548 6.21307 4.04159 6.57876C4.42839 6.94445 4.95299 7.1499 5.5 7.1499Z" fill="#FF5F00"/></svg></div>
                                            <div class="text">{{ $contacts->company_address_pickup_header }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($contacts->work_time_header) && $contacts->work_time_header)
                                    <div class="col-auto col pb-5">
                                        <div class="w-icon-left time">
                                            <div class="icon"><svg viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.5 0C5.64641 0 4.80117 0.168127 4.01256 0.494783C3.22394 0.821439 2.50739 1.30023 1.90381 1.90381C0.684819 3.12279 0 4.77609 0 6.5C0 8.22391 0.684819 9.87721 1.90381 11.0962C2.50739 11.6998 3.22394 12.1786 4.01256 12.5052C4.80117 12.8319 5.64641 13 6.5 13C8.22391 13 9.87721 12.3152 11.0962 11.0962C12.3152 9.87721 13 8.22391 13 6.5C13 5.64641 12.8319 4.80117 12.5052 4.01256C12.1786 3.22394 11.6998 2.50739 11.0962 1.90381C10.4926 1.30023 9.77606 0.821439 8.98744 0.494783C8.19883 0.168127 7.35359 0 6.5 0ZM9.23 9.23L5.85 7.15V3.25H6.825V6.63L9.75 8.385L9.23 9.23Z" fill="#FF5F00"/></svg></div>
                                            <div class="text">{{ $contacts->work_time_header }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-right col">
                        <div class="row md-gutters align-items-center justify-content-end">
                            @if(isset($contacts->contacts_emails) && count($contacts->contacts_emails) > 0)
                                @foreach($contacts->contacts_emails as $mail)
                                    @if($mail['is_main'] === true)
                                        <div class="col-auto col pb-5">
                                            @if ($mail['email'])
                                                <a href="{{ zContactsService::getEmailLink($mail['email']) }}" class="color-orange nul">
                                                    <span class="dashed dash">
                                                        {{ $mail['email'] }}
                                                    </span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <div class="col-auto col pb-5">
                                <div class="w-cloud-dropper w-header-phones-dropper css js _js-click-dropper">
                                    <div class="w-parent">
                                        @if(isset($contacts->contacts_phones) && count($contacts->contacts_phones) > 0)
                                            @foreach($contacts->contacts_phones as $phone)
                                                @if($phone['is_main'] === true)
                                                    <div class="parent">
                                                        <div class="row align-items-center sm-gutters">
                                                            @if($phone['phone'])
                                                                <div class="col-auto col">
                                                                    <a href="{{ zContactsService::getPhoneLink($phone['phone']) }}" class="color-black nul semibold"><span class="dashed dash">{{ $phone['phone'] }}</span></a>
                                                                </div>
                                                                <div class="col-auto col">
                                                                    <div class="row row-social-icons-list">
                                                                        @if($phone['is_viber'] === true)
                                                                            <div class="col-auto col">
                                                                                <a href="{{ zContactsService::getPhoneViberLink($phone['phone']) }}" class="social-colored-icon__link colored vi" target="_blank" rel="nofollow">
                                                                                    <svg x="0" y="0" viewBox="0 0 100 100"><path d="m58 10h-16c-15.4 0-28 12.6-28 28v12c0 10.8 6.3 20.7 16 25.3v13.4c0 1.2 1.5 1.8 2.3.9l11.6-11.6h14.1c15.4 0 28-12.6 28-28v-12c0-15.4-12.6-28-28-28zm10.5 52.5-4.1 4c-4.3 4.2-15.4-.6-25.2-10.6s-14.1-21.2-10-25.4l4-4c1.5-1.5 4-1.4 5.7.1l5.8 6c2.1 2.1 1.2 5.7-1.5 6.5-1.9.6-3.2 2.7-2.6 4.6 1 4.4 6.6 10 10.8 11.1 1.9.4 4-.6 4.7-2.5.9-2.7 4.5-3.5 6.5-1.4l5.8 6c1.6 1.4 1.6 3.9.1 5.6zm-14.9-33.5c-.4 0-.8 0-1.2.1-.7.1-1.4-.5-1.5-1.2s.5-1.4 1.2-1.5c.5-.1 1-.1 1.5-.1 7.3 0 13.3 6 13.3 13.3 0 .5 0 1-.1 1.5-.1.7-.8 1.3-1.5 1.2s-1.3-.8-1.2-1.5c0-.4.1-.8.1-1.2.1-5.8-4.7-10.6-10.6-10.6zm8 10.7c0 .7-.6 1.3-1.3 1.3s-1.3-.6-1.3-1.3c0-2.9-2.4-5.3-5.3-5.3-.7 0-1.3-.6-1.3-1.3s.6-1.3 1.3-1.3c4.3-.1 7.9 3.5 7.9 7.9zm10.2 4.3c-.2.7-.9 1.2-1.7 1-.7-.2-1.1-.9-.9-1.6.3-1.2.4-2.4.4-3.7 0-8.8-7.2-16-16-16-.4 0-.8 0-1.2 0-.7 0-1.4-.5-1.4-1.2s.5-1.4 1.2-1.4c.5 0 1-.1 1.4-.1 10.3 0 18.7 8.4 18.7 18.7 0 1.4-.2 2.9-.5 4.3z"></path></svg>
                                                                                </a>
                                                                            </div>			
                                                                        @endif
                                                                        @if($phone['is_whatsapp'] === true)
                                                                            <div class="col-auto col">
                                                                                <a href="{{ zContactsService::getPhoneWhatsappLink($phone['phone']) }}" class="social-colored-icon__link colored wh" target="_blank" rel="nofollow">
                                                                                    <svg viewBox="0 0 14 15">
                                                                                        <path d="M11.9512 2.70841C10.6341 1.39591 8.87805 0.666748 7.02439 0.666748C3.17073 0.666748 0.0487797 3.77786 0.0487797 7.61814C0.0487797 8.83342 0.390244 10.0487 0.97561 11.0695L0 14.6667L3.70732 13.6945C4.73171 14.2292 5.85366 14.5209 7.02439 14.5209C10.878 14.5209 14 11.4098 14 7.56953C13.9512 5.77092 13.2683 4.02091 11.9512 2.70841ZM10.3902 10.0973C10.2439 10.4862 9.56097 10.8751 9.21951 10.9237C8.92683 10.9723 8.53659 10.9723 8.14634 10.8751C7.90244 10.7779 7.56098 10.6806 7.17073 10.4862C5.41463 9.75703 4.29268 8.00703 4.19512 7.86119C4.09756 7.76397 3.46342 6.93758 3.46342 6.06258C3.46342 5.18758 3.90244 4.79869 4.04878 4.60425C4.19512 4.4098 4.39024 4.4098 4.53658 4.4098C4.63415 4.4098 4.78049 4.4098 4.87805 4.4098C4.97561 4.4098 5.12195 4.36119 5.26829 4.70147C5.41463 5.04175 5.7561 5.91675 5.80488 5.96536C5.85366 6.06258 5.85366 6.1598 5.80488 6.25703C5.7561 6.35425 5.70731 6.45147 5.60975 6.54869C5.51219 6.64591 5.41463 6.79175 5.36585 6.84036C5.26829 6.93758 5.17073 7.0348 5.26829 7.18064C5.36585 7.37508 5.70732 7.9098 6.2439 8.39591C6.92683 8.97925 7.46341 9.17369 7.65854 9.27092C7.85366 9.36814 7.95122 9.31953 8.04878 9.2223C8.14634 9.12508 8.48781 8.73619 8.58537 8.54175C8.68293 8.3473 8.82927 8.39592 8.97561 8.44453C9.12195 8.49314 10 8.93064 10.1463 9.02786C10.3415 9.12508 10.439 9.17369 10.4878 9.2223C10.5366 9.36814 10.5366 9.70841 10.3902 10.0973Z"></path>
                                                                                    </svg>
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                        @if($phone['is_telegram'] === true)
                                                                            <div class="col-auto col">
                                                                                <a href="{{ zContactsService::getTelegramLinkViaPhone($phone['phone']) }}" class="social-colored-icon__link colored tg" target="_blank" rel="nofollow">
                                                                                    <svg viewBox="0 0 14 13">
                                                                                        <path d="M14 0.847309L11.7855 12.4084C11.7855 12.4084 11.4756 13.21 10.6244 12.8255L5.51495 8.76862L5.49126 8.75667C6.18143 8.11491 11.5333 3.13184 11.7673 2.90597C12.1294 2.55615 11.9046 2.34789 11.4841 2.61214L3.57869 7.81102L0.528786 6.74834C0.528786 6.74834 0.0488212 6.57154 0.00264736 6.18712C-0.044134 5.80206 0.544582 5.5938 0.544582 5.5938L12.9781 0.542789C12.9781 0.542789 14 0.0778286 14 0.847309Z"></path>
                                                                                    </svg>
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        <div class="b-dropper"></div>
                                        <div class="overlay _js-b-click-dropper"></div>
                                        <div class="inset _js-inset">
                                            <div class="frame">
                                                <div class="corner"></div>
                                                @if(isset($contacts->contacts_phones) && count($contacts->contacts_phones) > 0)
                                                    @foreach($contacts->contacts_phones as $phone)
                                                        @if($phone['is_main'] === true)
                                                            <div class="w-touch-interface-phone-item pt-10">
                                                                @if($phone['phone'])
                                                                    <div class="row align-items-center sm-gutters">
                                                                        <div class="col-auto col">
                                                                            <a href="{{ zContactsService::getPhoneLink($phone['phone']) }}" class="color-black nul semibold"><span class="dashed dash">{{ $phone['phone'] }}</span></a>
                                                                        </div>
                                                                        <div class="col-auto col">
                                                                            <div class="row row-social-icons-list">
                                                                                @if($phone['is_viber'] === true)
                                                                                    <div class="col-auto col">
                                                                                        <a href="{{ zContactsService::getPhoneViberLink($phone['phone']) }}" class="social-colored-icon__link colored vi" target="_blank" rel="nofollow">
                                                                                            <svg x="0" y="0" viewBox="0 0 100 100"><path d="m58 10h-16c-15.4 0-28 12.6-28 28v12c0 10.8 6.3 20.7 16 25.3v13.4c0 1.2 1.5 1.8 2.3.9l11.6-11.6h14.1c15.4 0 28-12.6 28-28v-12c0-15.4-12.6-28-28-28zm10.5 52.5-4.1 4c-4.3 4.2-15.4-.6-25.2-10.6s-14.1-21.2-10-25.4l4-4c1.5-1.5 4-1.4 5.7.1l5.8 6c2.1 2.1 1.2 5.7-1.5 6.5-1.9.6-3.2 2.7-2.6 4.6 1 4.4 6.6 10 10.8 11.1 1.9.4 4-.6 4.7-2.5.9-2.7 4.5-3.5 6.5-1.4l5.8 6c1.6 1.4 1.6 3.9.1 5.6zm-14.9-33.5c-.4 0-.8 0-1.2.1-.7.1-1.4-.5-1.5-1.2s.5-1.4 1.2-1.5c.5-.1 1-.1 1.5-.1 7.3 0 13.3 6 13.3 13.3 0 .5 0 1-.1 1.5-.1.7-.8 1.3-1.5 1.2s-1.3-.8-1.2-1.5c0-.4.1-.8.1-1.2.1-5.8-4.7-10.6-10.6-10.6zm8 10.7c0 .7-.6 1.3-1.3 1.3s-1.3-.6-1.3-1.3c0-2.9-2.4-5.3-5.3-5.3-.7 0-1.3-.6-1.3-1.3s.6-1.3 1.3-1.3c4.3-.1 7.9 3.5 7.9 7.9zm10.2 4.3c-.2.7-.9 1.2-1.7 1-.7-.2-1.1-.9-.9-1.6.3-1.2.4-2.4.4-3.7 0-8.8-7.2-16-16-16-.4 0-.8 0-1.2 0-.7 0-1.4-.5-1.4-1.2s.5-1.4 1.2-1.4c.5 0 1-.1 1.4-.1 10.3 0 18.7 8.4 18.7 18.7 0 1.4-.2 2.9-.5 4.3z"></path></svg>
                                                                                        </a>
                                                                                    </div>			
                                                                                @endif
                                                                                @if($phone['is_whatsapp'] === true)
                                                                                    <div class="col-auto col">
                                                                                        <a href="{{ zContactsService::getPhoneWhatsappLink($phone['phone']) }}" class="social-colored-icon__link colored wh" target="_blank" rel="nofollow">
                                                                                            <svg viewBox="0 0 14 15">
                                                                                                <path d="M11.9512 2.70841C10.6341 1.39591 8.87805 0.666748 7.02439 0.666748C3.17073 0.666748 0.0487797 3.77786 0.0487797 7.61814C0.0487797 8.83342 0.390244 10.0487 0.97561 11.0695L0 14.6667L3.70732 13.6945C4.73171 14.2292 5.85366 14.5209 7.02439 14.5209C10.878 14.5209 14 11.4098 14 7.56953C13.9512 5.77092 13.2683 4.02091 11.9512 2.70841ZM10.3902 10.0973C10.2439 10.4862 9.56097 10.8751 9.21951 10.9237C8.92683 10.9723 8.53659 10.9723 8.14634 10.8751C7.90244 10.7779 7.56098 10.6806 7.17073 10.4862C5.41463 9.75703 4.29268 8.00703 4.19512 7.86119C4.09756 7.76397 3.46342 6.93758 3.46342 6.06258C3.46342 5.18758 3.90244 4.79869 4.04878 4.60425C4.19512 4.4098 4.39024 4.4098 4.53658 4.4098C4.63415 4.4098 4.78049 4.4098 4.87805 4.4098C4.97561 4.4098 5.12195 4.36119 5.26829 4.70147C5.41463 5.04175 5.7561 5.91675 5.80488 5.96536C5.85366 6.06258 5.85366 6.1598 5.80488 6.25703C5.7561 6.35425 5.70731 6.45147 5.60975 6.54869C5.51219 6.64591 5.41463 6.79175 5.36585 6.84036C5.26829 6.93758 5.17073 7.0348 5.26829 7.18064C5.36585 7.37508 5.70732 7.9098 6.2439 8.39591C6.92683 8.97925 7.46341 9.17369 7.65854 9.27092C7.85366 9.36814 7.95122 9.31953 8.04878 9.2223C8.14634 9.12508 8.48781 8.73619 8.58537 8.54175C8.68293 8.3473 8.82927 8.39592 8.97561 8.44453C9.12195 8.49314 10 8.93064 10.1463 9.02786C10.3415 9.12508 10.439 9.17369 10.4878 9.2223C10.5366 9.36814 10.5366 9.70841 10.3902 10.0973Z"></path>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </div>
                                                                                @endif
                                                                                @if($phone['is_telegram'] === true)
                                                                                    <div class="col-auto col">
                                                                                        <a href="{{ zContactsService::getTelegramLinkViaPhone($phone['phone']) }}" class="social-colored-icon__link colored tg" target="_blank" rel="nofollow">
                                                                                            <svg viewBox="0 0 14 13">
                                                                                                <path d="M14 0.847309L11.7855 12.4084C11.7855 12.4084 11.4756 13.21 10.6244 12.8255L5.51495 8.76862L5.49126 8.75667C6.18143 8.11491 11.5333 3.13184 11.7673 2.90597C12.1294 2.55615 11.9046 2.34789 11.4841 2.61214L3.57869 7.81102L0.528786 6.74834C0.528786 6.74834 0.0488212 6.57154 0.00264736 6.18712C-0.044134 5.80206 0.544582 5.5938 0.544582 5.5938L12.9781 0.542789C12.9781 0.542789 14 0.0778286 14 0.847309Z"></path>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @if(isset($contacts->contacts_phones) && count($contacts->contacts_phones) > 0)
                                                    @foreach($contacts->contacts_phones as $phone)
                                                        @if($phone['is_dropdown'] === true)
                                                            <div class="pt-10">
                                                                @if ($phone['phone_name'])
                                                                    <div class="color-gray semibold">{{ $phone['phone_name'] }}</div>
                                                                @endif
                                                                <div class="row align-items-center sm-gutters">
                                                                    @if ($phone['phone'])
                                                                        <div class="col-auto col">
                                                                            <a href="{{ zContactsService::getPhoneLink($phone['phone']) }}" class="color-black nul semibold"><span class="dashed dash">{{ $phone['phone'] }}</span></a>
                                                                        </div>
                                                                    @endif
                                                                    <div class="col-auto col">
                                                                        <div class="row row-social-icons-list">
                                                                                @if($phone['is_viber'] === true)
                                                                                    <div class="col-auto col">
                                                                                        <a href="{{ zContactsService::getPhoneViberLink($phone['phone']) }}" class="social-colored-icon__link colored vi" target="_blank" rel="nofollow">
                                                                                            <svg x="0" y="0" viewBox="0 0 100 100"><path d="m58 10h-16c-15.4 0-28 12.6-28 28v12c0 10.8 6.3 20.7 16 25.3v13.4c0 1.2 1.5 1.8 2.3.9l11.6-11.6h14.1c15.4 0 28-12.6 28-28v-12c0-15.4-12.6-28-28-28zm10.5 52.5-4.1 4c-4.3 4.2-15.4-.6-25.2-10.6s-14.1-21.2-10-25.4l4-4c1.5-1.5 4-1.4 5.7.1l5.8 6c2.1 2.1 1.2 5.7-1.5 6.5-1.9.6-3.2 2.7-2.6 4.6 1 4.4 6.6 10 10.8 11.1 1.9.4 4-.6 4.7-2.5.9-2.7 4.5-3.5 6.5-1.4l5.8 6c1.6 1.4 1.6 3.9.1 5.6zm-14.9-33.5c-.4 0-.8 0-1.2.1-.7.1-1.4-.5-1.5-1.2s.5-1.4 1.2-1.5c.5-.1 1-.1 1.5-.1 7.3 0 13.3 6 13.3 13.3 0 .5 0 1-.1 1.5-.1.7-.8 1.3-1.5 1.2s-1.3-.8-1.2-1.5c0-.4.1-.8.1-1.2.1-5.8-4.7-10.6-10.6-10.6zm8 10.7c0 .7-.6 1.3-1.3 1.3s-1.3-.6-1.3-1.3c0-2.9-2.4-5.3-5.3-5.3-.7 0-1.3-.6-1.3-1.3s.6-1.3 1.3-1.3c4.3-.1 7.9 3.5 7.9 7.9zm10.2 4.3c-.2.7-.9 1.2-1.7 1-.7-.2-1.1-.9-.9-1.6.3-1.2.4-2.4.4-3.7 0-8.8-7.2-16-16-16-.4 0-.8 0-1.2 0-.7 0-1.4-.5-1.4-1.2s.5-1.4 1.2-1.4c.5 0 1-.1 1.4-.1 10.3 0 18.7 8.4 18.7 18.7 0 1.4-.2 2.9-.5 4.3z"></path></svg>
                                                                                        </a>
                                                                                    </div>			
                                                                                @endif
                                                                                @if($phone['is_whatsapp'] === true)
                                                                                    <div class="col-auto col">
                                                                                        <a href="{{ zContactsService::getPhoneWhatsappLink($phone['phone']) }}" class="social-colored-icon__link colored wh" target="_blank" rel="nofollow">
                                                                                            <svg viewBox="0 0 14 15">
                                                                                                <path d="M11.9512 2.70841C10.6341 1.39591 8.87805 0.666748 7.02439 0.666748C3.17073 0.666748 0.0487797 3.77786 0.0487797 7.61814C0.0487797 8.83342 0.390244 10.0487 0.97561 11.0695L0 14.6667L3.70732 13.6945C4.73171 14.2292 5.85366 14.5209 7.02439 14.5209C10.878 14.5209 14 11.4098 14 7.56953C13.9512 5.77092 13.2683 4.02091 11.9512 2.70841ZM10.3902 10.0973C10.2439 10.4862 9.56097 10.8751 9.21951 10.9237C8.92683 10.9723 8.53659 10.9723 8.14634 10.8751C7.90244 10.7779 7.56098 10.6806 7.17073 10.4862C5.41463 9.75703 4.29268 8.00703 4.19512 7.86119C4.09756 7.76397 3.46342 6.93758 3.46342 6.06258C3.46342 5.18758 3.90244 4.79869 4.04878 4.60425C4.19512 4.4098 4.39024 4.4098 4.53658 4.4098C4.63415 4.4098 4.78049 4.4098 4.87805 4.4098C4.97561 4.4098 5.12195 4.36119 5.26829 4.70147C5.41463 5.04175 5.7561 5.91675 5.80488 5.96536C5.85366 6.06258 5.85366 6.1598 5.80488 6.25703C5.7561 6.35425 5.70731 6.45147 5.60975 6.54869C5.51219 6.64591 5.41463 6.79175 5.36585 6.84036C5.26829 6.93758 5.17073 7.0348 5.26829 7.18064C5.36585 7.37508 5.70732 7.9098 6.2439 8.39591C6.92683 8.97925 7.46341 9.17369 7.65854 9.27092C7.85366 9.36814 7.95122 9.31953 8.04878 9.2223C8.14634 9.12508 8.48781 8.73619 8.58537 8.54175C8.68293 8.3473 8.82927 8.39592 8.97561 8.44453C9.12195 8.49314 10 8.93064 10.1463 9.02786C10.3415 9.12508 10.439 9.17369 10.4878 9.2223C10.5366 9.36814 10.5366 9.70841 10.3902 10.0973Z"></path>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </div>
                                                                                @endif
                                                                                @if($phone['is_telegram'] === true)
                                                                                    <div class="col-auto col">
                                                                                        <a href="{{ zContactsService::getTelegramLinkViaPhone($phone['phone']) }}" class="social-colored-icon__link colored tg" target="_blank" rel="nofollow">
                                                                                            <svg viewBox="0 0 14 13">
                                                                                                <path d="M14 0.847309L11.7855 12.4084C11.7855 12.4084 11.4756 13.21 10.6244 12.8255L5.51495 8.76862L5.49126 8.75667C6.18143 8.11491 11.5333 3.13184 11.7673 2.90597C12.1294 2.55615 11.9046 2.34789 11.4841 2.61214L3.57869 7.81102L0.528786 6.74834C0.528786 6.74834 0.0488212 6.57154 0.00264736 6.18712C-0.044134 5.80206 0.544582 5.5938 0.544582 5.5938L12.9781 0.542789C12.9781 0.542789 14 0.0778286 14 0.847309Z"></path>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>											
                                </div>
                            </div>
                            @if (isset($contacts->instagram) && $contacts->instagram)
                                <div class="col-auto col pb-5">
                                    <div class="row row-social-icons-list align-items-center">
                                        <div class="col-auto col">
                                            <a href="{{ zContactsService::getInstagramLink($contacts->instagram) }}" class="social-colored-icon__link colored ig">
                                                <svg x="0" y="0" viewBox="0 0 20 20"><path d="m15 0h-10c-2.8 0-5 2.2-5 5v10c0 2.8 2.2 5 5 5h10c2.8 0 5-2.2 5-5v-10c0-2.8-2.2-5-5-5zm3 15c0 1.7-1.3 3-3 3h-10c-1.7 0-3-1.3-3-3v-10c0-1.7 1.3-3 3-3h10c1.7 0 3 1.3 3 3z"></path><path d="m10 5c-2.8 0-5 2.2-5 5s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5zm0 8c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3z"></path><circle cx="15" cy="5" r="1"></circle></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-middle">
        <div class="container pt-10">
            <div class="row row-header-middle align-items-center justify-content-between">
                <div class="col-logo col pb-10">
                    <a {{ request()->is('/') ? '' : 'href=/' }}  class="logo__link block__link">
                        <img src="{{ asset('assets/i/perf-by-logo.png') }}" alt="logo" class="img block">
                    </a>
                </div>
                <div class="col-catalog-btn col pb-10">
                    <a href="{{ route('catalog.index') }}" class="button header-catalog-btn row align-items-center justify-content-center sm-gutters">
                        <div class="col-auto col">
                            <div class="burger white">
                                <div class="line"></div>
                                <div class="line"></div>
                                <div class="line"></div>
                            </div>
                        </div>
                        <div class="col-auto col">Каталог</div>
                    </a>
                </div>
                <div class="col-header-nav col pb-10">
                    @if (isset($mainMenuItems) && $mainMenuItems?->isNotEmpty())
                        <ul class="main-menu row">
                            @foreach($mainMenuItems as $menu)
                                <li class="col-auto @if(request()->is($menu->slug)) _active @endif"><a href="/{{ $menu->slug }}" class="__link"><span class="dashed dash">{{ $menu->title}}</span></a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="col-header-cart col pb-10">
                    <a href="{{ route('cart.index') }}" class="header-cart-item cart">
                        <div class="w-icon">
                            <div class="icon">
                                <div class="count">{{ $cartCount }}</div>
                                <svg viewBox="0 0 23 22" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 1C0 0.447715 0.447715 0 1 0H3C3.47158 0 3.87907 0.329457 3.97783 0.790578L4.87936 5H21.04C21.3433 5 21.6302 5.13765 21.82 5.37422C22.0098 5.61079 22.082 5.92071 22.0162 6.21679L20.3666 13.645C20.3666 13.6453 20.3667 13.6447 20.3666 13.645C20.2197 14.3115 19.8498 14.9087 19.3182 15.3368C18.7863 15.7649 18.1244 15.9989 17.4416 16L7.67003 16C6.97666 16.0126 6.30018 15.7846 5.75581 15.3545C5.20825 14.922 4.82861 14.312 4.68225 13.6298L3.10272 6.25468C3.09506 6.22552 3.08869 6.19584 3.08367 6.16571L2.19149 2H1C0.447715 2 0 1.55228 0 1ZM5.3077 7L6.63775 13.2102C6.63778 13.2104 6.63773 13.2101 6.63775 13.2102C6.6866 13.4375 6.81318 13.6411 6.99561 13.7852C7.17813 13.9294 7.40521 14.0054 7.63775 14.0002L7.66 14L17.4384 14C17.4386 14 17.4382 14 17.4384 14C17.6658 13.9995 17.8868 13.9215 18.0639 13.7789C18.2412 13.6362 18.3645 13.4373 18.4134 13.215L19.7936 7H5.3077Z"/><path fill-rule="evenodd" clip-rule="evenodd" d="M15.95 19.95C15.95 18.8454 16.8454 17.95 17.95 17.95C19.0545 17.95 19.95 18.8454 19.95 19.95C19.95 21.0546 19.0545 21.95 17.95 21.95C16.8454 21.95 15.95 21.0546 15.95 19.95Z"/><path fill-rule="evenodd" clip-rule="evenodd" d="M4.94995 19.95C4.94995 18.8454 5.84538 17.95 6.94995 17.95C8.05452 17.95 8.94995 18.8454 8.94995 19.95C8.94995 21.0546 8.05452 21.95 6.94995 21.95C5.84538 21.95 4.94995 21.0546 4.94995 19.95Z"/></svg>
                            </div>
                        </div>
                        <div class="title">Корзина</div>
                    </a>
                </div>
                <div class="col-header-search col-12 col pb-10">
                    <div class="w-header-search">
                        <form action="{{ route('search') }}" method="get">
                            <div class="search">
                                <input type="text" name="query" id="site-search-input" class="input__default" placeholder="Поиск по сайту" autocomplete="off">
                                <div class="w-btn">
                                    <button type="submit" class="button fcm">
                                        <svg version="1.1" width="20px" height="20px" enable-background="new 0 0 515.558 515.558" viewBox="0 0 515.558 515.558" xmlns="http://www.w3.org/2000/svg"><path d="m378.344 332.78c25.37-34.645 40.545-77.2 40.545-123.333 0-115.484-93.961-209.445-209.445-209.445s-209.444 93.961-209.444 209.445 93.961 209.445 209.445 209.445c46.133 0 88.692-15.177 123.337-40.547l137.212 137.212 45.564-45.564c0-.001-137.214-137.213-137.214-137.213zm-168.899 21.667c-79.958 0-145-65.042-145-145s65.042-145 145-145 145 65.042 145 145-65.043 145-145 145z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-bottom">
        @if (isset($menuCategories) && $menuCategories?->isNotEmpty())
            <div class="container">
                <div class="content">
                    <ul class="main-menu row align-items-center justify-content-between">
                        @foreach ($menuCategories as $category)
                            <li class="col-auto @if(request()->route('category')?->id == $category->id) _active @endif"><a href="{{ $category->getLink() }}" class="__link"><span class="dashed dash">{{ $category->h1 ?: $category->title }}</span></a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</header>
<div class="header-empty"></div>