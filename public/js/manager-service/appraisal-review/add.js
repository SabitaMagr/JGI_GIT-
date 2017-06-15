(function ($) {
    'use strict';
    $(document).ready(function () {
//        $("#appraisalReview2").on("submit", function () {
//            var appraiseeTabFail = false;
//            $('#appraisalReview').find('input,select,textarea').each(function () {
//                if ($(this).attr('type') !== 'hidden' && $(this).attr('disabled') !== 'disabled') {
//                    if ($(this).attr("required") === "required") {
//                        if ($(this).val() === "") {
//                            appraiseeTabFail = true;
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
//                    parentId.find('span.errorMsg').remove();
//                    $("#tabContent").find('span.errorMsg').remove();
//                });
//            });
//            if (appraiseeTabFail) {
//                $("#portlet_tab2_1").addClass("active");
//                $("ul#tabList").find('a[href="#portlet_tab2_1"]').parent("li").addClass("active");
//                $("#portlet_tab2_3").removeClass("active");
//                $("ul#tabList").find('a[href="#portlet_tab2_3"]').parent("li").removeClass("active");
//                return false;
//            } else {
//                return true;
//            }
//        });
        

        var appraisalReviewForm = function (submitForm, checkingForm, submitTab, checkingTab) {
            $("#"+submitForm).on("submit", function () {
                App.blockUI({target: "#hris-page-content"});
                var appraiseeTabFail = false;
                $('#'+checkingForm).find('input,select,textarea').each(function () {
                    if ($(this).attr('type') !== 'hidden' && $(this).attr('disabled') !== 'disabled') {
                        if ($(this).attr("required") === "required") {
                            if ($(this).val() === "") {
                                appraiseeTabFail = true;
                                var parentId = $(this).parent("div");
                                var errorMsgSpan = parentId.find('span.errorMsg');
                                if (errorMsgSpan.length > 0) {
                                    $(this).html("This field is required");
                                } else {
                                    var errorMsgSpan = $('<span />', {
                                        "class": 'errorMsg',
                                        text: "This field is required"
                                    });
                                    parentId.append(errorMsgSpan);

                                    var errorMsgSpan1 = $('<span />', {
                                        "class": 'errorMsg',
                                        "style": 'margin-bottom:10px',
                                        text: "Appraisal Submission Failed!!!!"
                                    });
                                    $("#tabContent").prepend(errorMsgSpan1);
                                }
                            }
                        }
                    }
                    $(this).on("blur", function () {
                        parentId.find('span.errorMsg').remove();
                        $("#tabContent").find('span.errorMsg').remove();
                    });
                });
                if (appraiseeTabFail) {
                    App.unblockUI("#hris-page-content");
                    $("#"+checkingTab).addClass("active");
                    $("ul#tabList").find('a[href="#'+checkingTab+'"]').parent("li").addClass("active");
                    $("#"+submitTab).removeClass("active");
                    $("ul#tabList").find('a[href="#'+submitTab+'"]').parent("li").removeClass("active");
                    return false;
                } else {
                    return true;
                }
            });
        }
        appraisalReviewForm("appraisalReview2","appraisalReview1","portlet_tab2_3","portlet_tab2_2");
        appraisalReviewForm("appraisalReview1","appraisalReview","portlet_tab2_2","portlet_tab2_1");
    });
})(window.jQuery);