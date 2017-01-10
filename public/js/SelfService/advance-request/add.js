(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.addDatePicker(
                $("#startDate")
                );
    });
})(window.jQuery, window.app);

