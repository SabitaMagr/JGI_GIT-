

(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.addDatePicker($('#newsDate'));

        var selectedBranchId = (typeof document.selectedBranchId === 'undefined') ? null : document.selectedBranchId;
        var selectedDepartmentId = (typeof document.selectedDepartmentId === 'undefined') ? null : document.selectedDepartmentId;
        var selectedDesignationId = (typeof document.selectedDesignationId === 'undefined') ? null : document.selectedDesignationId;
        var $companySelect = $('#companyId');
        var $branchSelect = $('#branchId');
        var $designationSelect = $('#designationId');
        var $departmentSelect = $('#departmentId');

//        console.log(selectedBranchId);

        var companySelectChange = function (companyId) {
            $branchSelect.html('');
            $designationSelect.html('');
            $departmentSelect.html('');

            $branchSelect.append($("<option></option>")
                    .attr("value", "")
                    .text("Select Branch"));

            $designationSelect.append($("<option></option>")
                    .attr("value", "")
                    .text("Select Designation"));

            $departmentSelect.append($("<option></option>")
                    .attr("value", "")
                    .text("Select Branch"));

            var selectedCompanyBranches = document.branches[companyId];
            var selectedCompanyDesingations = document.designation[companyId];

            selectedCompanyBranches = (typeof selectedCompanyBranches === 'undefined') ? [] : selectedCompanyBranches;
            selectedCompanyDesingations = (typeof selectedCompanyDesingations === 'undefined') ? [] : selectedCompanyDesingations;

            $.each(selectedCompanyBranches, function (key, branch) {
                if (selectedBranchId != null) {
                    if (branch.BRANCH_ID == selectedBranchId) {
                        $branchSelect.append($("<option selected='selected'></option>")
                                .attr("value", branch.BRANCH_ID)
                                .text(branch.BRANCH_NAME));

                    } else {
                        $branchSelect.append($("<option></option>")
                                .attr("value", branch.BRANCH_ID)
                                .text(branch.BRANCH_NAME));
                    }

                } else {
                    $branchSelect.append($("<option></option>")
                            .attr("value", branch.BRANCH_ID)
                            .text(branch.BRANCH_NAME));

                }
            });


            $.each(selectedCompanyDesingations, function (key, designation) {
                if (selectedDesignationId != null) {
                    if (designation.DESIGNATION_ID == selectedDesignationId) {
                        $designationSelect.append($("<option selected='selected'></option>")
                                .attr("value", designation.DESIGNATION_ID)
                                .text(designation.DESIGNATION_TITLE));

                    } else {
                        $designationSelect.append($("<option></option>")
                                .attr("value", designation.DESIGNATION_ID)
                                .text(designation.DESIGNATION_TITLE));
                    }

                } else {
                    $designationSelect.append($("<option></option>")
                            .attr("value", designation.DESIGNATION_ID)
                            .text(designation.DESIGNATION_TITLE));
                }

            });



        };

        companySelectChange($companySelect.val());

        $companySelect.on('change', function () {
            companySelectChange($(this).val());
            branchSelectChange($branchSelect.val());
        });


        var branchSelectChange = function (branchId) {
            $departmentSelect.html('');
            $departmentSelect.append($("<option></option>")
                    .attr("value", "")
                    .text("Select Branch"));
//            var branchId = $branchSelect.val();
            var selectedBranchDepartment = document.department[branchId];
            selectedBranchDepartment = (typeof selectedBranchDepartment === 'undefined') ? [] : selectedBranchDepartment;


            $.each(selectedBranchDepartment, function (key, value) {
                if (selectedDepartmentId != null) {
                    ;
                    if (value.DEPARTMENT_ID == selectedDepartmentId) {
                        $departmentSelect.append($("<option selected='selected'></option>")
                                .attr("value", value.DEPARTMENT_ID)
                                .text(value.DEPARTMENT_NAME));

                    } else {
                        $departmentSelect.append($("<option></option>")
                                .attr("value", value.DEPARTMENT_ID)
                                .text(value.DEPARTMENT_NAME));
                    }

                } else {
                    $departmentSelect.append($("<option></option>")
                            .attr("value", value.DEPARTMENT_ID)
                            .text(value.DEPARTMENT_NAME));

                }

            });

        };


        $branchSelect.on('change', function () {
            branchSelectChange($(this).val());
        });

        branchSelectChange($branchSelect.val());


    });
})(window.jQuery, window.app);