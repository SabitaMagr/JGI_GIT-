(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $tableContainer = $("#departmentMonthReport");
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
                                '<a class="btn  widget-btn custom-btn-present totalbtn"></a>' +
                                '<a class="btn  widget-btn custom-btn-absent totalbtn"></a>' +
                                '<a class="btn widget-btn custom-btn-leave totalbtn"></a>' +
                                '</div>'
                    }

                }
            }
            var returnData = {rows: [], cols: []};
            returnData.cols.push({
                field: 'employeeId',
                title: 'Id',
                width: 30
            });
            returnData.cols.push({
                field: 'employee',
                title: 'employees',
                template: '<a href="' + document.linkToReportFour + '/#:employeeId#">#: employee# </a>'
            });
            for (var k in column) {
                returnData.cols.push(column[k]);
            }

            for (var k in data) {
                var row = data[k].MONTHS;
                row['employee'] = data[k].FULL_NAME;
                row['employeeId'] = data[k].EMPLOYEE_ID;
                returnData.rows.push(row);
            }
            return returnData;
        };
        var displayDataInBtnGroup = function (selector) {
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
        var initializeReport = function (departmentId) {
            if (firstTime) {
                App.blockUI({target: "#hris-page-content"});

            } else {
                App.blockUI({target: "#departmentMonthReport"});
            }
            app.pullDataById(document.wsDepartmentWise, {departmentId: departmentId}).then(function (response) {
                if (firstTime) {
                    App.unblockUI("#hris-page-content");
                    firstTime = false;
                } else {
                    App.unblockUI("#departmentMonthReport");
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
                displayDataInBtnGroup('.custom-btn-group.' + departmentId);


            }, function (error) {
                if (firstTime) {
                    App.unblockUI("#hris-page-content");
                    firstTime = false;
                } else {
                    App.unblockUI("#departmentMonthReport");
                }
                console.log('departmentWiseEmployeeMonthlyE', error);
            });
        };

        $('select').select2();
        var $companyList = $('#companyList');
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
        populateList($companyList, comBraDepList, 'COMPANY_ID', 'COMPANY_NAME', "Select Company");
        populateList($branchList, [], 'BRANCH_ID', 'BRANCH_NAME', "Select Branch");
        populateList($departmentList, [], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', "Select Department");

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
            if (departmentId == -1) {
                app.errorMessage("No Department Selected", "Notification");
            } else {
                initializeReport(departmentId);
            }
        });
        var departmentId = document.departmentId;
        if (departmentId != 0) {
            initializeReport(departmentId);
            var comAndDept = comBraDepList.findCompanyAndBranchId(departmentId);
            if (comAndDept != null) {
                $companyList.val(comAndDept['companyId']);
                companyListChange(comAndDept['companyId'], comAndDept['branchId']);
                branchListChange(comAndDept['branchId'], departmentId)
            }
        }
    });
})(window.jQuery, window.app);