(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.addDatePicker($("#startDate"));
        /* prevent past event post */
        //$('#startDate').datepicker("setStartDate", new Date());
        /* end of  prevent past event post */
    });
})(window.jQuery, window.app);

