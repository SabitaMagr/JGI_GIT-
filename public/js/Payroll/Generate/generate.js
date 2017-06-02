(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var displayKendoFirstTime = true;
        var fiscalYears = document.fiscalYears;
        var rules = document.rules;
        var months = [];
        var payRollGeneratedMonths = [];
        var employeeList = [];
        var employeeRuleValues = {};


        var datas = [];
        var headers = (function (cols) {
            var headers = [];
            headers.push({field: "employeeName", title: "Employee Name"});
            for (var i in cols) {
                headers.push({field: "h" + i, title: cols[i]});
            }
            headers.push({field: "calculatedValue", title: "Calculated Value"});
            return headers;
        })(rules);

        var $generateBtn = $('#generateBtn');
        var $month = $('#monthId');
        var $year = $('#fiscalYearId');
        var $salarySheetTable = $('#salarySheetTable');

        /*
         * 
         */
        var pullMonthlySheet = function (reqParams) {
            app.pullDataById(document.restful.generateMonthlySheet, reqParams).then(function (success) {
                employeeRuleValues = success.data;
                initializeDatas(employeeRuleValues);
                if (displayKendoFirstTime) {
                    initializekendoGrid(headers);
                    displayKendoFirstTime = false;
                }
                updateKendoGridData($salarySheetTable, datas);

                if (!reqParams.regenerateFlag) {
                    fetchPayRollGeneratedMonths();
                }
            }, function (failure) {
                console.log(failure);
            });

        };

        var fetchPayRollGeneratedMonths = function () {
            app.pullDataById(document.restfulUrl, {
                action: 'pullPayRollGeneratedMonths',
                data: {
                }
            }).then(function (success) {
                payRollGeneratedMonths = success.data;
            }, function (failure) {
                console.log("pullPayRollGeneratedMonths fail", failure);
            });
        };



        var viewMonthlySheet = function (monthId) {
            if (typeof monthId === "undefined" || monthId === null) {
                console.log("fn:viewMonthlySheet", "undefined month");
                return;
            }
            pullMonthlySheet({
                month: monthId,
                regenerateFlag: false
            });
        };

        var removeTable = function () {
            $salarySheetTable.empty();
        };

        var viewMonthlySheetIfAvailable = function () {
            $generateBtn.text('Generate');
            $generateBtn.attr("regenerateFlag", false);
            var monthId = $month.val();
            for (var i in payRollGeneratedMonths) {
                if (payRollGeneratedMonths[i].MONTH_ID == monthId) {
                    viewMonthlySheet(monthId);
                    $generateBtn.text('Regenerate');
                    $generateBtn.attr("regenerateFlag", true);
                    break;
                }
            }
            if ($generateBtn.attr("regenerateFlag") == "false") {
                removeTable();
            }

        };


        var initializeDatas = function (rows) {
            datas = [];
            var searchEmployeeList = document.searchManager.getEmployee();
            for (var i in searchEmployeeList) {
                if (typeof rows[searchEmployeeList[i]['EMPLOYEE_ID']] === "undefined") {
                    continue;
                }
                var temp = {};
                temp.employeeName = searchEmployeeList[i]['FIRST_NAME'];
                for (var j in rules) {
                    temp["h" + j  ] = rows[searchEmployeeList[i]['EMPLOYEE_ID']].ruleValueKV[j];
                }
                temp.calculatedValue = rows[searchEmployeeList[i]['EMPLOYEE_ID']].calculatedValue;
                datas.push(temp);
            }
        };

        var initializekendoGrid = function (columns) {
            $salarySheetTable.kendoGrid({
                columnMenu: true,
                height: 450,
                scrollable: true,
                sortable: true,
                filterable: true,
                pageable: {
                    input: true,
                    numeric: false
                },
                columns: columns
            });

        };

        var updateKendoGridData = function ($table, datas) {
            var dataSource = new kendo.data.DataSource({data: datas, pageSize: 20});
            var grid = $table.data("kendoGrid");
            dataSource.read();
            grid.setDataSource(dataSource);
        };

        /*
         * 
         */
        $year.on('change', function () {
            try {
                var $this = $(this);
                if ($this.val() == -1) {
                    throw {message: 'Default option selected.'};
                }

                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullMonthsByFiscalYear',
                    data: {
                        'fiscalYearId': $this.val(),
                    }
                }).then(function (success) {
                    months = success.data;
                    app.populateSelect($month, months, 'MONTH_ID', 'MONTH_EDESC', 'Month');
                }, function (failure) {
                    console.log("pullMonthsByFiscalYear fail", failure);
                });

            } catch (e) {
//                app.showMessage(e.message);
            }

        });

        $month.on('change', function () {
            viewMonthlySheetIfAvailable();
        });


        $generateBtn.on('click', function () {
            try {
                var $this = $(this);
                var monthId = $month.val();
                var regenerateFlag = ($this.attr('regenerateFlag') == "true");
                if (monthId == -1) {
                    throw {message: "Month Not Selected", object: $month};
                }
                pullMonthlySheet({
                    month: monthId,
                    regenerateFlag: regenerateFlag
                });
            } catch (e) {
                app.showMessage(e.message);
            }
        });


        /*
         * 
         */
        app.populateSelect($year, fiscalYears, 'FISCAL_YEAR_ID', 'NAME', 'Fiscal Year');
        app.populateSelect($month, [], 'MONTH_ID', 'MONTH_EDESC', 'Month');
        fetchPayRollGeneratedMonths();

        var searchListener = function () {
            if (!displayKendoFirstTime) {
                initializeDatas(employeeRuleValues);
                updateKendoGridData($salarySheetTable, datas);
            }
        };
        document.searchManager.setCompanyListener(searchListener);


    });
})(window.jQuery, window.app);


