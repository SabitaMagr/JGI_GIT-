(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("#instituteTable").kendoGrid({
            dataSource: {
                data: document.institutes,
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
//                {field: "INSTITUTE_CODE", title: "Institute Code",width:80},
                {field: "INSTITUTE_NAME", title: "Institute Name",width:130},
                {field: "LOCATION", title: "Location Detail",width:110},
                {field: "TELEPHONE", title: "Telephone",width:120},
                {field: "EMAIL", title: "Email",width:150},
                {title: "Action",width:110}
            ]
        });
        
        app.searchTable('instituteTable',['INSTITUTE_NAME','LOCATION','TELEPHONE','EMAIL']);
        
        app.pdfExport(
                'instituteTable',
                {
                    'INSTITUTE_NAME': 'Institute',
                    'LOCATION': 'Location',
                    'TELEPHONE': 'telephone',
                    'EMAIL': 'Email'
                }
        );
        
        
        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Institute Code"},
                        {value: "Institute Name"},
                        {value: "Location Detail"},
                        {value: "Telephone"},
                        {value: "Email"},
                        {value: "Remarks"}
                    ]
                }];
            var dataSource = $("#instituteTable").data("kendoGrid").dataSource;
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
                        {value: dataItem.INSTITUTE_CODE},
                        {value: dataItem.INSTITUTE_NAME},
                        {value: dataItem.LOCATION},
                        {value: dataItem.TELEPHONE},
                        {value: dataItem.EMAIL},
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
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Institute",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "InstituteList.xlsx"});
        }       
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);