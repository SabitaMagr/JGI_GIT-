(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePicker('fromDate', 'toDate');
    });
})(window.jQuery, window.app);
