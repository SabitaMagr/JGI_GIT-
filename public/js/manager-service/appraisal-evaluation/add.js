(function ($, appraisalCustom) {
    'use strict';
    $(document).ready(function () {
//        $("#appraisalEvaluation1").on("submit", function () {
//            App.blockUI({target: "#hris-page-content"});
//            var fail = false;
//            $('#appraisalEvaluation').find('input,select,textarea').each(function () {
//                if ($(this).attr('type') !== 'hidden' && $(this).attr('disabled') !== 'disabled') {
//                    if ($(this).attr("required") === "required") {
//                        if ($(this).val() === "") {
//                            fail = true;
//                            var parentId = $(this).parent("div");
//                            var errorMsgSpan = parentId.find('span.errorMsg');
//                            if (errorMsgSpan.length > 0) {
//                                $(this).html("This field is required");
//                            } else {
//                                var errorMsgSpan = $('<span />', {
//                                    "class": 'errorMsg',
//                                    text: "This field is required"
//                                });
//                                parentId.append(errorMsgSpan);
//
//                                var errorMsgSpan1 = $('<span />', {
//                                    "class": 'errorMsg',
//                                    "style": 'margin-bottom:10px',
//                                    text: "Appraisal Submission Failed!!!!"
//                                });
//                                $("#tabContent").prepend(errorMsgSpan1);
//                            }
//                        }
//                    }
//                }
//                $(this).on("blur", function () {
//                        parentId.find('span.errorMsg').remove();
//                        $("#tabContent").find('span.errorMsg').remove();
//                });
//            });
//            if (fail) {
//                App.unblockUI("#hris-page-content");
//                $("#portlet_tab2_1").addClass("active");
//                $("ul#tabList").find('a[href="#portlet_tab2_1"]').parent("li").addClass("active");
//                $("#portlet_tab2_2").removeClass("active");
//                $("ul#tabList").find('a[href="#portlet_tab2_2"]').parent("li").removeClass("active");
//                return false;
//            } else {
//                return true;
//            }
//        });
        appraisalCustom.tabFormValidation("competenciesForm", "KPIForm", "portlet_tab2_COM", "portlet_tab2_KPI");
        var comSettingId = $("#comSettingId").val();
        if (comSettingId == 'N') {
            appraisalCustom.tabFormValidation("appraisalEvaluation1", "appraisalEvaluation", "portlet_tab2_2", "portlet_tab2_1");
        } else if (comSettingId == 'Y') {
            appraisalCustom.tabFormValidation("appraisalEvaluation1", "competenciesForm", "portlet_tab2_2", "portlet_tab2_COM");
        }
        $('input[type=radio][name=defaultRating]').change(function () {
            console.log($(this).val());
            if ($(this).val() == 'Y') {
                var defaultRating = $("#defaultRating").val();
                $("#appraiserOverallRating").val(defaultRating);
                $('#tabContent').find('input,select,textarea').each(function () {
                    if ($(this).attr('type') !== 'hidden' && $(this).attr('disabled') !== 'disabled') {
                        $(this).removeAttr("required");
                    }
                })
            } else {
                var annualRatingCompetency = $('#annualRatingCompetency').val();
                var annualRating = $('#annualRating').val();
                $("#appraiserOverallRating").val(((!isNaN(annualRating))?annualRating:"") + annualRatingCompetency);
                $('#tabContent').find('input,select,textarea').each(function () {
                    if ($(this).attr('type') !== 'hidden' && $(this).attr('disabled') !== 'disabled') {
                        $(this).attr("required",true);
                    }
                })
            }
        });
    });
})(window.jQuery, window.appraisalCustom);