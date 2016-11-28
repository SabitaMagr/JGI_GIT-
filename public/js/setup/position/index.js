(function ($) {
    'use strict';
    $(document).ready(function () {
       
        $("#positionTable").kendoGrid({
            dataSource: {
                data: document.positions,
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
                {field: "POSITION_NAME", title: "Position Name"},
                {title: "Action"}
            ]
        }); 
    
    });   
})(window.jQuery, window.app);