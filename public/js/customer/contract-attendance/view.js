(function ($) {
    'use strict';
    $(document).ready(function () {

        var $table = $('#table');


        var columns = [
            {field: "ATTENDANCE_DT", title: "Attendance Date"},
            {field: "FULL_NAME", title: "Employee Name"},
            {field: "IN_TIME", title: "In Time"},
            {field: "OUT_TIME", title: "Out Time"},
            {field: "TOTAL_HOUR", title: "Total Hour"},
        ];
        var map = {
            'ATTENDANCE_DT': 'Attendance Date',
            'FULL_NAME': 'Employee Name',
            'IN_TIME': 'In Time',
            'OUT_TIME': 'Out Time',
            'TOTAL_HOUR': 'Total Hour',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['EMPLOYEE_ID', 'FULL_NAME', 'ATTENDANCE_DT','IN_TIME','OUT_TIME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Customer Contract List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Customer Contract List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });



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