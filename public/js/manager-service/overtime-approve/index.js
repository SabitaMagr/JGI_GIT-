(function ($) {
    'use strict';
    $(document).ready(function () {
        var overTimeGrid = $("#overtimeApproveTable").kendoGrid({
            excel: {
                fileName: "OvertimeRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.overtimeRequest,
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
            columns: [
                {
                    title: 'Select All',
                    headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                    template: "<input type='checkbox' id='#:OVERTIME_ID#' role-id='#:ROLE#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:OVERTIME_ID#'></label>",
                    width: 40
                },
                {field: "FULL_NAME", title: "Employee"},
                {title: "Requested Date",
                    columns: [{
                            field: "REQUESTED_DATE",
                            title: "English",
                            template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                        {field: "REQUESTED_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                {title: "Overtime Date",
                    columns: [{
                            field: "OVERTIME_DATE",
                            title: "English",
                            template: "<span>#: (OVERTIME_DATE == null) ? '-' : OVERTIME_DATE #</span>"},
                        {field: "OVERTIME_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (OVERTIME_DATE_N == null) ? '-' : OVERTIME_DATE_N #</span>"}]},
                {field: "DETAILS", title: "Time (From-To)", template: `<ul id="branchList">  
        #  ln=DETAILS.length #
        #for(var i=0; i<ln; i++) { #
        <li>
        #=i+1 #) #=DETAILS[i].START_TIME # - #=DETAILS[i].END_TIME #
        </li>
        #}#
        </ul>`},
                {field: "TOTAL_HOUR", title: "Total Hour"},
                {field: "YOUR_ROLE", title: "Your Role"},
                {field: ["OVERTIME_ID","ROLE"], title: "Action", template: `<span><a class="btn-edit"
        href=" ` + document.viewLink + `/#:OVERTIME_ID #/#:ROLE #" style="height:17px;" title="view">
        <i class="fa fa-search-plus"></i></a>
       </span>`}
            ]
        });



        var checkedIds = {};

        overTimeGrid.on("click", ".k-checkbox", selectRow);

        function selectRow() {
            var checked = this.checked,
                    row = $(this).closest("tr"),
                    grid = $("#overtimeApproveTable").data("kendoGrid"),
                    dataItem = grid.dataItem(row);
            checkedIds[dataItem.OVERTIME_ID] = {
                'checked': checked,
                data: {
                    'id': dataItem.OVERTIME_ID,
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
                    var grid = $('#overtimeApproveTable').data("kendoGrid");
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




        app.searchTable('overtimeApproveTable', ['FULL_NAME', 'REQUESTED_DATE', 'OVERTIME_DATE', 'REQUESTED_DATE_N', 'OVERTIME_DATE_N', 'TOTAL_HOUR', 'YOUR_ROLE']);

        app.pdfExport(
                'overtimeApproveTable',
                {
                    'FULL_NAME': 'Name',
                    'REQUESTED_DATE': 'Request Date(AD)',
                    'REQUESTED_DATE_N': 'Request Date(BS)',
                    'OVERTIME_DATE': 'Overtime Date(AD)',
                    'OVERTIME_DATE_N': 'Overtime Date(BS)',
                    'TOTAL_HOUR': 'Total Hour',
                    'DESCRIPTION': 'Description',
                    'YOUR_ROLE': 'Role',
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
                        {value: "Requested Date(AD)"},
                        {value: "Requested Date(BS)"},
                        {value: "Overtime Date(AD)"},
                        {value: "Overtime Date(BS)"},
                        {value: "Time (From-To)"},
                        {value: "Total Hour"},
                        {value: "Description"},
                        {value: "Your Role"},
                        {value: "Status"},
                        {value: "Remarks"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#overtimeApproveTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();

            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                var details = [];
                for (var j = 0; j < dataItem.DETAILS.length; j++) {
                    details.push(dataItem.DETAILS[j].START_TIME + "-" + dataItem.DETAILS[j].END_TIME);
                }
                var details1 = details.toString();
                rows.push({
                    cells: [
                        {value: dataItem.FULL_NAME},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.REQUESTED_DATE_N},
                        {value: dataItem.OVERTIME_DATE},
                        {value: dataItem.OVERTIME_DATE_N},
                        {value: details1},
                        {value: dataItem.TOTAL_HOUR},
                        {value: dataItem.DESCRIPTION},
                        {value: dataItem.YOUR_ROLE},
                        {value: dataItem.STATUS},
                        {value: dataItem.REMARKS},
                        {value: dataItem.RECOMMENDED_REMARKS},
                        {value: dataItem.RECOMMENDED_DATE},
                        {value: dataItem.APPROVED_REMARKS},
                        {value: dataItem.APPROVED_DATE},
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
                        title: "Overtime Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "OvertimeRequestList.xlsx"});
        }
    });
})(window.jQuery, window.app);
