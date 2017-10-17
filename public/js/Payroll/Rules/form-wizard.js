(function ($, app) {
    'use strict';
    var eclickFlag = false;
    /*
     * Rule Object
     */
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
        setRulesFromView: function (payCode, payEdesc, payLdesc, payTypeFlag, priorityIndex, remarks) {
            this.payCode = payCode;
            this.payEdesc = payEdesc;
            this.payLdesc = payLdesc;
            this.payTypeFlag = payTypeFlag;
            this.priorityIndex = priorityIndex;
            this.remarks = remarks;

        }
    };

    /*
     * RuleDetail Object
     */
    var ruleDetail = {
        srNo: null,
        mnenonicName: "",
        isMonthly: false,
        setRuleDetailFromRemote: function (ruleDetail) {
            this.srNo = ruleDetail.SR_NO;
            this.mnenonicName = ruleDetail.MNENONIC_NAME;
            this.isMonthly = (ruleDetail.IS_MONTHLY == "Y") ? true : false;
        },
        updateModel: function (mne) {
            this.mnenonicName = mne;
        },
        updateIsMonthly: function (isMonthly) {
            this.isMonthly = isMonthly;
        },
        updateView: function () {
            editor.setValue((this.mnenonicName == null) ? "" : this.mnenonicName);
            $isMonthlyCB.attr('checked', this.isMonthly);
        },
        pullRuleDetailByPayId: function (payId) {
            var obj = this;
            app.pullDataById(document.pullRuleDetailByPayIdLink, {
                payId: payId
            }).then(function (success) {
                if (!((typeof success.data === 'undefined') || (success.data == null))) {
                    obj.setRuleDetailFromRemote(success.data)
                    obj.updateView();
                }
            }, function (failure) {
                console.log("failure", failure);
            });
        },
        pullReferencedRules: function (payId) {
            var obj = this;
            app.pullDataById(document.pullReferencedRules, {
                payId: payId
            }).then(function (success) {
                var referencingRules = success;
                for (var i in referencingRules) {
                    referencingRules[i].PAY_EDESC = replaceAll(referencingRules[i].PAY_EDESC, " ", "_");
                    referencingRules[i].PAY_EDESC = referencingRules[i].PAY_EDESC.toUpperCase();
                    $('#referencingRules').append("<li class='ms-elem-selectable refVars' ruleId='" + referencingRules[i].PAY_ID + "' ><span>" + referencingRules[i].PAY_EDESC + "</span></li>");
                }
                $('.refVars').on('click', function () {
                    var $this = $(this);
                    var cursor = editor.getCursor();
                    editor.replaceRange("(" + $this.text() + ")", cursor, null);
                });
            }, function (failure) {
                console.log("pullReferencedrules fail", failure);
            });
        }
    };
    /*
     * EmployeeRule Object
     */
    var employeeRule = {
        employees: [],
        pullEmployees: function (payId) {
            app.pullDataById(document.getRuleEmployeeWS, {payId: payId}).then(function (response) {
                this.employees = response.data;
            }.bind(this), function (error) {
                console.log(error);
            });
        },
        pushEmployees: function (payId) {
            app.pullDataById(document.putRuleEmployeeWS, {payId: payId, employees: this.employees}).then(function (response) {
                eclickFlag = true;
                app.showMessage("Setup Completed Successfully!");
                location.href = document.rulesIndexUrl;
                eclickFlag = false;
            }, function (error) {
                console.log(error);
            });
        }
    };
    /*
     * Function for Setting Form on Edit
     */
    var setFormData = function () {
        $('#payCode').val(rulesForm.payCode);
        $('#payEdesc').val(rulesForm.payEdesc);
        $('#payLdesc').val(rulesForm.payLdesc);

        var $radios = $('input:radio[name=payTypeFlag]');
        $radios.filter('[value=' + rulesForm.payTypeFlag + ']').prop('checked', true);

        $('#priorityIndex').val(rulesForm.priorityIndex);
        $('#remarks').val(rulesForm.remarks);
    };

    /*
     * Function for Saving Rule Detail
     */
    var pushRuleDetail = function () {
        ruleDetail.payId = rulesForm.payId;
        ruleDetail.updateModel(editor.getValue());
        app.pullDataById(document.pushRuleDetailLink, JSON.parse(JSON.stringify(ruleDetail))).then(function (success) {
            eclickFlag = true;
            $('.button-next').click();
            employeeRule.pullEmployees(rulesForm.payId);
            eclickFlag = false;
        }, function (failure) {
            console.log("failure", failure);
        });
    };

    var editor = null;
    var initializeCodeMirror = function () {
        if (editor !== null) {
            return;
        }
        editor = CodeMirror.fromTextArea(document.getElementById('rule'), {
            lineNumbers: true,
            mode: "htmlmixed"
        });
    };


    var $isMonthlyCB = $('#isMonthly');
    $isMonthlyCB.on("change", function (e) {
        ruleDetail.updateIsMonthly(e.target.checked)
    });

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
                            if (e.is(":radio") && (e = $('[name="' + $(this).attr("data-display") + '"]:checked', r)), e.is(":text") || e.is("textarea"))
                                $(this).html(e.val());
                            else if (e.is("select"))
                                $(this).html(e.find("option:selected").text());
                            else if (e.is(":radio") && e.is(":checked"))
                                $(this).html(e.attr("data-title"));
                            else if ("payment[]" == $(this).attr("data-display")) {
                                var t = [];
                                $('[name="payment[]"]:checked', r).each(function () {
                                    t.push($(this).attr("data-title"))
                                }), $(this).html(t.join("<br>"))
                            }
                        })
                    }, o = function (e, r, t) {
                        var i = r.find("li").length, o = t + 1;
                        $(".step-title", $("#form_wizard_1")).text("Step " + (t + 1) + " of " + i), jQuery("li", $("#form_wizard_1")).removeClass("done");
                        for (var n = r.find("li"), s = 0; t > s; s++)
                            jQuery(n[s]).addClass("done");
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
                                case 2:
                                    pushRuleDetail();
                                    break;
                                case 3:
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
                    }),
                            $("#form_wizard_1").find(".button-previous").hide(),
                            $("#form_wizard_1 .button-submit").click(function () {
                        employeeRule.pushEmployees(rulesForm.payId);
                    }).hide(),
                            $("#country_list", r).change(function () {
                        r.validate().element($(this))
                    })
                }
            }
        }
    }();

    var replaceAll = function (rule, val, newVal) {
        if (rule.indexOf(val) >= 0) {
            rule = rule.replace(val, newVal);
            return replaceAll(rule, val, newVal);
        } else {
            return rule;
        }
    }
    var addValue = function (item) {
        console.log($(item).val());

    };


    window.MAX = function (val) {
        return 100 * val;
    }

    var registerForUniqueInput = function () {
        app.checkUniqueConstraints("payCode", "Rules", document.ruleTableName, document.ruleTableUniqueColName1, document.ruleTablePKColName, rulesForm.payId);
        app.checkUniqueConstraints("priorityIndex", "Rules", document.ruleTableName, document.ruleTableUniqueColName2, document.ruleTablePKColName, rulesForm.payId);
    };


    $(document).ready(function () {
        FormWizard.init();
        registerForUniqueInput();
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

                app.pullDataById(document.pushRuleLink,
                        JSON.parse(JSON.stringify(rulesForm))
                        ).then(function (success) {
                    if (typeof success.data !== 'undefined') {
                        rulesForm.payId = success.data.payId;
                    }

                    eclickFlag = true;
                    $('.button-next').click();
                    eclickFlag = false;
                    initializeCodeMirror();
                    ruleDetail.pullRuleDetailByPayId(rulesForm.payId);
                    ruleDetail.pullReferencedRules(rulesForm.payId);

                }, function (failure) {
                    console.log("failure", failure);

                });
            }
        });

        if ((typeof document.ruleId !== 'undefined') && (document.ruleId != 0)) {
            app.pullDataById(document.pullRuleLink, {
                ruleId: document.ruleId
            }).then(function (success) {
                rulesForm.setRulesFromRemote(success.data.rule);
                setFormData();
            }, function (failure) {
                console.log("failure", failure);
            });
        }

        var monthlyValues = document.monthlyValues;
        var flatValues = document.flatValues;
        var variables = document.variables;
        var systemRules = document.systemRules;


        for (var i in monthlyValues) {
            monthlyValues[i] = replaceAll(monthlyValues[i], " ", "_");
            monthlyValues[i] = monthlyValues[i].toUpperCase();
            $('#monthlyValueList').append("<li class='ms-elem-selectable' id='vars' ><span>" + monthlyValues[i] + "</span></li>");
        }

        for (var i in flatValues) {
            flatValues[i] = replaceAll(flatValues[i], " ", "_");
            flatValues[i] = flatValues[i].toUpperCase();
            $('#flatValueList').append("<li class='ms-elem-selectable' id='vars' ><span>" + flatValues[i] + "</span></li>");
        }

        for (var i in variables) {
            $('#variables').append("<li class='ms-elem-selectable' id='vars' ><span>" + variables[i] + "</span></li>");
        }

        for (var i in systemRules) {
            $('#systemRules').append("<li class='ms-elem-selectable' id='vars' ><span>" + systemRules[i] + "</span></li>");
        }




        $('#check').on("click", function (event) {
            var rule = editor.getValue();
            for (var i in monthlyValues) {
                rule = replaceAll(rule, monthlyValues[i], 1);
            }

            for (var i in flatValues) {
                rule = replaceAll(rule, flatValues[i], 1);
            }
            try {
                console.log("before eval", rule);
                console.log(eval(rule));
            } catch (e) {
                if (e instanceof SyntaxError) {
                    alert(e.message);
                }
            }


        });

        var insertAt = function (concatTo, concatWith, pos) {
            return [concatTo.slice(0, pos), concatWith, concatTo.slice(pos)].join('');
        };
        var vars = document.querySelectorAll('#vars');
        for (var i = 0; i < vars.length; i++) {
            $(vars[i]).on('click', function () {
                var $this = $(this);
                var cursor = editor.getCursor();
                editor.replaceRange("[" + $this.text() + "]", cursor, null);
            });
        }

        var Calendar = function (yearId, monthId, dayId, pickMonthId, $) {
            this.$year = $("#" + yearId)
            this.$month = $("#" + monthId);
            this.$day = $("#" + dayId);
            this.$pickMonth = $("#" + pickMonthId);
            this.years = document.fiscalYears;
            this.months = null;
            var parent = this;

            this.pullRemoteMonths = function (fiscalYearId) {
                if (fiscalYearId != null) {
                    app.pullDataById(document.restfulUrl, {
                        action: 'pullMonthsByFiscalYear',
                        data: {
                            'fiscalYearId': fiscalYearId,
                        }
                    }).then(function (success) {
                        parent.months = success.data;
                        parent.updateMonthView();
                    }, function (failure) {
                        console.log("pullMonthsByFiscalYear fail", failure);
                    });
                } else {
                    this.months = [];
                }

            };
            this.updateMonthView = function () {
                this.$month.html("");
                this.$month.append($("<option />").val(null).text('Select month'));
                $.each(this.months, function () {
                    parent.$month.append($("<option />").val(this.MONTH_ID).text(this.MONTH_EDESC));
                });
            };

            this.updateDayView = function (month) {
                var fromDateLongVal = Date.parse(month.FROM_DATE);
                var toDateLongVal = Date.parse(month.TO_DATE);

                var fromDate = new Date(fromDateLongVal);
                var toDate = new Date(toDateLongVal);

                var diffValue = toDateLongVal - fromDateLongVal;
                var dateDifference = diffValue / (1000 * 60 * 60 * 24);

                this.$day.html("");
                for (var i = 1; i <= dateDifference; i++) {
                    var newDate = new Date();
                    newDate.setDate(fromDate.getDate() + i);
                    var newDateFormatted = "(" + (newDate.getMonth() + 1) + "-" + newDate.getDate() + "-" + newDate.getFullYear() + ")";
                    this.$day.append($("<button></button>").text(i + "").attr("data-value", newDateFormatted));
                }
            };


            this.initializeView = function () {
                this.$year.append($("<option />").val(null).text('Select year'));

                for (var key in this.years) {
                    this.$year.append($("<option />").val(key).text(this.years[key]));
                }
                this.$year.on('change', function () {
                    parent.pullRemoteMonths($(this).val());
                });

                this.$month.on('change', function () {

                });
                this.$day.delegate('button', "click", function () {
                    var $this = $(this);
                    var cursor = editor.getCursor();
                    editor.replaceRange($this.attr('data-value'), cursor, null);
                });

                this.$pickMonth.on("click", function () {
                    var monthId = parent.$month.val();
                    if (typeof monthId === 'undefined' || monthId == null) {
                        return;
                    }
                    var cursor = editor.getCursor();
                    editor.replaceRange(monthId, cursor, null);

                });

            };

        };

        var calendar = new Calendar('years', 'months', 'days', 'pickMonth', $);
        calendar.initializeView();

//         populating gender
        var genders = document.genders;
        var $gender = $('#gender');
        for (var i in genders) {
            $gender.append($("<option ></option").val(i).text(genders[i]));
        }
        $gender.on('change', function () {
            var $this = $(this);
            var cursor = editor.getCursor();
            editor.replaceRange($this.val(), cursor, null);
        });
//        end ofgender

//    populating emp-type
        var serviceTypes = document.serviceTypes;
        var $serviceType = $('#serviceType');
        for (var i in serviceTypes) {
            $serviceType.append($("<option ></option").val(i).text(serviceTypes[i]));
        }
        $serviceType.on('change', function () {
            var $this = $(this);
            var cursor = editor.getCursor();
            editor.replaceRange($this.val(), cursor, null);
        });

// end of populating emp-type

        var $searchBtn = $('#searchBtn');
        var $table = $('#employeeTable');
        var $checkAll = $('#checkAll');
        $searchBtn.on('click', function () {
            $table.find('tbody').empty();
            var filteredEmployeeList = document.searchManager.getEmployee();
            $.each(filteredEmployeeList, function (k, v) {
                var appendData = "<tr>"
                        + "<td>" + v.FULL_NAME + "</td>"
                        + "<td>"
                        + "<div class='th-inner'><label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                        + "<input class='check' type='checkbox' name='checkapply[]' value='" + v.EMPLOYEE_ID + "'>"
                        + "<span></span></label></div></td>";
                +"<tr>";

                $("#employeeTable").find('tbody').append(appendData);
            });
            $('.check').each(function () {
                var $this = $(this);
                var value = $this.val();
                var filteredList = employeeRule.employees.filter(function (item) {
                    return item == value;
                });
                if (filteredList.length > 0) {
                    $this.prop('checked', true);
                }
                $this.on('change', function () {
                    var checkedStatus = $(this).is(":checked")
                    var index = employeeRule.employees.indexOf(value);
                    if (checkedStatus) {
                        if (index < 0) {
                            employeeRule.employees.push(value);
                        }
                    } else {
                        if (index > -1) {
                            employeeRule.employees.splice(index, 1);
                        }
                    }
                });

            });
        });

        $checkAll.on('click', function () {
            var checkedStatus = $(this).is(":checked");
            $('.check').prop('checked', checkedStatus);

            $('.check').each(function () {
                var $this = $(this);
                var checkedStatus = $this.is(":checked");
                var value = $this.val();
                var index = employeeRule.employees.indexOf(value);
                if (checkedStatus) {
                    if (index < 0) {
                        employeeRule.employees.push(value);
                    }
                } else {
                    if (index > -1) {
                        employeeRule.employees.splice(index, 1);
                    }
                }
            });
        });

    });


})(window.jQuery, window.app);
