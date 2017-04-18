(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $employeeId = $('#employeeId');
        var $oldAmount = $('#oldAmount');
        var $newAmount = $('#newAmount');
        var $effectiveDate = $('#effectiveDate');
        var $effectiveNepaliDate = $('#effectiveNepaliDate');
        var $jobHistoryId = $('#jobHistoryId');
        
        

        var salaryDetail = document.salaryDetail;
        console.log(salaryDetail);
        $oldAmount.val(salaryDetail['OLD_AMOUNT']);
        $oldAmount.attr("disabled", true);

        $newAmount.val(salaryDetail['NEW_AMOUNT']);
        $effectiveDate.val(salaryDetail['EFFECTIVE_DATE']);
        
        


//        var fetchServiceEvents = function ($this) {
//            app.pullDataById(document.serviceHistoryList, {
//                'employeeId': $this.val()
//            }).then(function (success) {
//                console.log("serviceHistoryList", success);
//                populateJobHistory(success.jobHistoryList);
//            }, function (failure) {
//                console.log("serviceHistoryList fail", failure);
//            });
//        };
//        var populateJobHistory = function (histories) {
//            $jobHistoryId.html("");
//            $jobHistoryId.append($("<option />").val(null).text("Select Service Event"));
//            $.each(histories, function () {
//                $jobHistoryId.append($("<option />").val(this.JOB_HISTORY_ID).text(this.SERVICE_EVENT_TYPE_NAME + "(" + this.START_DATE + ")"));
//            });
//        };
//
//        fetchServiceEvents($employeeId);
    
    
    app.datePickerWithNepali("effectiveDate","effectiveNepaliDate");

    });

})(window.jQuery, window.app);