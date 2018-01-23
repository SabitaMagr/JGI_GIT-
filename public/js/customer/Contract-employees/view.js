(function ($) {
    'use strict';
    $(document).ready(function () {

        var $table = $('#table');


        var columns = [
            {field: "ATTENDANCE_DT", title: "Attendance Date"},
            {field: "FULL_NAME", title: "Employee Name"},
        ];
        var map = {
            'ATTENDANCE_DT': 'Attendance Date',
            'FULL_NAME': 'Employee Name',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['EMPLOYEE_ID', 'FULL_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Contract Employee List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Contract Employee List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
        
        $('select').select2();
        
        
        app.populateSelect($('.employees'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
       
        app.addDatePicker($('.contractEmpStartDate'));
        app.addDatePicker($('.contractEmpEndDate'));
        
        
        
        $('#addContractEmp').on('click', function () {

            var appendValues = "<tr>"
                    + "<td><select name='employee[]' class='employees'></select></td>"
                    + "<td><input type='text' class='contractEmpStartDate' name='contractEmpStartDate[]'></td>"
                    + "<td><input type='text' class='contractEmpEndDate' name='contractEmpEndDate[]'></td>"
                    + "<td>"
                    + "<div class='th-inner '>"
                    + "<label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                    + "<input class='chkBoxContractEmp' type='checkbox'/>"
                    + "<span></span>"
                    + "</label>"
                    + "</div>"
                    + "</td>"
                    + "</tr>";

            $('#tblContractEmp tbody').append(appendValues);

            $('#tblContractEmp tbody').find('.contractEmpStartDate:last').datepicker({
                format: 'dd-M-yyyy',
                todayHighlight: true,
                autoclose: true,
                startDate: new Date(document.customerContractDetails.START_DATE),
                endDate: new Date(document.customerContractDetails.END_DATE) 
            });

            $('#tblContractEmp tbody').find('.contractEmpEndDate:last').datepicker({
                format: 'dd-M-yyyy',
                todayHighlight: true,
                autoclose: true,
                startDate: new Date(document.customerContractDetails.START_DATE),
                endDate: new Date(document.customerContractDetails.END_DATE)
            });
            
            

            app.populateSelect($('#tblContractEmp tbody').find('.employees:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
            $('#tblContractEmp tbody').find('.employees:last').select2();


        });
        
        
        function populateEmployees() {
            $("#tblContractEmp tbody").find("tr:gt(0)").remove();

            $.each(document.contractEmpDetails, function (index, value) {

                var appendValues = "<tr>"
                        + "<td><select name='employee[]' class='employees'></select></td>"
                        + "<td><input type='text' class='contractEmpStartDate' name='contractEmpStartDate[]'></td>"
                        + "<td><input type='text' class='contractEmpEndDate' name='contractEmpEndDate[]'></td>"
                        + "<td>"
                        + "<div class='th-inner '>"
                        + "<label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                        + "<input class='chkBoxContractEmp' type='checkbox'/>"
                        + "<span></span>"
                        + "</label>"
                        + "</div>"
                        + "</td>"
                        + "</tr>";

                $('#tblContractEmp tbody').append(appendValues);

                $('#tblContractEmp tbody').find('.contractEmpStartDate:last').datepicker({
                    format: 'dd-M-yyyy',
                    todayHighlight: true,
                    autoclose: true,
                    startDate: new Date(document.customerContractDetails.START_DATE),
                    endDate: new Date(document.customerContractDetails.END_DATE),
                    setDate: new Date()
                });

                $('#tblContractEmp tbody').find('.contractEmpEndDate:last').datepicker({
                    format: 'dd-M-yyyy',
                    todayHighlight: true,
                    autoclose: true,
                    startDate: new Date(document.customerContractDetails.START_DATE),
                    endDate: new Date(document.customerContractDetails.END_DATE),
                    setDate: new Date()
                });

                $('#tblContractEmp tbody').find('.contractEmpStartDate:last').datepicker("update", value.START_DATE);
                $('#tblContractEmp tbody').find('.contractEmpEndDate:last').datepicker("update", value.END_DATE);

                app.populateSelect($('#tblContractEmp tbody').find('.employees:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
                $('#tblContractEmp tbody').find('.employees:last').val(value.EMPLOYEE_ID);
                $('#tblContractEmp tbody').find('.employees:last').select2();


            });

        }
        
        
        $('#delContractEmp').on('click', function () {
            $('#tblContractEmp .chkBoxContractEmp:checked').each(function () {
                $(this).parents("tr").remove()
            });
        });
        
        
        populateEmployees();
        
        
        








    });
})(window.jQuery);