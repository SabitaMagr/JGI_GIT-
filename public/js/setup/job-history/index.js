(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate');
    });
})(window.jQuery, window.app);

angular.module('hris', [])
        .controller('jobHistoryController', function ($scope, $http) {
            var $table = $("#jobHistoryTable");
            var $excelExport = $('#export');
            var $pdfExport = $('#pdfExport');

            var data = [];
            var columns = [
                {field: "FULL_NAME", title: "Employee Name"},
                {field: "SERVICE_EVENT_TYPE_NAME", title: "Service Event Type"},
                {title: "Event Date", columns: [
                        {field: "EVENT_DATE_AD", title: "AD"},
                        {field: "EVENT_DATE_BS", title: "BS"},
                    ]},
                {title: "Effective From", columns: [
                        {field: "START_DATE_AD", title: "AD"},
                        {field: "START_DATE_BS", title: "BS"}
                    ]},
                {title: "Effective To", columns: [
                        {field: "END_DATE_AD", title: "AD"},
                        {field: "END_DATE_BS", title: "BS"},
                    ]},
                {field: "TO_BRANCH_NAME", title: "Branch"},
                {field: "TO_DEPARTMENT_NAME", title: "Department"},
                {field: "TO_POSITION_NAME", title: "Position"},
                {field: "TO_DESIGNATION_TITLE", title: "Designation"},
                {field: "TO_SERVICE_NAME", title: "Service Type"},
                {field: "JOB_HISTORY_ID", title: "Action", template: `
                        <a class="btn-edit"
                        href="` + document.editLink + `/#: JOB_HISTORY_ID #" style="height:17px;">
                        <i class="fa fa-edit"></i>
                        </a>
                        <a class="btn-delete confirmation"
                        href="` + document.deleteLink + `/#: JOB_HISTORY_ID #" id="bs_#:JOB_HISTORY_ID #" style="height:17px;">
                        <i class="fa fa-trash-o"></i></a>
                        </a>`}
            ];
            app.initializeKendoGrid($table, columns, "Employee Service History");
            app.searchTable('jobHistoryTable', ['FULL_NAME', 'START_DATE', 'SERVICE_EVENT_TYPE_NAME', 'TO_SERVICE_NAME', 'TO_BRANCH_NAME', 'TO_DEPARTMENT_NAME', 'TO_DESIGNATION_TITLE', 'TO_POSITION_NAME']);

            var exportKV = {
                'FULL_NAME': 'Employee Name',
                'SERVICE_EVENT_TYPE_NAME': 'Service Event Type',
                'EVENT_DATE_AD': 'Event Date(AD)',
                'EVENT_DATE_BS': 'Event Date(BS)',
                'START_DATE_AD': 'Effective From(AD)',
                'START_DATE_BS': 'Effective From(BS)',
                'END_DATE_AD': 'Effective To(AD)',
                'END_DATE_BS': 'Effective To(BS)',
                'TO_BRANCH_NAME': 'Branch',
                'TO_DEPARTMENT_NAME': 'Department',
                'TO_POSITION_NAME': 'Position',
                'TO_DESIGNATION_TITLE': 'Designation',
                'TO_SERVICE_NAME': 'Service',
            };
            $excelExport.on('click', function () {
                app.excelExport(data, exportKV, "Employee Service History");
            });
            $pdfExport.on('click', function () {
                app.exportToPDF(data, exportKV, "Employee Service History");
            });

            $scope.view = function () {
                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId1')).val();
                var fromDate = angular.element(document.getElementById('fromDate')).val();
                var toDate = angular.element(document.getElementById('toDate')).val();
                var employeeTypeId = angular.element(document.getElementById('employeeTypeId')).val();
                App.blockUI({target: "#hris-page-content"});
                window.app.pullDataById(document.getEmployeeLatestServiceWS, {
                    'fromDate': fromDate,
                    'toDate': toDate,
                    'employeeId': employeeId,
                    'serviceEventTypeId': serviceEventTypeId,
                    'companyId': companyId,
                    'branchId': branchId,
                    'departmentId': departmentId,
                    'designationId': designationId,
                    'positionId': positionId,
                    'serviceTypeId': serviceTypeId,
                    'employeeTypeId': employeeTypeId
                }).then(function (success) {
                    App.unblockUI("#hris-page-content");
                    data = success.data;
                    app.renderKendoGrid($table, data);
                    window.app.scrollTo('jobHistoryTable');
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                });
            }
        });
