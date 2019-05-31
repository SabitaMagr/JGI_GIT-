(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $table = $('#table');
//        var map = {};
//        var exportType = {
//            "ACCOUNT_NO": "STRING",
//        };
//        
        var exportMap = {
            "DEPARTMENT_NAME": "Department"
        };

        var columns = [
            {field: "DEPARTMENT_NAME", title: "Department", width: 200}
        ];

//
        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });


        $.each(document.ruleList, function (key, value) {
            var signFn = function ($type) {
                var sign = "";
                switch ($type) {
                    case "A":
                        sign = "+";
                        break;
                    case "D":
                        sign = "-";
                        break;
                    case "V":
                        sign = ".";
                        break;
                }
                return sign;
            };
            columns.push({field: "P_" + value['PAY_ID'], title: value['PAY_EDESC'] + "(" + signFn(value['PAY_TYPE_FLAG']) + ")", width: 150});
            exportMap["P_" + value['PAY_ID']] = value['PAY_EDESC'] + "(" + signFn(value['PAY_TYPE_FLAG']) + ")";
        });



        function loadKendo(treeData, columns) {
            $table.kendoTreeList({
                toolbar: ["excel"],
                excel: {
                    fileName: "Kendo UI TreeList Export.xlsx",
                    filterable: true,
                    allPages:true
                },
                dataSource: {
                    data: treeData,
                    schema: {
                        model: {
                            id: "DEPARTMENT_ID",
                            parentId: "PARENT_DEPARTMENT",
                            fields: {
                                PARENT_DEPARTMENT: {field: "PARENT_DEPARTMENT", nullable: true},
                                DEPARTMENT_ID: {field: "DEPARTMENT_ID", type: "number" }
                            },
//                            expanded: true
                        }
                    }
                },
                height: 540,
                filterable: true,
                sortable: true,
                columns: columns,
                pageable: {
                    pageSize: 15,
                    pageSizes: true
                }
            });
        }
        loadKendo([], columns);



        $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            app.serverRequest(document.pulldepartmentWiseLink, q).then(function (response) {
                if (response.success) {
                    $("#table").data("kendoTreeList").dataSource.data(response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

//        $('#excelExport').on('click', function () {
//            $table.table2excel({
//                exclude: ".noExl",
//                name: "leave-card",
//                filename: "leave-report-card"
//            });
//        });
//        
//        $('#pdfExport').on('click', function () {
//            app.exportDomToPdf2("summaryDiv");
//        });








    });
})(window.jQuery, window.app);


