(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#userTable").kendoGrid({
            excel: {
                fileName: "UserList.xlsx",
                filterable: true,
                allPages: true
            },
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
                {field: "FULL_NAME", title: "Employee Name", width: 200},
                {field: "USER_NAME", title: "User Name", width: 200},
                {field: "ROLE_NAME", title: "Role Name", width: 200},
                {title: "Action", width: 100}
            ]
        });

        app.searchTable('userTable', ['FULL_NAME', 'USER_NAME', 'ROLE_NAME']);

        app.pdfExport(
                'userTable',
                {
                    'FULL_NAME': 'Name',
                    'USER_NAME': 'UserName',
                    'ROLE_NAME': 'Role'
                }
        );

        $("#export").click(function (e) {
            var grid = $("#userTable").data("kendoGrid");
            grid.saveAsExcel();
        });

        $('#exportWithPassword').click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Employee Name"},
                        {value: "Username"},
                        {value: "Password"},
                        {value: "Role"},
                    ]
                }];
            for (var i = 0; i < document.users.length; i++) {
                var dataItem = document.users[i];
                rows.push({
                    cells: [
                        {value: dataItem.FULL_NAME},
                        {value: dataItem.USER_NAME},
                        {value: dataItem.PASSWORD},
                        {value: dataItem.ROLE_NAME},
                    ]
                });
            }

            var workbook = new kendo.ooxml.Workbook({
                sheets: [
                    {
                        columns: [
                            {autoWidth: true},
                            {autoWidth: true},
                        ],
                        title: "Users",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "Users(With Password).xlsx"});
        });
        window.app.UIConfirmations();
    });
})(window.jQuery, window.app);
