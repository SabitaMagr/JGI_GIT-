(function ($, app) {
    $(document).ready(function () {
        /*
         * Search javascript code starts here
         */
        var $company = $('#companyId');
        var $branch = $('#branchId');
        var $department = $('#departmentId');
        var $designation = $('#designationId');
        var $position = $('#positionId');
        var $serviceType = $('#serviceTypeId');
        var $serviceEventType = $('#serviceEventTypeId');
        var $employee = $('#employeeId');

        /* setup functions */
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
        /* setup functions */

        /* initialize dropdowns */
        populateList($company, document.searchValues['company'], 'COMPANY_ID', 'COMPANY_NAME', 'All Company');
        populateList($branch, document.searchValues['branch'], 'BRANCH_ID', 'BRANCH_NAME', 'All Branch');
        populateList($department, document.searchValues['department'], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'All Department');
        populateList($designation, document.searchValues['designation'], 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'All Designation');
        populateList($position, document.searchValues['position'], 'POSITION_ID', 'POSITION_NAME', 'All Position');
        populateList($serviceType, document.searchValues['serviceType'], 'SERVICE_TYPE_ID', 'SERVICE_TYPE_NAME', 'All Service Type');
        populateList($serviceEventType, document.searchValues['serviceEventType'], 'SERVICE_EVENT_TYPE_ID', 'SERVICE_EVENT_TYPE_NAME', 'All Service Event Type');
        populateList($employee, document.searchValues['employee'], 'EMPLOYEE_ID', 'FIRST_NAME', 'All Employee');
        /* initialize dropdowns */

        /* setup change events */
        onChangeEvent($company, function ($this) {
            populateList($branch, search(document.searchValues['branch'], {'COMPANY_ID': $this.val()}), 'BRANCH_ID', 'BRANCH_NAME', 'All Branch');
            populateList($department, search(document.searchValues['department'], {'COMPANY_ID': $this.val()}), 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'All Department');
            populateList($designation, search(document.searchValues['designation'], {'COMPANY_ID': $this.val()}), 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'All Designation');
            populateList($position, search(document.searchValues['position'], {'COMPANY_ID': $this.val()}), 'POSITION_ID', 'POSITION_NAME', 'All Position');

            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val(), 'SERVICE_EVENT_TYPE_ID': $serviceEventType.val()}), 'EMPLOYEE_ID', 'FIRST_NAME', 'All Employee');

        });

        onChangeEvent($branch, function ($this) {
            populateList($department, search(document.searchValues['department'], {'BRANCH_ID': $this.val()}), 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'All Department');

            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val(), 'SERVICE_EVENT_TYPE_ID': $serviceEventType.val()}), 'EMPLOYEE_ID', 'FIRST_NAME', 'All Employee');
        });

        onChangeEvent($department, function ($this) {
            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val(), 'SERVICE_EVENT_TYPE_ID': $serviceEventType.val()}), 'EMPLOYEE_ID', 'FIRST_NAME', 'All Employee');
        });
        onChangeEvent($designation, function ($this) {
            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val(), 'SERVICE_EVENT_TYPE_ID': $serviceEventType.val()}), 'EMPLOYEE_ID', 'FIRST_NAME', 'All Employee');
        });
        onChangeEvent($position, function ($this) {
            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val(), 'SERVICE_EVENT_TYPE_ID': $serviceEventType.val()}), 'EMPLOYEE_ID', 'FIRST_NAME', 'All Employee');
        });
        onChangeEvent($serviceType, function ($this) {
            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val(), 'SERVICE_EVENT_TYPE_ID': $serviceEventType.val()}), 'EMPLOYEE_ID', 'FIRST_NAME', 'All Employee');
        });
        onChangeEvent($serviceEventType, function ($this) {
            populateList($employee, search(document.searchValues['employee'], {'COMPANY_ID': $company.val(), 'BRANCH_ID': $branch.val(), 'DEPARTMENT_ID': $department.val(), 'DESIGNATION_ID': $designation.val(), 'POSITION_ID': $position.val(), 'SERVICE_TYPE_ID': $serviceType.val(), 'SERVICE_EVENT_TYPE_ID': $serviceEventType.val()}), 'EMPLOYEE_ID', 'FIRST_NAME', 'All Employee');
        });


        /* setup change events */
    });

})(window.jQuery, window.app);