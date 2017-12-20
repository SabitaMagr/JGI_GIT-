(function ($) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali("nepaliFromDate1", "fromDate1", "nepaliToDate1", "toDate1", null, true);
    });
})(window.jQuery);

angular.module("hris", [])
        .controller("appraisalList", function ($scope, $http, $window) {
            var displayKendoFirstTime = true;
            $scope.view = function () {
                var userId = angular.element(document.getElementById('userId')).val();
                var reportType = angular.element(document.getElementById('reportType')).val();
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var appraisalId = angular.element(document.getElementById('appraisalId')).val();
                var appraisalStageId = angular.element(document.getElementById('appraisalStageId')).val();
                var fromDate = angular.element(document.getElementById('fromDate1')).val();
                var toDate = angular.element(document.getElementById('toDate1')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAppraisalViewList',
                    data: {
                        'employeeId': employeeId,
                        'companyId': companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'appraisalId': appraisalId,
                        'appraisalStageId': appraisalStageId,
                        'fromDate': fromDate,
                        'toDate': toDate,
                        'reportType': reportType,
                        'userId': userId
                    }
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#appraisalListTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });
            }

            var objectiveSet = `
        <span id="#if(KPI_ANS_NUM>0){ #green#}else{#red#}#">
            #if(KPI_ANS_NUM>0 && KPI_SETTING=='Y'){   #
            #= "&\\#10004;" #
            # }else if(KPI_SETTING=='N'){ #
            #='-'#
            #}else{ #
            #= "&\\#10006;" #
            # } #
        </span>
            `;
            var objectiveApproved = `
        <span id="#if(KPI_APPROVED_DATE==null){#red#}else{##}#">
            #if(KPI_APPROVED_DATE!=null){   #
            #= KPI_APPROVED_DATE #
            # }else if(KPI_SETTING=='N'){ #
            #='-'#
            # }else{ #
            #= "&\\#10006;" #
            # } #
        </span>
`;
            var appraiseeSelfRating = `
        <span id="#if(KPI_SELF_RATING_NUM>0){ #green#}else{#red#}#">
            #if(KPI_SELF_RATING_NUM>0){   #
            #= "&\\#10004;" #
            # }else if(KPI_SETTING=='N'){ #
            #='-'#
            # }else{ #
            #= "&\\#10006;" #
            # } #
        </span>
`;
            var appraisalEvaluation = `
        <span id="#if(APPRAISED_BY!=null){ #green#}else{#red#}#">
            #if(APPRAISED_BY!=null){   #
            #= "&\\#10004;" #
            # }else{ #
            #= "&\\#10006;" #
            # } #
        </span>
`;
            var reviewerView = `
        <span id="#if(REVIEWED_BY!=null){ #green#}else{#red#}#">
            #if(REVIEWED_BY!=null){   #
            #= "&\\#10004;" #
            #}else if(REVIEWED_BY==null && DEFAULT_RATING=='N'){ #
            #= "&\\#10006;" #
            # }else{#-#}#
        </span>
`;
            var finalRating = `
        <span id="#if(APPRAISER_OVERALL_RATING!=null){ #green#}else{#red#}#">
            #if(APPRAISER_OVERALL_RATING!=null){   #
            #= "&\\#10004;" #
            # }else if(KPI_SETTING=='N'){ #
            #='-'#
            # }else{ #
            #= "&\\#10006;" #
            # } #
        </span>
`;
            var rating = `
        <span>    
        #: (APPRAISER_OVERALL_RATING == null) ? '-' : APPRAISER_OVERALL_RATING #
        </span>
`;

            var superReviewerAgree = `        
        <span id="#if(SUPER_REVIEWER_AGREE!='Y'){ #green#}else{#red#}#">
            #if(SUPER_REVIEWER_AGREE=='Y'){   #
            #= "&\\#10004;" #
            #}else if(SUPER_REVIEWER_AGREE=='N'){ #
            #= "&\\#10006;" #
            # }else{#-#}#
        </span>`;

            var appraiseeAgree = `
        <span id="#if(APPRAISEE_AGREE=='Yes'){ #green#}else{#red#}#">  
            #if(APPRAISEE_AGREE=='Yes'){   #
            #= "&\\#10004;" #
            # }else if(APPRAISEE_AGREE=='No'){ #
            #= "&\\#10006;" #
            # }else{#-#}#
        </span>
`;
            var action = `
        <a class="btn-edit"
        href="` + document.viewLink + `/#:APPRAISAL_ID#/#:EMPLOYEE_ID#/1" tital="view" style="height:17px;">
        <i class="fa fa-search-plus"></i>
        </a>
`;
            $scope.initializekendoGrid = function () {
                $("#appraisalListTable").kendoGrid({
                    excel: {
                        fileName: "AppraisalViewList.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    height: 450,
                    scrollable: true,
                    sortable: true,
                    filterable: true,
                    pageable: {
                        input: true,
                        numeric: false
                    },
                    dataBound: gridDataBound,
//                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "EMPLOYEE_CODE", title: "Code", width: 50, locked: true},
                        {field: "FULL_NAME", title: "Employee", width: 150, locked: true},
                        {field: "APPRAISAL_EDESC", title: "Appraisal", width: 120, locked: true},
                        {field: "APPRAISAL_TYPE_EDESC", title: "Appraisal Type", width: 150},
                        {field: "STAGE_EDESC", title: "Current Stage", width: 140},
                        {field: "START_DATE", title: "Start Date", width: 120},
                        {field: "END_DATE", title: "End Date", width: 100},
                        {field: ["KPI_ANS_NUM", "KPI_SETTING"], title: "Objective Set?", width: 130, template: objectiveSet},
                        {field: ["KPI_APPROVED_DATE", "KPI_SETTING"], title: "Objective Approved?", width: 170, template: objectiveApproved},
                        {field: ["KPI_SELF_RATING_NUM", "KPI_SETTING"], title: "Appraisee Self Rating?", width: 170, template: appraiseeSelfRating},
                        {field: ["APPRAISED_BY"], title: "Appraiser Evaluation?", width: 170, template: appraisalEvaluation},
                        {field: ["REVIEWED_BY", "DEFAULT_RATING"], title: "Reviewer View?", width: 140, template: reviewerView},
                        {field: ["APPRAISER_OVERALL_RATING", "KPI_SETTING"], title: "Final Rating?", width: 120, template: finalRating},
                        {field: "APPRAISER_OVERALL_RATING", title: "Rating", width: 100, template: rating},
                        {field: "SUPER_REVIEWER_AGREE", title: "Super Reviewer Agree", width: 170, template: superReviewerAgree},
                        {field: "APPRAISEE_AGREE", title: "Appraisee Agree", width: 140, template: appraiseeAgree},
                        {field: "APPRAISER_NAME", title: "Appraiser Name", width: 150},
                        {field: "REVIEWER_NAME", title: "Reviewer Name", width: 150},
                        {field: ["APPRAISAL_ID", "EMPLOYEE_ID"], title: "Action", width: 90, template: action}
                    ]
                });

                app.searchTable('appraisalListTable', ['FULL_NAME', 'APPRAISAL_EDESC', 'APPRAISAL_TYPE_EDESC', 'STAGE_EDESC', 'START_DATE', 'END_DATE', 'APPRAISER_NAME', 'REVIEWER_NAME']);

                app.pdfExport(
                        'appraisalListTable',
                        {
                            'FULL_NAME': 'Name',
                            'APPRAISAL_EDESC': 'Appraisal',
                            'APPRAISAL_TYPE_EDESC': 'Appraisal Type',
                            'STAGE_EDESC': 'Current Stage',
                            'START_DATE': 'Start Date',
                            'END_DATE': 'End Date',
                            'APPRAISER_OVERALL_RATING': 'Rating',
                            'APPRAISER_NAME': 'Appraiser Name',
                            'ALT_APPRAISER_NAME': 'Alt. Appraiser Name',
                            'REVIEWER_NAME': 'Reviewer Name',
                            'ALT_REVIEWER_NAME': 'Alt. Reviewer Name'

                        });



                function gridDataBound(e) {
                    var grid = e.sender;
                    if (grid.dataSource.total() == 0) {
                        var colCount = grid.columns.length;
                        $(e.sender.wrapper)
                                .find('tbody')
                                .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
                    }
                }
                ;

                $("#export").click(function (e) {
                    var rows = [{
                            cells: [
                                {value: "Employee Name"},
                                {value: "Appraisal Name"},
                                {value: "Appraisal Type Name"},
                                {value: "Current Stage"},
                                {value: "From Date"},
                                {value: "To Date"},
                                {value: "Rating"},
                                {value: "Appraiser Name"},
                                {value: "Alt. Appraiser Name"},
                                {value: "Reviewer Name"},
                                {value: "Alt. Reviewer Name"},
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
                        var mn1 = dataItem.MN1 != null ? " " + dataItem.MN1 + " " : " ";
                        var mn2 = dataItem.MN2 != null ? " " + dataItem.MN2 + " " : " ";
                        rows.push({
                            cells: [
                                {value: dataItem.FULL_NAME},
                                {value: dataItem.APPRAISAL_EDESC},
                                {value: dataItem.APPRAISAL_TYPE_EDESC},
                                {value: dataItem.STAGE_EDESC},
                                {value: dataItem.START_DATE},
                                {value: dataItem.END_DATE},
                                {value: dataItem.APPRAISER_OVERALL_RATING},
                                {value: dataItem.APPRAISER_NAME},
                                {value: dataItem.ALT_APPRAISER_NAME},
                                {value: dataItem.REVIEWER_NAME},
                                {value: dataItem.ALT_REVIEWER_NAME},
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
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                    {autoWidth: true},
                                ],
                                title: "Appraisal List",
                                rows: rows
                            }
                        ]
                    });
                    kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AppraisalViewList.xlsx"});
                }
            };

            $scope.msg = $window.localStorage.getItem("msg");
            if ($window.localStorage.getItem("msg")) {
                window.toastr.success($scope.msg, "Notifications");
            }
            $window.localStorage.removeItem("msg");
        });