(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali('dateOfadvance', 'nepalidateOfadvance');
        var $advance = $('#advanceId');
        var $requestAmt = $('#requestedAmount');
        var $recommender = $('#overrideRecommenderId');
        var $approver = $('#overrideApproverId');
        var $monthlyDeductionPercentage = $('#deductionRate');
        var $monthToRepay = $('#deductionIn');
        var $deductionType = $('#deductionType');
        var $overrideDeductionMonth = $('#overrideDeductionMonth');
        var $overrideDeductionPer = $('#overrideDeductionPer');
        var advanceDetails;
        var maxRequestAmt;
        var monthlySalary = document.salary;
        var monthlyDeductionValue = 0;

        function searchList(arrayList, searchField, searchValue) {
            for (var i = 0; i < arrayList.length; i++) {
                if (eval('arrayList[i].' + searchField) === searchValue) {
                    return arrayList[i];
                }
            }
        }

        app.populateSelect($advance, document.advanceList, 'ADVANCE_ID', 'ADVANCE_ENAME', '---', '');
        app.populateSelect($recommender, document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', '---', '');
        app.populateSelect($approver, document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', '---', '');



        function advanceConfig() {
            $overrideDeductionMonth.text('');
            $monthToRepay.attr('max', advanceDetails.MAX_OVERRIDE_MONTH);
            $monthToRepay.prop('readonly', true);
            /* */
            $overrideDeductionPer.text('');
            $monthlyDeductionPercentage.attr('min', advanceDetails.DEDUCTION_RATE);
            $monthlyDeductionPercentage.prop('readonly', true);
            /**/
            $recommender.prop('disabled', true);
            $('#overrideRecommenderDiv').hide();
            /**/
            $approver.prop('disabled', true);
            $('#overrideApproverDiv').hide();


//            
            $deductionType.val(advanceDetails.DEDUCTION_TYPE);
            if (advanceDetails.DEDUCTION_TYPE === 'M') {
                $monthToRepay.val(advanceDetails.DEDUCTION_IN);
                if (advanceDetails.ALLOW_OVERRIDE_MONTH === 'Y') {
                    $overrideDeductionMonth.text('Max Override Payment months=' + advanceDetails.MAX_OVERRIDE_MONTH);
                    $monthToRepay.attr('max', advanceDetails.MAX_OVERRIDE_MONTH);
                    $monthToRepay.prop('readonly', false);
                    $monthToRepay.val('');
                }
            }

            if (advanceDetails.DEDUCTION_TYPE === 'S') {
                $monthlyDeductionPercentage.val(advanceDetails.DEDUCTION_RATE);
                if (advanceDetails.ALLOW_OVERRIDE_RATE === 'Y') {
                    $overrideDeductionPer.text('Min Payment Override rate=' + advanceDetails.MIN_OVERRIDE_RATE);
                    $monthlyDeductionPercentage.prop('readonly', false);
                    $monthlyDeductionPercentage.attr('min', advanceDetails.MIN_OVERRIDE_RATE);
                    $monthlyDeductionPercentage.val('');
                }
            }

            if (advanceDetails.OVERRIDE_RECOMMENDER_FLAG === 'Y') {
                $('#overrideRecommenderDiv').show();
                $recommender.prop('disabled', false);
            }
            if (advanceDetails.OVERRIDE_APPROVER_FLAG === 'Y') {
                $('#overrideApproverDiv').show();
                $approver.prop('disabled', false);
            }

            maxRequestAmt = (advanceDetails.MAX_SALARY_RATE * advanceDetails.MAX_ADVANCE_MONTH * monthlySalary) / 100;
            $requestAmt.attr('max', maxRequestAmt);
            $('#maxReqAmt').text("Max Request Amount=Rs " + maxRequestAmt);

            $monthlyDeductionPercentage.prop('readonly', !(advanceDetails.DEDUCTION_TYPE === 'S' && advanceDetails.ALLOW_OVERRIDE_RATE === 'Y'));
            $monthToRepay.prop('readonly', !(advanceDetails.DEDUCTION_TYPE === 'M' && advanceDetails.ALLOW_OVERRIDE_MONTH === 'Y'));
        }


        $advance.on('change', function () {
            advanceDetails = null;
            $('.clearText').text(" ");
            var selectedAdvanceId = $(this).val();
            var selectedAdvanceValues = searchList(document.advanceList, 'ADVANCE_ID', selectedAdvanceId);
            if (typeof selectedAdvanceValues !== 'undefined') {
                advanceDetails = selectedAdvanceValues;
                advanceConfig();
            }
            defineRateOrMonth();
        });


        $monthlyDeductionPercentage.on('change keydown paste input', function () {
        });

        $monthToRepay.on('change keydown paste input', function () {

        });

        var defineRateOrMonth = function () {
            if (advanceDetails === null) {
                return;
            }
            var requestAmount = $requestAmt.val();
            if (requestAmount === null || requestAmount === '') {
                return;
            }
            switch (advanceDetails.DEDUCTION_RATE) {
                case 'M':

                    break;
                case 'S':
                    break;
            }

        }

        $requestAmt.on('change keydown paste input', function () {
            defineRateOrMonth();
        });




        app.setLoadingOnSubmit('AdvanceRequest');







    });
})(window.jQuery, window.app);
    