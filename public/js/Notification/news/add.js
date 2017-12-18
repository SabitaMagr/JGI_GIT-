(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali("newsDate", "nepaliDate");
    });
})(window.jQuery, window.app);
