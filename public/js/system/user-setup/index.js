(function ($) {
    'use strict';
    $(document).ready(function () {    
       
        $("#userTable").kendoGrid({
            dataSource: {
                data: document.users,
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
                {field: "FIRST_NAME", title: "Employee Name"},
                {field: "USER_NAME", title: "User Name"},
                {field: "ROLE_NAME", title: "Role Name"},
                {title: "Action"}
            ]
        });    
    });   
})(window.jQuery, window.app);
