(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $tableContainer = $("#reportTable");
        var extractDetailData = function (rawData, employeeId) {
            var data = {};
            var column = {};

            for (var i in rawData) {
                if (typeof data[rawData[i].MONTH_ID] !== 'undefined') {
                    data[rawData[i].MONTH_ID].MONTHS['c' + rawData[i].DAY_COUNT] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE
                            });
                    data[rawData[i].MONTH_ID].TOTAL.IS_ABSENT = data[rawData[i].MONTH_ID].TOTAL.IS_ABSENT + parseFloat(rawData[i].IS_ABSENT);
                    data[rawData[i].MONTH_ID].TOTAL.IS_PRESENT = data[rawData[i].MONTH_ID].TOTAL.IS_PRESENT + parseFloat(rawData[i].IS_PRESENT);
                    data[rawData[i].MONTH_ID].TOTAL.ON_LEAVE = data[rawData[i].MONTH_ID].TOTAL.ON_LEAVE + parseFloat(rawData[i].ON_LEAVE);
                    data[rawData[i].MONTH_ID].TOTAL.IS_DAYOFF = data[rawData[i].MONTH_ID].TOTAL.IS_DAYOFF + parseFloat(rawData[i].IS_DAYOFF);

                } else {
                    data[rawData[i].MONTH_ID] = {
                        MONTH_ID: rawData[i].MONTH_ID,
                        MONTH_EDESC: rawData[i].MONTH_EDESC,
                        MONTHS: {},
                        TOTAL: {
                            IS_ABSENT: parseFloat(rawData[i].IS_ABSENT),
                            IS_PRESENT: parseFloat(rawData[i].IS_PRESENT),
                            ON_LEAVE: parseFloat(rawData[i].ON_LEAVE),
                            IS_DAYOFF: parseFloat(rawData[i].IS_DAYOFF)
                        }
                    };
                    data[rawData[i].MONTH_ID].MONTHS['c' + rawData[i].DAY_COUNT] =
                            JSON.stringify({
                                IS_ABSENT: rawData[i].IS_ABSENT,
                                IS_PRESENT: rawData[i].IS_PRESENT,
                                ON_LEAVE: rawData[i].ON_LEAVE
                            });

                }
                if (typeof column[rawData[i].DAY_COUNT] === 'undefined') {
                    var temp = 'c' + rawData[i].DAY_COUNT;
                    column[rawData[i].DAY_COUNT] = {
                        field: temp,
                        title: rawData[i].DAY_COUNT,
                        template: '<span data="#: ' + temp + ' #" class="custom-btn-group"></span>'
                    }
                }
            }


            var returnData = {rows: [], cols: []};

            returnData.cols.push({field: 'month', title: 'Month'});
            for (var k in column) {
                returnData.cols.push(column[k]);
            }
            returnData.cols.push({
                field: 'total',
                title: 'Total',
                template: '<div data="#: total #" class="btn-group widget-btn-list total-attendance">' +
                        '<a class="btn btn-default widget-btn custom-btn-present"></a>' +
                        '<a class="btn btn-danger widget-btn custom-btn-absent"></a>' +
                        '<a class="btn btn-info widget-btn custom-btn-leave"></a>' +
                        '</div>'});


            for (var k in data) {
                var row = data[k].MONTHS;
                for (var i in column) {
                    if (typeof row[column[i]['field']] === 'undefined') {
                        row[column[i]['field']] = null;
                    }
                }

                row['month'] = data[k].MONTH_EDESC;
                returnData.rows.push(row);
                row['total'] = JSON.stringify(data[k].TOTAL);
            }

            return returnData;
        };
        var displayDataInBtnGroup = function (selector) {
            $(selector).each(function (k, group) {
                var $group = $(group);

                var data = $group.attr('data');
                if (data == "null") {

                } else {
                    data = JSON.parse(data);
                    if (data['IS_PRESENT'] == 1) {
                        $group.html('P');
                        $group.parent().addClass('bg-green');
                    } else {
                        if (data['IS_ABSENT'] == 1) {
                            $group.html('A');
                            $group.parent().addClass('bg-red1 textcolor1');

                        } else {
                            if (data['ON_LEAVE'] == 1) {
                                $group.html('L');
                                $group.parent().addClass('bg-blue textcolor2');

                            } else {
                                $group.html('H');
                                $group.parent().addClass('bg-white textcolor3');
                            }

                        }

                    }
                }
            });

        };

        var initializeReport = function (employeeId) {
            $tableContainer.block();
            app.pullDataById(document.wsEmployeeWiseDailyReport, {employeeId: employeeId}).then(function (response) {
                $tableContainer.unblock();
                console.log('departmentWiseEmployeeMonthlyR', response);
                var extractedDetailData = extractDetailData(response.data, employeeId);
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
                displayDataInBtnGroup('.custom-btn-group');
                displayTotalInGrid('.total-attendance');

            }, function (error) {
                $tableContainer.unblock();
                console.log('departmentWiseEmployeeMonthlyE', error);
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

                $present.attr('title', data['IS_PRESENT']);
                $absent.attr('title', data['IS_ABSENT']);
                $leave.attr('title', data['ON_LEAVE']);

                var total = presentDays + absentDays + leaveDays;

                $present.html(Number((presentDays * 100 / total).toFixed(1)));
                $absent.html(Number((absentDays * 100 / total).toFixed(1)));
                $leave.html(Number((leaveDays * 100 / total).toFixed(1)));
            });
        };
        $('select').select2();
        var $companyList = $('#companyList');
        var $employeeList = $('#employeeList');
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
        var employeeList = document.employeeList;
        employeeList.findDepartmentId = function (searchKey) {
            var empList = JSON.parse(JSON.stringify(this));
            var keys = Object.keys(empList);

            var returnData = {'departmentId': null};
            for (var i in keys) {
                var key = keys[i];
                if (empList[key]['EMPLOYEE_ID'] == searchKey) {
                    returnData['departmentId'] = empList[key]['DEPARTMENT_ID'];
                    break;
                }

            }
            return returnData;
        };
        populateList($employeeList, employeeList, 'EMPLOYEE_ID', 'FULL_NAME', "Select Employee");
        populateList($companyList, comBraDepList, 'COMPANY_ID', 'COMPANY_NAME', "All Company");
        populateList($branchList, [], 'BRANCH_ID', 'BRANCH_NAME', "All Branch");
        populateList($departmentList, [], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', "All Department");

        var employeeFilter = function (empList, companyId, branchId, departmentId) {
            return empList.filter(function (item) {
                if (companyId != -1) {
                    if (branchId != -1) {
                        if (departmentId != -1) {
//                            return item['COMPANY_ID'] == companyId && item['BRANCH_ID'] == branchId && item['DEPARTMENT_ID'] == departmentId;
                            return item['DEPARTMENT_ID'] == departmentId;
                        } else {
//                            return item['COMPANY_ID'] == companyId && item['BRANCH_ID'] == branchId;
                            return item['BRANCH_ID'] == branchId;
                        }
                    } else {

                        return item['COMPANY_ID'] == companyId;
                    }
                } else {
                    return true;
                }
            });
        }
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

        var departmentListChange = function (val, selectedId) {
            if (val != -1) {
                populateList($employeeList, employeeFilter(employeeList, $companyList.val(), $branchList.val(), val), 'EMPLOYEE_ID', 'FULL_NAME', "Select Employee", selectedId);
            }

        }
        $departmentList.on('change', function () {
            departmentListChange($(this).val());
        });

        $generateReport.on('click', function () {
            var employeeId = $employeeList.val();
            if (employeeId == -1) {
                app.errorMessage("No Department Selected", "Notification");
            } else {
                initializeReport(employeeId);
            }
        });

        var employeeId = document.employeeId;
        if (employeeId != 0) {
            initializeReport(employeeId);
            var departmentId = employeeList.findDepartmentId(employeeId)['departmentId'];

            if (departmentId != null) {
                var comAndDept = comBraDepList.findCompanyAndBranchId(departmentId);
                if (comAndDept != null) {
                    $companyList.val(comAndDept['companyId']);
                    companyListChange(comAndDept['companyId'], comAndDept['branchId']);
                    branchListChange(comAndDept['branchId'], departmentId);
                    departmentListChange(departmentId, employeeId);
                } else {
                    console.log("system message", "companyId and branchId for department with departmentId " + departmentId + "not found");
                }
            } else {
                console.log("system message", "departmentId for employee with employeeId " + employeeId + "not found");
            }
        }


    });
})(window.jQuery, window.app);