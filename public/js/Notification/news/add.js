(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali("newsDate", "nepaliDate");
        app.datePickerWithNepali("newsExpiryDate", "nepaliDateExpiry");
    });
})(window.jQuery, window.app);
