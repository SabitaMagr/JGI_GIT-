(function ($) {
    'use strict';
    $(document).ready(function () {

        $('select').select2();
        var $totalWorkingHr = $('.totalWorkingHr');
        var $employeeStartTime = $('.employeeStartTime');
        var $employeeEndTime = $('.employeeEndTime');

        app.addComboTimePicker($totalWorkingHr, $employeeStartTime, $employeeEndTime);







        app.populateSelect($('.employees'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');

//        app.addDatePicker($('.contractEmpStartDate'));
//        app.addDatePicker($('.contractEmpEndDate'));



        $('#addContractEmp').on('click', function () {

            var appendValues = "<tr>"
                    + "<td><select required='required' name='employee[]' class='employees'></select></td>"
                    + "<td><input name='totalWorkingHr[]' type='text' class='totalWorkingHr' data-format='h:mm' data-template='hh : mm' ></td>"
                    + "<td><input name='employeeStartTime[]' type='text' class='employeeStartTime' data-format='h:mm a' data-template='hh : mm A'></td>"
                    + "<td><input name='employeeEndTime[]' type='text' class='employeeEndTime' data-format='h:mm a' data-template='hh : mm A'></td>"
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




            app.populateSelect($('#tblContractEmp tbody').find('.employees:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
            $('#tblContractEmp tbody').find('.employees:last').select2();

            app.addComboTimePicker(
                    $('#tblContractEmp tbody').find('.totalWorkingHr:last'),
                    $('#tblContractEmp tbody').find('.employeeStartTime:last'),
                    $('#tblContractEmp tbody').find('.employeeEndTime:last')
                    );


        });


        function populateEmployees(employeeData) {
            $("#tblContractEmp tbody").find("tr:gt(0)").remove();

            $.each(employeeData, function (index, value) {

                var appendValues = "<tr>"
                        + "<td><select name='employee[]' class='employees'></select></td>"
//                        + "<td><input type='text' class='contractEmpStartDate' name='contractEmpStartDate[]'></td>"
//                        + "<td><input type='text' class='contractEmpEndDate' name='contractEmpEndDate[]'></td>"
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
//                    startDate: new Date(),
//                    endDate: new Date(),
                    setDate: new Date()
                });

                $('#tblContractEmp tbody').find('.contractEmpEndDate:last').datepicker({
                    format: 'dd-M-yyyy',
                    todayHighlight: true,
                    autoclose: true,
//                    startDate: new Date(),
//                    endDate: new Date(),
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


//        populateEmployees();



//        console.log(document.monthDetails);
        app.populateSelect($('#monthSelect'), document.monthDetails, 'MONTH_ID', 'MONTH_TITLE', 'Select Month', '');


        $('#monthSelect').on('change', function () {
            var selectedVal = $(this).val();
            app.pullDataById(document.pullEmployeeAssignBy, {monthId: selectedVal}).then(function (response) {

                if (response.data.length > 0) {
                    populateEmployees(response.data);
                }

            }, function (error) {
                console.log(error);
            });
        });


        var displayErrorMsg = function (object) {
            var selectedVal = object.val()
            var $parent = object.parent();
            if (selectedVal == "") {
                var $errorElement = $('</br><span class="errorMsg" aria-required="true">Field is Required</span>');
                if (!($parent.find('span.errorMsg').length > 0)) {
                    $parent.append($errorElement);
                }
                return 'error';
            } else {
                if ($parent.find('span.errorMsg').length > 0) {
                    $parent.find('span.errorMsg').remove();
                    $parent.find('br').remove();
                }
                return 'no error';
            }
        }


        $('#employeeAssign').submit(function () {

            var error = [];

            $('.totalWorkingHr').each(function (index) {
                console.log(this);
                var errorResult = displayErrorMsg($(this));
                if (errorResult == 'error') {
                    error.push('error');
                }
            });

            $('.employeeStartTime').each(function (index) {
                var errorResult = displayErrorMsg($(this));
                if (errorResult == 'error') {
                    error.push('error');
                }
            });

            $('.employeeEndTime').each(function (index) {
                var errorResult = displayErrorMsg($(this));
                if (errorResult == 'error') {
                    error.push('error');
                }
            });

            console.log(error);
            if (error.length > 0) {
                return false;
            } else {
                return true;
            }

        });












    });
})(window.jQuery);