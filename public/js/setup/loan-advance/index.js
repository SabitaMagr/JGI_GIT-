(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.addDatePicker(
                $("#fromDate"),
                $("#toDate")
                );
        $("#loanAdvanceTable").kendoGrid();
    });
})(window.jQuery, window.app);