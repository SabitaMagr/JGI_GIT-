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
                var whereKeys = Object.keys(where);
                return item[whereKeys[0]] === where[whereKeys[0]] || where[whereKeys[0]] == -1;
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
        populateList($company, document.searchValues['company'], 'COMPANY_ID', 'COMPANY_NAME', 'Company');
        populateList($branch, document.searchValues['branch'], 'BRANCH_ID', 'BRANCH_NAME', 'Branch');
        populateList($department, document.searchValues['department'], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'Department');
        populateList($designation, document.searchValues['designation'], 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'Designation');
        populateList($position, document.searchValues['position'], 'POSITION_ID', 'POSITION_NAME', 'Position');
        /* initialize dropdowns */

        /* setup change events */
        onChangeEvent($company, function ($this) {
            populateList($branch, search(document.searchValues['branch'], {'COMPANY_ID': $this.val()}), 'BRANCH_ID', 'BRANCH_NAME', 'Branch');
            populateList($department, search(document.searchValues['department'], {'COMPANY_ID': $this.val()}), 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'Department');
            populateList($designation, search(document.searchValues['designation'], {'COMPANY_ID': $this.val()}), 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'Designation');
            populateList($position, search(document.searchValues['position'], {'COMPANY_ID': $this.val()}), 'POSITION_ID', 'POSITION_NAME', 'Position');
        });

        onChangeEvent($branch, function ($this) {
            populateList($department, search(document.searchValues['department'], {'BRANCH_ID': $this.val()}), 'DEPARTMENT_ID', 'DEPARTMENT_NAME', 'Department');
        });
        /* setup change events */
    });

})(window.jQuery, window.app);