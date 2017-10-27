(function ($) {
    'use strict';
    $(document).ready(function () {
        console.log(document.appraisals);
        $("#appraisalListTable").kendoGrid({
            excel: {
                fileName: "AppraisalList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.appraisals,
                page: 1,
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: true,
//            rowTemplate: kendo.template($("#rowTemplate").html()),
            columns: [
                {field: "APPRAISAL_EDESC", title: "Appraisal Name"},
                {field: "APPRAISAL_TYPE_EDESC", title: "Appraisal Type"},
                {field: "STAGE_EDESC", title: "Current Status"},
                {title: "Start Date",
                    columns: [{
                            field: "START_DATE",
                            title: "English",
                            template: "<span>#: (START_DATE == null) ? '-' : START_DATE #</span>"},
                        {field: "START_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (START_DATE_N == null) ? '-' : START_DATE_N #</span>"}
                    ]},
                {title: "End Date",
                    columns: [{
                            field: "END_DATE",
                            title: "English",
                            template: "<span>#: (END_DATE == null) ? '-' : END_DATE #</span>"},
                        {field: "END_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (END_DATE_N == null) ? '-' : END_DATE_N #</span>"}
                    ]},
                
                {field: ["APPRAISAL_ID","ALLOW_ADD","ALLOW_EDIT"], title: "Action", template: `<span># if(ALLOW_ADD){ #
        <a class="btn-edit" href="`+ document.viewLink + ` /#:APPRAISAL_ID#" style="height:17px;">
        <i class="fa fa-search-plus"></i>
        </a>
        # }else if(ALLOW_EDIT){ #
        <a class="btn-edit" href="`+ document.viewLink + `/#:APPRAISAL_ID#" title="view" style="height:17px;">
        <i class="fa fa-search-plus"></i>
        </a>
        #} #
        </span>`
                }],
        });
        
        app.searchTable('appraisalListTable',['APPRAISAL_EDESC','APPRAISAL_TYPE_EDESC','STAGE_EDESC','START_DATE', 'START_DATE_N','END_DATE', 'END_DATE_N']);
        
        app.pdfExport(
                        'appraisalListTable',
                        {
                            'APPRAISAL_EDESC': ' Appraisal',
                            'APPRAISAL_TYPE_EDESC': 'Type',
                            'STAGE_EDESC': 'Stage',
                            'START_DATE': 'Start Date(AD)',
                            'START_DATE_N': 'Start Date(BS)',
                            'END_DATE': 'End Date(AD)',
                            'END_DATE_N': 'End Date(BS)',
                        }
                );
        
       $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Appraisal"},
                        {value: "Type"},
                        {value: "Stage"},
                        {value: "Start Date(AD)"},
                        {value: "Start Date(BS)"},
                        {value: "End Date(AD)"},
                        {value: "End Date(BS)"}
                    ]
                }];
              var dataSource = $("#appraisalListTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();

            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];
                rows.push({
                    cells: [
                        {value: dataItem.APPRAISAL_EDESC},
                        {value: dataItem.APPRAISAL_TYPE_EDESC},
                        {value: dataItem.STAGE_EDESC},
                        {value: dataItem.START_DATE},
                        {value: dataItem.START_DATE_N},
                        {value: dataItem.END_DATE},
                        {value: dataItem.END_DATE_N}

                    ]
                });
            }
            excelExport(rows);
            e.preventDefault();
        });
        function excelExport(rows) {
            var workbook = new kendo.ooxml.Workbook({
                sheets: [
                    {
                        columns: [
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Appraisal List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AppraisalList.xlsx"});
        }
    });
})(window.jQuery);


angular.module("hris",[])
        .controller("appraisalList",function($scope,$http,$window){
            $scope.msg =  $window.localStorage.getItem("msg");
            if($window.localStorage.getItem("msg")){
                window.toastr.success($scope.msg, "Notifications");
            }
            $window.localStorage.removeItem("msg");
});