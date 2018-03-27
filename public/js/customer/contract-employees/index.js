(function ($) {
    'use strict';
    $(document).ready(function () {
//        $('select').select2();
        $("#submitBtn").hide();

        var locationList;
        var empAssignId;

        var $table = $('#table');


        var editBtn = `<a class="btn-edit" title="Edit"  style="height:17px;">
                    <i class="fa fa-edit"></i>
                </a>
                <a class="btn-delete" title="Delete"  style="height:17px;">
                    <i class="fa fa-trash-o"></i>
                </a>`;



        var $cutomerSelect = $('#CustomerSelect');
        var $contractSelect = $('#ContractSelect');
        var customer;
        var contract;

        $('#addEmployeeStartTime').combodate({
            minuteStep: 1,
        });
        $('#addEmployeeEndTime').combodate({
            minuteStep: 1,
        });

        app.startEndDatePickerWithNepali('nepaliStartDate', 'startDate', 'nepaliEndDate', 'endDate');
        app.startEndDatePickerWithNepali('editnepaliStartDate', 'editstartDate', 'editnepaliEndDate', 'editendDate', );

        //  console.log(document.customerList);

        app.populateSelect($('#CustomerSelect'), document.customerList, 'CUSTOMER_ID', 'CUSTOMER_ENAME', 'Select An Customer', '');


        var columns = [
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
                ]},
            {field: ["ID"], width: "90px", title: "Action", template: editBtn}
        ]


        app.initializeKendoGrid($table, columns);


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
                locationList = response.data.locationList;
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

        $('#editEmployeeStartTime').combodate({
            minuteStep: 1
        });

        $('#editEmployeeEndTime').combodate({
            minuteStep: 1
        });


        $table.on('click', '.btn-edit', function () {
            var row = $(this).closest("tr"),
                    grid = $table.data("kendoGrid"),
                    dataItem = grid.dataItem(row);

            console.log(dataItem);

            empAssignId = dataItem.ID;

            app.populateSelect($('#editLocation'), locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '', dataItem.LOCATION_ID);
            app.populateSelect($('#editDesignation'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'Select An Designation', '', dataItem.DESIGNATION_ID);
            app.populateSelect($('#editEmployee'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '', dataItem.EMPLOYEE_ID);
            app.populateSelect($('#editDutyType'), document.dutyTypeList, 'DUTY_TYPE_ID', 'DUTY_TYPE_NAME', 'Select An Duty Type', '', dataItem.DUTY_TYPE_ID);



            $('#editEmployeeStartTime').combodate('setValue', dataItem.START_TIME);
            $('#editEmployeeEndTime').combodate('setValue', dataItem.END_TIME);





            $('#editstartDate').datepicker('setDate', dataItem.START_DATE_AD);
            $('#editendDate').datepicker('setDate', dataItem.END_DATE_AD);

            $('#editModal').modal('show');



        });


        $('#updateBtn').on('click', function () {

            var designation = $('#editDesignation').val();
            var employee = $('#editEmployee').val();
            var location = $('#editLocation').val();
            var dutyType = $('#editDutyType').val();
            var startDate = $('#editstartDate').val();
            var endDate = $('#editendDate').val();
            var startTime = $('#editEmployeeStartTime').val();
            var endTime = $('#editEmployeeEndTime').val();





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

            app.serverRequest(document.updateLink, {
                'id': empAssignId,
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
                if (response.success == true) {
                    app.renderKendoGrid($table, response.data);
                    $('#editModal').modal('hide');
                    app.showMessage('SucessfullyUpdated', 'success', 'Edited Sucessfully')

                }
            });

        });


        $table.on('click', '.btn-delete', function () {
            var row = $(this).closest("tr"),
                    grid = $table.data("kendoGrid"),
                    dataItem = grid.dataItem(row);

            console.log(dataItem);
            var contractId = dataItem.CONTRACT_ID;
            var id = dataItem.ID;

            app.serverRequest(document.deleteLink, {
                'id': id,
                'contractId': contractId,

            }).then(function (response) {
                if (response.success == true) {
                    app.renderKendoGrid($table, response.data);
                    app.showMessage('SucessfullyDelted', 'success', 'Deleteled Sucessfully')
                }
            });

        });







    });
})(window.jQuery);