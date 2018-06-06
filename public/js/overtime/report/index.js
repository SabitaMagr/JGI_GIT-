(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        /**/
        var months = null;
        var selectedMonth = null;
        /**/
        var $year = $('#fiscalYearId');
        var $month = $('#monthId');
        var $table = $('#table');
        var $search = $('#search');

        $month.on('change', function () {
            changeGrid();
        });
        $search.on('click', function () {
            changeGrid();
        });

        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
            months = monthList;
        }, data.getFiscalYearMonthLink);

        var changeGrid = function () {
            var monthId = $month.val();

            if (monthId === null && monthId === '') {
                return;
            }

            fetchGridColumns({monthId: monthId}, function (kendoConfig) {
                if (typeof $table.data('kendoGrid') === 'undefined') {
                    $table.kendoGrid(kendoConfig);
                    app.searchTable($table, ['FULL_NAME']);
                } else {
                    $table.kendoGrid('destroy').empty();
                    $table.kendoGrid(kendoConfig);
                }
            });

        };
        var columns = null;
        var fields = null;
        var fetchGridColumns = function (q, fn) {
            app.serverRequest('', q).then(function (response) {
                var columnList = response.data;
                columns = [
                    {field: 'FULL_NAME', title: 'Employee', width: 150, locked: true}
                ];
                fields = {
                    'FULL_NAME': {editable: false},
                };
                $.each(columnList, function (k, v) {
                    columns.push({field: v['MONTH_DAY_FIELD'], title: v['MONTH_DAY_TITLE'], width: 50});
                    fields[v['MONTH_DAY_FIELD']] = {type: "number"};
                });

                fn(getkendoConfig(columns, fields));

            }, function (error) {

            });


        }

        var getkendoConfig = function (c, f) {
            return {
                dataSource: {
                    transport: {
                        type: "json",
                        read: {
                            url: data.gridReadLink,
                            type: "POST",
                        },
                        update: {
                            url: data.gridUpdateLink,
                            type: "POST",
                        },
                        parameterMap: function (options, operation) {

                            if (operation === "read") {
                                selectedMonth = $month.val();
                                var q = document.searchManager.getSearchValues();
                                q['monthId'] = selectedMonth;
                                return q;
                            }
                            if (operation !== "read" && options.models) {
                                return {
                                    monthId: selectedMonth,
                                    models: kendo.stringify(options.models)};
                            }


                        }
                    },
                    batch: true,
                    schema: {
                        model: {
                            id: "EMPLOYEE_ID",
                            fields: f
                        }
                    },
                    pageSize: 20
                },
                pageable: true,
                height: 550,
                toolbar: ["save", "cancel"],
                columns: c,
                editable: true
            };
        };
    });
})(window.jQuery, window.app);