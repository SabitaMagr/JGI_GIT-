/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $overtimeDate = $("#overtimeDate");
        if (!($overtimeDate.is('[readonly]'))) {
            app.datePickerWithNepali("overtimeDate", "nepaliDate");
            app.getServerDate().then(function (response) {
                $overtimeDate.datepicker('setEndDate', app.getSystemDate(response.data.serverDate));
            }, function (error) {
                console.log("error=>getServerDate", error);
            });
        } else {
            app.datePickerWithNepali("overtimeDate", "nepaliDate");
        }

        var $employeeId = $('#employeeId');
        app.floatingProfile.setDataFromRemote($employeeId.val());

        $employeeId.on("change", function (e) {
            app.floatingProfile.setDataFromRemote($(e.target).val());
        });
        app.setLoadingOnSubmit("overtimeRequest-form", function ($form) {
            var formData = new FormData($form[0]);
            if (formData.getAll('startTime[]').length == 0) {
                app.showMessage("Minimum One Start time and End time is required.", 'error');
                return false;
            } else {
                return true;
            }
        });
    });
})(window.jQuery, window.app);


