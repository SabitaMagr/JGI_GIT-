(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'form-fromDate', 'nepaliEndDate1', 'form-toDate')
        var employeeId = $('#employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });
    });
})(window.jQuery, window.app);
