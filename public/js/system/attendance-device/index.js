(function ($) {
    'use strict';
    $(document).ready(function () {    
        $("#attendanceDevice").kendoGrid({
            excel: {
                fileName: "AttendaceDeviceList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.attendanceDevice,
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
                {field: "DEVICE_NAME", title: "Device Name",width:200},
                {field: "DEVICE_IP", title: "Device IP",width:200},
                {field: "DEVICE_LOCATION", title: "Device Location",width:200},
                {field: "ISACTIVE", title: "Status",width:200},
                    {title: "Action",width:100}
            ]
        }); 
        $("#export").click(function (e) {
            var grid = $("#attendanceDevice").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();
    });   
})(window.jQuery, window.app);
