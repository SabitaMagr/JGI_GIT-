(function ($) {
    'use strict';
    $(document).ready(function () {
        
        $('#FromnepaliDate').nepaliDatePicker({
            ndpEnglishInput: 'FromenglishDate'            
        });
        $('#TonepaliDate').nepaliDatePicker({
            ndpEnglishInput: 'ToenglishDate'           
        });

        $('#FromenglishDate').datepicker({format: 'dd-M-yyyy',autoclose: true});
        $('#FromenglishDate').change(function () {
            $('#FromnepaliDate').val(AD2BS($('#FromenglishDate').val()));
        });
        $('#ToenglishDate').datepicker({format: 'dd-M-yyyy',autoclose: true});
        $('#ToenglishDate').change(function () {
            $('#TonepaliDate').val(AD2BS($('#ToenglishDate').val()));
        });

        $('#FromnepaliDate').change(function () {
            $('#FromenglishDate').val(BS2AD($('#FromnepaliDate').val()));
        });
        $('#TonepaliDate').change(function () {
            $('#ToenglishDate').val(BS2AD($('#TonepaliDate').val()));
        });


        $("#positionTable").kendoGrid({
            dataSource: {
                data: document.positions,
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
            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "SN", title: "S.N.", width: 100},
                {field: "POSITION_NAME", title: "Position Name", width: 400},
                {field: "REMARKS", title: "Remarks", hidden: true},
                {title: "Action", width: 80}
            ]
        });

        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "SN"},
                        {value: "Position Name"},
                        {value: "Remarks"}
                    ]
                }];
            var dataSource = $("#positionTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.SN},
                        {value: dataItem.POSITION_NAME},
                        {value: dataItem.REMARKS}
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
                            {autoWidth: true}
                        ],
                        title: "Position",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "PositionList.xlsx"});
        }

        window.app.UIConfirmations();

//        var exportFlag = true;
//        $("#positionTable").data("kendoGrid").bind("excelExport", function (e) {
//            if (exportFlag) {
//                e.sender.showColumn("REMARKS");
//                e.preventDefault();
//                exportFlag = false;
//                e.sender.saveAsExcel();
//            } else {
//                e.sender.hideColumn("REMARKS");
//                exportFlag = true;
//            }
//        });
    });
})(window.jQuery, window.app);



