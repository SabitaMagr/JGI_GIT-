(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
//        app.floatingProfile.setDataFromRemote(employeeId);
        app.datePickerWithNepali('dateOfadvance', 'nepalidateOfadvance');
        var $advance = $('#advanceId');
        var $requestAmt = $('#requestedAmount');
        var $recommender = $('#overrideRecommenderId');
        var $approver = $('#overrideApproverId');
        var $monthlyDeductionPercentage = $('#deductionRate');
        var $monthToRepay = $('#deductionIn');
        var advanceDetails;
        var maximunRequestAmt;

//        console.log(document.salary);


        function searchList(arrayList, searchField, searchValue) {
            for (var i = 0; i < arrayList.length; i++) {
                if (eval('arrayList[i].' + searchField) === searchValue) {
                    return arrayList[i];
                }
            }
        }

        app.populateSelect($advance, document.advanceList, 'ADVANCE_ID', 'ADVANCE_ENAME', '---', '');
        app.populateSelect($recommender, document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', '---');
        app.populateSelect($approver, document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', '---');



        function advanceConfig(advanceData) {
//            console.log(advanceData);
            console.log(advanceData.DEDUCTION_TYPE);
            $('#deductionType').val(advanceData.DEDUCTION_TYPE);
            $monthlyDeductionPercentage.val(advanceData.DEDUCTION_RATE);
            $monthToRepay.val(advanceData.DEDUCTION_IN);

            if (advanceData.ALLOW_OVERRIDE_RATE == 'Y') {
                $monthlyDeductionPercentage.prop('readonly', false);
                $monthlyDeductionPercentage.attr('min', advanceData.MIN_OVERRIDE_RATE);
            } else {
                $monthlyDeductionPercentage.prop('readonly', true);
            }

            if (advanceData.ALLOW_OVERRIDE_MONTH == 'Y') {
                $monthToRepay.attr('max', advanceData.MAX_OVERRIDE_MONTH);
                $monthToRepay.prop('readonly', false);
            } else {
                $monthToRepay.prop('readonly', true);
            }
//            

            (advanceData.OVERRIDE_RECOMMENDER_FLAG == 'Y') ? $('#overrideRecommenderDiv').show() : $('#overrideRecommenderDiv').hide();
            (advanceData.OVERRIDE_RECOMMENDER_FLAG == 'Y') ? $recommender.prop('disabled', false) : $recommender.prop('disabled', true);
            (advanceData.OVERRIDE_APPROVER_FLAG == 'Y') ? $('#overrideApproverDiv').show() : $('#overrideApproverDiv').hide();
            (advanceData.OVERRIDE_APPROVER_FLAG == 'Y') ? $approver.prop('disabled', false) : $approver.prop('disabled', true);
            var salaryRate = advanceData.MAX_SALARY_RATE;
            var maxMonths = advanceData.MAX_ADVANCE_MONTH;
            maximunRequestAmt = (salaryRate / 100) * document.salary * maxMonths;
            $('#maxReqAmt').text("Max Req.Amt=Rs " + maximunRequestAmt);
            $requestAmt.attr('max', maximunRequestAmt);
            (advanceData.DEDUCTION_RATE != null) ? $('#minDeductionPer').text("Min Deduction Per=Rs " + advanceData.DEDUCTION_RATE) : $('#minDeductionPer').text('Min Deduction Per= Not Defined');
            (advanceData.DEDUCTION_IN != null) ? $('#maxDeductionMonth').text("Max Repayment Months " + advanceData.DEDUCTION_IN) : $('#maxDeductionMonth').text("Max Repayment Months= Not Defined");

            (advanceData.DEDUCTION_TYPE == 'S' && advanceData.ALLOW_OVERRIDE_RATE == 'Y') ? $monthlyDeductionPercentage.prop('readonly', false) : $monthlyDeductionPercentage.prop('readonly', true);
            (advanceData.DEDUCTION_TYPE == 'M' && advanceData.ALLOW_OVERRIDE_MONTH == 'Y') ? $monthToRepay.prop('readonly', false) : $monthToRepay.prop('readonly', true);



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
            var monthlyDeductionValue = (deductionRateValue / 100) * document.salary;
            var repaymnetMonts = Math.ceil(requestAmt / monthlyDeductionValue);

            $monthToRepay.val(repaymnetMonts);

        });


        $monthToRepay.on('change keydown paste input', function () {
            var repaymentMonthValue = $(this).val();
            var requestAmt = $requestAmt.val();
            var monthlyDeductAmt = requestAmt / repaymentMonthValue;
            var monthlyDeductPer = (monthlyDeductAmt * 100) / document.salary;

            $monthlyDeductionPercentage.val(monthlyDeductPer);
        });

        $requestAmt.on('change keydown paste input', function () {
            if (advanceDetails) {
                $monthlyDeductionPercentage.val(advanceDetails.DEDUCTION_RATE);
                $monthToRepay.val(advanceDetails.DEDUCTION_IN);
            }
        });







    });
})(window.jQuery, window.app);
    