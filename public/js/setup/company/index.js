(function ($) {
    'use strict';
    $(document).ready(function () {
        $("#companyTable").kendoGrid({
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
                {field: "COMPANY_CODE", title: "Company Code"},
                {field: "COMPANY_NAME", title: "Company Name"},
                {title: "Action"}
            ]
        });

    });
})(window.jQuery);