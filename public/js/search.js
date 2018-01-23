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
            employeeListener: null,
            ids: []
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
            },
            setIds: function (ids) {
                this.ids = ids;
            },
            getIds: function () {
                return this.ids;
            },
            getSearchValues: function () {
                var values = {};
                $.each(this.ids, function (key, value) {
                    if (typeof value !== "undefined") {
                        values[value] = $('#' + value).val();
                    }
                });
                return values;
            },
            reset: function () {
                $.each(this.ids, function (key, value) {
                    if (typeof value !== "undefined") {
                        $('#' + value).val(-1).change();
                    }
                });
                if (this.resetEvent !== null) {
                    this.resetEvent();
                }
            },
            resetEvent: null,
            registerResetEvent: function (fn) {
                this.resetEvent = fn;
            },
            setSearchValues: function (values) {
                $.each(this.ids, function (key, value) {
                    if (typeof values[value] !== "undefined") {
                        $('#' + value).val(values[value]).trigger('change.select2');
                    }
                });
            }
        };
        (function () {
            $('.hris-reset-btn').on('click', function () {
                document.searchManager.reset();
            });
        })();

        /*
         * Search javascript code starts here
         */
        var changeSearchOption = function (companyId, branchId, departmentId, designationId, positionId, serviceTypeId, serviceEventTypeId, employeeId, genderId, employeeTypeId) {
            document.searchManager.setIds(JSON.parse(JSON.stringify(arguments)));

            var $company = $('#' + companyId);
            var $branch = $('#' + branchId);
            var $department = $('#' + departmentId);
            var $designation = $('#' + designationId);
            var $position = $('#' + positionId);
            var $serviceType = $('#' + serviceTypeId);
            var $serviceEventType = $('#' + serviceEventTypeId);
            var $employee = $('#' + employeeId);

            var $gender = $('#' + "random-random");
            var $employeeType = $('#' + "random-random");
            if (genderId != null) {
                $gender = $('#' + genderId);
            }
            if (typeof employeeTypeId !== 'undefined' && employeeTypeId !== null) {
                $employeeType = $('#' + employeeTypeId);
            }

            /* setup functions */
            var populateList = function ($element, list, id, value, defaultMessage, selectedId) {
                $element.html('');
                if (typeof defaultMessage !== "undefined" && !$element.prop('multiple')) {
                    $element.append($("<option></option>").val(-1).text(defaultMessage));
                }
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
//            var search = function (list, where) {
//                return list.filter(function (item) {
//                    for (var i in where) {
//                        if (!(item[i] === where[i] || where[i] == -1)) {
//                            return false;
//                        }
//                    }
//                    return true;
//                });
//            };
            var search = function (list, where) {
                return list.filter(function (item) {
                    for (var i in where) {
                        var value = where[i];
                        if (Array.isArray(value)) {
                            var xc = false;
                            for (var x in value) {
                                if (item[i] === value[x]) {
                                    xc = true;
                                    break;
                                }
                            }
                            return xc;
                        } else if (value === null) {

                        } else {
                            if (!(item[i] === value || value == -1)) {
                                return false;
                            }
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
                if ($employeeType.length != 0) {
                    searchParams['EMPLOYEE_TYPE'] = $employeeType.val();
                }
                var employeeList = search(document.searchValues['employee'], searchParams);
                document.searchManager.setEmployee(employeeList);
                populateList($employee, employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'All Employee');
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
            populateList($employee, document.searchValues['employee'], 'EMPLOYEE_ID', 'FULL_NAME', 'All Employee');

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
            if ($employeeType.length != 0) {
                populateList($employeeType, document.searchValues['employeeType'], 'EMPLOYEE_TYPE_KEY', 'EMPLOYEE_TYPE_VALUE', 'All Employee Type');
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
            if ($employeeType.length != 0) {
                onChangeEvent($employeeType, function ($this) {
                    employeeSearchAndPopulate();
                });
            }
            var acl = document.acl;
            var employeeDetail = document.employeeDetail;
            if (typeof acl !== 'undefined' && typeof employeeDetail !== 'undefined') {
                switch (acl['CONTROL']) {
                    case 'C':
                        $company.val(employeeDetail['COMPANY_ID']);
                        $company.prop('disabled', true);
                        break;
                    case 'B':
                        $branch.val(employeeDetail['BRANCH_ID']);
                        $branch.prop('disabled', true);
                        break;
                    case 'DS':
                        $designation.val(employeeDetail['DESIGNATION_ID']);
                        $designation.prop('disabled', true);
                        break;
                    case 'DP':
                        $department.val(employeeDetail['DEPARTMENT_ID']);
                        $department.prop('disabled', true);
                        break;
                    case 'P':
                        $position.val(employeeDetail['POSITION_ID']);
                        $position.prop('disabled', true);
                        break;
                }
            }

        };
        changeSearchOption("companyId", "branchId", "departmentId", "designationId", "positionId", "serviceTypeId", "serviceEventTypeId", "employeeId", "genderId", "employeeTypeId");

        /* setup change events */



    });

})(window.jQuery, window.app);