(function ($) {
    'use strict';
    $(document).ready(function () {

        var $customer = $('#customer');
        var $contract = $('#contract');
        var $location = $('#location');
        var $viewBtn = $('#viewBtn');
        var $tblDetails = $('#tblDetails');

        var locationEmployeeList;

        $('select').select2();
        app.datePickerWithNepali('engDate', 'nepaliDate');

        $('#engDate').datepicker("setDate", new Date((new Date()).valueOf() - 1000 * 3600 * 24));

        app.populateSelect($customer, document.customerList, 'CUSTOMER_ID', 'CUSTOMER_ENAME', 'Select An Customer', '');

        app.populateSelect($('.employee'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
        app.populateSelect($('.subEmployee'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');


        $customer.on('change', function () {
            var customerValue = $(this).val();
            app.serverRequest(document.pullContractDetails, {
                customerId: customerValue
            }).then(function (response) {
//                console.log(response.data);
                app.populateSelect($contract, response.data, 'CONTRACT_ID', 'CONTRACT_NAME', 'Select An Contract', '');
            }, function (error) {
                console.log(error);
            });

        });


        $contract.on('change', function () {
            var contractValue = $(this).val();
            app.serverRequest(document.pullLocationDetails, {
                contractId: contractValue
            }).then(function (response) {
                console.log(response.data);
                app.populateSelect($location, response.data, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '');
            }, function (error) {
                console.log(error);
            });

        });

        $viewBtn.on('click', function () {

            var customerVal = $customer.val();
            var contractVal = $contract.val();
            var locationVal = $location.val();
            var dateVal = $('#engDate').val();

            if (customerVal == '') {
                app.errorMessage('Customer not Selected', 'Not Selected');
                return;
            }
            if (contractVal == '') {
                app.errorMessage('Contract not Selected', 'Not Selected');
                return;
            }
            if (locationVal == '') {
                app.errorMessage('Location not Selected', 'Not Selected');
                return;
            }
            if (locationVal == '') {
                app.errorMessage('Location not Selected', 'Not Selected');
                return;
            }
            if (dateVal == '') {
                app.errorMessage('Date is not Selected', 'Not Selected');
                return;
            }

            app.serverRequest(document.pullEmployeeContractLocation, {
                contractId: contractVal,
                locationId: locationVal,
                attendanceDate: dateVal
            }).then(function (response) {
                console.log(response);
                if (response.success === true) {
                    locationEmployeeList = response.data;
                    $('#customerId').val(customerVal);
                    $('#contractId').val(contractVal);
                    $('#locationId').val(locationVal);
                    $('#attendanceDate').val(dateVal);
                    $tblDetails.find("tr:gt(0)").remove();
                }
            }, function (error) {
                console.log(error);
            });

        })









        $('#addDetails').on('click', function () {

            var appendValues = "<tr>"
                    + "<td>"
                    + "<input type='text' class='attDate' name='attDate[]'>"
                    + "</td>"
                    + "<td><select required='required' name='employee[]' class='employee'></select>"
                    + "<input type='hidden' class='empDesId' name='empDesId[]'>"
                    + "<input type='hidden' class='empShiftId' name='empShiftId[]'>"
                    + "</td>"
                    + "<td><span class='empDes'></span></td>>"
                    + "<td><span class='empShift'></span></td>>"
                    + "<td><select required='required' name='subEmployee[]' class='subEmployee'></select>"
                    + "<input type='hidden' class='subDesId' name='subDesId[]'>"
                    + "</td>"
                    + "<td><span class='subDes'></span></td>"
                    + "<td>"
                    + "<select required='required' name='postingType[]' class='postingType'>"
                    + "<option value='SU'>Substitute</option>"
                    + "<option value='OT'>Over Time</option>"
                    + "<option value='PT'>Part Time</option>"
                    + "</select>"
                    + "</td>"
                    + "<td>"
                    + "<div class='th-inner '>"
                    + "<label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                    + "<input class='chkBoxContractDetails' type='checkbox'/>"
                    + "<span></span>"
                    + "</label>"
                    + "</div>"
                    + "</td>"
                    + "</tr>";

            $('#tblDetails tbody').append(appendValues);

            app.populateSelect($('#tblDetails tbody').find('.employee:last'), locationEmployeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
            app.populateSelect($('#tblDetails tbody').find('.subEmployee:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');

            $('#tblDetails tbody').find('.employee:last').select2();
            $('#tblDetails tbody').find('.subEmployee:last').select2();
            $('#tblDetails tbody').find('.postingType:last').select2();
        });


        $('#delDetails').on('click', function () {
            $('#tblDetails .chkBoxContractDetails:checked').each(function () {
                $(this).parents("tr").remove()
            });
        });


        $('#tblDetails tbody').on('change', '.employee', function () {
            var selectedEmployeeId = $(this).val();

            var tempDesingationElement = $(this).parent().parent().children('td').children('span.empDes');
            var tempShiftElement = $(this).parent().parent().children('td').children('span.empShift');

            var tempDesingationIdElement = $(this).parent().children('input.empDesId');
            var tempShiftIdElement = $(this).parent().children('input.empShiftId');


            if (selectedEmployeeId > 0) {

                app.serverRequest(document.pullEmployeeDetails, {
                    employeeId: selectedEmployeeId,
                    contractId: $contract.val()

                }).then(function (response) {
                    var employeeDetails = response.data['employeeContractDetails'];
                    var tempDesignationName = employeeDetails.DESIGNATION_TITLE;
                    var tempShiftName = employeeDetails.SHIFT_ENAME;

                    tempDesingationIdElement.val(employeeDetails.DESIGNATION_ID);
                    tempShiftIdElement.val(employeeDetails.SHIFT_ID);

                    tempDesingationElement.text(tempDesignationName);
                    tempShiftElement.text(tempShiftName);
                }, function (error) {
                    console.log(error);
                });
            } else {
                tempDesingationElement.text('');
            }

        });

        $('#tblDetails tbody').on('change', '.subEmployee', function () {
            var selectedSubEmployeeId = $(this).val();

            var tempSubDesingationElement = $(this).parent().parent().children('td').children('span.subDes');

            var subDesingationIdElement = $(this).parent().children('input.subDesId');

            if (selectedSubEmployeeId > 0) {

                app.serverRequest(document.pullEmployeeDetails, {
                    employeeId: selectedSubEmployeeId
                }).then(function (response) {
                    var subEmployeeDetails = response.data['employeeContractDetails'];
                    if (subEmployeeDetails) {
                        console.log(subEmployeeDetails);
                        var tempSubDesignationName = subEmployeeDetails.DESIGNATION_TITLE;


                        subDesingationIdElement.val(subEmployeeDetails.DESIGNATION_ID);


                        tempSubDesingationElement.text(tempSubDesignationName);
                    }


                }, function (error) {
                    console.log(error);
                });
            } else {
                tempSubDesingationElement.text('');
            }


        });



    });
})(window.jQuery);
