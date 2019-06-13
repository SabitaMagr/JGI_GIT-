(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();
        let aggredCols = [];
        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $table = $('#table');
        var $salaryTypeId = $('#salaryTypeId');

        var columns = [
            {field: "DEPARTMENT_NAME", title: "Department", width: 200},
            {field: "JV_NAME", title: "JV Name", width: 200},
            {field: "JV_VALUE", title: "JV Value", width: 200},
            {field: "PAY_TYPE_FLAG", title: "Debit/Credit", width: 200}
        ];

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });
        
        app.populateSelect($salaryTypeId, document.salaryType, 'SALARY_TYPE_ID', 'SALARY_TYPE_NAME', 'All',-1,-1);

        var exportMap = {
            "DEPARTMENT_NAME": "Department",
            "JV_NAME": "JV Name",
            "JV_VALUE": "JV Value",
            "PAY_TYPE_FLAG": "PAY_TYPE_FLAG"
        };

        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Jv-report.xlsx');
        });
                   
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Jv-report.pdf');
        });

        function loadKendo(treeData) {
            $table.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "JV.xlsx",
                    filterable: true,
                    allPages:true
                },
                dataSource: {
                    data: treeData,
                    pageSize: 20
                },
                height: 550,
                scrollable: true,
                sortable: true,
                filterable: true,
                pageable: {
                    input: true,
                    numeric: false
                },
                columns: columns
            });
        }
        app.searchTable($table, ['DEPARTMENT_NAME', 'JV_NAME', 'JV_VALUE'], false);
        
        $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            q['salaryTypeId'] = $salaryTypeId.val();
            app.serverRequest('', q).then(function (response) {
                if (response.success) {
                    $table.empty();
                    loadKendo(response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });
    });
})(window.jQuery, window.app);


