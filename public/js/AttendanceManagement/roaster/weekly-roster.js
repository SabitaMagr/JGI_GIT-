(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
//        
        var $table = $('#table');
        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);

        var $search = $('#search');
        var columns = [{title: "Code", field: "EMPLOYEE_CODE", width: 80},
            {title: "Employee", field: "FULL_NAME", width: 100} ];

        var selectedFromDate;
        var selectedToDate;

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

                $('.r-cell').each(function () {
                    var $this = $(this);
                    var selectedShift = $this.attr('shift-id');
                    app.populateSelect($this, document.shifts, 'SHIFT_ID', 'SHIFT_ENAME', 'Select Shift', -1, selectedShift != "" ? selectedShift : null);
                });

            },
            pageable: {
                input: true,
                numeric: false,
//                 refresh: true,
                pageSizes: true,
            },
            columns: columns,
            selectable: "multiple cell",
        };

        var cellTemplate = function (forDay) {
            return  `<select class="r-cell" employee-id="#:EMPLOYEE_ID#" for-day="#:${forDay}#"></select>`;
        };
        var initialize = function ($table, kendoConfig) {
            $table.empty();
            $table.kendoGrid(kendoConfig);
        };

        $search.on('click', function () {

            $table.html("");
            columns.splice(2);
            columns.push({title: "Sunday", field: "SUN", width: 80, template: cellTemplate("SUN")},
            {title: "Monday", field: "MON", width: 80, template: cellTemplate("MON")},
            {title: "Tuesday", field: "TUE", width: 80, template: cellTemplate("TUE")},
            {title: "Wednesday", field: "WED", width: 80, template: cellTemplate("WED")},
            {title: "Thursday", field: "THU", width: 80, template: cellTemplate("THU")},
            {title: "Friday", field: "FRI", width: 80, template: cellTemplate("FRI")},
            {title: "Saturday", field: "SAT", width: 80, template: cellTemplate("SAT")});
            
            initialize($table, kendoConfig);
            getRoaster(function (rData) {
                app.renderKendoGrid($table, rData);
                $('.r-cell').each(function () {
                    var $this = $(this);
                    var selectedShift = $this.attr('shift-id');
                    app.populateSelect($this, document.shifts, 'SHIFT_ID', 'SHIFT_ENAME', 'Select Shift', -1, selectedShift != "" ? selectedShift : null);
                });

            });
        });

        var getRoaster = function (fn) {
            var q = document.searchManager.getSearchValues();
            app.pullDataById(document.getWeeklyRosterListLink, {q}).then(function (response) {
                var data = response.data;
                fn(data);
                for (var i in data) {
                    $("select[employee-id=" + data[i]['EMPLOYEE_ID'] + "][sunday=" + data[i]['SUN'] + "]").val(data[i]['SUN']);
                    $("select[employee-id=" + data[i]['EMPLOYEE_ID'] + "][monday=" + data[i]['MON'] + "]").val(data[i]['MON']);
                    $("select[employee-id=" + data[i]['EMPLOYEE_ID'] + "][tuesday=" + data[i]['TUE'] + "]").val(data[i]['TUE']);
                    $("select[employee-id=" + data[i]['EMPLOYEE_ID'] + "][wednesday=" + data[i]['WED'] + "]").val(data[i]['WED']);
                    $("select[employee-id=" + data[i]['EMPLOYEE_ID'] + "][thursday=" + data[i]['THU'] + "]").val(data[i]['THU']);
                    $("select[employee-id=" + data[i]['EMPLOYEE_ID'] + "][friday=" + data[i]['FRI'] + "]").val(data[i]['FRI']);
                    $("select[employee-id=" + data[i]['EMPLOYEE_ID'] + "][saturday=" + data[i]['SAT'] + "]").val(data[i]['SAT']);
                    
                }
            }, function () {

            });
        };

        $('#assignBtn').on('click', function () {
            var data = [];
            $('.r-cell').each(function () {
                var $this = $(this);
//                if ($this.val() !== null && $this.val() != -1) {
                if ($this.val() !== null) {
                    data.push({
                        'EMPLOYEE_ID': $this.attr('employee-id'),
                        'SUN': $this.attr('sunday'),
                        'MON': $this.attr('monday'),
                        'TUE': $this.attr('tuesday'),
                        'WED': $this.attr('wednesday'),
                        'THU': $this.attr('thursday'),
                        'FRI': $this.attr('friday'),
                        'SAT': $this.attr('saturday')
                    });
                }
            });
            app.serverRequest(document.assignRoasterLink, {'data': data}).then(function (response) {
                app.showMessage('Roaster assigned successfully.');
            }, function (error) {

            });

        });



        $table.on("click", ".r-cell", function () {
            var $this = $(this);
            let row = $(this).closest("tr"),
                    grid = $('#table').data("kendoGrid"),
                    dataItem = grid.dataItem(row);
            var ds = grid.dataSource;
            var selectedRow = ds.getByUid(dataItem.uid);
            var selectedDay = $this.attr('for-day');
            var shiftId =  $this.val();

           

            console.log(selectedDay,shiftId);
                    
            app.serverRequest(document.getShiftDetails, {
                selectedDay: selectedDay,
                shiftId : shiftId
            }).then(function (response) {
                if (response.success == true) {
                    console.log(response);
                    $.each(response.data, function (key, value) {
                        var tempDate = new Date(value.DATES);
                        var tempDateVal = "F" + tempDate.getFullYear() + ("0" + (tempDate.getMonth() + 1)).slice(-2) + ("0" + tempDate.getDate()).slice(-2) + "_S";
                        if (key > 0) {
                            if (value.DAY_OFF == 'DAY_OFF' && value.DAY == value.WEEK_NO) {
                                var currentSelectElement = $this.closest('tr').find('[for-date=' + value.DATES + ']');
                                app.populateSelect(currentSelectElement, document.shifts, 'SHIFT_ID', 'SHIFT_ENAME', 'Select Shift', -1, value.SHIFT_ID);
                                selectedRow.set(tempDateVal, value.SHIFT_ID);
                            }

                            if (value.DAY_OFF != 'DAY_OFF') {
                                var currentSelectElement = $this.closest('tr').find('[for-date=' + value.DATES + ']');
                                app.populateSelect(currentSelectElement, document.shifts, 'SHIFT_ID', 'SHIFT_ENAME', 'Select Shift', -1, value.SHIFT_ID);
                                selectedRow.set(tempDateVal, value.SHIFT_ID);
                            }

                        } else {
                            selectedRow.set(tempDateVal, value.SHIFT_ID);
                        }
                    });
                }
            });
        });




//        $("#grid").on("click", "table", function (e) {
//            console.log('sdfsdf');
//            console.log("clicked", e.ctrlKey, e.altKey, e.shiftKey);
//        });
        $('#reset').on('click', function () {
            $('.form-control').val("");
        });

    });
})(window.jQuery, window.app);