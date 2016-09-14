/**
 * Created by ukesh on 9/13/16.
 */
(function ($) {
    'use strict';
    $(document).ready(function () {
        var selectedMenu = $('#' + document.menu.id);
        selectedMenu.addClass('open').addClass('active');
        $('#' + document.menu.id + ' > a :nth-child(2)').addClass('active').addClass('open');
        $('#' + document.menu.id + " > span").addClass("bg-success")
        if (typeof document.menu.subMenu !== "undefined") {
            var selectedMenu = $('#' + document.menu.subMenu.id);
            selectedMenu.addClass('active');
        }
    });

})(window.jQuery);