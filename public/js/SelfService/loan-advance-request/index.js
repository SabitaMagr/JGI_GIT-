(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.addDatePicker(
                $("#startDate"),
                $("#endDate")
                );
        $("#grid").kendoGrid({
            height: 450,
            sortable: true
        });
    });
})(window.jQuery, window.app);