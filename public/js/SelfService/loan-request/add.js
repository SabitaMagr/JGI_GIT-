(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("form-loanDate","nepaliDate");
        /* prevent past event post */
        $('#form-loanDate').datepicker("setStartDate", new Date());
        /* end of  prevent past event post */
        app.setLoadingOnSubmit("loanApprove-form");
        app.setLoadingOnSubmit("loan-form");
    });
})(window.jQuery, window.app);

