/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {

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
        app.setLoadingOnSubmit("overtimeRequest-form");
    });
})(window.jQuery, window.app);


