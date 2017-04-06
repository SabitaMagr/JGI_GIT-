(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $tableContainer = $("#reportTable");
        var extractDetailData = function (rawData, departmentId) {
            var data = {};
            var column = {};
            for (var i in rawData) {
                if (typeof data[rawData[i].EMPLOYEE_ID] !== 'undefined') {
                    data[rawData[i].EMPLOYEE_ID].DAYS[rawData[i].FORMATTED_ATTENDANCE_DT] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE,
                                IS_DAYOFF: rawData[i].IS_DAYOFF
                            });
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_ABSENT = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_ABSENT + parseFloat(rawData[i].IS_ABSENT);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_PRESENT = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_PRESENT + parseFloat(rawData[i].IS_PRESENT);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.ON_LEAVE = data[rawData[i].EMPLOYEE_ID].TOTAL.ON_LEAVE + parseFloat(rawData[i].ON_LEAVE);
                    data[rawData[i].EMPLOYEE_ID].TOTAL.IS_DAYOFF = data[rawData[i].EMPLOYEE_ID].TOTAL.IS_DAYOFF + parseFloat(rawData[i].IS_DAYOFF);
                } else {
                    data[rawData[i].EMPLOYEE_ID] = {
                        EMPLOYEE_ID: rawData[i].EMPLOYEE_ID,
                        FULL_NAME: rawData[i].FULL_NAME,
                        DAYS: {},
                        TOTAL: {
                            IS_ABSENT: parseFloat(rawData[i].IS_ABSENT),
                            IS_PRESENT: parseFloat(rawData[i].IS_PRESENT),
                            ON_LEAVE: parseFloat(rawData[i].ON_LEAVE),
                            IS_DAYOFF: parseFloat(rawData[i].IS_DAYOFF)
                        }
                    };
                    data[rawData[i].EMPLOYEE_ID].DAYS[rawData[i].FORMATTED_ATTENDANCE_DT] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE,
                                IS_DAYOFF: rawData[i].IS_DAYOFF
                            });

                }
                if (typeof column[rawData[i].FORMATTED_ATTENDANCE_DT] === 'undefined') {
                    var temp = rawData[i].FORMATTED_ATTENDANCE_DT;
                    column[rawData[i].FORMATTED_ATTENDANCE_DT] = {
                        field: temp,
                        title: "" + rawData[i].DAY_COUNT,
                        template: '<span data="#: ' + temp + ' #" class="daily-attendance"></span>'
                    }

                }
            }
            var returnData = {rows: [], cols: []};

            returnData.cols.push({
                field: 'employee',
                title: 'employees'
            });
            for (var k in column) {
                returnData.cols.push(column[k]);
            }
            returnData.cols.push({
                field: 'total',
                title: 'Total',
                template: '<div data="#: total #" class="btn-group widget-btn-list total-attendance">' +
                        '<a class="btn widget-btn custom-btn-present totalbtn"></a>' +
                        '<a class="btn widget-btn custom-btn-absent totalbtn"></a>' +
                        '<a class="btn widget-btn custom-btn-leave totalbtn"></a>' +
                        '</div>'});

            for (var k in data) {
                var row = data[k].DAYS;
                row['employee'] = data[k].FULL_NAME;
                returnData.rows.push(row);
                row['total'] = JSON.stringify(data[k].TOTAL);
            }
            return returnData;
        };
        var displayDataInBtnGroup = function (selector) {
            $(selector).each(function (k, group) {
                var $group = $(group);
                var data = JSON.parse($group.attr('data'));
                if (data.IS_PRESENT == 1) {
                    $group.html('P');
                    $group.parent().addClass('bg-green');
                } else {
                    if (data.IS_ABSENT == 1) {
                        $group.html('A');
                        $group.parent().addClass('bg-red1 textcolor1');

                    } else {
                        if (data.ON_LEAVE == 1) {
                            $group.html('L');
                            $group.parent().addClass('bg-blue1 textcolor2');

                        } else {
                            $group.html('H');
                            $group.parent().addClass('bg-white1 textcolor3 ');
                        }

                    }

                }
//                $group.html((data.IS_PRESENT == 1) ? 'P' : ((data.IS_ABSENT == 1) ? 'A' : (data.ON_LEAVE == 1) ? 'L' : 'H'));
//                $group.parent().addClass('bg-red');
            });

        };
        var displayTotalInGrid = function (selector) {
            $(selector).each(function (k, group) {
                var $group = $(group);
                var data = JSON.parse($group.attr('data'));
                var $childrens = $group.children();
                var $present = $($childrens[0]);
                var $absent = $($childrens[1]);
                var $leave = $($childrens[2]);

                var presentDays = parseFloat(data['IS_PRESENT']);
                var absentDays = parseFloat(data['IS_ABSENT']);
                var leaveDays = parseFloat(data['ON_LEAVE']);

                var total = presentDays + absentDays + leaveDays;

                $present.attr('title', data['IS_PRESENT']);
                $absent.attr('title', data['IS_ABSENT']);
                $leave.attr('title', data['ON_LEAVE']);

                $present.html(Number((presentDays * 100 / total).toFixed(1)));
                $absent.html(Number((absentDays * 100 / total).toFixed(1)));
                $leave.html(Number((leaveDays * 100 / total).toFixed(1)));
            });
        };
        var firstTime = true;
        var initializeReport = function (monthId, departmentId) {
            if (firstTime) {
                App.blockUI({target: "#hris-page-content"});

            } else {
                App.blockUI({target: "#reportTable"});

            }
            app.pullDataById(document.wsDepartmentWiseDailyReport, {departmentId: departmentId, monthId: monthId}).then(function (response) {
                if (firstTime) {
                    App.unblockUI("#hris-page-content");
                    firstTime = false;
                } else {
                    App.unblockUI("#reportTable");
                }
                console.log('departmentWiseEmployeeMonthlyR', response);
                var extractedDetailData = extractDetailData(response.data, departmentId);
                console.log('extractedDetailData', extractedDetailData);
                $tableContainer.kendoGrid({
                    dataSource: {
                        data: extractedDetailData.rows,
                        pageSize: 20
                    },
                    scrollable: false,
                    sortable: false,
                    pageable: false,
                    columns: extractedDetailData.cols
                });
                displayDataInBtnGroup('.daily-attendance');
                displayTotalInGrid('.total-attendance');


            }, function (error) {
                if (firstTime) {
                    App.unblockUI("#hris-page-content");
                    firstTime = false;
                } else {
                    App.unblockUI("#reportTable");
                }
                console.log('departmentWiseEmployeeMonthlyE', error);
            });
        };

        $('select').select2();
        var $companyList = $('#countryList');
        var $monthList = $('#monthList');
        var $branchList = $('#branchList');
        var $departmentList = $('#departmentList');
        var $generateReport = $('#generateReport');

        var populateList = function ($element, list, id, value, defaultMessage, selectedId) {
            $element.html('');
            $element.append($("<option></option>").val(-1).text(defaultMessage));
            for (var i in list) {
                if (typeof selectedId !== 'undefined' && selectedId != null && selectedId == list[i][id]) {
                    $element.append($("<option selected='selected'></option>").val(list[i][id]).text(list[i][value]));
                } else {
                    $element.append($("<option></option>").val(list[i][id]).text(list[i][value]));
                }
            }
        }

        var comBraDepList = document.comBraDepList;
        comBraDepList.findCompanyAndBranchId = function (deptId) {
            var companyList = JSON.parse(JSON.stringify(this));
            var cKeys = Object.keys(companyList);

            for (var i in cKeys) {
                var company = companyList[cKeys[i]];
                var branchList = company['BRANCH_LIST'];
                var bKeys = Object.keys(branchList);

                for (var j in bKeys) {
                    var branch = branchList[bKeys[j]];
                    var departmentList = branch['DEPARTMENT_LIST'];
                    var dKeys = Object.keys(departmentList);
                    for (var k in dKeys) {
                        var department = departmentList[dKeys[k]];

                        if (department['DEPARTMENT_ID'] == deptId) {
                            return {"companyId": company['COMPANY_ID'], "branchId": branch['BRANCH_ID']};
                        }
                    }
                }
                return null;
            }

        };
        var monthList = document.monthList;

        var monthId = document.monthId;
        var departmentId = document.departmentId;
        populateList($monthList, monthList, 'MONTH_ID', 'MONTH_EDESC', "Select Month", monthId);
        populateList($companyList, comBraDepList, 'COMPANY_ID', 'COMPANY_NAME', "Select Company");
        populateList($branchList, [], 'BRANCH_ID', 'BRANCH_NAME', "SELECT BRANCH");
        populateList($departmentList, [], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', "SELECT DEPARTMENT");

        var companyListChange = function (val, selectedId) {
            if (val != -1) {
                populateList($branchList, comBraDepList[val]['BRANCH_LIST'], 'BRANCH_ID', 'BRANCH_NAME', "Select Branch", selectedId);
                populateList($departmentList, [], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', "Select Department");
            }
        };
        $companyList.on('change', function () {
            companyListChange($(this).val());
        });
        var branchListChange = function (val, selectedId) {
            if (val != -1) {
                populateList($departmentList, comBraDepList[$companyList.val()]['BRANCH_LIST'][val]['DEPARTMENT_LIST'], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', "Select Department", selectedId);
            }
        };
        $branchList.on('change', function () {
            branchListChange($(this).val());
        });
        $generateReport.on('click', function () {
            var departmentId = $departmentList.val();
            var monthId = $monthList.val();
            if (departmentId == -1 || monthId == -1) {
                app.errorMessage("No Department Selected", "Notification");
            } else {
                initializeReport(monthId, departmentId);
            }
        });



        if (monthId != 0 && departmentId != 0) {
            initializeReport(monthId, departmentId);
            var comAndDept = comBraDepList.findCompanyAndBranchId(departmentId);
            if (comAndDept != null) {
                $companyList.val(comAndDept['companyId']);
                companyListChange(comAndDept['companyId'], comAndDept['branchId']);
                branchListChange(comAndDept['branchId'], departmentId)
            }
        }

    });
})(window.jQuery, window.app);