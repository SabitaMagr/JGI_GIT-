(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#departmentTable").kendoGrid({
            dataSource: {
                data: document.departments,
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
                {field: "DEPARTMENT_CODE", title: "Department Code",width:120},
                {field: "DEPARTMENT_NAME", title: "Department Name",width:200},
                 {field: "PARENT_DEPARTMENT", title: "Paren Department Name",width:200},
                {title: "Action",width:50}
            ]
        });

            $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Department Code"},
                        {value: "Department Name"},
                        {value: "Country Name"},
                        {value: "Parent Department Name"},
                        {value: "Remarks"}
                    ]
                }];
            var dataSource = $("#departmentTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.DEPARTMENT_CODE},
                        {value: dataItem.DEPARTMENT_NAME},
                        {value: dataItem.COUNTRY_NAME},
                        {value: dataItem.PARENT_DEPARTMENT},
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
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Department",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "DepartmentList.xlsx"});
        }
        
        window.app.UIConfirmations();

    });
})(window.jQuery);