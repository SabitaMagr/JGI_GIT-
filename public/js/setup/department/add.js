/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var branchList = document.branches;

        var inputFieldId = "form-departmentName";
        var formId = "department-form";
        var tableName = "HRIS_DEPARTMENTS";
        var columnName = "DEPARTMENT_NAME";
        var checkColumnName = "DEPARTMENT_ID";
        var selfId = $("#departmentId").val();
        if (typeof (selfId) == "undefined") {
            selfId = 0;
        }
        window.app.checkUniqueConstraints(inputFieldId, formId, tableName, columnName, checkColumnName, selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });   
        window.app.checkUniqueConstraints("form-departmentCode", formId, tableName, "DEPARTMENT_CODE", checkColumnName, selfId);
        var selectedBranchId = (typeof document.selectedBranchId === 'undefined' || document.selectedBranchId === '') ? null : document.selectedBranchId;
        var $companySelect = $('#form-companyId');
        var $branchSelect = $('#form-branchId');

//        console.log(selectedBranchId);

        var companySelectChange = function (companyId) {
            $branchSelect.html('');
            $branchSelect.append($("<option></option>")
                    .attr("value", "")
                    .text("Select Branch"));

            var selectedCompanyBranches = branchList[companyId];

            selectedCompanyBranches = (typeof selectedCompanyBranches === 'undefined') ? [] : selectedCompanyBranches;

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


        };

        companySelectChange($companySelect.val());

        $companySelect.on('change', function () {
            companySelectChange($(this).val());
        });
    });
})(window.jQuery, window.app);
