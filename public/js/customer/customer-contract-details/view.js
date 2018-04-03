(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        console.log(document.dutyTypeList);


        var populateWeekList = function (selector) {
            var weekList = {
                'SUN': 'SUN',
                'MON': 'MON',
                'TUE': 'TUE',
                'WED': 'WED',
                'THU': 'THU',
                'FRI': 'FRI',
                'SAT': 'SAT',
            };
            $.each(weekList, function (key, value) {
                selector.append($("<option />").val(key).text(value));
            });
        }




        console.log(document.contractDetail.length);

        if (document.contractDetail.length > 0) {
            $("#tblContractDetails tbody").find("tr:gt(0)").remove();

            $.each(document.contractDetail, function (index, value) {
                console.log(value);

                var appendValues = "<tr>"
                        + "<td><select required='required' name='designation[]' class='designation'></select></td>"
                        + "<td><input  required='required'  name='quantity[]' value='" + value.QUANTITY + "' type='number' class='quantity'></td>"
                        + "<td><input  required='required'  name='rate[]' value='" + value.RATE + "' type='number' class='rate' step='0.01'></td>"
                        + "<td><select required='required' name='dutyType[]' class='dutyType'></td>"
                        + "<td><input  required='required'  name='daysInMonth[]' value='" + value.DAYS_IN_MONTH + "' type='number' class='daysInMonth'></td>"
                        + "<td>"
                        + "<div class='th-inner '>"
                        + "<label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                        + "<input class='chkBoxContractDetails' type='checkbox'/>"
                        + "<span></span>"
                        + "</label>"
                        + "</div>"
                        + "</td>"
                        + "</tr>";

                $('#tblContractDetails tbody').append(appendValues);

                app.populateSelect($('#tblContractDetails tbody').find('.designation:last'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'Select An Designation', '', value.DESIGNATION_ID);
                app.populateSelect($('#tblContractDetails tbody').find('.dutyType:last'), document.dutyTypeList, 'DUTY_TYPE_ID', 'DUTY_TYPE_NAME', 'Select An DutyType', '', value.DUTY_TYPE_ID);
                $('#tblContractDetails tbody').find('.designation:last').select2();
                $('#tblContractDetails tbody').find('.dutyType:last').select2();


            });

        } else {
            app.populateSelect($('.designation'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'Select An Designation', '');
            app.populateSelect($('.dutyType'), document.dutyTypeList, 'DUTY_TYPE_ID', 'DUTY_TYPE_NAME', 'Select An DutyType', '');
        }


        $('#addContractDetails').on('click', function () {

            var appendValues = "<tr>"
                    + "<td><select required='required' name='designation[]' class='designation'></select></td>"
                    + "<td><input  required='required'  name='quantity[]' type='number' class='quantity'></td>"
                    + "<td><input  required='required'  name='rate[]' type='number' class='rate' step='0.01'></td>"
                    + "<td><select required='required' name='dutyType[]' class='dutyType'></td>"
                    + "<td><input  required='required'  name='daysInMonth[]' type='number' class='daysInMonth'></td>"
                    + "<td>"
                    + "<div class='th-inner '>"
                    + "<label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                    + "<input class='chkBoxContractDetails' type='checkbox'/>"
                    + "<span></span>"
                    + "</label>"
                    + "</div>"
                    + "</td>"
                    + "</tr>";

            $('#tblContractDetails tbody').append(appendValues);

            app.populateSelect($('#tblContractDetails tbody').find('.designation:last'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'Select An Designation', '');
            app.populateSelect($('#tblContractDetails tbody').find('.dutyType:last'), document.dutyTypeList, 'DUTY_TYPE_ID', 'DUTY_TYPE_NAME', 'Select An Shift', '');
            $('#tblContractDetails tbody').find('.designation:last').select2();
            $('#tblContractDetails tbody').find('.dutyType:last').select2();
        });

        $('#delContractDetails').on('click', function () {
            $('#tblContractDetails .chkBoxContractDetails:checked').each(function () {
                $(this).parents("tr").remove()
            });
        });




    });
})(window.jQuery);