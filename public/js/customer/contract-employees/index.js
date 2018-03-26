(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#submitBtn").hide();

        var $table = $('#table');

//        $('select').select2();


        var $cutomerSelect = $('#CustomerSelect');
        var $contractSelect = $('#ContractSelect');
        var customer;
        var contract;
//        var $assignTable = $('#assignTable');

        $('#addEmployeeStartTime').combodate({
            minuteStep: 1,
        });
        $('#addEmployeeEndTime').combodate({
            minuteStep: 1,
        });

        app.startEndDatePickerWithNepali('nepaliStartDate', 'startDate', 'nepaliEndDate', 'endDate');

        //  console.log(document.customerList);

        app.populateSelect($('#CustomerSelect'), document.customerList, 'CUSTOMER_ID', 'CUSTOMER_ENAME', 'Select An Customer', '');



        var grid = app.initializeKendoGrid($table, [
            {field: "FULL_NAME", title: "Employee", width: 120},
            {field: "LOCATION_NAME", title: "Location", width: 120},
            {field: "DUTY_TYPE_NAME", title: "Duty Type", width: 120},
            {field: "DESIGNATION_TITLE", title: "Designation", width: 120},
            {field: "START_TIME", title: "Start Time", width: 120},
            {field: "END_TIME", title: "End Time", width: 120},
            {title: "Start Date", columns: [
                    {field: "START_DATE_AD", title: "AD"},
                    {field: "START_DATE_BS", title: "BS"},
                ]},
            {title: "End Date", columns: [
                    {field: "END_DATE_AD", title: "AD"},
                    {field: "END_DATE_BS", title: "BS"},
                ]}
        ]);


        $cutomerSelect.on('change', function () {
            var selectedCutomer = $(this).val();
            app.pullDataById(document.pullContractList, {
                customerId: selectedCutomer
            }).then(function (response) {
                console.log(response);
                app.populateSelect($contractSelect, response.data, 'CONTRACT_ID', 'CONTRACT_NAME', 'Select An Contract', '');
            }, function (error) {
                console.log(error);
            });
        });


        $('#viewBtn').on('click', function () {
            customer = $cutomerSelect.val();
            contract = $contractSelect.val();



            if (contract == null || contract == '') {
                app.errorMessage('Contract not Selected', 'Not Selected');
                return;
            }



            app.serverRequest(document.pullContractWiseEmployeeAssign, {
                customerId: customer,
                contractId: contract
            }).then(function (response) {
//                console.log(response.data);
                var locationList = response.data.locationList;
                var employeeList = response.data.empDetails;

                app.populateSelect($('#addLocation'), locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '');
                app.renderKendoGrid($table, employeeList);

            }, function (error) {
                console.log(error);
            });




        });



        $('#addModalBtn').on('click', function () {

            app.populateSelect($('#addDesignation'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'Select An Designation', '');
            app.populateSelect($('#addEmployee'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
            app.populateSelect($('#addDutyType'), document.dutyTypeList, 'DUTY_TYPE_ID', 'DUTY_TYPE_NAME', 'Select An Duty Type', '');

            $('#addModal').modal('show');



        });


        $('#addBtn').on('click', function () {
            var designation = $('#addDesignation').val();
            var employee = $('#addEmployee').val();
            var location = $('#addLocation').val();
            var dutyType = $('#addDutyType').val();
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var startTime = $('#addEmployeeStartTime').val();
            var endTime = $('#addEmployeeEndTime').val();

            if (designation == null || designation == '') {
                app.showMessage('Desingation is Required', 'info', 'Required')
                return;
            }

            if (employee == null || employee == '') {
                app.showMessage('Employee is Required', 'info', 'Required')
                return;
            }

            if (location == null || location == '') {
                app.showMessage('Location is Required', 'info', 'Required')
                return;
            }

            if (dutyType == null || dutyType == '') {
                app.showMessage('Duty type is Required', 'info', 'Required')
                return;
            }

            if (startDate == null || startDate == '') {
                app.showMessage('StartDate is Required', 'info', 'Required')
                return;
            }

            if (startTime == null || startTime == '') {
                app.showMessage('StartTime is Required', 'info', 'Required')
                return;
            }

            if (endTime == null || endTime == '') {
                app.showMessage('End Time is Required', 'info', 'Required')
                return;
            }

            app.serverRequest(document.addContractEmpAssign, {
                'customer': customer,
                'contract': contract,
                'designation': designation,
                'employee': employee,
                'location': location,
                'dutyType': dutyType,
                'startDate': startDate,
                'endDate': endDate,
                'startTime': startTime,
                'endTime': endTime
            }).then(function (response) {
                console.log(response);
                if (response.success == true) {
                    app.renderKendoGrid($table, response.data);
//                    $('#addLocation').val('');
//                    $('#startDate').val('');
//                    $('#endDate').val('');
//                    $('#addEmployeeStartTime').val('');
//                    $('#addEmployeeEndTime').val('');
                    $('#addModal').modal('hide');
                    app.showMessage('SucessfullyAdded', 'success', 'Added Sucessfully')

                }
            });



        });




    });
})(window.jQuery);