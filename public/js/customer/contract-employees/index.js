(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#submitBtn").hide();

        $('select').select2();


        var $cutomerSelect = $('#CustomerSelect');
        var $contractSelect = $('#ContractSelect');
        var $assignTable = $('#assignTable');

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


        $('#viewBtn').on('click', function () {
            var selectedContractId = $contractSelect.val();
            var selectedCustomerId = $cutomerSelect.val();
            $('#customerId').val(selectedCustomerId);
            $('#contractId').val(selectedContractId);

            app.serverRequest(document.pullContractDetails, {
                contractId: selectedContractId
            }).then(function (response) {
                var locationList = response.data.locationList;
                $("#assignTable").empty();
                console.log(response.data.contractDetails.length);

                if (response.data.contractDetails.length > 0) {
                    $("#submitBtn").show();
                } else {
                    $("#submitBtn").hide();
                }

                var headerAppendData = `
                    <tr>
                    <td>Employee</td>
                    <td>Location</td>
                    <td>Start Time</td>
                    <td>End Time</td>
                    </tr>`;
                $assignTable.append(headerAppendData);

                $.each(response.data.contractDetails, function (index, value) {
                    console.log(value);

                    app.serverRequest(document.pullDesignationWiseEmpAssign, {
                        contractId: selectedContractId,
                        designationId: value.DESIGNATION_ID


                    }).then(function (response) {
                        console.log(response.data.length);
                        var empAssignData = response.data;

                        $assignTable.append(`<tr>
                    <td><b>` + value.DESIGNATION_TITLE + ` NO:` + value.QUANTITY + `</b></td>
                    </tr>`);

//                    if(response.data.length>0){
//                        
//                    }else{

                        for (var n = 0; n < value.QUANTITY; n++) {

//                            console.log(empAssignData[n]);

                            var appendData = `<tr>
                        <td>
                        <input name='designation[]' type='hidden' value='` + value.DESIGNATION_ID + `'>
                        <select required='required' name='employee[]' class='employees'></select>
                        </td>
                        <td><select required='required' name='location[]' class='location'></select></td>
                        <td><input name='employeeStartTime[]' type='text' class='employeeStartTime' data-format='h:mm a' data-template='hh : mm A'></td>
                        <td><input name='employeeEndTime[]' type='text' class='employeeEndTime' data-format='h:mm a' data-template='hh : mm A'></td>
                    
                        </tr>`;
                            $assignTable.append(appendData);



                            if (empAssignData[n]) {
                                console.log(empAssignData[n]);
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

                            } else {
                                app.populateSelect($('#assignTable tbody').find('.employees:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '', );
                                app.populateSelect($('#assignTable tbody').find('.location:last'), locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '', );

                                app.addComboTimePicker(
                                        $('#assignTable tbody').find('.employeeStartTime:last'),
                                        $('#assignTable tbody').find('.employeeEndTime:last')
                                        );
                            }

                            $('#assignTable tbody').find('.employees:last').select2();
                            $('#assignTable tbody').find('.location:last').select2();
                        }

//                    }


                    });






                });

            }, function (error) {
                console.log(error);
            });

        });




    });
})(window.jQuery);