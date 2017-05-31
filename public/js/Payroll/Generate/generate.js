(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var displayKendoFirstTime = true;

        var fiscalYears = document.fiscalYears;
        var months = [];
        var rules = document.rules;
        var payRollGeneratedMonths = [];
        var employeeList = [];

        var headers = [];
        var datas = [];

        var employeeRuleValues = {};



        var $generateBtn = $('#generateBtn');
        var $month = $('#monthId');
        var $year = $('#fiscalYearId');


        /*
         * 
         */
        var pullMonthlySheet = function (reqParams) {
            window.app.pullDataById(document.restful.generateMonthlySheet, reqParams).then(function (success) {
                var employeeRuleValues = success.data;

                initializeHeaders(rules);
                initializeDatas(rules, employeeRuleValues);
                if (displayKendoFirstTime) {
                    initializekendoGrid(headers);
                    displayKendoFirstTime = false;
                }
                var dataSource = new kendo.data.DataSource({data: datas, pageSize: 20});
                var grid = $('#salarySheetTable').data("kendoGrid");
                dataSource.read();
                grid.setDataSource(dataSource);
                if (!reqParams.regenerateFlag) {
                    $scope.fetchPayRollGeneratedMonths();
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
                console.log("pullPayRollGeneratedMonths res", success.data);
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
            $("#salarySheetTable").empty();
        };

        var viewMonthlySheetIfAvailable = function () {
            $generateBtn.text("Generate");
            $generateBtn.attr("regenerateFlag", false);
            var monthId = $month.val();
            for (var i in payRollGeneratedMonths) {
                if (payRollGeneratedMonths[i].MONTH_ID == monthId) {
                    viewMonthlySheet(monthId);
                    $generateBtn.text("Regenerate");
                    $generateBtn.attr("regenerateFlag", true);
                    break;
                }
            }
            if ($generateBtn.attr("regenerateFlag") == "false") {
                removeTable();
            }

        };

        var initializeHeaders = function (cols) {
            headers = [];
            headers.push({field: "employeeName", title: "Employee Name"});
            for (var i in cols) {
                headers.push({field: "h" + i, title: cols[i]});
            }
            headers.push({field: "calculatedValue", title: "Calculated Value"});
        };

        var initializeDatas = function (cols, rows) {
            datas = [];
            for (var i in rows) {
                var temp = {};
                temp.employeeName = $scope.employeeList[i];
                for (var j in cols) {
                    temp["h" + j  ] = rows[i].ruleValueKV[j];
                }
                temp.calculatedValue = rows[i].calculatedValue;
                datas.push(temp);
            }
        };

        var initializekendoGrid = function (columns) {
            $("#salarySheetTable").kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "SalarySheet.xlsx",
                    filterable: true,
                    allPages: true
                },
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

        /*
         * 
         */
        $year.on('change', function () {
            var $this = $(this);
            window.app.pullDataById(document.restfulUrl, {
                action: 'pullMonthsByFiscalYear',
                data: {
                    'fiscalYearId': $this.val(),
                }
            }).then(function (success) {
                months = success.data;
            }, function (failure) {
                console.log("pullMonthsByFiscalYear fail", failure);
            });

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
        app.populateSelectElement($year, fiscalYears, 'FISCAL_YEAR_ID', 'START_DATE', 'Fiscal Years');
        fetchPayRollGeneratedMonths();


    });
})(window.jQuery, window.app);


