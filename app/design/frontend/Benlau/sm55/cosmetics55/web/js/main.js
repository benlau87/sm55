define([
        "jquery"
    ],
    function($) {
        "use strict";

        $('#algolia-glass').on('click', function() {
            $('#search_mini_form').submit();
        });

        $(document).on('mouseenter', '.sticky-menu.active .mb-tooltip', function(e) {
            $('.magebig-tooltip').addClass('hidden');
        });

        $(document).on('mouseout', '.sticky-menu .mb-tooltip', function(e) {
            $('.magebig-tooltip').removeClass('hidden');
        });

    });