(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
//        
        var $table = $('#table');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $search = $('#search');
        var columns = [{title: "Employee", field: "FULL_NAME"}];

        var kendoConfig = {
            height: 500,
            scrollable: true,
            sortable: true,
            filterable: true,
            groupable: true,
            dataBound: function (e) {
                var grid = e.sender;
                if (grid.dataSource.total() === 0) {
                    var colCount = grid.columns.length;
                    $(e.sender.wrapper)
                            .find('tbody')
                            .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
                }
            },
            pageable: {
                input: true,
                numeric: false
            },
            columns: columns,
            selectable: "multiple cell",
        };

        var cellTemplate = function (forDate, shiftId) {
            return  `<select class="r-cell" employee-id="#:EMPLOYEE_ID#" for-date="#:${forDate}#" shift-id="#:${shiftId}#"></select>`;
        };
        var initialize = function ($table, kendoConfig) {
            $table.kendoGrid(kendoConfig);
        };

        $search.on('click', function () {
            if ($fromDate.val() === "") {
                $fromDate.focus();
                app.showMessage("From Date is required.", 'error');
                return;
            }
            if ($toDate.val() === "") {
                $toDate.focus();
                app.showMessage("To Date is required.", 'error');
                return;
            }
            $table.html("");
            var fromDate = $fromDate.val();
            var toDate = $toDate.val();
            var dateRange = app.getDateRangeBetween(nepaliDatePickerExt.getDate(fromDate), nepaliDatePickerExt.getDate(toDate));
            columns.splice(1);
            for (var i in dateRange) {
                var columnTitle = dateRange[i].getFullYear() + "-" + dateRange[i].getMonth() + "-" + dateRange[i].getDate();
                var forDate = "f" + dateRange[i].getFullYear() + dateRange[i].getMonth() + dateRange[i].getDate();
                var shiftId = "s" + dateRange[i].getFullYear() + dateRange[i].getMonth() + dateRange[i].getDate();
                columns.push({title: columnTitle, field: [forDate, "EMPLOYEE_ID", shiftId], template: cellTemplate(forDate, shiftId)});
            }
            initialize($table, kendoConfig);
            getRoaster(function (rData) {
                var employees = document.searchManager.getSelectedEmployee();
                var data = [];
                var searchInRData = function (on, by) {
                    for (var i in on) {
                        if (on[i]['EMPLOYEE_ID'] == by['EMPLOYEE_ID'] && on[i]['FOR_DATE'] == by['FOR_DATE']) {
                            return on[i];
                        }
                    }
                    return false;
                };

                for (var i in employees) {
                    var cell = {'FULL_NAME': employees[i]['FULL_NAME']};
                    for (var j in dateRange) {
                        var columndate = nepaliDatePickerExt.getFormatedDate(dateRange[j]);
                        cell["f" + dateRange[j].getFullYear() + dateRange[j].getMonth() + dateRange[j].getDate()] = columndate;
                        cell['EMPLOYEE_ID'] = employees[i]['EMPLOYEE_ID'];
                        var check = searchInRData(rData, cell);
                        cell["s" + dateRange[j].getFullYear() + dateRange[j].getMonth() + dateRange[j].getDate()] = check != false ? check['SHIFT_ID'] : '';
                    }
                    data.push(cell);
                }
                app.renderKendoGrid($table, data);
                $('.r-cell').each(function () {
                    var $this = $(this);
                    var selectedShift = $this.attr('SHIFT_ID');
                    app.populateSelect($this, document.shifts, 'SHIFT_ID', 'SHIFT_ENAME', null, null, selectedShift != "" ? selectedShift : null);
                });
            });
        });

        var getRoaster = function (fn) {
            app.pullDataById(document.getRoasterListLink, {}).then(function (response) {
                var data = response.data;
                fn(data);
                for (var i in data) {
                    $("select[employee-id=" + data[i]['EMPLOYEE_ID'] + "][for-date=" + data[i]['FOR_DATE'] + "]").val(data[i]['SHIFT_ID']);
                }
            }, function () {

            });
        };

        $('#assignBtn').on('click', function () {
            var data = [];
            $('.r-cell').each(function () {
                var $this = $(this);
                if ($this.val() !== null && $this.val() != -1) {
                    data.push({
                        'EMPLOYEE_ID': $this.attr('employee-id'),
                        'FOR_DATE': $this.attr('for-date'),
                        'SHIFT_ID': $this.val()
                    });
                }
            });
            app.pullDataById(document.assignRoasterLink, {'data': data}).then(function (response) {
                app.showMessage('Roaster assigned successfully.');
            }, function (error) {

            });

        });


    });
})(window.jQuery, window.app);