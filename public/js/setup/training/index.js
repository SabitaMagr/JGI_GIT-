(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.addDatePicker(
                $("#fromDate"),
                $("#toDate")
                );
        $("#trainingTable").kendoGrid();
    });
})(window.jQuery, window.app);