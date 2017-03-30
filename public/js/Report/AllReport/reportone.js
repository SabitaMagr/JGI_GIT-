(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var initializeGrid = function (rows, cols) {
            var $tableContainer = $("#departmentMonthReport");
            $tableContainer.kendoGrid({
                dataSource: {
                    data: rows,
                    pageSize: 20
                },
                height: 600,
                sortable: true,
                pageable: true,
                columns: cols,
                detailInit: function (e) {
                    $tableContainer.block();
                    console.log('drillInitCBObj', e);
                    var departmentId = e.data.id;
                    app.pullDataById(document.wsDepartmentWise, {departmentId: departmentId}).then(function (response) {
                        $tableContainer.unblock();
                        console.log('departmentWiseEmployeeMonthlyR', response);
//                        console.log('departmentWiseEmployeeMonthlyRString', JSON.stringify(response));

                        var extractedDetailData = extractDetailData(response.data, departmentId);
                        console.log('extractedDetailData', extractedDetailData);
                        $("<div/>").appendTo(e.detailCell).kendoGrid({
                            dataSource: {
                                data: extractedDetailData.rows,
                                pageSize: 20
                            },
                            scrollable: false,
                            sortable: true,
                            pageable: true,
                            columns: extractedDetailData.cols
                        });
                        displayDataInBtnGroup('.custom-btn-group.' + departmentId);


                    }, function (error) {
                        $tableContainer.unblock();
                        console.log('departmentWiseEmployeeMonthlyE', error);
                    });
                }
            });
            displayDataInBtnGroup('.custom-btn-group');
        };

        var displayDataInBtnGroup = function (selector) {
            $(selector).each(function (k, group) {
                var $group = $(group);
                var data = JSON.parse($group.attr('data'));
                var $childrens = $group.children();
                var $present = $($childrens[0]);
                var $absent = $($childrens[1]);
                var $leave = $($childrens[2]);

                $present.html(data['IS_PRESENT']);
                $absent.html(data['IS_ABSENT']);
                $leave.html(data['ON_LEAVE']);
            });

        };



        var extractData = function (rawData) {
            var data = {};
            var column = {};

            for (var i in rawData) {
                if (typeof data[rawData[i].DEPARTMENT_ID] !== 'undefined') {
                    data[rawData[i].DEPARTMENT_ID].MONTHS[rawData[i].MONTH_EDESC] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE
                            });
                } else {
                    data[rawData[i].DEPARTMENT_ID] = {
                        DEPARTMENT_ID: rawData[i].DEPARTMENT_ID,
                        DEPARTMENT_NAME: rawData[i].DEPARTMENT_NAME,
                        MONTHS: {}
                    };
                    data[rawData[i].DEPARTMENT_ID].MONTHS[rawData[i].MONTH_EDESC] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE
                            });

                }
                if (typeof column[rawData[i].MONTH_ID] === 'undefined') {
                    var temp = rawData[i].MONTH_EDESC;
                    column[rawData[i].MONTH_ID] = {
                        field: temp,
                        title: rawData[i].MONTH_EDESC,
                        template: '<div data="#: ' + temp + ' #" class="btn-group widget-btn-list custom-btn-group">' +
                                '<a class="btn btn-default widget-btn custom-btn-present"></a>' +
                                '<a class="btn btn-danger widget-btn custom-btn-absent"></a>' +
                                '<a class="btn btn-info widget-btn custom-btn-leave"></a>' +
                                '</div>'
                    }

                }
            }
            var returnData = {rows: [], cols: []};

            returnData.cols.push({field: 'id', title: 'Id', width: 30});
            returnData.cols.push({field: 'department', title: 'Departments'});
            for (var k in column) {
                returnData.cols.push(column[k]);
            }

            for (var k in data) {
                var row = data[k].MONTHS;
                row['department'] = data[k].DEPARTMENT_NAME;
                row['id'] = data[k].DEPARTMENT_ID;
                returnData.rows.push(row);
            }
            return returnData;
        };
        app.pullDataById(document.wsUrl, {}).then(function (response) {
            console.log('departmentMonthlyReportResponse', response);
//            console.log('departmentMonthlyReportResponseString', JSON.stringify(response));
            if (response.success) {
                var extractedData = extractData(response.data);
                initializeGrid(extractedData.rows, extractedData.cols);
            }
        }, function (failure) {
            console.log('departmentMonthlyReportError', failure);
        });


        var extractDetailData = function (rawData, departmentId) {
            var data = {};
            var column = {};

            for (var i in rawData) {
                console.log('data', rawData[i]);
                if (typeof data[rawData[i].EMPLOYEE_ID] !== 'undefined') {
                    data[rawData[i].EMPLOYEE_ID].MONTHS[rawData[i].MONTH_EDESC] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE
                            });
                } else {
                    data[rawData[i].EMPLOYEE_ID] = {
                        EMPLOYEE_ID: rawData[i].EMPLOYEE_ID,
                        FULL_NAME: rawData[i].FULL_NAME,
                        MONTHS: {}
                    };
                    data[rawData[i].EMPLOYEE_ID].MONTHS[rawData[i].MONTH_EDESC] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE
                            });

                }
                if (typeof column[rawData[i].MONTH_ID] === 'undefined') {
                    var temp = rawData[i].MONTH_EDESC;
                    column[rawData[i].MONTH_ID] = {
                        field: temp,
                        title: rawData[i].MONTH_EDESC,
                        template: '<div data="#: ' + temp + ' #" class="btn-group widget-btn-list custom-btn-group ' + departmentId + '">' +
                                '<a class="btn btn-default widget-btn custom-btn-present"></a>' +
                                '<a class="btn btn-danger widget-btn custom-btn-absent"></a>' +
                                '<a class="btn btn-info widget-btn custom-btn-leave"></a>' +
                                '</div>'
                    }

                }
            }
            var returnData = {rows: [], cols: []};

            returnData.cols.push({field: 'employee', title: 'employees'});
            for (var k in column) {
                returnData.cols.push(column[k]);
            }

            for (var k in data) {
                var row = data[k].MONTHS;
                row['employee'] = data[k].FULL_NAME;
                returnData.rows.push(row);
            }
            return returnData;
        };

    });
})(window.jQuery, window.app);