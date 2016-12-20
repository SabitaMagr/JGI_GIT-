(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.addDatePicker(
                $("#fromDate"),
                $("#toDate")
                );
        $("#grid").kendoGrid({
            height: 450,
            sortable: true
        });
    });
})(window.jQuery, window.app);