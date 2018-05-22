(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var exportMap = null;
        var columns = null;

        var onLoad = function (columnList, dataList) {
            exportMap = {"FULL_NAME": "Employee"};
            columns = [
                {field: "FULL_NAME", title: "Employee", width: 150}
            ];
            $.each(columnList, function (key, value) {
                columns.push({field: value['MONTH_DAY'], title: value['MONTH_DAY'], width: 50});
                exportMap[value['MONTH_DAY']] = value['MONTH_DAY'];
            });
            app.initializeKendoGrid($table, columns);
            app.renderKendoGrid($table, dataList);
        };

        app.searchTable($table, ['COMPANY_NAME']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Penalty List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Penalty List');
        });
        var months = null;
        var $year = $('#fiscalYear');
        var $month = $('#fiscalMonth');
        var $noOfDeductionDays = $('#noOfDeductionDays');
        $noOfDeductionDays.val(document.noOfDeductionDays);
        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
            months = monthList;
        });

        var monthChange = function ($this) {
            var value = $this.val();
            if (value == null) {
                return;
            }
            var selectedMonthList = months.filter(function (item) {
                return item['MONTH_ID'] === value;
            });
            if (selectedMonthList.length <= 0) {
                return;
            }
            app.serverRequest("", {monthId: selectedMonthList[0]['MONTH_ID']}).then(function (response) {
                console.log(response.data);
                onLoad(response.data.columnList, response.data.gridData)
            }, function (error) {

            });
        };
        $month.on('change', function () {
            monthChange($(this));
        });

    });
})(window.jQuery, window.app);
