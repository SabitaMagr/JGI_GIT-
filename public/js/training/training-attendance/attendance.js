(function ($, app) {
    'use strict';
    $(document).ready(function () {

        $('.trainingAtdChk').on('change', function () {
            var employeeId = $(this).attr('data-empId');
            var attDate = $(this).attr('data-AttDate');
            var attendanceStatus;

            if (this.checked) {
                attendanceStatus = 'P';
            } else
            {
                attendanceStatus = 'A';
            }

            console.log(employeeId);
            console.log(attDate);
            console.log(attendanceStatus);

            app.pullDataById(document.updateUrl, {
                'employeeId': employeeId,
                'trainingId': document.trainingId,
                'trainingDate': attDate,
                'attendanceStatus': attendanceStatus
            }).then(function (success) {
                console.log(success);
            });

        });


//        $('.checkAll').on('change', function () {
//            var columnNo = $(this).attr('data-col');
//            if (this.checked) {
//                $('.checkbox' + columnNo).prop('checked', true);
//            } else
//            {
//                $('.checkbox' + columnNo).prop('checked', false);
//            }
//        });

//        $('#submitBtn').on('click', function () {
//            console.log(clicked);
//        });



    });
})(window.jQuery, window.app);