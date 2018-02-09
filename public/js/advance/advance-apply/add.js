(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali('dateOfadvance', 'nepalidateOfadvance');

        var $employeeId = $('#employeeId');
        var $advance = $('#advanceId');
        var $recommender = $('#overrideRecommenderId');
        var $approver = $('#overrideApproverId');
        var monthlySalary;

        var advanceDetails;
        var $requestAmt = $('#requestedAmount');
        var $monthlyDeductionPercentage = $('#deductionRate');
        var $monthToRepay = $('#deductionIn');
        var maxRequestAmt;
        var monthlyDeductionValue = 0;

        app.populateSelect($employeeId, document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', '---', '');
        app.populateSelect($recommender, document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', '---', '');
        app.populateSelect($approver, document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', '---', '');


        function searchList(arrayList, searchField, searchValue) {
            for (var i = 0; i < arrayList.length; i++) {
                if (eval('arrayList[i].' + searchField) === searchValue) {
                    return arrayList[i];
                }
            }
        }


        $employeeId.on('change', function () {
            var selectedEmpVal = $(this).val();
            if (selectedEmpVal) {
                app.floatingProfile.setDataFromRemote(selectedEmpVal);
                app.populateSelect($advance, document.advanceList, 'ADVANCE_ID', 'ADVANCE_ENAME', '---', '');
                var employeeDetail = searchList(document.employeeList, 'EMPLOYEE_ID', selectedEmpVal)
                if (employeeDetail['SALARY']) {
                    monthlySalary = employeeDetail['SALARY'];
                    $('#monthlySalary').text('Monthly Salary = ' + employeeDetail['SALARY']);
                }
            } else {
                $('#monthlySalary').text('');
                $advance.find('option').remove()
            }

        });

        function calcMonthlyDecution(deductionRateValue) {
            monthlyDeductionValue = (deductionRateValue / 100) * monthlySalary;
            $('#monthlyDeductionAmt').text('Monthly Deduction= ' + monthlyDeductionValue);
        }


        function advanceConfig(advanceData) {
            $('#deductionType').val(advanceData.DEDUCTION_TYPE);
            $monthlyDeductionPercentage.val(advanceData.DEDUCTION_RATE);
            $monthToRepay.val(advanceData.DEDUCTION_IN);
            $('#defaultValues').text(advanceData.DEDUCTION_RATE + ' % within ' + advanceData.DEDUCTION_IN + ' Months');

//if allow overrite rate='Y'
            if (advanceData.ALLOW_OVERRIDE_RATE == 'Y') {
                $('#overrideDeductionPer').text('override rate=' + advanceData.MIN_OVERRIDE_RATE);
                $monthlyDeductionPercentage.prop('readonly', false);
                $monthlyDeductionPercentage.attr('min', advanceData.MIN_OVERRIDE_RATE);
                $monthlyDeductionPercentage.val('');
            } else {
                $('#overrideDeductionPer').text('');
                $monthlyDeductionPercentage.attr('min', advanceData.DEDUCTION_RATE);
                $monthlyDeductionPercentage.prop('readonly', true);
            }

//if allow overrite month='Y'
            if (advanceData.ALLOW_OVERRIDE_MONTH == 'Y') {
                $('#overrideDeductionMonth').text('override months=' + advanceData.MAX_OVERRIDE_MONTH);
                $monthToRepay.attr('max', advanceData.MAX_OVERRIDE_MONTH);
                $monthToRepay.prop('readonly', false);
                $monthToRepay.val('');
            } else {
                $('#overrideDeductionMonth').text('');
                $monthToRepay.attr('max', advanceData.MAX_OVERRIDE_MONTH);
                $monthToRepay.prop('readonly', true);
            }
//if override recommender='Y'
            if (advanceData.OVERRIDE_RECOMMENDER_FLAG == 'Y') {
                $('#overrideRecommenderDiv').show();
                $recommender.prop('disabled', false);
            } else {
                $recommender.prop('disabled', true);
                $('#overrideRecommenderDiv').hide();
            }
//if override Approver='Y'
            if (advanceData.OVERRIDE_APPROVER_FLAG == 'Y') {
                $('#overrideApproverDiv').show();
                $approver.prop('disabled', false);
            } else {
                $approver.prop('disabled', true);
                $('#overrideApproverDiv').hide();
            }
//            
            var salaryRate = advanceData.MAX_SALARY_RATE;
            var maxMonths = advanceData.MAX_ADVANCE_MONTH;
            maxRequestAmt = (salaryRate / 100) * monthlySalary * maxMonths;
            $('#maxReqAmt').text("Max Request Amount=Rs " + maxRequestAmt);
            $requestAmt.attr('max', maxRequestAmt);

            (advanceData.DEDUCTION_TYPE == 'S' && advanceData.ALLOW_OVERRIDE_RATE == 'Y') ? $monthlyDeductionPercentage.prop('readonly', false) : $monthlyDeductionPercentage.prop('readonly', true);
            (advanceData.DEDUCTION_TYPE == 'M' && advanceData.ALLOW_OVERRIDE_MONTH == 'Y') ? $monthToRepay.prop('readonly', false) : $monthToRepay.prop('readonly', true);

            (advanceData.ALLOW_OVERRIDE_RATE == 'N' && advanceData.ALLOW_OVERRIDE_MONTH == 'N') ? calcMonthlyDecution(advanceData.DEDUCTION_RATE) : $('#monthlyDeductionAmt').text('');

        }



        $advance.on('change', function () {
            var selectedAdvanceId = $(this).val();
            var selectedAdvanceValues = searchList(document.advanceList, 'ADVANCE_ID', selectedAdvanceId);
            if (typeof selectedAdvanceValues != 'undefined') {
                advanceDetails = selectedAdvanceValues;
                advanceConfig(selectedAdvanceValues);
            } else {
                advanceDetails = null;
                $('.clearText').text(" ");
            }
        });

        $monthlyDeductionPercentage.on('change keydown paste input', function () {
            var deductionRateValue = $(this).val();
            var requestAmt = $requestAmt.val();

            if (requestAmt > 0) {
                calcMonthlyDecution(deductionRateValue);
                var repaymentMonths = Math.ceil(requestAmt / monthlyDeductionValue);
                if (advanceDetails.ALLOW_OVERRIDE_MONTH == 'Y') {
                    $monthToRepay.val(repaymentMonths);
                }
            }

        });

        $monthToRepay.on('change keydown paste input', function () {
            var repaymentMonthValue = $(this).val();
            if (repaymentMonthValue > 0) {
                var requestAmt = $requestAmt.val();
                var monthlyDeductAmt = requestAmt / repaymentMonthValue;
                var monthlyDeductPer = (monthlyDeductAmt * 100) / monthlySalary;

                $monthlyDeductionPercentage.val(monthlyDeductPer.toFixed(2));
                calcMonthlyDecution(monthlyDeductPer.toFixed(2));
            }

        });



        $requestAmt.on('change keydown paste input', function () {
            if (advanceDetails) {
                if (advanceDetails.ALLOW_OVERRIDE_RATE == 'Y') {
                    $monthlyDeductionPercentage.val('');
                }
                if (advanceDetails.ALLOW_OVERRIDE_MONTH == 'Y') {
                    $monthToRepay.val('');
                }
            }
        });


        app.setLoadingOnSubmit('AdvanceRequest', function () {
            var deductionPercentage = $monthlyDeductionPercentage.val();
            var deductionMonthValue = $monthToRepay.val();
            var requestAmt = $requestAmt.val();

            var totalPaymnetVal = monthlyDeductionValue * deductionMonthValue;

            var minPercentage = (advanceDetails.ALLOW_OVERRIDE_RATE == 'Y') ? advanceDetails.MIN_OVERRIDE_RATE : advanceDetails.DEDUCTION_RATE;

            if (deductionPercentage > 100) {
                $('#beforeSubmitVal').text('Deduction Percetnage Cannot be Greater Than 100');
                return false;
            }
            if (minPercentage > deductionPercentage) {
                $('#beforeSubmitVal').text('Deduction Percetnage Cannot be less Than ' + minPercentage);
                return false;
            }
            if (requestAmt > totalPaymnetVal) {
                $('#beforeSubmitVal').text(requestAmt + 'cant be paid in ' + deductionMonthValue + ' Months with deduction of Rs' + monthlyDeductionValue);
                return false;
            }
            return true;
        });


    });
})(window.jQuery, window.app);
