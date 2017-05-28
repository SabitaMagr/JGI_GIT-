(function ($,app) {
    'use strict';

    $(document).ready(function () {
        console.log(document.issue);
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
        $("#export").click(function (e) {
            var grid = $("#assetIssueTable").data("kendoGrid");
            grid.saveAsExcel();
        });
        window.app.UIConfirmations();


        app.addDatePicker($('#returnedDate'));

        $("#assetIssueTable").on("click", "#btnReturn", function () {
//            returnedDate
            
            $('#returnedDate').val('');
            var returnButton =$(this);
            
            var selectedassetId=returnButton.attr('data-assetid');
            var selectedIssueId=returnButton.attr('data-issueid');
            
            var selectedEmployee=returnButton.attr('data-employee');
            var selectedAsset=returnButton.attr('data-asset');
            var selectedQuantity=returnButton.attr('data-quantity');
            var selectedRdate=returnButton.attr('data-rdate');

            $('#returnEmployee').text(selectedEmployee);
            $('#returnAsset').text(selectedAsset);
            $('#returnQuantity').text(selectedQuantity);
            $('#rDate').text(selectedRdate);
            
//            console.log(selectedAsset);
//            console.log(selectedassetId);
//            console.log(selectedIssueId);
            
            $('#assetId').val(selectedassetId);
            $('#issueId').val(selectedIssueId);
            $('#issueBal').val(selectedQuantity);
            
          });
          
          
          app.searchTable('assetIssueTable',['ASSET_EDESC','FIRST_NAME','ISSUE_DATE','QUANTITY','RETURN_DATE','RETURNED_DATE']);


//          $("#kendoSearchField").keyup(function () {
//            var val = $('#kendoSearchField').val();
//            console.log(val);
//            $("#assetIssueTable").data("kendoGrid").dataSource.filter({
//                logic: "or",
//                filters: [
//                    {
//                        field: "ASSET_EDESC",
//                        operator: "contains",
//                        value: val
//                    },
//                    {
//                        field: "FIRST_NAME",
//                        operator: "contains",
//                        value: val
//                    },
//                    {
//                        field: "ISSUE_DATE",
//                        operator: "contains",
//                        value: val
//                    },
//                    {
//                        field: "QUANTITY",
//                        operator: "contains",
//                        value: val
//                    },
//                    {
//                        field: "RETURN_DATE",
//                        operator: "contains",
//                        value: val
//                    },
//                    {
//                        field: "RETURNED_DATE",
//                        operator: "contains",
//                        value: val
//                    },
//                ]
//            });
//        });
        


    });



})(window.jQuery, window.app);