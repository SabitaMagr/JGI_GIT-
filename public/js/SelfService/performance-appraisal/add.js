(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#selfEvaluation1").on("submit", function () {
            App.blockUI({target: "#hris-page-content"});
        });
    });
})(window.jQuery);