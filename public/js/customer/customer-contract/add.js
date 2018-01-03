(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate', 'startDate', 'nepaliEndDate', 'endDate');
        var $form = $('#customerContract')
        var $inTime = $('#inTime');
        var $outTime = $('#outTime');
        var $workingHours = $('#workingHours');
        app.addComboTimePicker($inTime, $outTime, $workingHours);
        app.addDatePicker($('.contractDates'));

        $form.on('submit', function () {
            if ($inTime.val() == '') {
                app.showMessage('In Time is required.', 'error');
                $inTime.focus();
                return false;
            }
            if ($outTime.val() == '') {
                app.showMessage('Out Time is required.', 'error');
                $outTime.focus();
                return false;
            }
            if ($workingHours.val() == '') {
                app.showMessage('Working Hours is required.', 'error');
                $workingHours.focus();
                return false;
            }
        });

        $('#addContractdate').on('click', function () {
            var appendValues = "<tr>"
                    + "<td><input type='text' class='contractDates' name='contractDates[]'></td>"
                    + "<td>"
                    + "<div class='th-inner '>"
                    + "<label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                    + "<input class='chkBoxContractDates' type='checkbox'/>"
                    + "<span></span>"
                    + "</label>"
                    + "</div>"
                    + "</td>"
                    + "</tr>";

            $('#tblContractDates tbody').append(appendValues);
            $('#tblContractDates tbody').find('.contractDates:last').datepicker({
                format: 'dd-M-yyyy',
                todayHighlight: true,
                autoclose: true,
                setDate: new Date()
            });
        });


        $('#delContractdate').on('click', function () {
            $('#tblContractDates .chkBoxContractDates:checked').each(function () {
                $(this).parents("tr").remove()
            });
        });



        var checkCycleType = function () {
            var selectedVal = $('input[name=workingCycle]:checked').val();
            console.log(selectedVal);
            (selectedVal == 'W') ? $('#monthWise').show() : $('#monthWise').hide();
            (selectedVal == 'R') ? $('#dateWise').show() : $('#dateWise').hide();
        }

        $("input[name=workingCycle]").on("change", function () {
            checkCycleType();
        });

        checkCycleType();


    });
})(window.jQuery, window.app);