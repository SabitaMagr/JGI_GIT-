(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePicker('fromDate', 'toDate');
        /* prevent past event post */
        $('#fromDate').datepicker("setStartDate", new Date());
        $('#toDate').datepicker("setStartDate", new Date());
        /* end of  prevent past event post */
    });
})(window.jQuery, window.app);
