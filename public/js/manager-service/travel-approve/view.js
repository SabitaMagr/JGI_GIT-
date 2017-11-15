(function ($, app) {
    'use strict';
    $(document).ready(function (e) {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'form-fromDate', 'nepaliEndDate1', 'form-toDate')
        app.setLoadingOnSubmit("travelApprove-form");
        var employeeId = $('#employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });
    });
})(window.jQuery, window.app);
