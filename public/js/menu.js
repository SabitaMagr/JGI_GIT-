/**
 * Created by ukesh on 9/13/16.
 */
(function ($) {
    'use strict';
    $(document).ready(function () {
        var selectedMenu = $('#' + document.menu.id);
        selectedMenu.addClass('open').addClass('active');
        $('#' + document.menu.id + ' > a :nth-child(3)').addClass('open');
        $('<span class="selected"></span>').insertAfter('#' + document.menu.id + ' > a :nth-child(2)');
        $('#' + document.menu.id + " > span").addClass("bg-success")

        var selectedSubMenu;
        if (typeof document.menu.subMenu !== "undefined") {
            selectedSubMenu = $('#' + document.menu.subMenu.id);
            selectedSubMenu.addClass('active');
        }

        if (typeof document.menu.subMenuChild !== "undefined") {
            selectedMenu.addClass('open');
            $('#' + document.menu.subMenu.id + ' > a :nth-child(2)').addClass('open').addClass('active');
            $('#' + document.menu.subMenu.id + ' > ul').css('display', 'block');

            var selectedSubMenuChild = $('#' + document.menu.subMenuChild.id);
            selectedSubMenuChild.addClass('active');
        }
    });

})(window.jQuery);