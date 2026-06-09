<div class="w-index-callback-frame color-white">
    <form class="form-callback">
        <div class="row row-index-callback-frame align-items-center no-gutters">
            <div class="col-decorated-image col">
                <div class="image">
                    <img src="assets/i/index-callback-image.svg" alt="" class="img block">
                </div>
            </div>
            <div class="col-aside-content col">
                <div class="decorated-frame">
                    <svg viewBox="0 0 769 430" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.59022 14.4169C-1.39401 7.67752 3.5504 0 10.921 0H759C764.523 0 769 4.47715 769 10V420C769 425.523 764.523 430 759 430H11.1246C3.70425 430 -1.23397 422.216 1.85034 415.467C17.6965 380.793 55.6681 291.215 57.4024 225.5C59.3298 152.469 18.0165 51.5129 1.59022 14.4169Z" fill="url(#index-callback-content-frame-gradient)"/><defs><linearGradient id="index-callback-content-frame-gradient" x1="382" y1="0" x2="382" y2="430" gradientUnits="userSpaceOnUse"><stop stop-color="#FF5F00"/><stop offset="1" stop-color="#FF5F00"/></linearGradient></defs></svg>
                </div>
                <div class="content">
                    @if ($callback_form ?? false && $callback_form['title'])
                        <div class="s-name _h3 semibold lg-mb-15 mb-10 align-md-left align-center">{{ $callback_form['title'] }}</div>
                    @endif
                    @if ($callback_form ?? false && $callback_form['desc'])
                        <div class="s-name description _h6 lg-mb-10 mb-10">{!! $callback_form['desc'] !!}</div>
                    @endif
                    <div class="row lg-lg-gutters sm-gutters">
                        <div class="col-sm-6 col-12 col">
                            <div class="input input-icon-left person lg-mt-15 mt-10">
                                <div class="icon"><svg width="27" height="29" viewBox="0 0 27 29" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.6982 0.666748C9.83226 0.666748 6.69824 3.80076 6.69824 7.66675C6.69824 11.5327 9.83226 14.6667 13.6982 14.6667C17.5642 14.6667 20.6982 11.5327 20.6982 7.66675C20.6982 3.80076 17.5642 0.666748 13.6982 0.666748Z" fill="#FF985B"/><path d="M25.5252 19.8191C18.1303 15.8894 9.26626 15.8894 1.87133 19.8191C1.14949 20.2027 0.698303 20.9534 0.698303 21.7709V26.6666C0.698303 27.9553 1.74297 28.9999 3.03164 28.9999H24.365C25.6536 28.9999 26.6983 27.9553 26.6983 26.6666V21.7709C26.6983 20.9534 26.2471 20.2027 25.5252 19.8191Z" fill="#FF985B"/></svg></div>
                                <input type="text" name="name" class="input__default orange" placeholder="Ваше имя *">
                            </div>
                        </div>
                        <div class="col-sm-6 col-12 col">
                            <div class="input input-icon-left phone lg-mt-15 mt-10">
                                <div class="icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.83333 10.3933C6.75333 14.1667 9.84 17.2533 13.62 19.1733L16.5533 16.2333C16.92 15.8667 17.4467 15.76 17.9067 15.9067C19.4 16.4 21.0067 16.6667 22.6667 16.6667C23.4067 16.6667 24 17.26 24 18V22.6667C24 23.4067 23.4067 24 22.6667 24C10.1467 24 0 13.8533 0 1.33333C0 0.593333 0.6 0 1.33333 0H6C6.74 0 7.33333 0.593333 7.33333 1.33333C7.33333 2.99333 7.6 4.6 8.09333 6.09333C8.24 6.55333 8.13333 7.08 7.76667 7.44667L4.83333 10.3933Z" fill="#FF985B"/></svg></div>
                                <input type="text" name="phone" class="input__default orange" placeholder="Номер телефона *">
                            </div>
                        </div>
                    </div>
                    <div class="custom-selector check xl-mt-25 mt-10">
                        <label class="label block pointer">
                            <div class="input">
                                <input type="checkbox" name="agree" class="selector hidden">
                                <div class="styled-figure">
                                    <div class="border">
                                        <div class="inset-figure"></div>
                                    </div>
                                </div>
                                <div class="label label-inner">Согласен на <a @if(\App\Services\Support\TextService::getSettingValue('content', 'privacy')) href="{{ route('page', ['slug' => \App\Services\Support\TextService::getSettingValue('content', 'privacy') ]) }}"@endif class="color-white">обработку персональных данных</a></div>
                            </div> 
                        </label>
                    </div>
                    <div class="xl-mt-15 mt-10"><span class="color-red">*</span> поля обязательные для заполнения</div>
                    <div class="row justify-content-md-end justify-content-center mt-20">
                        <div class="col-lg-6 col-md-8 col-sm-7 col-12">
                            <button type="submit" class="button transparent white block">Перезвоните мне</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>	