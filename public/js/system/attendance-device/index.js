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
            console.log(error);
        });

        var map = {
            'DEVICE_NAME': 'Device',
            'DEVICE_IP': 'Device IP',
            'PING_STATUS': 'Ping Status',
            'DEVICE_LOCATION': 'Device Location',
            'ISACTIVE': 'Active',
        };

        app.searchTable($table, ['DEVICE_NAME', 'DEVICE_IP', 'PING_STATUS', 'DEVICE_LOCATION', 'ISACTIVE']);


        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Device List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Device List.pdf');
        });


        $('#pingBtn').on('click', function () {
            app.serverRequest(document.pullDeviceWithPingStatus, {}).then(function (response) {
                app.renderKendoGrid($table, response.data);
            }, function (error) {
                console.log(error);
            });
        });



    });
})(window.jQuery, window.app);
