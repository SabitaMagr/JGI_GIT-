(function ($, app) {
    'use strict';
    var eclickFlag = false;
    var rulesForm = {
        payId: null,
        payCode: "",
        payEdesc: "",
        payLdesc: "",
        payTypeFlag: 'A',
        priorityIndex: 0,
        remarks: "",
        setRulesFromRemote: function (rules) {
            this.payId = rules.PAY_ID;
            this.payCode = rules.PAY_CODE;
            this.payEdesc = rules.PAY_EDESC;
            this.payLdesc = rules.PAY_LDESC;
            this.payTypeFlag = rules.PAY_TYPE_FLAG;
            this.priorityIndex = rules.PRIORITY_INDEX;
            this.remarks = rules.REMARKS;
        },
        setRulesFromView: function ( payCode, payEdesc, payLdesc, payTypeFlag, priorityIndex, remarks) {
            this.payCode = payCode;
            this.payEdesc = payEdesc;
            this.payLdesc = payLdesc;
            this.payTypeFlag = payTypeFlag;
            this.priorityIndex = priorityIndex;
            this.remarks = remarks;

        }
    };

    var setFormData = function () {
        $('#payCode').val(rulesForm.payCode);
        $('#payEdesc').val(rulesForm.payEdesc);
        $('#payLdesc').val(rulesForm.payLdesc);
        var $radios = $('input:radio[name=payTypeFlag]');
        // if ($radios.is(':checked') === false) {
        $radios.filter('[value=' + rulesForm.payTypeFlag + ']').prop('checked', true);
        // }
        // $("input[name=payTypeFlag]:checked").val();
        $('#priorityIndex').val(rulesForm.priorityIndex);
        $('#remarks').val(rulesForm.remarks);
    };

    var FormWizard = function () {
        return {
            init: function () {
                function e(e) {
                    return e.id ? "<img class='flag' src='../../assets/global/img/flags/" + e.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + e.text : e.text
                }

                if (jQuery().bootstrapWizard) {
                    var r = $("#submit_form"), t = $(".alert-danger", r), i = $(".alert-success", r);
                    var a = function () {
                        $("#tab4 .form-control-static", r).each(function () {
                            var e = $('[name="' + $(this).attr("data-display") + '"]', r);
                            if (e.is(":radio") && (e = $('[name="' + $(this).attr("data-display") + '"]:checked', r)), e.is(":text") || e.is("textarea"))$(this).html(e.val()); else if (e.is("select"))$(this).html(e.find("option:selected").text()); else if (e.is(":radio") && e.is(":checked"))$(this).html(e.attr("data-title")); else if ("payment[]" == $(this).attr("data-display")) {
                                var t = [];
                                $('[name="payment[]"]:checked', r).each(function () {
                                    t.push($(this).attr("data-title"))
                                }), $(this).html(t.join("<br>"))
                            }
                        })
                    }, o = function (e, r, t) {
                        var i = r.find("li").length, o = t + 1;
                        $(".step-title", $("#form_wizard_1")).text("Step " + (t + 1) + " of " + i), jQuery("li", $("#form_wizard_1")).removeClass("done");
                        for (var n = r.find("li"), s = 0; t > s; s++)jQuery(n[s]).addClass("done");
                        1 == o ? $("#form_wizard_1").find(".button-previous").hide() : $("#form_wizard_1").find(".button-previous").show(), o >= i ? ($("#form_wizard_1").find(".button-next").hide(), $("#form_wizard_1").find(".button-submit").show(), a()) : ($("#form_wizard_1").find(".button-next").show(), $("#form_wizard_1").find(".button-submit").hide()), App.scrollTo($(".page-title"))
                    };
                    $("#form_wizard_1").bootstrapWizard({
                        nextSelector: ".button-next",
                        previousSelector: ".button-previous",
                        onTabClick: function (e, r, t, i) {
                            return !1
                        },
                        onNext: function (e, a, n) {
                            if (eclickFlag) {
                                return i.hide(), t.hide(), void o(e, a, n);
                            }
                            switch (n) {
                                case 1:
                                    $('#Rules').submit();
                                    break;
                            }
                            return false;

                        },
                        onPrevious: function (e, r, a) {
                            i.hide(), t.hide(), o(e, r, a)
                        },
                        onTabShow: function (e, r, t) {
                            var i = r.find("li").length, a = t + 1, o = a / i * 100;
                            $("#form_wizard_1").find(".progress-bar").css({width: o + "%"})
                        }
                    }), $("#form_wizard_1").find(".button-previous").hide(), $("#form_wizard_1 .button-submit").click(function () {
                        alert("Finished! Hope you like it :)")
                    }).hide(), $("#country_list", r).change(function () {
                        r.validate().element($(this))
                    })
                }
            }
        }
    }();

    $(document).ready(function () {
        FormWizard.init();
        $('#Rules').validate({
            doNotHideMessage: !0,
            errorElement: "span",
            errorClass: "help-block help-block-error",
            focusInvalid: !1,
            rules: {
                payCode: {maxlength: 4, required: !0},
                payEdesc: {maxlength: 100, required: !0},
                payLdesc: {maxlength: 100, required: !0},
                priorityIndex: {required: !0},
                remarks: {maxlength: 255, required: !0}
            },
            messages: {},
            submitHandler: function (form) {
                rulesForm.setRulesFromView(
                    $('#payCode').val(),
                    $('#payEdesc').val(),
                    $('#payLdesc').val(),
                    $("input[name=payTypeFlag]:checked").val(),
                    $('#priorityIndex').val(),
                    $('#remarks').val()
                );

                console.log(rulesForm);


                app.pullDataById(document.url, {
                    action: 'pushRule',
                    data: JSON.parse(JSON.stringify(rulesForm))
                }).then(function (success) {
                    if(typeof success.data !== 'undefined'){
                    rulesForm.payId = success.data.payId;
                    }

                    eclickFlag = true;
                    $('.button-next').click();
                    eclickFlag = false;


                }, function (failure) {
                    console.log("failure", failure);

                });
            }
        });

        if (typeof  document.ruleId !== 'undefined') {
            app.pullDataById(document.url, {
                action: 'pullRule',
                data: {ruleId: document.ruleId}
            }).then(function (success) {
                console.log("success", success);
                rulesForm.setRulesFromRemote(success.data.rule);
                setFormData();
            }, function (failure) {
                console.log("failure", failure);
            });
        }

        var monthlyValues=document.monthlyValues;
        for(var i in monthlyValues){
            $('#monthlyValueList').append("<li>"+monthlyValues[i]+"</li>");
        }

        var flatValues=document.flatValues;
        for(var i in flatValues){
            $('#flatValueList').append("<li>"+flatValues[i]+"</li>");
        }


    });


})(window.jQuery, window.app);
