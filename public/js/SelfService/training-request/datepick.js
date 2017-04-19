
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'form-startDate', 'nepaliEndDate1', 'form-endDate');
        app.setLoadingOnSubmit("trainingRequest-form");
    }); 
})(window.jQuery, window.app);



