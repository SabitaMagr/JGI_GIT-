(function ($) {
    'use strict';
    $(document).ready(function () {
        $('#rootwizard').bootstrapWizard({
            onTabShow: function (tab, navigation, index) {
                $('#tab' + (index + 1) + " select").select2();

                switch (index + 1) {
                    case 1:
                        window.app.addDatePicker($("#employeeBirthDate"));
                        break;
                    case 2:
                        app.addDatePicker(
                                $("#famSpouseWeddingAnniversary"),
                                $("#famSpouseBirthDate")
                                );
                        break;
                    case 3:
                        window.app.addDatePicker(
                                $("#idPassportExpiry"),
                                $("#idDrivingLicenseExpiry"),
                                $("#idCitizenshipIssueDate"));
                        break;
                    case 4:
                        window.app.addDatePicker($("#joinDate"));
                        break;

                }

                var $total = navigation.find('li').length;
                var $current = index + 1;
                if ($current >= $total) {
                    $('#rootwizard').find('.pager .next').hide();
                    $('#rootwizard').find('.pager .finish').show().removeClass('disabled hidden');
                } else {
                    $('#rootwizard').find('.pager .next').show();
                    $('#rootwizard').find('.pager .finish').hide();
                }
                var li = navigation.find('li.active');
                var btnNext = $('#rootwizard').find('.pager .next').find('button');
                var btnPrev = $('#rootwizard').find('.pager .previous').find('button');

                function removeIcons(btn) {
                    btn.removeClass(function (index, css) {
                        return (css.match(/(^|\s)fa-\S+/g) || []).join(' ');
                    });
                }

//                if ($current > 1 && $current < $total) {
//                    var nextIcon = li.next().find('.fa');
////                    var nextIconClass = nextIcon.attr('class').match(/fa-[\w-]*/).join();
//                    removeIcons(btnNext);
////                    btnNext.addClass(nextIconClass + ' btn-animated from-left fa');
//                    var prevIcon = li.prev().find('.fa');
//                    var prevIconClass = prevIcon.attr('class').match(/fa-[\w-]*/).join();
//                    removeIcons(btnPrev);
//                    btnPrev.addClass(prevIconClass + ' btn-animated from-left fa');
//                } else if ($current == 1) {
//                    btnPrev.removeClass('btn-animated from-left fa');
//                    removeIcons(btnPrev);
//                } else {
//                    btnNext.removeClass('btn-animated from-left fa');
//                    removeIcons(btnNext);
//                }
            }, onNext: function (tab, navigation, index) {
                console.log("Showing next tab");
                if (typeof document.currentTab !== 'undefined') {
                    if (index <= 4) {
                        $('#btnform' + index).click();
                    } else if (index == 5) {
                        angular.element('#quaConId').scope().addQualification();
                        return true;
                    } else {
                        return true;
                    }
                } else {
                    if (index == 1) {
                        $('#btnform' + index).click();
                    } else {
                        return true;
                    }
                }

                return false;

            }, onPrevious: function (tab, navigation, index) {
                console.log("Showing previous tab");
            }, onInit: function () {
                console.log("On Init");
                $('#rootwizard ul').removeClass('nav-pills');

            }
        });
        $('.remove-item').click(function () {
            $(this).parents('tr').fadeOut(function () {
                $(this).remove();
            });
        });
    });
})(window.jQuery);