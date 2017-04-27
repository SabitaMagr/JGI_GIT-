/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
//        $('select').select2();
        var scopeArea = function () {
            var parentDesignation = $("#form-parentDesignation").val();
            if (parentDesignation == "" || parentDesignation == "-1") {
                $("#scopeArea").slideUp();
                $("input[name=withinBranch]").attr("disabled", "disabled");
                $("input[name=withinDepartment]").attr("disabled", "disabled");
            } else {
                $("#scopeArea").slideDown();
                $("input[name=withinBranch]").removeAttr("disabled");
                $("input[name=withinDepartment]").removeAttr("disabled");
            }
        };
        $("#form-parentDesignation").on("change", scopeArea);

        var inputFieldId = "form-designationTitle";
        var formId = "designation-form";
        var tableName = "HRIS_DESIGNATIONS";
        var columnName = "DESIGNATION_TITLE";
        var checkColumnName = "DESIGNATION_ID";
        var selfId = $("#designationId").val();
        if (typeof (selfId) == "undefined") {
            selfId = 0;
        }
        window.app.checkUniqueConstraints(inputFieldId, formId, tableName, columnName, checkColumnName, selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
        window.app.checkUniqueConstraints("form-designationCode", formId, tableName, "DESIGNATION_CODE", checkColumnName, selfId);


        var companyName = $('#companyId');
        var designation = $('#form-parentDesignation');
        var designationListCompanyWise = document.designationListCompanyWise;

        // company change function
        var companyChange = function () {
            designation.html('');
            designation.append($("<option></option>")
                    .attr("value", "")
                    .text("Select Designation"));
            var deparmentList = designationListCompanyWise[companyName.val()];
            if (deparmentList != 'undefined') {
                $.each(deparmentList, function (key, des) {
                    designation.append($("<option></option>")
                            .attr("value", des.DESIGNATION_ID)
                            .text(des.DESIGNATION_TITLE));

                });
            }
            scopeArea();
        }

        companyName.on('change', companyChange);
        companyChange();


    });
})(window.jQuery, window.app);
