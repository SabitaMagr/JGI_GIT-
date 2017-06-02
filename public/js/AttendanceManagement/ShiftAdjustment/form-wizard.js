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

                        if (index == 1 || index == 2 || index == 3) {
                            var returnval= processing(index);
                            if(returnval==false){
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
                    alert('Finished! Hope you like it :)');
                }).hide();


                var processing = function (tabindex) {
                    if (tabindex == 1) {
                        console.log(tabindex);
                        var startDate = $('#adjustmentStartDate').val();
                        var endDate = $('#adjustmentEndDate').val();
                        var startTime = $('#startTime').val();
                        var endTime = $('#endTime').val();


                        if (!startTime.trim() || !endTime.trim()) {
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
                            console.log(shiftAssignId);
                        }, function (failure) {
                            console.log("failure", failure);
                        });
                    }
                    return true;
                }

            }

        };


    }();

    $(document).ready(function () {
        FormWizard.init();
    });


})(window.jQuery, window.app);


angular.module('hris', [])
        .controller('ShiftAjustmentController', function ($scope) {
//            var $companyId = angular.element(document.getElementById('companyId'));
//            var $branchId = angular.element(document.getElementById('branchId'));
//            var $departmentId = angular.element(document.getElementById('departmentId'));
//            var $designationId = angular.element(document.getElementById('designationId'));
//            var $positionId = angular.element(document.getElementById('positionId'));
//            var $serviceTypeId = angular.element(document.getElementById('serviceTypeId'));
//            var $serviceEventTypeId = angular.element(document.getElementById('serviceEventTypeId'));
//            var $employeeId = angular.element(document.getElementById('employeeId'));
//            var $genderId = angular.element(document.getElementById('genderId'));


//            $scope.holidayList = document.holidayList;
//
//            $scope.employeeList = [];
//            $scope.alreadyAssignedEmpList = [];
//            $scope.all = false;
//
//            $scope.checkAll = function (item) {
//                for (var i = 0; i < $scope.employeeList.length; i++) {
//                    $scope.employeeList[i].checked = item;
//                }
//            };



//            $scope.view = function () {
//                window.app.pullDataById(document.wsGetEmployeeList, {
//                    companyId: $companyId.val(),
//                    branchId: $branchId.val(),
//                    departmentId: $departmentId.val(),
//                    designationId: $designationId.val(),
//                    positionId: $positionId.val(),
//                    serviceTypeId: $serviceTypeId.val(),
//                    serviceEventTypeId: $serviceEventTypeId.val(),
//                    employeeId: $employeeId.val(),
//                    genderId: $genderId.val()
//                }).then(function (response) {
//                    $scope.$apply(function () {
//                        $scope.employeeList = [];
//                        var empList = response.data;
//                        for (var i in empList) {
//                            var emp = empList[i];
//                            emp.checked = ($scope.alreadyAssignedEmpList.indexOf(emp.EMPLOYEE_ID) >= 0);
//                            $scope.employeeList.push(emp);
//                        }
//                    });
//                    window.app.scrollTo('employeeTable');
//
//                }, function (failure) {
//
//                });
//                $scope.holidayChangeFn();
//            };

//            $scope.assign = function () {
//                if ($scope.employeeList.length == 0) {
//                    window.app.showMessage("No Employees to Assign.", "error");
//                    return;
//                }
//                if ($scope.holiday == null) {
//                    window.app.showMessage("Select the holiday first to assign to", "error");
//                    return;
//                }
//
//
//                var checkedEmpList = [];
//                for (var index in $scope.employeeList) {
//                    if ($scope.employeeList[index].checked) {
//                        checkedEmpList.push($scope.employeeList[index].EMPLOYEE_ID);
//                    }
//                }
//                App.blockUI({target: "#hris-page-content"});
//                window.app.pullDataById(document.wsAssignHolidayToEmployees, {
//                    holidayId: $scope.holiday,
//                    employeeIdList: checkedEmpList
//                }).then(function (response) {
//                    App.unblockUI("#hris-page-content");
//                    if (response.success) {
//                        window.app.showMessage("Holiday Assigned Successfully");
//                    } else {
//                        window.app.showMessage(response.error);
//                    }
//                }, function (failure) {
//                    console.log("shift Assign Filter Success Response", failure);
//                });
//
//            };

        });