(function ($) {
    'use strict';
    $(document).ready(function () {

        var $table = $('#table');

        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["DEVICE_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["DEVICE_ID"],
                'url': document.deleteLink
            }
        };

        var columns = [
            {field: "DEVICE_NAME", title: "Device", width: 200},
            {field: "DEVICE_IP", title: "Device IP", width: 200},
            {field: "PING_STATUS", title: "Ping Status", width: 200},
            {field: "DEVICE_LOCATION", title: "Device Location", width: 200},
            {field: "ISACTIVE", title: "Active", width: 200},
            {title: "Action", width: 100, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];

        app.initializeKendoGrid($table, columns);

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });



//        $("#attendanceDevice").kendoGrid({
//            excel: {
//                fileName: "AttendaceDeviceList.xlsx",
//                filterable: true,
//                allPages: true
//            },
//            dataSource: {
//                data: document.attendanceDevice,
//                pageSize: 20
//            },
//            height: 450,
//            scrollable: true,
//            sortable: true,
//            filterable: true,
//            pageable: {
//                input: true,
//                numeric: false
//            },
//            rowTemplate: kendo.template($("#rowTemplate").html()),
//            columns: [
//                {field: "DEVICE_NAME", title: "Device",width:200},
//                {field: "DEVICE_IP", title: "Device IP",width:200},
//                {field: "PING_STATUS", title: "Ping Status",width:200},
//                {field: "DEVICE_LOCATION", title: "Device Location",width:200},
//                {field: "ISACTIVE", title: "Active",width:200},
//                    {title: "Action",width:100}
//            ]
//        }); 
//        
//        app.searchTable('attendanceDevice',['DEVICE_NAME','DEVICE_IP','DEVICE_LOCATION','ISACTIVE']);
//        
//        app.pdfExport(
//                'attendanceDevice',
//                {
//                    'DEVICE_NAME': 'Device Name',
//                    'DEVICE_IP': 'DeviceIp',
//                    'DEVICE_LOCATION': 'DeviceLocation',
//                    'ISACTIVE': 'IsActive'
//                }
//        );
//        
//        $("#export").click(function (e) {
//            var grid = $("#attendanceDevice").data("kendoGrid");
//            grid.saveAsExcel();
//        });
    });
})(window.jQuery, window.app);
