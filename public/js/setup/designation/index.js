(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#designationTable").kendoGrid({
            dataSource: {
                data: document.designations,
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
                {field: "COMPANY_NAME", title: "Company",width:120},
                {field: "DESIGNATION_CODE", title: "Code",width:120},
                {field: "DESIGNATION_TITLE", title: "Name",width:200},
                {field: "PARENT_DESIGNATION_TITLE", title: "Parent Designation",width:200},
                {field: "BASIC_SALARY", title: "Basic Salary",width:120},
                {title: "Action",width:100}
            ]
        });
               $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Designation Code"},
                        {value: "Designation Name"},                        
                        {value: "Basic Salary"},
                        {value: "Parent Designation Name"},
                        {value: "Company Name"},
                        {value: "Within Branch"},
                       {value: "Within Department"}
                    ]
                }];
            var dataSource = $("#designationTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();
            
            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                var withinBranch = dataItem.WITHIN_BRANCH=='Y'?'Yes':'No';
                var withinDepartment = dataItem.WITHIN_DEPARTMENT=='Y'?'Yes':'No';
                rows.push({
                    cells: [
                        {value: dataItem.DESIGNATION_CODE},
                        {value: dataItem.DESIGNATION_TITLE},
                        {value: dataItem.BASIC_SALARY},
                        {value: dataItem.PARENT_DESIGNATION_TITLE},
                        {value: dataItem.COMPANY_NAME},
                        {value: withinBranch},
                        {value: withinDepartment}
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
                            {autoWidth: true}
                        ],
                        title: "Designation",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "DesignationList.xlsx"});
        }
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);
