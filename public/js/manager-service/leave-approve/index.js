(function ($) {
    'use strict';
    $(document).ready(function () {

        console.log(document.leaveApprove);
        var leaveGrid = $("#leaveApproveTable").kendoGrid({
            excel: {
                fileName: "LeaveRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.leaveApprove,
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
//            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {
                    title: 'Select All',
                    headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                    width: 80
                },
                {field: "FULL_NAME", title: "Employee"},
                {field: "LEAVE_ENAME", title: "Leave"},
                {title: "Requested Date",
                    columns: [{
                            field: "APPLIED_DATE",
                            title: "English",
                            template: "<span>#: (APPLIED_DATE == null) ? '-' : APPLIED_DATE #</span>"
                    }  ,   
                    {field: "APPLIED_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (APPLIED_DATE_N == null) ? '-' : APPLIED_DATE_N #</span>"
                    } ]},
            {title: "From Date",
                    columns: [{
                            field: "START_DATE",
                            title: "English",
                            template: "<span>#: (START_DATE == null) ? '-' : START_DATE #</span>"
                    }  ,   
                    {field: "START_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (START_DATE_N == null) ? '-' : START_DATE_N #</span>"
                    } ]},
            {title: "To Date",
                columns: [{
                        field: "END_DATE",
                        title: "English",
                        template: "<span>#: (END_DATE == null) ? '-' : END_DATE #</span>"},
                    { field: "END_DATE_N",
                        title: "English",
                        template: "<span>#: (END_DATE_N == null) ? '-' : END_DATE_N #</span>"
                }]},
                {field: "NO_OF_DAYS", title: "Duration"},
                {field: "YOUR_ROLE", title: "Your Role"},
                {field: ["ID"], title: "Action", template: `<span><a class="btn-edit" href="` + document.viewLink + `/#: ID #" style="height:17px;" title="view detail">
                            <i class="fa fa-search-plus"></i>
                            </a>
                            </span>`}
            ]
        });
        var checkedIds = {};
//        console.log(leaveGrid.table);
        leaveGrid.on("click", ".k-checkbox", selectRow);
//          leaveGrid.table.on("click", ".k-checkbox" , selectRow);

//on click of the checkbox:


        function selectRow() {
            var checked = this.checked,
                    row = $(this).closest("tr"),
                    grid = $("#leaveApproveTable").data("kendoGrid"),
                    dataItem = grid.dataItem(row);
            checkedIds[dataItem.ID] = {
                'checked': checked,
                data: {
                    'id': dataItem.ID,
                    'role': dataItem.ROLE
                }

            }
            if (checked) {
                //-select the row
                row.addClass("k-state-selected");
            } else {
                //-remove selection
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
//                console.log($item);
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

//            console.log(selectedValues);
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(
                    document.approveRejectUrl,
                    {data: selectedValues, btnAction: btnId}
            ).then(function (success) {
                App.unblockUI("#hris-page-content");
                console.log(success);
                if (success.success == true) {
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#leaveApproveTable').data("kendoGrid");
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
        app.searchTable('leaveApproveTable', ['FULL_NAME', 'LEAVE_ENAME', 'APPLIED_DATE', 'APPLIED_DATE_N', 'START_DATE', 'START_DATE_N', 'END_DATE','END_DATE_N', 'NO_OF_DAYS', 'YOUR_ROLE']);
        app.pdfExport(
                'leaveApproveTable',
                {
                    'FULL_NAME': 'Name',
                    'LEAVE_ENAME': 'Leave',
                    'APPLIED_DATE': 'Applied Date(AD)',
                    'APPLIED_DATE_N': 'Applied Date(BS)',
                    'START_DATE': 'Start Date(AD)',
                    'START_DATE_N': 'Start Date(BS)',
                    'END_DATE': 'End Date(AD)',
                    'END_DATE_N': 'End Date(BS)',
                    'YOUR_ROLE': 'Role',
                    'NO_OF_DAYS': 'No Of Days',
                    'STATUS': 'Status',
                    'REMARKS': 'Remarks',
                    'RECOMMENDED_REMARKS': 'Recommended Remarks',
                    'RECOMMENDED_DATE': 'Recommended Date',
                    'APPROVED_REMARKS': 'Approved Remarks',
                    'APPROVED_DATE': 'Approved Date'

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
                        {value: "Leave Name"},
                        {value: "Requested Date(AD"},
                        {value: "Requested Date(BS)"},
                        {value: "From Date(AD)"},
                        {value: "From Date(BS)"},
                        {value: "To Date(AD)"},
                        {value: "To Date(BS)"},
                        {value: "Your Role"},
                        {value: "Duration"},
                        {value: "Status"},
                        {value: "Remarks By Employee"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"},
                    ]
                }];
            var dataSource = $("#leaveApproveTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });
            filteredDataSource.read();
            var data = filteredDataSource.view();
            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                var middleName = dataItem.MIDDLE_NAME != null ? " " + dataItem.MIDDLE_NAME + " " : " ";
                rows.push({
                    cells: [
                        {value: dataItem.FULL_NAME},
                        {value: dataItem.LEAVE_ENAME},
                        {value: dataItem.APPLIED_DATE},
                        {value: dataItem.APPLIED_DATE_N},
                        {value: dataItem.START_DATE},
                        {value: dataItem.START_DATE_N},
                        {value: dataItem.END_DATE},
                        {value: dataItem.END_DATE_N},
                        {value: dataItem.YOUR_ROLE},
                        {value: dataItem.NO_OF_DAYS},
                        {value: dataItem.STATUS},
                        {value: dataItem.REMARKS},
                        {value: dataItem.RECOMMENDED_REMARKS},
                        {value: dataItem.RECOMMENDED_DT},
                        {value: dataItem.APPROVED_REMARKS},
                        {value: dataItem.APPROVED_DT}
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
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Leave Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "LeaveRequestList.xlsx"});
        }
    });
})(window.jQuery, window.app);
