(function ($) {
    'use strict';
    $(document).ready(function () {
//        $('select').select2();
        $("#submitBtn").hide();

        var locationList;
        var empAssignId;

        var $assignTable = $('#assignTable');
        var $table = $('#table');

        $('#addModalBtn').hide();


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
        var contractStartDate;
        var contractEndDate;

        $('#addEmployeeStartTime').combodate({
            minuteStep: 1,
        });
        $('#addEmployeeEndTime').combodate({
            minuteStep: 1,
        });



        //  console.log(document.customerList);

        app.populateSelect($('#CustomerSelect'), document.customerList, 'CUSTOMER_ID', 'CUSTOMER_ENAME', 'Select An Customer', '');



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



        function showEmpAssignList() {

            app.serverRequest(document.pullContractDetails, {
                contractId: contract
            }).then(function (response) {
                locationList = response.data.locationList;
                contractStartDate = response.data.contractDetails[0]['START_DATE'];
                contractEndDate = response.data.contractDetails[0]['END_DATE'];
                app.populateSelect($('#addLocation'), locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '');
                $("#assignTable").empty();
                console.log('contractDetails', response.data.contractDetails);


                var headerAppendData = `
                <thead>
                    <tr>
                    <th style="width:150px;">Designation</th>
                    <th style="width:80px;">DutyType</th>
                    <th>Employee</th>
                    <th>Location</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Action</th>
                    </tr></thead>`;
                $assignTable.append(headerAppendData);

                $.each(response.data.contractDetails, function (index, value) {
//                    console.log('contractDetails',value);

                    app.serverRequest(document.pullDesignationWiseEmpAssign, {
                        contractId: contract,
                        designationId: value.DESIGNATION_ID,
                        dutyTypeId: value.DUTY_TYPE_ID


                    }).then(function (response) {
                        var empAssignData = response.data;

                        for (var n = 0; n < value.QUANTITY; n++) {

//                            console.log(empAssignData[n]);

                            var appendData = `<tr>
                        <td><input type='hidden' name='assignId[]' class='assignId' value=''>
                            <input class='designation' name='designation[]' type='hidden' value='` + value.DESIGNATION_ID + `'>` + value.DESIGNATION_TITLE + `</td>
                        <td><input class='dutyTypeId' name='dutyTypeId[]' type='hidden' value='` + value.DUTY_TYPE_ID + `'>` + value.DUTY_TYPE_NAME + `</td>
                        <td>
                        <select required='required' name='employees[]' class='employees'></select>
                        </td>
                        <td><select required='required' name='location[]' class='location'></select></td>
                        <td style="white-space:nowrap;"><input name='employeeStartTime[]' type='text' class='employeeStartTime' data-format='h:mm a' data-template='HH : mm'></td>
                        <td style="white-space:nowrap;"><input name='employeeEndTime[]' type='text' class='employeeEndTime' data-format='h:mm a' data-template='HH : mm'></td>
                        <td><input style='width: 88px;' name='employeeStartDate[]' type='text' class='employeeStartDate' ></td>
                        <td><input style='width: 88px;' name='employeeEndDate[]' type='text' class='employeeEndDate' ></td>
                        <td>
                        <input type='button' class='btn assignEditBtn  button-sm' value='Edit'>
                        <button class='btn assignCancelBtn button-sm'><i class="fa fa-close"></i></button>
                        <input type='button' class=' btn assignDeleteBtn button-sm' value='Delete'>
                        </td>
                    
                        </tr>`;
                            $assignTable.append(appendData);



                            if (empAssignData[n]) {

                                $('#assignTable tbody').find('.assignId:last').val(empAssignData[n].ID)
                                app.populateSelect($('#assignTable tbody').find('.employees:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '', empAssignData[n].EMPLOYEE_ID);
                                app.populateSelect($('#assignTable tbody').find('.location:last'), locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '', empAssignData[n].LOCATION_ID);

                                $('#assignTable tbody').find('.employeeStartTime:last').combodate({
                                    minuteStep: 1,
                                    value: empAssignData[n].START_TIME
                                });

                                $('#assignTable tbody').find('.employeeEndTime:last').combodate({
                                    minuteStep: 1,
                                    value: empAssignData[n].END_TIME
                                });

//                                
                                $('#assignTable tbody').find('.employeeStartDate:last').datepicker({
                                    format: 'dd-M-yyyy',
                                    autoclose: true,
                                });

                                $('#assignTable tbody').find('.employeeEndDate:last').datepicker({
                                    format: 'dd-M-yyyy',
                                    autoclose: true
                                });

                                $('#assignTable tbody').find('.employeeStartDate:last').datepicker("update", new Date(empAssignData[n].START_DATE));
                                $('#assignTable tbody').find('.employeeEndDate:last').datepicker("update", new Date(empAssignData[n].END_DATE));
                            } else {
                                app.populateSelect($('#assignTable tbody').find('.employees:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '', );
                                app.populateSelect($('#assignTable tbody').find('.location:last'), locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '', );

                                $('#assignTable tbody').find('.employeeStartTime:last').combodate({
                                    minuteStep: 1,
                                });

                                $('#assignTable tbody').find('.employeeEndTime:last').combodate({
                                    minuteStep: 1,
                                });


                                $('#assignTable tbody').find('.employeeStartDate:last').datepicker({
                                    format: 'dd-M-yyyy',
                                    todayHighlight: true,
                                    autoclose: true,
                                    startDate: getDateFormat(contractStartDate),
                                    endDate: getDateFormat(contractEndDate)
                                });

                                $('#assignTable tbody').find('.employeeEndDate:last').datepicker({
                                    format: 'dd-M-yyyy',
                                    todayHighlight: true,
                                    autoclose: true,
                                    startDate: getDateFormat(contractStartDate),
                                    endDate: getDateFormat(contractEndDate)
                                });

                            }



                            $('#assignTable tbody').find('.employees:last').select2();
                            $('#assignTable tbody').find('.location:last').select2();
                        }
                        $('.assignCancelBtn').hide();
                        $('.employees').prop("disabled", true);
                        $('.location').prop("disabled", true);
                        $('.employeeStartDate').prop("disabled", true);
                        $('.employeeEndDate').prop("disabled", true);
                        $('#assignTable tbody').find('.hour').prop("disabled", true);
                        $('#assignTable tbody').find('.minute').prop("disabled", true);
                        $('#assignTable tbody').find('.ampm').prop("disabled", true);

                        $('#assignTable tbody').find('.combodate').css("opacity", "0.6");


                    });






                });

            }, function (error) {
                console.log(error);
            });




        }


        $('#viewBtn').on('click', function () {
            customer = $cutomerSelect.val();
            contract = $contractSelect.val();
            if (contract == null || contract == '') {
                app.errorMessage('Contract not Selected', 'Not Selected');
                return;
            }
            showEmpAssignList();
            $('#addModalBtn').show();
        });


        function getDateFormat(date) {
            var m_names = new Array("Jan", "Feb", "Mar",
                    "Apr", "May", "Jun", "Jul", "Aug", "Sep",
                    "Oct", "Nov", "Dec");


            var d = new Date(date);
            var curr_date = d.getDate();
            var curr_month = d.getMonth();
            var curr_year = d.getFullYear();
            return curr_date + "-" + m_names[curr_month]
                    + "-" + curr_year;

        }



        app.startEndDatePickerWithNepali('nepaliStartDate', 'startDate', 'nepaliEndDate', 'endDate');
        $('#addModalBtn').on('click', function () {
            $('#addRate').val('');
            $('#addRate').prop('readonly', false);


            if (contractStartDate && contractEndDate) {
                $('#startDate').datepicker("update", getDateFormat(contractStartDate));
                $('#nepaliStartDate').val(nepaliDatePickerExt.fromEnglishToNepali(getDateFormat(contractStartDate)));
                $('#endDate').datepicker("update", getDateFormat(contractEndDate));
                $('#nepaliEndDate').val(nepaliDatePickerExt.fromEnglishToNepali(getDateFormat(contractEndDate)));

                $('#startDate').datepicker("setStartDate", getDateFormat(contractStartDate));
                $('#startDate').datepicker("setEndDate", getDateFormat(contractEndDate));
                $('#endDate').datepicker("setStartDate", getDateFormat(contractStartDate));
                $('#endDate').datepicker("setEndDate", getDateFormat(contractEndDate));
            }






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
            var rate = $('#addRate').val();
            var monthDays = $('#addMonthDays').val();

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

            if (rate == null || rate == '') {
                app.showMessage('Rate is Required', 'info', 'Required')
                return;
            }

            if (monthDays == null || monthDays == '') {
                app.showMessage('Month Days is Required', 'info', 'Required')
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
                'endTime': endTime,
                'rate': rate,
                'monthDays': monthDays
            }).then(function (response) {
                console.log(response);
                if (response.success == true) {
                    $('#addModal').modal('hide');
                    app.showMessage('SucessfullyAdded', 'success', 'Added Sucessfully')
                    showEmpAssignList();

                }
            });
        });




        $('#assignTable').on('click', '.assignDeleteBtn', function () {
            console.log('delete');
            $(this).confirmation({
                onConfirm: function () {
                    console.log('confirm');
                    var selectedtr = $(this).parent().parent();
                    var selectedAssignId = $(selectedtr.find('.assignId'));
                    var selectedDesignation = $(selectedtr.find('.designation'));
                    var selectedDutyTypeId = $(selectedtr.find('.dutyTypeId'));
                    console.log(selectedAssignId.val());

                    if (selectedAssignId.val() > 0) {
                        app.serverRequest(document.deleteLink, {
                            id: selectedAssignId.val(),
                            contract: contract,
                            designation: selectedDesignation.val(),
                            dutyType: selectedDutyTypeId.val()
                        }).then(function (response) {
                            console.log(response);
                            if (response.success = true) {
                                selectedtr.remove();
                            }
                        });
                    }
                }
                ,
                onCancel: function () {
                    console.log('cancel');
                }, });
//
//            



        });






        $('#assignTable').on('click', '.assignEditBtn', function () {
            var btnValue = $(this).val();

            console.log(btnValue);

            var selectedtr = $(this).parent().parent();
            var selectedEmployee = $(selectedtr.find('.employees'));
            var selectedLocation = $(selectedtr.find('.location'));
            var selectedStartDate = $(selectedtr.find('.employeeStartDate'));
            var selectedEndDate = $(selectedtr.find('.employeeEndDate'));
            var selectedStartTime = $(selectedtr.find('.employeeStartTime'));
            var selectedEndTime = $(selectedtr.find('.employeeEndTime'));
            var selectedHour = $(selectedtr.find('.hour'));
            var selectedMinute = $(selectedtr.find('.minute'));
            var selectedampm = $(selectedtr.find('.ampm'));
            var selectedAssignId = $(selectedtr.find('.assignId'));
            var selectedDutyTypeId = $(selectedtr.find('.dutyTypeId'));
            var selectedDesignation = $(selectedtr.find('.designation'));
            var selectedComboDate = $(selectedtr.find('.combodate'));
            var selectedDeleteBtn = $(selectedtr.find('.assignDeleteBtn'));
            var selectedCancelBtn = $(selectedtr.find('.assignCancelBtn'));

            if (btnValue == 'Edit') {
                $(this).val('Update')
                selectedDeleteBtn.hide();
                selectedCancelBtn.show();
                selectedtr.css("background-color", "rgb(222, 225, 228)");
                selectedEmployee.prop("disabled", false);
                selectedLocation.prop("disabled", false);
                selectedStartDate.prop("disabled", false);
                selectedEndDate.prop("disabled", false);
                selectedHour.prop("disabled", false);
                selectedMinute.prop("disabled", false);
                selectedampm.prop("disabled", false);
                selectedComboDate.css("opacity", "1");

                if (selectedStartDate.val() == '' && selectedEndDate.val() == '') {
                    selectedStartDate.datepicker("update", new Date(contractStartDate));
                    selectedEndDate.datepicker("update", new Date(contractEndDate));
                }

            } else if (btnValue == 'Update') {

                var employeeValue = selectedEmployee.val();
                var locationValue = selectedLocation.val();
                var startDateValue = selectedStartDate.val();
                var startEndValue = selectedEndDate.val();
                var startTimeValue = selectedStartTime.val();
                var endTimeValue = selectedEndTime.val();
                var assignIdValue = selectedAssignId.val();
                var dutyTypeIdValue = selectedDutyTypeId.val();
                var designationValue = selectedDesignation.val();

                var requiredList = [
                    selectedEmployee,
                    selectedLocation,
                    selectedStartDate,
                    selectedEndDate,
                    selectedStartTime,
                    selectedEndTime
                ];
                var error = [];
                $.each(requiredList, function (index, value) {
                    var elementVal = value.val();
                    value.next(".red").remove();
                    if (elementVal.length == 0) {
                        error.push('error');
                        value.after('<div class="red" style="color:red;">Required</div>');
                    }
                });
                if (error.length == 0) {
                    app.serverRequest(document.updateLink, {
                        id: assignIdValue,
                        contractId: contract,
                        customerId: customer,
                        locationId: locationValue,
                        employeeId: employeeValue,
                        designationId: designationValue,
                        dutyTypeId: dutyTypeIdValue,
                        startTime: startTimeValue,
                        endTime: endTimeValue,
                        startDate: startDateValue,
                        endDate: startEndValue

                    }).then(function (response) {
                        console.log(response);

                        if (response.success == true) {
                            var responseData = response.data;
                            var operation = responseData.operation;

                            if (operation == 'add') {
                                selectedAssignId.val(responseData.id);
                            }
                        }
                    });

                    selectedDeleteBtn.show();
                    selectedCancelBtn.hide();
                    $(this).val('Edit')
                    selectedtr.css("background-color", "");
                    selectedEmployee.prop("disabled", true);
                    selectedLocation.prop("disabled", true);
                    selectedStartDate.prop("disabled", true);
                    selectedEndDate.prop("disabled", true);
                    selectedHour.prop("disabled", true);
                    selectedMinute.prop("disabled", true);
                    selectedampm.prop("disabled", true);
                    selectedComboDate.css("opacity", "0.6");
                }
            }
        });


        $('#addDesignation').on('change', function () {
            getContractDetailRate();
        });

        $('#addDutyType').on('change', function () {
            getContractDetailRate();
        });


        function getContractDetailRate() {
            var designation = $('#addDesignation').val();
            var dutyType = $('#addDutyType').val();

//            console.log(designation);
//            console.log(dutyType);
//            console.log(typeof designation);
//            console.log(typeof dutyType);

            if (designation != '' && dutyType != '') {
                app.serverRequest(document.pullCdContractDesignationDutyType, {
                    contract: contract,
                    designation: designation,
                    dutyType: dutyType
                }).then(function (response) {
                    if (response.success == true) {
                        console.log(response);
                        if (response.data != false) {
                            $('#addRate').val(response.data.RATE);
                            $('#addRate').prop('readonly', true);
                            $('#addMonthDays').val(response.data.DAYS_IN_MONTH);
                            $('#addMonthDays').prop('readonly', true);

                        } else {
                            $('#addRate').val('');
                            $('#addRate').prop('readonly', false);
                            $('#addMonthDays').val('');
                            $('#addMonthDays').prop('readonly', false);
                        }

                    }
                });

            } else {
                console.log('no vals');
            }


        }


        $('#assignTable').on('click', '.assignCancelBtn', function () {

            var selectedbtn = $(this);
            var selectedtr = $(this).parent().parent();
            var selectedEmployee = $(selectedtr.find('.employees'));
            var selectedLocation = $(selectedtr.find('.location'));
            var selectedStartDate = $(selectedtr.find('.employeeStartDate'));
            var selectedEndDate = $(selectedtr.find('.employeeEndDate'));
            var selectedStartTime = $(selectedtr.find('.employeeStartTime'));
            var selectedEndTime = $(selectedtr.find('.employeeEndTime'));
            var selectedHour = $(selectedtr.find('.hour'));
            var selectedMinute = $(selectedtr.find('.minute'));
            var selectedampm = $(selectedtr.find('.ampm'));
            var selectedAssignId = $(selectedtr.find('.assignId'));
            var selectedComboDate = $(selectedtr.find('.combodate'));
            var selectedDeleteBtn = $(selectedtr.find('.assignDeleteBtn'));
            var selectedEditBtn = $(selectedtr.find('.assignEditBtn'));

            var assignIdValue = selectedAssignId.val();
            if (assignIdValue > 0) {
                app.serverRequest(document.pullEmployeeAssignDataById, {
                    id: assignIdValue
                }).then(function (response) {
                    var responseData = response.data;
                    console.log(response);
                    if (responseData) {
                        selectedEmployee.val(responseData.EMPLOYEE_ID).change();
                        selectedLocation.val(responseData.LOCATION_ID).change();

                        selectedStartDate.datepicker("update", getDateFormat(responseData.START_DATE));
                        selectedEndDate.datepicker("update", getDateFormat(responseData.END_DATE));
                        selectedStartTime.combodate('setValue', responseData.START_TIME);
                        selectedEndTime.combodate('setValue', responseData.END_TIME);
                    }
                });
            } else {
                selectedEmployee.val('').change();
                selectedLocation.val('').change();

                selectedStartDate.datepicker("update", '');
                selectedEndDate.datepicker("update", '');
                selectedHour.val('').change;
                selectedMinute.val('').change;

            }

            selectedbtn.hide();
            selectedDeleteBtn.show();
            selectedEditBtn.val('Edit');
            selectedtr.css("background-color", "");
            selectedEmployee.prop("disabled", true);
            selectedLocation.prop("disabled", true);
            selectedStartDate.prop("disabled", true);
            selectedEndDate.prop("disabled", true);
            selectedHour.prop("disabled", true);
            selectedMinute.prop("disabled", true);
            selectedampm.prop("disabled", true);
            selectedComboDate.css("opacity", "0.6");
//


        });






    });
})(window.jQuery);