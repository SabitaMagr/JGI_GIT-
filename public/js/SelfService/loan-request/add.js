(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("form-loanDate","nepaliDate");
        /* prevent past event post */
//        $('#form-loanDate').datepicker("setStartDate", new Date());
        $('#form-loanDate').datepicker("setStartDate",);
        /* end of  prevent past event post */
        app.setLoadingOnSubmit("loanApprove-form");
        app.setLoadingOnSubmit("loan-form");
        
        var employeeId = $('#employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);
    });
})(window.jQuery, window.app);

