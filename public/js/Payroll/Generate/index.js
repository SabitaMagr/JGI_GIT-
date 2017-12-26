(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);

        var data = document.data;
        var salarySheetList = data['salarySheetList'];
        var monthList = data['monthList'];
        var generateLink = data['links']['generateLink'];
        var getSalarySheetListLink = data['links']['getSalarySheetListLink'];
//        
        var selectedMonth = {};
//
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $table = $('#table');
        var $fromDate = $('#fromDate');
        var $nepaliFromDate = $('#nepaliFromDate');
        var $toDate = $('#toDate');
        var $nepaliToDate = $('#nepaliToDate');
        var $viewBtn = $('#viewBtn');
        var $generateBtn = $('#generateBtn');

        $fiscalYear.select2();
        $month.select2();

        $viewBtn.hide();
        $generateBtn.hide();
        app.populateSelect($fiscalYear, data.fiscalYearList, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");
        app.populateSelect($month, [], "MONTH_ID", "MONTH_EDESC", "Select Month");

        $fiscalYear.on('change', function () {
            var value = $(this).val();
            var filteredMonths = [];
            if (value != -1) {
                var filteredMonths = data.monthList.filter(function (item) {
                    return item['FISCAL_YEAR_ID'] == value;
                });
            }
            app.populateSelect($month, filteredMonths, "MONTH_ID", "MONTH_EDESC", "Select Month");
        });
        var monthChangeAction = function (value) {
            for (var i in monthList) {
                if (monthList[i]['MONTH_ID'] == value) {
                    selectedMonth = monthList[i];
                    break;
                }
            }
            var genFlag = false;
            for (var i in salarySheetList) {
                if (salarySheetList[i]['MONTH_ID'] == value) {
                    $fromDate.val(salarySheetList[i]['START_DATE']);
                    $nepaliFromDate.val(nepaliDatePickerExt.fromEnglishToNepali(salarySheetList[i]['START_DATE']));
                    $toDate.val(salarySheetList[i]['END_DATE']);
                    $nepaliToDate.val(nepaliDatePickerExt.fromEnglishToNepali(salarySheetList[i]['END_DATE']));
                    $viewBtn.attr('sheet-no', salarySheetList[i]['SHEET_NO']);
                    $viewBtn.show();
                    $generateBtn.hide();
                    genFlag = true;
                    break;
                }
            }
            if (!genFlag) {
                $viewBtn.attr('sheet-no', '');
                $viewBtn.hide();
                $generateBtn.show();

                $fromDate.val(selectedMonth['FROM_DATE']);
                $nepaliFromDate.val(nepaliDatePickerExt.fromEnglishToNepali(selectedMonth['FROM_DATE']));
                $toDate.val(selectedMonth['TO_DATE']);
                $nepaliToDate.val(nepaliDatePickerExt.fromEnglishToNepali(selectedMonth['TO_DATE']));
            }
        };
        $month.on('change', function () {
            var value = $(this).val();
            monthChangeAction(value);
        });


        var exportMap = {"EMPLOYEE_NAME": "Employee"};
        var employeeNameColumn = {field: "EMPLOYEE_NAME", title: "Employee", width: 150};
        if (data.ruleList.length > 0) {
            employeeNameColumn.locked = true;
        }
        var columns = [
            employeeNameColumn
        ];

        $.each(data.ruleList, function (key, value) {
            var signFn = function ($type) {
                var sign = "";
                switch ($type) {
                    case "A":
                        sign = "+";
                        break;
                    case "D":
                        sign = "-";
                        break;
                    case "V":
                        sign = ".";
                        break;
                }
                return sign;
            };
            columns.push({field: "P_" + value['PAY_ID'], title: value['PAY_EDESC'] + "(" + signFn(value['PAY_TYPE_FLAG']) + ")", width: 150});
            exportMap["P_" + value['PAY_ID']] = value['PAY_EDESC'] + "(" + signFn(value['PAY_TYPE_FLAG']) + ")";
        });
        app.initializeKendoGrid($table, columns);

        $viewBtn.on('click', function () {
            app.serverRequest(data['links']['viewLink'], {sheetNo: $(this).attr('sheet-no')}).then(function (response) {
                app.renderKendoGrid($table, response.data);
            });
        });
        $generateBtn.on('click', function () {
            payrollGeneration();
        });


        var payrollGeneration = function () {
            var stage = 1;
            var monthId = selectedMonth['MONTH_ID'];
            var year = selectedMonth['YEAR'];
            var monthNo = selectedMonth['MONTH_NO'];
            var fromDate = selectedMonth['FROM_DATE'];
            var toDate = selectedMonth['TO_DATE'];

            var stage1 = function () {
                app.pullDataById(data['links']['generateLink'], {
                    stage: stage,
                    monthId: monthId,
                    year: year,
                    monthNo: monthNo,
                    fromDate: fromDate,
                    toDate: toDate
                }).then(function (response) {
                    stage2(response.data);
                }, function (error) {

                });
            };
            stage1();
            var sheetNo = null;
            var employeeList = null;

            var stage2 = function (data) {
                sheetNo = data['sheetNo'];
                employeeList = data['employeeList'];
                var dataList = [];
                for (var i in employeeList) {
                    dataList.push({
                        stage: 2,
                        sheetNo: sheetNo,
                        monthId: monthId,
                        year: year,
                        monthNo: monthNo,
                        fromDate: fromDate,
                        toDate: toDate,
                        employeeId: employeeList[i]['EMPLOYEE_ID']
                    });
                }
                (function (dataList) {
                    var counter = 0;
                    var length = dataList.length;
                    var recursionFn = function (data) {
                        app.pullDataById(generateLink, data).then(function (response) {
                            NProgress.set((counter + 1) / length);
                            counter++;
                            if (!response.success) {
                                stage2Error(data, response.error);
                            }
                            if (counter >= length) {
                                stage3();
                                return;
                            }
                            recursionFn(dataList[counter]);
                        }, function (error) {
                            stage2Error(data, error);
                        });
                    };
                    NProgress.start();
                    recursionFn(dataList[counter]);
                })(dataList);
            };
            var stage2Error = function (data, error) {
                app.showMessage(error, 'error');
            };
            var stage3 = function () {
                app.serverRequest(getSalarySheetListLink, {}).then(function (response) {
                    salarySheetList = response.data;
                    monthChangeAction($month.val());
                }, function (error) {

                });
            };

        };



        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Salary Sheet');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Salary Sheet');
        });

    });
})(window.jQuery, window.app);


