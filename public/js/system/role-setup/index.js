(function ($) {
    'use strict';
    $(document).ready(function () {    
       
        $("#roleTable").kendoGrid({
            dataSource: {
                data: document.roles,
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
                {field: "SN", title: "S.N."},
                {field: "ROLE_NAME", title: "Role Name"},
                {title: "Action"}
            ]
        });    
    });   
})(window.jQuery, window.app);
