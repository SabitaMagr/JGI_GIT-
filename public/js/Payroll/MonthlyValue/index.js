(function ($) {
    'use strict';
    $(document).ready(function () {    
       
        $("#monthlyValueTable").kendoGrid({
            dataSource: {
                data: document.monthlyValues,
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
                {field: "MTH_CODE", title: "Code"},
                {field: "MTH_EDESC", title: "EDesc"},
                {field: "MTH_LDESC", title: "NDesc"},
                {field: "SHOW_AT_RULE", title: "Show At Rule"},
                {field: "SH_INDEX_NO", title: "Index No"},
                {title: "Action"}
            ]
        });    
    });   
})(window.jQuery, window.app);
