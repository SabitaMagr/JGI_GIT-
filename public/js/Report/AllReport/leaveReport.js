(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        $leaveReportTable = $("#leaveReportTable");

        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        app.initializeKendoGrid()
    });
})(window.jQuery, window.app);

