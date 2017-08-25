(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.attendanceApprove);
        var attendanceGrid = $("#attendanceApproveTable").kendoGrid({
            excel: {
                fileName: "AttendanceRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.attendanceApprove,
                pageSize: 20
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: {
                input: true,
                numeric: false
            },
            dataBound: gridDataBound,
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {
                    title: 'Select All',
                    headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                    width: 40
                },
                {field: "FULL_NAME", title: "Employee", width: 200},
                {field: "REQUESTED_DT", title: "Requested Date", width: 150},
                {field: "ATTENDANCE_DT", title: "Attendance Date", width: 160},
                {field: "IN_TIME", title: "Check In", width: 120},
                {field: "OUT_TIME", title: "Check Out", width: 140},
                {field: "YOUR_ROLE", title: "Your Role", width: 140},
                {title: "Action", width: 80}
            ]
        });

        var checkedIds = {};
        attendanceGrid.on("click", ".k-checkbox", selectRow);

        function selectRow() {
            var checked = this.checked,
                    row = $(this).closest("tr"),
                    grid = $("#attendanceApproveTable").data("kendoGrid"),
                    dataItem = grid.dataItem(row);
            checkedIds[dataItem.ID] = {
                'checked': checked,
                data: {
                    'id': dataItem.ID,
                    'role': dataItem.ROLE
                }
            }
            if (checked) {
                row.addClass("k-state-selected");
            } else {
                row.removeClass("k-state-selected");
            }

            var checkedNo = $('.k-state-selected').length;
            if (checkedNo > 0) {
                $('#acceptRejectDiv').show();
                if ($('#header-chb').prop('checked') == 1 && checkedNo == 1) {
                    $('#acceptRejectDiv').hide();
                }
            } else {
                $('#acceptRejectDiv').hide();
            }
        }


        $('#header-chb').change(function (ev) {
            var checked = ev.target.checked;
            $('.row-checkbox').each(function (idx, item) {
                if (checked) {
                    if (!($(item).closest('tr').is('.k-state-selected'))) {
                        $(item).click();
                    }
                } else {
                    if ($(item).closest('tr').is('.k-state-selected')) {
                        $(item).click();
                    }
                }
            });
        });



        $(".btnApproveReject").bind("click", function () {
            var btnId = $(this).attr('id');
            var selectedValues = [];
            for (var i in checkedIds) {
                if (checkedIds[i].checked) {
                    selectedValues.push(checkedIds[i].data);
                }
            }

            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(
                    document.approveRejectUrl,
                    {data: selectedValues, btnAction: btnId}
            ).then(function (success) {
                App.unblockUI("#hris-page-content");
                console.log(success);
                if (success.success == true) {
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#attendanceApproveTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                    checkedIds = {};
                    $('#acceptRejectDiv').hide();
                }

            }, function (failure) {
                App.unblockUI("#hris-page-content");
                console.log(failure);
            });
        });



        app.searchTable('attendanceApproveTable', ['FULL_NAME', 'REQUESTED_DT', 'ATTENDANCE_DT', 'IN_TIME', 'OUT_TIME', 'YOUR_ROLE']);

        app.pdfExport(
                'attendanceApproveTable',
                {
                    'FULL_NAME': 'Name',
                    'REQUESTED_DT': 'Request Date',
                    'ATTENDANCE_DT': 'Attendance Date',
                    'IN_TIME': 'In Time',
                    'OUT_TIME': 'Out Time',
                    'TOTAL_HOUR': 'Total Hrs',
                    'IN_REMARKS': 'In Remarks',
                    'OUT_REMARKS': 'Out Remarks',
                    'STATUS': 'Status',
                    'APPROVED_DT': 'Approved Date',
                    'APPROVED_REMARKS': 'Approved Remarks',

                });


        function gridDataBound(e) {
            var grid = e.sender;
            if (grid.dataSource.total() == 0) {
                var colCount = grid.columns.length;
                $(e.sender.wrapper)
                        .find('tbody')
                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
            }
        }
        ;
        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Employee Name"},
                        {value: "Requested Date"},
                        {value: "Attendance Date"},
                        {value: "Check In Time"},
                        {value: "Check Out Time"},
                        {value: "Total Hour"},
                        {value: "Late In Reason"},
                        {value: "Early Out Reason"},
                        {value: "Your Role"},
                        {value: "Status"},
                        {value: "Approved Date"},
                        {value: "Remarks By You"}
                    ]
                }];
            var dataSource = $("#attendanceApproveTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();

            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                rows.push({
                    cells: [
                        {value: dataItem.FULL_NAME},
                        {value: dataItem.REQUESTED_DT},
                        {value: dataItem.ATTENDANCE_DT},
                        {value: dataItem.IN_TIME},
                        {value: dataItem.OUT_TIME},
                        {value: dataItem.TOTAL_HOUR},
                        {value: dataItem.IN_REMARKS},
                        {value: dataItem.OUT_REMARKS},
                        {value: "Approver"},
                        {value: dataItem.STATUS},
                        {value: dataItem.APPROVED_DT},
                        {value: dataItem.APPROVED_REMARKS}
                    ]
                });
            }
            excelExport(rows);
            e.preventDefault();
        });

        function excelExport(rows) {
            var workbook = new kendo.ooxml.Workbook({
                sheets: [
                    {
                        columns: [
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Attendance Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AttendanceRequest.xlsx"});
        }
    });
})(window.jQuery, window.app);
