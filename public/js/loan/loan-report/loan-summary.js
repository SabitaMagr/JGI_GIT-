(function ($, app) {

    'use strict';
    $(document).ready(function () {
    
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var monthShortNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        var d = new Date();
        d.getMonth() <= 6 ? $("#fromDate").val("17-Jul-" + (d.getFullYear()-1)) : $("#fromDate").val(("0" + d.getDate()).slice(-2) + "-Jul-" + d.getFullYear()) ;
        $("#nepaliFromDate").val(window.nepaliDatePickerExt.fromEnglishToNepali($("#fromDate").val()));
        $("#toDate").val(("0" + d.getDate()).slice(-2) + "-" + monthShortNames[(d.getMonth())] + "-" +d.getFullYear());
        $("#nepaliToDate").val(window.nepaliDatePickerExt.fromEnglishToNepali($("#toDate").val()));
        var $table = $("#summaryTable");
        var $search = $("#search");

        var columns = [
            {field: "EMPLOYEE", title: "Employee", width: 150},
            {field: "OPENING_BALANCE", title: "Opening", width: 120},
            {field: "DR_SALARY", title: "Dr. Salary", width: 120, format: "{0:0.##}"},
            {field: "DR_INTEREST", title: "Dr. Interest", width: 120, format: "{0:0.##}"},
            {field: "CR_SALARY", title: "Cr. Salary", width: 120, format: "{0:0.##}"},
            {field: "CR_INTEREST", title: "Cr. Interest", width: 120, format: "{0:0.##}"},
            {field: "BALANCE", title: "Balance", width: 120, format: "{0:0.##}"}
        ];

        app.initializeKendoGrid($table, columns);
        app.searchTable($table, ['EMPLOYEE']);

        $search.on('click', function () {
            var employee = $("#employeeId").val();
            var fromDate = $("#fromDate").val();
            var toDate = $("#toDate").val();
            var loanType = $("#account").val();
            var employeeOptions = $("#employeeId option");

            if(employee == null || employee.length == 0){
                employee = $.map(employeeOptions ,function(option) {
                    return option.value;
                });
            }

            if(fromDate == "" || toDate == ""){
                alert("From date and To Date are required");
                return false;
            }

            var data = {
                emp_id : employee,
                fromDate : fromDate,
                toDate : toDate,
                loanId : loanType
            };
            
            App.blockUI({target: "#hris-page-content"});
            window.app.pullDataById(document.pullLoanVoucherSummaryLink, data).then(function (success) {
                App.unblockUI("#hris-page-content");
                var data = success.data;
                app.renderKendoGrid($table, data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });
    });

})(window.jQuery, window.app);