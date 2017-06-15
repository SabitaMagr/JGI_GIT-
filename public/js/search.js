(function ($, app) {
    $(document).ready(function () {
        document.searchManager = {
            company: [],
            branch: [],
            department: [],
            designation: [],
            serviceType: [],
            serviceEventType: [],
            employee: [],
            companyListener: null,
            branchListener: null,
            departmentListener: null,
            designationListener: null,
            serviceTypeListener: null,
            serviceEventTypeListener: null,
            employeeListener: null
            , setCompany: function (company) {
                this.company = company;
            }, getCompany: function () {
                return this.company;
            }, setBranch: function (branch) {
                this.branch = branch;
            }, getBranch: function () {
                return this.company
            }, setDepartment: function (department) {
                this.department = department;
            }, getDepartment: function () {
                return this.department
            }, setDesignation: function (designation) {
                this.designation = designation;
            }, getDesignation: function () {
                return this.designation;
            }, setServiceType: function (servicetype) {
                this.serviceType = servicetype;
            }, getServiceType: function () {
                return this.serviceType
            }, setServiceEventType: function (serviceEventType) {
                this.serviceEventType = serviceEventType;
            }, getServiceEventType: function () {
                return this.serviceEventType
            }, setEmployee: function (employee) {
                this.employee = employee;
            }, getEmployee: function () {
                return this.employee
            }, getEmployeeById: function (id) {
                var filteredList = this.employee.filter(function (item) {
                    return item['EMPLOYEE_ID'] == id;
                });
                return filteredList[0];
            }, setCompanyListener: function (listener) {
                this.companyListener = listener;
            }, setBranchListener: function (listener) {
                this.branchListener = listener
            }, setDepartmentListener: function (listener) {
                this.departmentListener = listener;
            }, setDesignationListener: function (listener) {
                this.designationListener = listener;
            }, setServiceTypeListener: function (listener) {
                this.serviceTypeListener = listener;
            }, setServiceEventTypeListener: function (listener) {
                this.serviceEventTypeListener = listener;
            }, setEmployeeListener: function (listener) {
                this.employeeListener = listener;
            }, callCompanyListener: function () {
                if (this.companyListener !== null) {
                    this.companyListener();
                }
            }, callBranchListener: function () {
                if (this.branchListener !== null) {
                    this.branchListener();
                }
            }, callDepartmentListener: function () {
                if (this.departmentListener !== null) {
                    this.departmentListener();
                }
            }, callDesignationListener: function () {
                if (this.designationListener !== null) {
                    this.designationListener();
                }
            }, callServiceTypeListener: function () {
                if (this.serviceTypeListener !== null) {
                    this.serviceTypeListener();
                }
            }, callServiceEventTypeListener: function () {
                if (this.serviceEventTypeListener !== null) {
                    this.serviceEventTypeListener();
                }
            }, callEmployeeListener: function () {
                if (this.employeeListener !== null) {
                    this.employeeListener();
                }
            }
        };

        /*
         * Search javascript code starts here
         */
        var changeSearchOption = function (companyId, branchId, departmentId, designationId, positionId, serviceTypeId, serviceEventTypeId, employeeId, genderId) {


            var $company = $('#' + companyId);
            var $branch = $('#' + branchId);
            var $department = $('#' + departmentId);
            var $designation = $('#' + designationId);
            var $position = $('#' + positionId);
            var $serviceType = $('#' + serviceTypeId);
            var $serviceEventType = $('#' + serviceEventTypeId);
            var $employee = $('#' + employeeId);

            var $gender = $('#' + "random-random");
            if (genderId != null) {
                $gender = $('#' + genderId);
            }
            /* setup functions */
            var populateList = function ($element, list, id, value, defaultMessage, selectedId) {
                $element.html('');
                $element.append($("<option></option>").val(-1).text(defaultMessage));
                var concatArray = function (keyList, list, concatWith) {
                    var temp = '';
                    if (typeof concatWith === 'undefined') {
                        concatWith = ' ';
                    }
                    for (var i in keyList) {
                        var listValue = list[keyList[i]];
                        if (i == (keyList.length - 1)) {
                            temp = temp + ((listValue === null) ? '' : listValue);
                            continue;
                        }
                        temp = temp + ((listValue === null) ? '' : listValue) + concatWith;
                    }

                    return temp;
                };
                for (var i in list) {
                    var text = null;
                    if (typeof value === 'object') {
                        text = concatArray(value, list[i], ' ');
                    } else {
                        text = list[i][value];
                    }
                    if (typeof selectedId !== 'undefined' && selectedId != null && selectedId == list[i][id]) {
                        $element.append($("<option selected='selected'></option>").val(list[i][id]).text(text));
                    } else {
                        $element.append($("<option></option>").val(list[i][id]).text(text));
                    }
                }
            };
            var search = function (list, where) {
                return list.filter(function (item) {
                    for (var i in where) {
                        if (!(item[i] === where[i] || where[i] == -1)) {
                            return false;
                        }
                    }
                    return true;
                });
            };
            var onChangeEvent = function ($element, fn) {
                $element.on('change', function () {
                    var $this = $(this);
                    fn($this);
                });
            };

            var employeeSearchAndPopulate = function () {
                var searchParams = {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val(), 'SERVICE_EVENT_TYPE_ID': $serviceEventType.val()};
                if ($gender.length != 0) {
                    searchParams['GENDER_ID'] = $gender.val();
                }
                var employeeList = search(document.searchValues['employee'], searchParams);
                document.searchManager.setEmployee(employeeList);
                populateList($employee, employeeList, 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');
            };
            /* setup functions */

            /* initialize dropdowns */
            populateList($company, document.searchValues['company'], 'COMPANY_ID', 'COMPANY_NAME', 'All Company');
            populateList($branch, document.searchValues['branch'], 'BRANCH_ID', 'BRANCH_NAME', 'All Branch');
            populateList($department, document.searchValues['department'], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'All Department');
            populateList($designation, document.searchValues['designation'], 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'All Designation');
            populateList($position, document.searchValues['position'], 'POSITION_ID', 'POSITION_NAME', 'All Position');
            populateList($serviceType, document.searchValues['serviceType'], 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', 'All Service Type');
            populateList($serviceEventType, document.searchValues['serviceEventType'], 'SERVICE_EVENT_TYPE_ID', 'SERVICE_EVENT_TYPE_NAME', 'Working');
            populateList($employee, document.searchValues['employee'], 'EMPLOYEE_ID', ['FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME'], 'All Employee');

            document.searchManager.setCompany(document.searchValues['company']);
            document.searchManager.setBranch(document.searchValues['branch']);
            document.searchManager.setDepartment(document.searchValues['department']);
            document.searchManager.setDesignation(document.searchValues['designation']);
            document.searchManager.setServiceType(document.searchValues['serviceType']);
            document.searchManager.setServiceEventType(document.searchValues['serviceEventType']);
            document.searchManager.setEmployee(document.searchValues['employee']);

            if ($gender.length != 0) {
                populateList($gender, document.searchValues['gender'], 'GENDER_ID', 'GENDER_NAME', 'All Gender');
            }
            /* initialize dropdowns */

            /* setup change events */
            onChangeEvent($company, function ($this) {
                employeeSearchAndPopulate();
                document.searchManager.callCompanyListener();
            });

            onChangeEvent($branch, function ($this) {
                employeeSearchAndPopulate();
                document.searchManager.callBranchListener();
            });

            onChangeEvent($department, function ($this) {
                employeeSearchAndPopulate();
                document.searchManager.callDepartmentListener();
            });
            onChangeEvent($designation, function ($this) {
                employeeSearchAndPopulate();
                document.searchManager.callDesignationListener();
            });
            onChangeEvent($position, function ($this) {
                employeeSearchAndPopulate();
            });
            onChangeEvent($serviceType, function ($this) {
                employeeSearchAndPopulate();
                document.searchManager.callServiceTypeListener();
            });
            onChangeEvent($serviceEventType, function ($this) {
                employeeSearchAndPopulate();
                document.searchManager.callServiceEventTypeListener();
            });
            onChangeEvent($employee, function ($this) {
                document.searchManager.callEmployeeListener();
            })

            if ($gender.length != 0) {
                onChangeEvent($gender, function ($this) {
                    employeeSearchAndPopulate();
                });
            }
        };
        changeSearchOption("companyId", "branchId", "departmentId", "designationId", "positionId", "serviceTypeId", "serviceEventTypeId", "employeeId", "genderId");

        $("#reset").on("click", function () {
            changeSearchOption("companyId", "branchId", "departmentId", "designationId", "positionId", "serviceTypeId", "serviceEventTypeId", "employeeId");
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });
        /* setup change events */



    });

})(window.jQuery, window.app);