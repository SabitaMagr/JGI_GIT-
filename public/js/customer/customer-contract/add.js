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
    });
})(window.jQuery, window.app);