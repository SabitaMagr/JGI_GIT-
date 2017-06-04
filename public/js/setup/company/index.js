(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#companyTable").kendoGrid({
            excel: {
                fileName: "CompanyList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.companyList,
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
//                {field: "COMPANY_CODE", title: "Company Code",width:120},
                {field: "COMPANY_NAME", title: "Company",width:400},
                {title: "Action",width:110}
            ]
        });
        $("#export").click(function (e) {
            var grid = $("#companyTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        
        app.searchTable('companyTable',['COMPANY_NAME']);
        
        app.pdfExport(
                'companyTable',
                {
                    'COMPANY_NAME': 'Company'
                }
        );
        
        window.app.UIConfirmations();

    });
})(window.jQuery);