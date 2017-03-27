
(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var selectedBranchId = (typeof document.selectedBranchId === 'undefined') ? null : document.selectedBranchId;
        var $companySelect = $('#companyId');
        var $branchSelect = $('#branchId');

//        console.log(selectedBranchId);

        var companySelectChange = function (companyId) {
            $branchSelect.html('');
            $branchSelect.append($("<option></option>")
                    .attr("value", "")
                    .text("Select Branch"));

            var selectedCompanyBranches = document.branches[companyId];

            selectedCompanyBranches = (typeof selectedCompanyBranches === 'undefined') ? [] : selectedCompanyBranches;

            $.each(selectedCompanyBranches, function (key, branch) {
                if (selectedBranchId != null) {
                    ;
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