(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali('dateOfadvance', 'nepalidateOfadvance');
        var $advance = $('#advanceId');
        var $requestAmt = $('#requestedAmount');
        var $recommender = $('#overrideRecommenderId');
        var $approver = $('#overrideApproverId');
        var $deductionRate = $('#deductionRate');
        var $deductionIn = $('#deductionIn');
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
            console.log(advanceDetails);



            /*to show recommeder approver accpording to setip value start*/
            $recommender.prop('disabled', true);
            $('#overrideRecommenderDiv').hide();
            $approver.prop('disabled', true);
            $('#overrideApproverDiv').hide();

            if (advanceDetails.OVERRIDE_RECOMMENDER_FLAG === 'Y') {
                $('#overrideRecommenderDiv').show();
                $recommender.prop('disabled', false);
            }
            if (advanceDetails.OVERRIDE_APPROVER_FLAG === 'Y') {
                $('#overrideApproverDiv').show();
                $approver.prop('disabled', false);
            }
            /*to show recommeder approver accpording to setip value start*/


            $deductionType.val(advanceDetails.DEDUCTION_TYPE);
            $('#deductionRateDiv').hide();
            $('#deductionInDiv').hide();

            if (advanceDetails.DEDUCTION_TYPE === 'M') {
                $deductionIn.prop('required',true);
                $deductionRate.prop('required',false);
                $('#deductionInDiv').show();
                $deductionIn.val(advanceDetails.DEDUCTION_IN);
                $deductionIn.prop('readonly', true);
                if (advanceDetails.ALLOW_OVERRIDE_MONTH === 'Y') {
                $deductionIn.prop('readonly', false);
                    $overrideDeductionMonth.text('Max Override Payment months=' + advanceDetails.MAX_OVERRIDE_MONTH);
                    $deductionIn.attr('max', advanceDetails.MAX_OVERRIDE_MONTH);
                    $deductionIn.attr('min', advanceDetails.DEDUCTION_IN);
//                    $deductionIn.val('');
                }
            }


            if (advanceDetails.DEDUCTION_TYPE === 'S') {
                $deductionRate.prop('required',true);
                $deductionIn.prop('required',false);
                $('#deductionRateDiv').show();
                $deductionRate.val(advanceDetails.DEDUCTION_RATE);
                    $deductionRate.prop('readonly', true);
                if (advanceDetails.ALLOW_OVERRIDE_RATE === 'Y') {
                    $deductionRate.prop('readonly', false);
                    $overrideDeductionPer.text('Min Payment Override rate=' + advanceDetails.MIN_OVERRIDE_RATE);
                    $deductionRate.attr('min', advanceDetails.MIN_OVERRIDE_RATE);
                    $deductionRate.attr('max', advanceDetails.MIN_OVERRIDE_RATE);
//                    $deductionRate.val('');
                }else{
                    
                }
            }


            //TO VIEW ADVANCE DETAILS START
            var selectedAdvanceDtl = advanceDetails.DEDUCTION_RATE + ' % of ' + monthlySalary + ' up to ' + advanceDetails.DEDUCTION_IN + ' months.'
            $('#defaultValues').text(selectedAdvanceDtl);
            
            //TO VIEW ADVANCE DETAILS END


            //calculate MaxRequestAmt Start

            maxRequestAmt = (advanceDetails.MAX_SALARY_RATE * advanceDetails.DEDUCTION_IN * monthlySalary) / 100;
            $('#maxReqAmt').text("Max Request Amount=Rs " + maxRequestAmt);

            $requestAmt.attr('max', maxRequestAmt);
            //caculate MaxRequestAmt End





//            $overrideDeductionMonth.text('');
//            $deductionIn.attr('max', advanceDetails.MAX_OVERRIDE_MONTH);
//            $deductionIn.prop('readonly', true);
//            /* */
//            $overrideDeductionPer.text('');
//            $deductionRate.attr('min', advanceDetails.DEDUCTION_RATE);
//            $deductionRate.prop('readonly', true);
//            /**/
//            $recommender.prop('disabled', true);
//            $('#overrideRecommenderDiv').hide();
//            /**/
//            $approver.prop('disabled', true);
//            $('#overrideApproverDiv').hide();


//            
//            $deductionType.val(advanceDetails.DEDUCTION_TYPE);
//            if (advanceDetails.DEDUCTION_TYPE === 'M') {
//                $deductionIn.val(advanceDetails.DEDUCTION_IN);
//                if (advanceDetails.ALLOW_OVERRIDE_MONTH === 'Y') {
//                    $overrideDeductionMonth.text('Max Override Payment months=' + advanceDetails.MAX_OVERRIDE_MONTH);
//                    $deductionIn.attr('max', advanceDetails.MAX_OVERRIDE_MONTH);
//                    $deductionIn.prop('readonly', false);
//                    $deductionIn.val('');
//                }
//            }

//            if (advanceDetails.DEDUCTION_TYPE === 'S') {
//                $deductionRate.val(advanceDetails.DEDUCTION_RATE);
//                if (advanceDetails.ALLOW_OVERRIDE_RATE === 'Y') {
//                    $overrideDeductionPer.text('Min Payment Override rate=' + advanceDetails.MIN_OVERRIDE_RATE);
//                    $deductionRate.prop('readonly', false);
//                    $deductionRate.attr('min', advanceDetails.MIN_OVERRIDE_RATE);
//                    $deductionRate.val('');
//                }
//            }

//to overide recommend and approver if available start

//to overide recommend and approver if available start

//            maxRequestAmt = (advanceDetails.MAX_SALARY_RATE * advanceDetails.MAX_ADVANCE_MONTH * monthlySalary) / 100;
//            $requestAmt.attr('max', maxRequestAmt);
//            $('#maxReqAmt').text("Max Request Amount=Rs " + maxRequestAmt);
//
//            $deductionRate.prop('readonly', !(advanceDetails.DEDUCTION_TYPE === 'S' && advanceDetails.ALLOW_OVERRIDE_RATE === 'Y'));
//            $deductionIn.prop('readonly', !(advanceDetails.DEDUCTION_TYPE === 'M' && advanceDetails.ALLOW_OVERRIDE_MONTH === 'Y'));
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
//            defineRateOrMonth();
        });


        $deductionRate.on('change keydown paste input', function () {
        });

        $deductionIn.on('change keydown paste input', function () {

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
    