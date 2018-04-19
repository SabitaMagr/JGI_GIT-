(function ($, app) {
    'use strict';
    $(document).ready(function (e) {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'form-fromDate', 'nepaliEndDate1', 'form-toDate')
        $('#approve').on('click', function () {
            var recommendRemarksId = $("#form-recommendedRemarks");
            var approveRemarksId = $("#form-approvedRemarks");

            if (typeof recommendRemarksId !== "undefined") {
                recommendRemarksId.removeAttr("required");
            }
            if (typeof approveRemarksId !== "undefined") {
                approveRemarksId.removeAttr("required");
            }
            App.blockUI({target: "#hris-page-content"});
        });
        var employeeId = $('#employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });
    });
})(window.jQuery, window.app);
