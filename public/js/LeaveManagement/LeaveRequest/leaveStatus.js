(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
        window.app.floatingProfile.setDataFromRemote(document.employeeId);
    });
})(window.jQuery, window.app);