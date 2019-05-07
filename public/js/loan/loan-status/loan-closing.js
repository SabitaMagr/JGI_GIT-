(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("form-paidDate","nepaliDate");

        $('#form-paidDate').datepicker("setStartDate", new Date());
        $('#submit').on('click', function(){
            var paidAmount = $('#form-paidAmount').val();
            var unpaidTotal = $('#unpaidTotal').val();
            var remainingAmount = unpaidTotal - paidAmount;
            if(remainingAmount > 0){
                var repaymentmonths = prompt("Sum of " + remainingAmount + " is still remaining. This sum will be treated as a new loan. Please specify its repayment months.");
                $("#repaymentMonths").val(repaymentmonths);
            }
            return true;
        });
    });
})(window.jQuery, window.app);

