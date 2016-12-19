(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#recommendApproveTable").kendoGrid({
            dataSource: {
                data: document.recommendApproves,
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
                {field: "FIRST_NAME", title: "Employee Name",width:200},
                {field: "FIRST_NAME_R", title: "Recommender",width:200},
                {field: "FIRST_NAME_A", title: "Approver",width:200}
            ]
        });
            $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Employee Name"},
                        {value: "Recommender Name"},
                        {value: "Approver Name"}
                    ]
                }];
            var dataSource = $("#recommendApproveTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();
            
            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                var middleName = dataItem.MIDDLE_NAME!=null ?" "+dataItem.MIDDLE_NAME+" " : " ";
                var middleNameR = dataItem.MIDDLE_NAME_R!=null ?" "+dataItem.MIDDLE_NAME_R+" " : " ";
                var middleNameA = dataItem.MIDDLE_NAME_A!=null ?" "+dataItem.MIDDLE_NAME_A+" " : " ";
                rows.push({
                    cells: [
                        {value: dataItem.FIRST_NAME+middleName+dataItem.LAST_NAME},
                        {value: dataItem.FIRST_NAME_R+middleNameR+dataItem.LAST_NAME_R},
                        {value: dataItem.FIRST_NAME_A+middleNameA+dataItem.LAST_NAME_A}
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
                        title: "Recommender And Approver List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "ReportingHierarchy.xlsx"});
        }
        
        window.app.UIConfirmations();

    });
})(window.jQuery);