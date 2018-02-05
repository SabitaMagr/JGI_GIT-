(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var $table = $('#table');


        var columns = [
            {field: "ATTENDANCE_DT", title: "Attendance Date"},
            {field: "FULL_NAME", title: "Employee Name"},
            {field: "IN_TIME", title: "In Time"},
            {field: "OUT_TIME", title: "Out Time"},
            {field: "NORMARL_HOUR", title: "Normal hour"},
            {field: "PT_HOUR", title: "PT Hour"},
            {field: "OT_HOUR", title: "OT Hour"},
            {field: "TOTAL_HOUR", title: "Total Hour"},
            {field: "IS_ABSENT", title: "Absent"},
            {field: "IS_SUBSTITUTE", title: "Substitute"},
        ];


        var map = {
            'ATTENDANCE_DT': 'Attendance Date',
            'FULL_NAME': 'Employee Name',
            'IN_TIME': 'In Time',
            'OUT_TIME': 'Out Time',
            'NORMARL_HOUR': 'Normal Hour',
            'PT_HOUR': 'Part Hour',
            'OT_HOUR': 'OT Hour',
            'TOTAL_HOUR': 'Total Hour',
            'IS_ABSENT': 'Is Absent',
            'IS_SUBSTITUTE': 'Is Substitue',
        }

        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['EMPLOYEE_ID', 'FULL_NAME', 'ATTENDANCE_DT', 'IN_TIME', 'OUT_TIME','NORMARL_HOUR','PT_HOUR','OT_HOUR','TOTAL_HOUR','IS_ABSENT','IS_SUBSTITUTE']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Customer Contract List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Customer Contract List.pdf');
        });

//        app.pullDataById("", {}).then(function (response) {
//            app.renderKendoGrid($table, response.data);
//        }, function (error) {
//
//        });

        app.populateSelect($('#monthSelect'), document.monthDetails, 'MONTH_ID', 'MONTH_TITLE', 'Select Month', '');

        $('#monthSelect').on('change', function () {
            var selectedVal = $(this).val();
//            console.log(selectedVal);
            app.pullDataById(document.pullEmployeeAssignBy, {monthId: selectedVal}).then(function (response) {
                console.log(response);
                if (response.data.length == 0) {
                    app.successMessage(['No Employee Assigned For This Month']);
                }
                app.renderKendoGrid($table, response.data);
            }, function (error) {
                console.log(error);
            });
        });


        $('#templateExport').on('click', function () {
            var monthId = $('#monthSelect').val();
            if (monthId > 0) {
                console.log(monthId);
                var url = document.downloadExcelLink + '/' + monthId;
                location.replace(url);
            } else {
                app.errorMessage(['No Select A month first']);
            }

        });

//        $('#import_excel').submit(function () {
//            
//            $('#excel_file').val();
//            
//
//            console.log('sdf');
//            return false;
//        });





//        $('#uploadBtn').on('click', function () {
//            $('#export_excel').submit();
//        });

//        $('#export_excel').on('submit', function (event) {
//            event.preventDefault();
//
//            $.ajax({
//                url: document.UploadExcelLink,
//                method: "POST",
//                data: new FormData(this),
//                contentType: false,
//                processData: false,
//                success: function (data) {
//                    console.log(data);
//                    if(data.success==true){
//                    location.reload();
//                    }else{
//                        
//                    }
//                }
//            });
//
//        });






    });
})(window.jQuery);