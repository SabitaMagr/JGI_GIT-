(function ($, app) {
    'use strict';
    $(document).ready(function () {
        
        
        
        
                    $("#grid").kendoGrid({
                        height: 550,
                        sortable: true
                    });

//
//        console.log(document.list);
//        console.log(document.list[0]);


//        $("#TrainingAttendance").kendoGrid({
//            dataSource: {
//                data: document.list,
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
////                {field: "COMPANY_CODE", title: "Company Code",width:120},
//                {field: "FIRST_NAME", title: "Name", width: 400},
////                {title: "Action", width: 110}
//            ]
//        });


    });
})(window.jQuery, window.app);