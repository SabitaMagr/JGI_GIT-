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
        setRulesFromView: function (payCode, payEdesc, payLdesc, payTypeFlag, priorityIndex, remarks) {
            this.payCode = payCode;
            this.payEdesc = payEdesc;
            this.payLdesc = payLdesc;
            this.payTypeFlag = payTypeFlag;
            this.priorityIndex = priorityIndex;
            this.remarks = remarks;

        }
    };
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
            console.log("fn updateView", this);
            editor.setValue((this.mnenonicName == null) ? "" : this.mnenonicName);
            isMonthlyCB.attr('checked', this.isMonthly);
        },
        pullRuleDetailByPayId: function (payId) {
            var obj = this;
            app.pullDataById(document.url, {
                action: 'pullRuleDetailByPayId',
                data: {payId: payId}
            }).then(function (success) {
                console.log("pullRuleDetailByPayId res", success);
                if (!((typeof success.data === 'undefined') || (success.data == null))) {
                    obj.setRuleDetailFromRemote(success.data)
                    obj.updateView();
                }
            }, function (failure) {
                console.log("failure", failure);
            });
        }
    };
    var positionAssigned = {
        remotePositions: [],
        positions: [],
        setPositionsFromRemote: function (positions) {
            for (var i in positions) {
                this.remotePositions.push(positions[i]["POSITION_ID"]);
                this.positions.push(positions[i]["POSITION_ID"]);
            }
        },
        updatePositions: function (id, status) {
            var filtered = this.positions.filter(function (value) {
                return value == id;
            });
            if (status && (filtered.length == 0)) {
                this.positions.push(id);
            } else if (!status && (filtered.length > 0)) {
                this.positions.splice(this.positions.indexOf(id), 1);
            }
        },
        updateView: function (id) {
            var positionListUl = $("#" + id);
            positionListUl.empty();
            var positionIds = Object.keys(document.positions);
            for (var pI in positionIds) {
                var checked = false;
                for (var sI in this.positions) {
                    if (positionIds[pI] == this.positions[sI]) {
                        checked = true;
                    }
                }
                var checkedHtml = checked ? 'checked' : '';
                positionListUl.append("<div class='md-checkbox'>" +
                        "<input type='checkbox' class='md-check' id='positionCB" + positionIds[pI] + "'  p-id='" + positionIds[pI] + "' " + checkedHtml + ">" +
                        "<label for='positionCB" + positionIds[pI] + "'>" +
                        "<span></span>" +
                        "<span class='check'></span>" +
                        "<span class='box'></span>" +
                        document.positions[positionIds[pI]] +
                        "</label>" +
                        "</div>")
                $("#positionCB" + positionIds[pI]).on('change', function () {
                    console.log($(this).prop('checked'));
                    positionAssigned.updatePositions($(this).attr('p-id'), $(this).prop('checked'));
                });
            }

        },
        pullPositionAssignedByPayId: function (payId) {
            var obj = this;
            app.pullDataById(document.url, {
                action: 'pullPositionsAssignedByPayId',
                data: {payId: payId}
            }).then(function (success) {
                console.log("success", success);
                if (typeof success.data !== 'undefined') {
                    obj.setPositionsFromRemote(success.data)
                    obj.updateView("positionList");
                }
            }, function (failure) {
                console.log("failure", failure);
            });
        },
        pushPositionAssigned: function () {
            var notChangedPositions = [];
            var needsToBeDeleted = [];
            var needsToBeAdded = [];
            for (var i in this.remotePositions) {
                var isNotChanged = false;
                for (var j in this.positions) {
                    if (this.remotePositions[i] == this.positions[j]) {
                        isNotChanged = true;
                    }
                }
                if (isNotChanged) {
                    notChangedPositions.push(this.remotePositions[i]);
                } else {
                    needsToBeDeleted.push(this.remotePositions[i]);
                }
            }


            for (var i in this.positions) {
                var common = false;
                for (var j in notChangedPositions) {
                    if (this.positions[i] == notChangedPositions[j]) {
                        common = true;
                    }
                }
                if (!common) {
                    needsToBeAdded.push(this.positions[i]);
                }
            }
            console.log("not changed", notChangedPositions);
            console.log("delete", needsToBeDeleted);
            console.log("add", needsToBeAdded);

            var promises = [];
            if (needsToBeAdded.length > 0) {
                promises.push(app.pullDataById(document.url, {
                    action: 'addPositionAssigned',
                    data: {
                        positions: needsToBeAdded,
                        payId: rulesForm.payId
                    }
                }));
            }

            if (needsToBeDeleted.length > 0) {
                promises.push(app.pullDataById(document.url, {
                    action: 'deletePositionAssigned',
                    data: {
                        positions: needsToBeDeleted,
                        payId: rulesForm.payId
                    }
                }));
            }
            Promise.all(promises)
                    .then(function (success) {
                        console.log('PositionAssigned', success);
                        eclickFlag = true;
//                        $('.button-next').click();
                        app.successMessage("Setup complete!");
                        location.href = document.rulesIndexUrl;
                        eclickFlag = false;
                    }, function (failure) {
                        console.log('PositionAssigned', failure);
                    });
        }
    }

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

    var pushRuleDetail = function () {
        ruleDetail.payId = rulesForm.payId;
        ruleDetail.updateModel(editor.getValue());
//        console.log("ruledetail", JSON.parse(JSON.stringify(ruleDetail)));
//        return;
        app.pullDataById(document.url, {
            action: 'pushRuleDetail',
            data: JSON.parse(JSON.stringify(ruleDetail))
        }).then(function (success) {
            console.log("success", success);
            eclickFlag = true;
            $('.button-next').click();
            positionAssigned.pullPositionAssignedByPayId(rulesForm.payId);
            eclickFlag = false;
        }, function (failure) {
            console.log("failure", failure);
        });
    };
    var editor;
    var initializeCodeMirror = function () {
        editor = CodeMirror.fromTextArea(document.getElementById('rule'), {
            lineNumbers: true,
            mode: "htmlmixed"
        });
    };

    var isMonthlyCB = $('#isMonthly');
    isMonthlyCB.on("change", function (e) {
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
//                                    positionAssigned.pushPositionAssigned();
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
                        positionAssigned.pushPositionAssigned();
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

                console.log(rulesForm);


                app.pullDataById(document.url, {
                    action: 'pushRule',
                    data: JSON.parse(JSON.stringify(rulesForm))
                }).then(function (success) {
                    if (typeof success.data !== 'undefined') {
                        rulesForm.payId = success.data.payId;
                    }

                    eclickFlag = true;
                    $('.button-next').click();
                    eclickFlag = false;
                    initializeCodeMirror();
                    ruleDetail.pullRuleDetailByPayId(rulesForm.payId);

                }, function (failure) {
                    console.log("failure", failure);

                });
            }
        });

        if ((typeof document.ruleId !== 'undefined') && (document.ruleId != 0)) {
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

        var monthlyValues = document.monthlyValues;
        var flatValues = document.flatValues;
        var variables = document.variables;
        var systemRules = document.systemRules;

        for (var i in monthlyValues) {
            monthlyValues[i] = replaceAll(monthlyValues[i], " ", "_");
            monthlyValues[i] = monthlyValues[i].toUpperCase();
            $('#monthlyValueList').append("<button class='list-group-item ' id='vars' >" + monthlyValues[i] + "</button>");
        }

        for (var i in flatValues) {
            flatValues[i] = replaceAll(flatValues[i], " ", "_");
            flatValues[i] = flatValues[i].toUpperCase();
            $('#flatValueList').append("<button class='list-group-item ' id='vars' >" + flatValues[i] + "</button>");
        }

        for (var i in variables) {
            $('#variables').append("<button class='list-group-item .list-group-item-text' id='vars' >" + variables[i] + "</button>");
        }

        for (var i in systemRules) {
            $('#systemRules').append("<button class='list-group-item ' id='vars' >" + systemRules[i] + "</button>");
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

//        var month = {
//            fiscalYearId: null,
//            monthId: null,
//            monthEdesc: null,
//            monthNdesc: null,
//            fromDate: null,
//            toDate: null,
//        };

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
                        console.log("pullMonthsByFiscalYear res", success);
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
                console.log('month', month);
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
                    var monthId = $(this).val();
                    if (typeof monthId === 'undefined' || monthId == null || monthId == '') {
                        console.log("not a monthId", monthId);
                        return;
                    }
                    var month = parent.months.filter(function (item) {
                        return item.MONTH_ID == monthId;
                    });
                    parent.updateDayView(month[0]);
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

    });


})(window.jQuery, window.app);
