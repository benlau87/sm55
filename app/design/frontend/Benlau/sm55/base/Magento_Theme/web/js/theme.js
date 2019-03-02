/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'jquery/ui',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 &&
            $('#co-shipping-method-form .fieldset.rates :checked').length === 0
        ) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }

    setTimeout(function () {
        $('.cart-summary').mage('sticky', {
            container: '#maincontent',
            spacingTop: 70
        });
    }, 500);

    keyboardHandler.apply();

    _tooltip();
    _buildToggle();
    _toogleNav();

    function _buildToggle() {
        $('.sidebar > .block .block-title').append('<span class="toggle-class"></span>');
        $('.sidebar > .block').append('<div class="close-expand-mb"></div>');
        $('.sidebar > .block').append('<div class="close-expanded"></div>');
        $('.toggle-class').on('click', function(event) {
            var toggle = $(this).parent().next();
            $(this).toggleClass('expanded');
            $(this).parents('.block').toggleClass('active');
            $('body').toggleClass('hide-over');

            toggle.toggleClass('show-expanded');

            if ($('.sticky-menu').hasClass('active')) {
                $('.sticky-menu').removeClass('active');
            }

            if ($('.filter-options-item .nano').length) {
                $('.filter-options-item .nano').nanoScroller();
            }
        });
        $('.close-expanded, .close-expand-mb').on('click', function (event) {
            $(this).parents('.block').find('.toggle-class').trigger('click');
        });
    }

    function _tooltip() {
        $('.mb-tooltip').each(function() {
            $(this).tooltip({
                show: null,
                hide: {
                    delay: 250
                },
                position: {
                    my: "center bottom-30",
                    at: "center top",
                    using: function(position, feedback) {
                        $(this).css(position);
                        $(this).addClass("magebig-tooltip");
                    }
                },
                open: function( event, ui ) {
                    ui.tooltip.addClass('in');
                },
                close: function( event, ui ) {
                    ui.tooltip.removeClass('in');
                }
            });
        })
    }

    function _toogleNav() {
        // button show hide menu mobile
        $('.btn-nav').on('click', function(event) {
            event.preventDefault();
            $('.overlay-contentpush').addClass('open');
            $('.page-wrapper').addClass('overlay-open');
            $('html').addClass('nav-open');
        });
        $('.nav-bar-wrap').on('click', function(event) {
            if (!$(event.target).closest('.nav-bar').length) {
                $('.overlay-contentpush').removeClass('open');
                $('.btn-nav').removeClass('active');
                $('.page-wrapper').removeClass('overlay-open');
                $('html').removeClass('nav-open');
            }
        });

        var toggles = document.querySelectorAll(".mb-toggle-switch");
        for (var i = toggles.length - 1; i >= 0; i--) {
            var toggle = toggles[i];
            toggleHandler(toggle);
        }
    }

    function toggleHandler(toggle) {
        toggle.addEventListener("click", function(e) {
            e.preventDefault();
            (this.classList.contains("active") === true) ? this.classList.remove("active"): this.classList.add("active");
        });
    }

    function removeURLParameter(url, parameter) {
        //prefer to use l.search if you have a location/link object
        var urlparts = url.split('?');
        if (urlparts.length >= 2) {

            var prefix = encodeURIComponent(parameter) + '=';
            var pars = urlparts[1].split(/[&;]/g);

            //reverse iteration as may be destructive
            for (var i = pars.length; i-- > 0;) {
                //idiom for string.startsWith
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                    pars.splice(i, 1);
                }
            }
            return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
        }
        return url;
    }
    window.history && window.history.replaceState && window.history.replaceState(null, null, removeURLParameter(window.location.href, 'btp'));
});
