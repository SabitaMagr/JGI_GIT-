(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var months = document.months;

        var $monthlyValueId = $("#monthlyValueId");
        var $fiscalYearId = $("#fiscalYearId");

        var $companyId = $("#companyId");
        var $branchId = $("#branchId");
        var $departmentId = $("#departmentId");
        var $designationId = $("#designationId");
        var $positionId = $("#positionId");
        var $serviceTypeId = $("#serviceTypeId");
        var $serviceEventTypeId = $("#serviceEventTypeId");
        var $employeeTypeId = $("#employeeTypeId");
        var $employeeId = $("#employeeId");

        var $searchEmployeesBtn = $('#searchEmployeesBtn');
        var $assignMonthlyValueBtn = $('#assignMonthlyValueBtn');

        var $grid = $('#monthlyValueDetailGrid');
        var $header = $('#monthlyValuesDetailHeader');
        var $table = $('#monthlyValueDetailTable');
        var $footer = $('#monthlyValueDetailFooter');

        app.populateSelect($monthlyValueId, document.monthlyValues, "MTH_ID", "MTH_EDESC", "Select Monthly Value");
        app.populateSelect($fiscalYearId, document.fiscalYears, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");

        $searchEmployeesBtn.on('click', function () {
            if ($monthlyValueId.val() == -1) {
                app.showMessage("No monthly value Selected.", 'error');
                $monthlyValueId.focus();
                return;
            }
            if ($fiscalYearId.val() == -1) {
                app.showMessage("No fiscal year Selected.", 'error');
                $fiscalYearId.focus();
                return;
            }
            app.pullDataById(document.getMonthlyValueDetailWS, {
                mthId: $monthlyValueId.val(),
                fiscalYearId: $fiscalYearId.val(),
                employeeFilter: {
                    companyId: $companyId.val(),
                    branchId: $branchId.val(),
                    departmentId: $departmentId.val(),
                    designationId: $designationId.val(),
                    positionId: $positionId.val(),
                    serviceTypeId: $serviceTypeId.val(),
                    serviceEventTypeId: $serviceEventTypeId.val(),
                    employeeTypeId: $employeeTypeId.val(),
                    employeeId: $employeeId.val()
                }}).then(function (response) {
                initTable($fiscalYearId.val(), document.searchManager.getEmployee(), response.data);
            }, function (error) {
                console.log(error);
            });
        });

        var findMonthValue = function (serverData, employeeId, monthId) {
            var result = serverData.filter(function (item) {
                return item['EMPLOYEE_ID'] == employeeId && item['MONTH_ID'] == monthId;
            });

            if (result.length > 0) {
                return result[0]['MTH_VALUE'];
            } else {
                return null;
            }
        };

        var initTable = function (fiscalYearId, employeeList, serverData) {
            var filteredMonths = months.filter(function (item) {
                return item['FISCAL_YEAR_ID'] == fiscalYearId;
            });

            $header.html('');
            $header.append($('<th>', {text: 'Id'}));
            $header.append($('<th>', {text: 'Name'}));
            $.each(filteredMonths, function (index, item) {
                $header.append($('<th>', {text: item['MONTH_EDESC']}));
            });
            $header.append($('<th>', {text: ''}));

            $grid.html('');
            $.each(employeeList, function (index, item) {
                var $tr = $('<tr>');

                $tr.append($('<td>', {text: item['EMPLOYEE_ID']}));
                $tr.append($('<td>', {text: item['FULL_NAME']}))

                $.each(filteredMonths, function (k, v) {
                    var $td = $('<td>');
                    $td.append($('<input>', {type: 'number', row: item['EMPLOYEE_ID'], col: v['MONTH_ID'], value: findMonthValue(serverData, item['EMPLOYEE_ID'], v['MONTH_ID']), class: 'form-control'}));
                    $tr.append($td);
                });
                var $td = $('<td>');
                $td.append($('<input>', {type: 'number', row: item['EMPLOYEE_ID'], class: 'group form-control'}));
                $tr.append($td);

                $grid.append($tr);
            });

            $footer.html('');
            var $tr = $('<tr>');

            $tr.append($('<td>', {text: ''}));
            $tr.append($('<td>', {text: ''}))

            $.each(filteredMonths, function (k, v) {
                var $td = $('<td>');
                $td.append($('<input>', {type: 'number', col: v['MONTH_ID'], class: 'group form-control'}));
                $tr.append($td);
            });
            var $td = $('<td>');
            $td.append($('<input>', {type: 'number', class: 'group form-control'}));
            $tr.append($td);

            $footer.append($tr);
            $table.bootstrapTable({height: 400});
        };

        $table.on('change', '.group', function () {
            var $this = $(this);
            var value = $this.val();
            var row = $this.attr('row');
            if (typeof row !== typeof undefined && row !== false) {
                $('input[row=' + row + ']').val(value);
            }
            var col = $this.attr('col');
            if (typeof col !== typeof undefined && col !== false) {
                $('input[col=' + col + ']').val(value);
            }

            if ((typeof row === "undefined" || row === false) && (typeof col === "undefined" || col === false)) {
                $.each($this.parent().parent().children(), function (key, item) {
                    var $td = $(item);
                    var $input = $td.find('input');
                    if ($input.attr('col')) {
                        $input.val(value);
                        $input.trigger('change');
                    }
                });
            }


        });

        $assignMonthlyValueBtn.on('click', function () {
            var fiscalYearId = $fiscalYearId.val();
            var mthId = $monthlyValueId.val();

            var promiseList = [];
            App.blockUI({target: "#hris-page-content"});
            $.each($grid.find('input'), function (key, item) {
                var $item = $(item);
                var rowValue = $item.attr('row');
                var colValue = $item.attr('col');
                var value = $item.val();
                if (typeof rowValue !== "undefined" && rowValue != null && rowValue != "" && typeof colValue !== "undefined" && colValue != null && colValue != "" && typeof value !== "undefined" && value != null && value != "") {
                    promiseList.push(app.pullDataById(document.postMonthlyValueDetailWS, {
                        data: {
                            mthId: mthId,
                            fiscalYearId: fiscalYearId,
                            employeeId: rowValue,
                            monthId: colValue,
                            mthValue: value
                        }
                    }));
                }

            });

            Promise.all(promiseList).then(function (response) {
                App.unblockUI("#hris-page-content");
                app.showMessage("Monthly Value assigned successfully!!!");
            }, function (error) {
                App.unblockUI("#hris-page-content");
            });



        });



    });
})(window.jQuery, window.app);
