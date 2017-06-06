(function ($, app) {
    'use strict';
    var shiftAssignId;

    var FormWizard = function () {


        return {
            //main function to initiate the module
            init: function () {
                if (!jQuery().bootstrapWizard) {
                    return;
                }

                function format(state) {
                    if (!state.id)
                        return state.text; // optgroup
                    return "<img class='flag' src='../../assets/global/img/flags/" + state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
                }

                $("#country_list").select2({
                    placeholder: "Select",
                    allowClear: true,
                    formatResult: format,
                    formatSelection: format,
                    escapeMarkup: function (m) {
                        return m;
                    }
                });

                var form = $('#submit_form');
                var error = $('.alert-danger', form);
                var success = $('.alert-success', form);


                var displayConfirm = function () {
                    $('#tab4 .form-control-static', form).each(function () {
                        var input = $('[name="' + $(this).attr("data-display") + '"]', form);
                        if (input.is(":radio")) {
                            input = $('[name="' + $(this).attr("data-display") + '"]:checked', form);
                        }
                        if (input.is(":text") || input.is("textarea")) {
                            $(this).html(input.val());
                        } else if (input.is("select")) {
                            $(this).html(input.find('option:selected').text());
                        } else if (input.is(":radio") && input.is(":checked")) {
                            $(this).html(input.attr("data-title"));
                        } else if ($(this).attr("data-display") == 'payment[]') {
                            var payment = [];
                            $('[name="payment[]"]:checked', form).each(function () {
                                payment.push($(this).attr('data-title'));
                            });
                            $(this).html(payment.join("<br>"));
                        }
                    });
                }

                var handleTitle = function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard_1')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard_1').find('.button-previous').hide();
                    } else {
                        $('#form_wizard_1').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard_1').find('.button-next').hide();
                        $('#form_wizard_1').find('.button-submit').show();
                        displayConfirm();
                    } else {
                        $('#form_wizard_1').find('.button-next').show();
                        $('#form_wizard_1').find('.button-submit').hide();
                    }
//                Metronic.scrollTo($('.page-title'));
                }

                // default form wizard
                $('#form_wizard_1').bootstrapWizard({
                    'nextSelector': '.button-next',
                    'previousSelector': '.button-previous',
                    onTabClick: function (tab, navigation, index, clickedIndex) {
                        return false;
                        /*
                         success.hide();
                         error.hide();
                         if (form.valid() == false) {
                         return false;
                         }
                         handleTitle(tab, navigation, clickedIndex);
                         */
                    },
                    onNext: function (tab, navigation, index) {
//                        success.hide();
//                        error.hide();
                        if (form.valid() == false) {
                            return false;
                        }

                        if (index == 1 || index == 2) {
                            var returnval = processing(index);
                            if (returnval == false) {
                                return false;
                            }
                        }
//                    else{
//                        return false;
//                    }

//
                        handleTitle(tab, navigation, index);
                    },
                    onPrevious: function (tab, navigation, index) {
                        success.hide();
                        error.hide();

                        handleTitle(tab, navigation, index);
                    },
                    onTabShow: function (tab, navigation, index) {
                        var total = navigation.find('li').length;
                        var current = index + 1;
                        var $percent = (current / total) * 100;
                        $('#form_wizard_1').find('.progress-bar').css({
                            width: $percent + '%'
                        });
                    }
                });

                $('#form_wizard_1').find('.button-previous').hide();
                $('#form_wizard_1 .button-submit').click(function () {
//                    alert('Finished! Hope you like it :)');
                    $('#submit_form').submit();
                }).hide();


                var processing = function (tabindex) {
//                        console.log(tabindex);
                    var startDate = $('#adjustmentStartDate').val();
                    var endDate = $('#adjustmentEndDate').val();
                    var startTime = $('#startTime').val();
                    var endTime = $('#endTime').val();

                    if (tabindex == 1) {

                        if (!startTime.trim() || !endTime.trim()) {
                            if (!startTime.trim()) {
                                $('#errorStartTime').text('Start Time field is required');
                            } else {
                                $('#errorStartTime').text('');
                            }
                            if (!startTime.trim()) {
                                $('#errorEndTime').text('End Time field is required');
                            } else {
                                $('#errorEndTime').text('');
                            }
                            return false;
                        }

                        app.pullDataById(document.addShiftAdjustUrl, {
                            'adjustmentStartDate': startDate,
                            'adjustmentEndDate': endDate,
                            'startTime': startTime,
                            'endTime': endTime
                        }).then(function (success) {
//                            console.log("success", success);
                            shiftAssignId = success.dataId;
//                            console.log(shiftAssignId);
                        }, function (failure) {
                            console.log("failure", failure);
                        });
                    }

                    if (tabindex == 2) {
                        var todayDate;
                        var checkedCount = $(".allchekbox:checked").length;
                        if (checkedCount <= 0) {
                            alert('no employee assigned');
                            return false;
                        }

                        app.getServerDate().then(function (response) {
                            todayDate = response.data['serverDate'];
                            if (startDate > todayDate) {
                            } else {
                            }
                        })

                    }


                    return true;
                }

            }

        };


    }();

    $(document).ready(function () {
        FormWizard.init();

        var $companyId = $('#companyId');
        var $branchId = $('#branchId');
        var $departmentId = $('#departmentId');
        var $designationId = $('#designationId');
        var $positionId = $('#positionId');
        var $serviceTypeId = $('#serviceTypeId');
        var $serviceEventTypeId = $('#serviceEventTypeId');
        var $employeeId = $('#employeeId');
        var $genderId = $('#genderId');


        $('#filterEmp').on('click', function () {

            app.pullDataById(document.wsGetEmployeeList, {
                companyId: $companyId.val(),
                branchId: $branchId.val(),
                departmentId: $departmentId.val(),
                designationId: $designationId.val(),
                positionId: $positionId.val(),
                serviceTypeId: $serviceTypeId.val(),
                serviceEventTypeId: $serviceEventTypeId.val(),
                employeeId: $employeeId.val(),
                genderId: $genderId.val()
            }).then(function (success) {
                console.log(success.data);
                $("#employeeTable").find('tbody').empty();
                $.each(success.data, function (k, v) {
                    var appendData = "<tr>"
                            + "<td>" + v.FIRST_NAME + "</td>"
                            + "<td>" + v.BRANCH_NAME + "</td>"
                            + "<td>" + v.DEPARTMENT_NAME + "</td>"
                            + "<td>" + v.DESIGNATION_TITLE + "</td>"
                            + "<td></td>"
                            + "<td></td>"
                            + "<td>"
                            + "<div class='th-inner'><label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                            + "<input class='allchekbox' type='checkbox' name='checkapply[]' value='" + v.EMPLOYEE_ID + "'>"
                            + "<span></span></label></div></td>";
                    +"<tr>";

                    $("#employeeTable").find('tbody').append(appendData);
                });
            }, function (failure) {
                console.log("failure", failure);
            });


        });


        $('#ckeckAll').on('click', function () {
            var checkedStatus = $(this).is(":checked");
            console.log($(this).is(":checked"));
            if (checkedStatus) {
                $('.allchekbox').attr('checked', 'checked');
            } else {
                $('.allchekbox').removeAttr('checked');
            }
        });



    });


})(window.jQuery, window.app);

