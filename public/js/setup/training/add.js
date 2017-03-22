(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePicker('startDate', 'endDate');

        /* prevent past events */
        $('#startDate').datepicker("setStartDate", new Date());
        $('#endDate').datepicker("setStartDate", new Date());
        /* end of prevent past events */

        var inputFieldId = "form-trainingName";
        var formId = "training-form";
        var tableName = "HRIS_TRAINING_MASTER_SETUP";
        var columnName = "TRAINING_NAME";
        var checkColumnName = "TRAINING_ID";
        var selfId = $("#trainingID").val();
        if (typeof selfId === "undefined") {
            selfId = 0;
        }
        window.app.checkUniqueConstraints(inputFieldId, formId, tableName, columnName, checkColumnName, selfId);
        window.app.checkUniqueConstraints("form-trainingCode", formId, tableName, "TRAINING_CODE", checkColumnName, selfId);
    });
})(window.jQuery, window.app);


