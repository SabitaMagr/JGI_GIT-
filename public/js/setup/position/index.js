(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali("FromnepaliDate", "FromenglishDate", "TonepaliDate", "ToenglishDate");
//        var $fromNepaliDate = $('#FromnepaliDate');
//        var $fromEnglishDate = $('#FromenglishDate');
//        var $toNepaliDate = $('#TonepaliDate');
//        var $toEnglishDate = $('#ToenglishDate');
//
//        $fromNepaliDate.nepaliDatePicker({
//            onChange: function () {
//                $fromEnglishDate.val(nepaliDatePickerExt.fromNepaliToEnglish($fromNepaliDate.val()));
//            }
//        });
//
//        $fromEnglishDate.datepicker({format: 'dd-M-yyyy', autoclose: true}).on('changeDate', function () {
//            $fromNepaliDate.val(nepaliDatePickerExt.fromEnglishToNepali($(this).val()));
//        });
//
//        $toNepaliDate.nepaliDatePicker({
//            onChange: function () {
//                $toEnglishDate.val(nepaliDatePickerExt.fromNepaliToEnglish($toNepaliDate.val()));
//
//            }
//        });
//
//        $toEnglishDate.datepicker({format: 'dd-M-yyyy', autoclose: true}).on('changeDate', function () {
//            $toNepaliDate.val(nepaliDatePickerExt.fromEnglishToNepali($(this).val()));
//        });



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
                {field: "LEVEL_NO", title: "Level", width: 100},
                {field: "POSITION_NAME", title: "Position", width: 300},
                {field: "COMPANY_NAME", title: "Company", width: 300},
                {field: "REMARKS", title: "Remarks", hidden: true},
                {title: "Action", width: 80, width: 100}
            ]
        });
        
        app.searchTable('positionTable',['LEVEL_NO','POSITION_NAME','COMPANY_NAME']);

        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Level"},
                        {value: "Position Name"},
                        {value: "Company Name"},
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
                        {value: dataItem.LEVEL_NO},
                        {value: dataItem.POSITION_NAME},
                        {value: dataItem.COMPANY_NAME},
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



