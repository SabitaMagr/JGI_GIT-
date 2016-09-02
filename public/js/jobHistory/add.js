/**
 * Created by ukesh on 9/2/16.
 */
(function ($) {
    'use strict';
    var format="d-M-yyyy";

    $("#startDate").datepicker({
        format: format,
        autoclose:true
    });
    $("#endDate").datepicker({
        format: format,
        autoclose:true
    });

})(window.jQuery);