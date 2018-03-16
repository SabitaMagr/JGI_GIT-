(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        app.populateSelect($('#employeeId'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '', document.employeeIdVal);
        app.populateSelect($('#subEmployeeId'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '', document.subEmployeeIdVal);

        $('#employeeId').attr("disabled", "disabled");



    });
})(window.jQuery);
