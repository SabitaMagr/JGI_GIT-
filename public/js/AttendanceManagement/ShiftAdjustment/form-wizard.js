(function ($, app) {
    'use strict';
    $(document).ready(function () {

        if (!jQuery().bootstrapWizard) {
            return;
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
            } else {
                $('#form_wizard_1').find('.button-next').show();
                $('#form_wizard_1').find('.button-submit').hide();
            }
        }

        var shiftAdjustment = {adjustmentStartDate: null, adjustmentEndDate: null, startTime: null, endTime: null};

        // default form wizard
        $('#form_wizard_1').bootstrapWizard({
            'nextSelector': '.button-next',
            'previousSelector': '.button-previous',
            onTabClick: function (tab, navigation, index, clickedIndex) {
                return false;
            },
            onNext: function (tab, navigation, index) {
                var nextFlag = true;
                switch (index) {
                    case 1:
                        $shiftAdjustmentForm.on('submit', function () {
                            var $this = $(this);
                            if (!$this.valid()) {
                                nextFlag = false;
                            }

                            shiftAdjustment['adjustmentStartDate'] = $adjustmentStartDate.val();
                            shiftAdjustment['adjustmentEndDate'] = $adjustmentEndDate.val();
                            shiftAdjustment['startTime'] = $startTime.val();
                            shiftAdjustment['endTime'] = $endTime.val();


                            return false;
                        });
                        $shiftAdjustmentForm.submit();
                        break;
                    case 2:
                        if (shiftAdjustedEmployeeList.length === 0) {
                            nextFlag = false;
                        }
                        break;
                    case 3:


                        break;
                }
                if (!nextFlag) {
                    return false;
                }

                handleTitle(tab, navigation, index);

            },
            onPrevious: function (tab, navigation, index) {
                handleTitle(tab, navigation, index);
            },
            onTabShow: function (tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                var $percent = (current / total) * 100;
                $('#form_wizard_1').find('.progress-bar').css({
                    width: $percent + '%'
                });
                if (index === 2) {
                    $adjustmentStartDateLabel.html(shiftAdjustment.adjustmentStartDate);
                    $adjustmentEndDateLabel.html(shiftAdjustment.adjustmentEndDate);
                    $startTimeLabel.html(shiftAdjustment.startTime);
                    $endTimeLabel.html(shiftAdjustment.endTime);

                    $.each(shiftAdjustedEmployeeList, function (item) {
                        $assignedEmployeeList.append('<a href="javascript:;" class="btn btn-sm default"> Ukesh Gaiju<i class="fa fa-user"></i></a>');
                    });
                }
            }
        });

        $('#form_wizard_1').find('.button-previous').hide();
        $('#form_wizard_1 .button-submit').click(function () {
            shiftAdjustment['employeeList'] = shiftAdjustedEmployeeList;
            app.pullDataById(document.shiftAdjustAddUrl, shiftAdjustment).then(function (response) {

            }, function (error) {

            });
        }).hide();


        /*
         * 
         */
        var $shiftAdjustmentForm = $('#shift-adjustment-form');
        var $adjustmentStartDate = $('#adjustmentStartDate');
        var $adjustmentEndDate = $('#adjustmentEndDate');
        var $startTime = $('#startTime');
        var $endTime = $('#endTime');

        var shiftAdjustedEmployeeList = [];

        var $searchBtn = $('#searchBtn');
        var $table = $('#employeeTable');
        var $checkAll = $('#checkAll');

        var $adjustmentStartDateLabel = $('#adjustmentStartDateLabel');
        var $adjustmentEndDateLabel = $('#adjustmentEndDateLabel');
        var $startTimeLabel = $('#startTimeLabel');
        var $endTimeLabel = $('#endTimeLabel');

        var $assignedEmployeeList = $('#assignedEmployeeList');

        $searchBtn.on('click', function () {
            $table.find('tbody').empty();
            var filteredEmployeeList = document.searchManager.getEmployee();
            $.each(filteredEmployeeList, function (k, v) {
                var appendData = "<tr>"
                        + "<td>" + v.FIRST_NAME + "</td>"
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
                var filteredList = shiftAdjustedEmployeeList.filter(function (item) {
                    return item == value;
                });
                if (filteredList.length > 0) {
                    $this.prop('checked', true);
                }
                $this.on('change', function () {
                    var checkedStatus = $(this).is(":checked")
                    var index = shiftAdjustedEmployeeList.indexOf(value);
                    if (checkedStatus) {
                        if (index < 0) {
                            shiftAdjustedEmployeeList.push(value);
                        }
                    } else {
                        if (index > -1) {
                            shiftAdjustedEmployeeList.splice(index, 1);
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
                var index = shiftAdjustedEmployeeList.indexOf(value);
                if (checkedStatus) {
                    if (index < 0) {
                        shiftAdjustedEmployeeList.push(value);
                    }
                } else {
                    if (index > -1) {
                        shiftAdjustedEmployeeList.splice(index, 1);
                    }
                }
            });
        });



    });


})(window.jQuery, window.app);

