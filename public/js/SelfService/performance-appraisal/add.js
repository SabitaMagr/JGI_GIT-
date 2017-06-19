(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $("#selfEvaluation1").on("submit", function () {
            app.blockUI({target: "#hris-page-content"});
        });
    });
})(window.jQuery,window.App);