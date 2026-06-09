$(document).ready(function(){
//CUSTOM JS LUTOVICH//
////////////validation close
    $('._js-b-close-validation-alert').on('click', function () {
        $('._js-validation-alert').addClass('hide');
        return false;
    });
////////////cookie close
    $('._js-b-cookie-alert').on('click', function () {
        $('._js-cookie-alert').addClass('hide');
        return false;
    });
////////////b-wrapper min-height
    $(window).on('resize scroll load', function () {
        var footer_H = $('.s-footer').height();
        var b_wrapper_H = $(window).height() - footer_H;
        $('.b-wrapper').css("min-height" , ""+b_wrapper_H +"px");
    });
////////////device type
    $(window).on('scroll resize load', function () {
        var touchscreen = jQuery.browser.mobile;
        if (touchscreen) {
            $('body').removeClass('_desk').addClass('_touch');
        }
        else {
            $('body').removeClass('_touch').addClass('_desk');
        }
    })
////////////article scroll tables mobile
    $(document).each(function(e){
        $('._js-article-table-mobile-scroll table').wrap('<div class="w-scrollable-table-shades"><div class="w-scrollable-table"></div></div>');
    });
////////////article float images mobile
    $('article img').each(function(e){
        if($(this).css('float') === 'left'){$(this).addClass('img-article-left');} 
        if($(this).css('float') === 'right'){$(this).addClass('img-article-right');} 
    });
////////////article fancy images
    $(function(){
        $('._js-article-fancy-images img').each(function () {
            var $this = $(this);
            var $thisparentdatafancy = $(this).parents('._js-article-fancy-images').attr('data-images-fancy')
            $this.wrap('<a class="block__link grouped_elements" data-fancybox="' + $thisparentdatafancy + '" rel="" href="' + $this.attr('src') + '" title="' + $this.attr('alt') + '"></a>');
        });
    });
////////////pop open
    $('body').on('click', '._js-b-pop', function () {
        var pop_id = $(this).attr('data-pop-id');
        $('.s-popup').show();
        $('.s-popup__background').show();
        $('._js-popup.' + pop_id ).addClass('animate');
        $('._js-popup.' + pop_id ).css("display" , "inline-block");
        return false;
    });
////////////popup CLOSE
    $('._js-pop-close').on('click', function () {
        $('.s-popup').hide();
        $('.w-popup').hide();
        $('.s-popup__background').hide();
        $('.w-popup').removeClass('animate');
        return false;
    });
////////////toggler-button-inset-default
    $('body').on('click', '._js-b-toggler-button', function () {
        const insetCurrent = $(this).closest('._js-toggler-button-parrent').find('._js-inset');
        if (insetCurrent.is(":visible")){
            insetCurrent.slideUp();
            $(this).removeClass('_toggled');
            /*$(this).closest('._js-toggler-button-parrent').removeClass('_toggled');*/
            setTimeout(function() {
                $(this).parents('._js-toggler-button-parrent').removeClass('');
            }.bind(this), 200);
        }
        else {
            insetCurrent.slideDown();
            $(this).addClass('_toggled');
            /*$(this).closest('._js-toggler-button-parrent').addClass('_toggled');*/
            setTimeout(function() {
                $(this).parents('._js-toggler-button-parrent').addClass('');
            }.bind(this), 100);
        }
        $(this).closest('._js-toggler-button-parrent').toggleClass('_toggled');
        return false;
    });
////////////double-changed-button
    $('._js-b-double-changed').on('click', function () {
        $(this).find('.info').toggleClass('_active');
        return false;
    });
////////////button-more-toggler
    $('._js-b-show-more').on('click', function () {
        $(this).parents('._js-show-more-filters-group').toggleClass('_toggled');
        return false;
    });

////////////mobile-menu-new
    $('._js-b-toggle-mobile-menu').on('click', function () {
        $('._js-s-toggle-mobile-menu').toggleClass('_toggled');
        $('body').toggleClass('_blocked-mobile');
        $('body').toggleClass('_js-header-nav-shown');
        return false;
    });
////////////mobile-menu-new-toggle-inset
    $('._js-s-toggle-mobile-menu .ul-mobile-menu.default ._js-b-dropper').on('click', function () {
        let insetMenuCurrent = $(this).closest('._js-li-dropper').children('._js-inset');
        if (insetMenuCurrent.is(":visible")){
            insetMenuCurrent.slideUp();
            $(this).removeClass('_toggled');
            $(this).siblings('.b-dropper').removeClass('_toggled');
            $(this).closest('._js-li-dropper').removeClass('_toggled');
        }
        else {
            insetMenuCurrent.slideDown();
            $(this).siblings('.b-dropper').addClass('_toggled');
            $(this).addClass('_toggled');
            $(this).closest('._js-li-dropper').addClass('_toggled');
        }
        return false;
    });
////////////js cloud-dropper
    $('._js-b-click-dropper').on('click', function () {
        /*$('._js-click-dropper').removeClass('_toggled');*/
        if ($(this).parents('._js-click-dropper').hasClass('_toggled')) {
            $(this).parents('._js-click-dropper').removeClass('_toggled')
        }
        else {
            $(this).parents('._js-click-dropper').addClass('_toggled');
        }
        return false;
    });
    $(document).on('click', function (e) {
        if ($('._js-click-dropper ._js-inset').has(e.target).length === 0){
            $('._js-click-dropper').removeClass('_toggled');
        }
    });
////////////pager up
    $(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() != 0) {
                $('._js-w-pager-up').fadeIn();
            } else {
                $('._js-w-pager-up').fadeOut();
            }
        });
        $('._js-b-pager-up').click(function () {
            $('body,html').animate({scrollTop: 0}, 300);
            return false;
        });
    });
////////////mobile-search
    $('._js-b-mobile-search').on('click', function () {
        $('._js-click-dropper').removeClass('_toggled');
        $('body').toggleClass('_js-mobile-search-toggled');
        $('#site-search-input').focus();
        return false;
    });
////////////hide mobile header on scroll 
    $(document).ready(() => {
        const onScrollHeader = () => {
            const header = $('.s-header-mobile')
            let prevScroll = $(window).scrollTop()
            let currentScroll
            $(window).scroll(() => {
                currentScroll = $(window).scrollTop()
                const headerHidden = () => header.hasClass('header_hidden')
                if (currentScroll > prevScroll && !headerHidden()) {
                    var scrollTop = $(window).scrollTop();
                    if (scrollTop >= 1000) {
                       header.addClass('header_hidden')
                   }
               }
               if (currentScroll < prevScroll && headerHidden()) {
                header.removeClass('header_hidden')
            }
            prevScroll = currentScroll
        })
        }
        onScrollHeader()
    })
////////////fixed header responsive
    $(window).on('resize scroll load', function () {
        var windowSize = $(window).width();
        var header_H = $('.s-header').height();
        var header_top_H = $('.header-top').height();
        var header_middle_H = $('.header-middle').height();
        var header_bottom_H = $('.header-bottom').height();
        var offsetTop = $('.s-header').offset().top;
        var scrollTop = $(window).scrollTop();
        if (windowSize >= 310) {
            if (scrollTop >= header_H - header_middle_H - header_bottom_H) {
                $('.s-header').addClass('fixed');
            } 
            if (scrollTop < header_H - header_middle_H - header_bottom_H) {
                $('.s-header').removeClass('fixed');
            } 
        }
        $('.header-empty').css("height" , +header_middle_H +"px");
    });
//fixed top menu 
    /*
        $(window).scroll(function() {
            if ($(window).scrollTop() > 241) {$('header').addClass('fixed');} 
            else {$('header').removeClass('fixed');}
        });
    */
//change-tabs
    if ($('._js-switchible-tabs').length) {
        $('._js-w-tabs ._js-change-tab').on('click', function () {
            var tabs_id = $(this).attr('data-tabs-id');
            $(this).parents('._js-switchible-tabs.' + tabs_id).find('._js-w-tabs-content ._js-tab-content.' + tabs_id).removeClass('_active').eq($(this).parents('._js-parent-element.' + tabs_id).index()).addClass('_active');
            $(this).parents('._js-w-tabs.' + tabs_id).find('._js-parent-element.' + tabs_id).removeClass('_active').eq($(this).parents('._js-parent-element.' + tabs_id).index()).addClass('_active');
            $(this).parents('._js-w-tabs.' + tabs_id).find('._js-change-tab').removeClass('_active').eq($(this).parents('._js-parent-element.' + tabs_id).index()).addClass('_active');

            $('._js-mobile-select-overlay').removeClass('_toggled-mobile');
            return false;
        });
    }
//toggle-navigation-menu CUSTOM
    $('._js-b-toggle-navigation-menu').on('click', function () {
        var nav_id = $(this).attr('data-nav-id');
        $('._js-navigation-menu.' + nav_id ).toggleClass('_toggled');
        $('._js-navigation-menu.' + nav_id ).children('.menu-layout').toggleClass('_toggled');
        $('._js-mobile-menu.' + nav_id ).toggleClass('_toggled');
        $('body').toggleClass('_blocked-mobile');
        $('body').toggleClass('_nav-menu-shown');
        return false;
    });
//fancybox
    if ($('._js-w-fancy').length > 0) {    
        $("a.grouped_elements").fancybox();
    }
//custom checkboxes
$('body').on('change', '.custom-selector .selector', function() {
    var this_name = $(this).attr('name');
    if ($(this).is(':checked')) {
        $('.custom-selector .selector[name ="'+this_name+'"]').parents('.custom-selector').removeClass('_checked');
        $(this).parents('.custom-selector').addClass('_checked');
    }
    else {
        $(this).parents('.custom-selector').removeClass('_checked');
    }
});
//INDEX SLIDER
$('.owl-index-slider').owlCarousel({
    items: 1,
    navText: false,
    loop: false,
    mouseDrag: true,
    touchDrag: true,
    nav: false,
    dots: true,
    loop: false,
    smartSpeed: 800,
    navSpeed: 800,
    dotsSpeed: 200,
    autoplaySpeed: 600,
});

//categorys-list-slider
$('.owl-categorys-list-slider ').owlCarousel({
    items: 5,
    loop: false,
    nav: true,
    navText: ['<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>', '<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>'],
    dots: true,
    margin: 10,
    mouseDrag: true,
    touchDrag: true,
    center: false,
    responsive:{
        0:{
            items:1,
            margin: 10
        },
        360:{
            items:2,
            margin: 10
        },
        576:{
            items:2,
            margin: 10
        },
        768:{
            items:3,
            margin: 10
        },
        992:{
            items:4,
            margin: 20
        },
        1200:{
            items:5,
            margin: 20
        }
    }    
});
//catalog-list-slider
$('.owl-catalog-list-slider').owlCarousel({
    items: 5,
    loop: false,
    nav: true,
    navText: ['<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>', '<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>'],
    dots: true,
    margin: 10,
    mouseDrag: true,
    touchDrag: true,
    center: false,
    responsive:{
        0:{
            items:1,
            margin: 10
        },
        360:{
            items:2,
            margin: 10
        },
        576:{
            items:2,
            margin: 10
        },
        768:{
            items:3,
            margin: 10
        },
        992:{
            items:4,
            margin: 20
        },
        1200:{
            items:5,
            margin: 20
        }
    }    
});
//brands-list-slider
$('.owl-brands-list-slider').owlCarousel({
    items: 5,
    loop: false,
    nav: true,
    navText: ['<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>', '<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>'],
    dots: true,
    margin: 10,
    mouseDrag: true,
    touchDrag: true,
    center: false,
    responsive:{
        0:{
            items:1,
            margin: 10
        },
        360:{
            items:3,
            margin: 10
        },
        576:{
            items:3,
            margin: 10
        },
        768:{
            items:4,
            margin: 10
        },
        992:{
            items:5,
            margin: 20
        },
        1200:{
            items:6,
            margin: 20
        }
    }    
});
//news-list-slider
$('.owl-news-list-slider').owlCarousel({
    items: 5,
    loop: false,
    nav: true,
    navText: ['<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>', '<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>'],
    dots: true,
    margin: 10,
    mouseDrag: true,
    touchDrag: true,
    center: false,
    responsive:{
        0:{
            items:1,
            margin: 10
        },
        360:{
            items:1,
            margin: 10
        },
        576:{
            items:2,
            margin: 10
        },
        768:{
            items:2,
            margin: 10
        },
        992:{
            items:2,
            margin: 20
        },
        1200:{
            items:3,
            margin: 20
        }
    }    
});
//OWL PRDUCT-SLIDER
    var thumbSlider = $('.owl-product-slider-thumb');
    thumbSlider.owlCarousel({
        items: 5,
        loop: false,
        nav: true,
        dots: false,
        margin: 10,
        mouseDrag: true,
        touchDrag: true,
        center: false,
        navText: ['<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>', '<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>'],
        responsive:{
            0:{
                items:3,
                margin: 15
            },
            360:{
                items:5,
                margin: 15
            },
            576:{
                items:5,
                margin: 15
            },
            768:{
                items:5,
                margin: 15
            },
            992:{
                items:5,
                margin: 15
            },
            1200:{
                items:5,
                margin: 15
            }
        }
    });
    var mainSlider = $('.owl-product-slider');
    mainSlider.owlCarousel({
        items: 1,
        navText: ['<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>', '<svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.37498 1.33464L7.83331 8.0013L1.37498 14.668" stroke-linecap="round" stroke-linejoin="round"/></svg>'],
        loop: false,
        mouseDrag: true,
        touchDrag: true,
        nav: false,
        dots: true,
        loop: false,
        smartSpeed: 800,
        navSpeed: 800,
        dotsSpeed: 200,
        autoplaySpeed: 600,
        animateOut: 'fadeOut',
        dotsContainer: '.owl-product-slider-thumb .owl-stage'
    });

    thumbSlider.on('click', '.owl-item', function(e){
      e.preventDefault();
      var index = $(this).index();
      mainSlider.trigger('to.owl.carousel', [index, 300, true]);
    });
    mainSlider.on('changed.owl.carousel', function(event) {
      var current = event.item.index;
      thumbSlider.find('.owl-item').removeClass('current');
      thumbSlider.find('.owl-item').eq(current).addClass('current');
    });
//psccontrolls
    // $('._js-pcscontrolls ._js-b-minus').on('click', function (e) {
    //     e.preventDefault();
    //     var countField = $(this).parents('._js-pcscontrolls').children('.input__default'),
    //     rowid = $(countField).attr('data-rowid'),
    //     currentCount = parseInt(countField.val(), 10);
    //     countField.val(currentCount - 1);
    // });
    // $('._js-pcscontrolls ._js-b-plus').on('click', function (e) {
    //     e.preventDefault();
    //     var countField = $(this).parents('._js-pcscontrolls').children('.input__default'),
    //     rowid = $(countField).attr('data-rowid'),
    //     currentCount = parseInt(countField.val(), 10);
    //     countField.val(currentCount + 1);
    // });
});

