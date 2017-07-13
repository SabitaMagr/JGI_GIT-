(function ($, app) {
    'use strict';
    $(document).ready(function () {
        if (document.employeeId == document.selfEmployeeId) {
            app.lockField(true, ['birthdate', 'firstName', 'middleName', 'lastName', 'nameNepali', 'nepaliBirthDate', 'companyId', 'idCardNo', 'idThumbId', 'idLbrf', 'tab4', 'tab5', 'tab7', 'tab8']);
        }
    });
})(window.jQuery, window.app);