(function ($, app) {
    'use strict';

    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali('returnedDate', 'returnedDateNepali');
        
        $("#assetReturnForm").submit(function () {
                        App.blockUI({target: "form"});
            });
        
        
        

//        $("#export").click(function (e) {
//            var grid = $("#assetIssueTable").data("kendoGrid");
//            grid.saveAsExcel();
//        });
//        window.app.UIConfirmations();



        $("#assetIssueTable").on("click", "#btnReturn", function () {
            $('#myModal').modal('show');
            

            $('#returnedDate').val('');
            $('#returnedDateNepali').val('');
            var returnButton = $(this);

            var selectedassetId = returnButton.attr('data-assetid');
            var selectedIssueId = returnButton.attr('data-issueid');

            var selectedEmployee = returnButton.attr('data-employee');
            var selectedAsset = returnButton.attr('data-asset');
            var selectedQuantity = returnButton.attr('data-quantity');
            var selectedRdate = returnButton.attr('data-rdate');

            if (selectedRdate == 'null') {
                selectedRdate = '-';
            }

            $('#returnEmployee').text(selectedEmployee);
            $('#returnAsset').text(selectedAsset);
            $('#returnQuantity').text(selectedQuantity);
            $('#rDate').text(selectedRdate);

//            console.log(selectedAsset);
//            console.log(selectedassetId);
//            console.log(selectedIssueId);

            console.log(selectedassetId);
            
            $('#assetId').val(selectedassetId);
            $('#issueId').val(selectedIssueId);
            $('#issueBal').val(selectedQuantity);

        });


//        app.searchTable('assetIssueTable', ['ASSET_EDESC', 'FIRST_NAME', 'ISSUE_DATE', 'QUANTITY', 'RETURN_DATE', 'RETURNED_DATE']);
//
//        app.pdfExport(
//                'assetIssueTable',
//                {
//                    'ASSET_EDESC': 'Asset',
//                    'FIRST_NAME': 'Name',
//                    'MIDDLE_NAME': 'Middle',
//                    'LAST_NAME': 'last',
//                    'ISSUE_DATE': 'Issue Date',
//                    'QUANTITY': 'Quantity',
//                    'RETURN_DATE': 'Return Date',
//                    'RETURNED_DATE': 'Retutned Date'
//                });






    });



})(window.jQuery, window.app);



angular.module('hris', [])
        .controller("assetListController", function ($scope, $http) {
            
            var displayKendoFirstTime = true;

            $scope.view = function () {
                console.log('view');

                var employeeId = angular.element(document.getElementById('employeeId')).val();
                var companyId = angular.element(document.getElementById('companyId')).val();
                var branchId = angular.element(document.getElementById('branchId')).val();
                var departmentId = angular.element(document.getElementById('departmentId')).val();
                var designationId = angular.element(document.getElementById('designationId')).val();
                var positionId = angular.element(document.getElementById('positionId')).val();
                var serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId')).val();
                var serviceTypeId = angular.element(document.getElementById('serviceTypeId')).val();
                var assetTypeId = angular.element(document.getElementById('assetTypeId')).val();
                var assetId = angular.element(document.getElementById('asset')).val();
                var assetStatusId = angular.element(document.getElementById('assetStatusId')).val();



                window.app.pullDataById(document.url, {
                    data: {
                        'employeeId': employeeId,
                        'companyId': companyId,
                        'branchId': branchId,
                        'departmentId': departmentId,
                        'designationId': designationId,
                        'positionId': positionId,
                        'serviceTypeId': serviceTypeId,
                        'serviceEventTypeId': serviceEventTypeId,
                        'assetTypeId': assetTypeId,
                        'assetId': assetId,
                        'assetStatusId': assetStatusId
                    }
                }).then(function (success) {
                    console.log(success);
                    App.unblockUI("#hris-page-content");
                    if (displayKendoFirstTime) {
                        $scope.initializekendoGrid();
                        displayKendoFirstTime = false;
                    }
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#assetIssueTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                }, function (failure) {
                    App.unblockUI("#hris-page-content");
                    console.log(failure);
                });



            }


            $scope.initializekendoGrid = function () {
                $("#assetIssueTable").kendoGrid({
                    excel: {
                        fileName: "AssetIssueList.xlsx",
                        filterable: true,
                        allPages: true
                    },
                    dataSource: {
                        data: document.issue,
                        page: 1,
                    },
                    height: 450,
                    scrollable: true,
                    sortable: true,
                    filterable: true,
                    pageable: true,
                    rowTemplate: kendo.template($("#rowTemplate").html()),
                    columns: [
                        {field: "ASSET_EDESC", title: "Asset", width: 120},
                        {field: "FIRST_NAME", title: "Employee ", width: 140},
                        {field: "ISSUE_DATE", title: "Issue Date", width: 90},
                        {field: "QUANTITY", title: "Quantity ", width: 80},
                        {field: "RETURN_DATE", title: "ReturnDate", width: 130},
                        {field: "RETURNED_DATE", title: "ReturnedDate", width: 130},
                        {title: "Action", width: 120}
                    ],
                });
                
                       app.searchTable('assetIssueTable', ['ASSET_EDESC', 'FIRST_NAME', 'ISSUE_DATE', 'QUANTITY', 'RETURN_DATE', 'RETURNED_DATE']);

        app.pdfExport(
                'assetIssueTable',
                {
                    'ASSET_EDESC': 'Asset',
                    'FIRST_NAME': 'Name',
                    'MIDDLE_NAME': 'Middle',
                    'LAST_NAME': 'last',
                    'ISSUE_DATE': 'Issue Date',
                    'QUANTITY': 'Quantity',
                    'RETURN_DATE': 'Return Date',
                    'RETURNED_DATE': 'Retutned Date'
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



                window.app.UIConfirmations();



            };







        });

