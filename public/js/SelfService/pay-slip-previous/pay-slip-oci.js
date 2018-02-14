(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $periodDtCode = $('#mcode');
        var $salaryType = $('#salaryType');
        var $tbodyA = $('#tbody-a');
        var $tbodyD = $('#tbody-d');
        var render = function (list) {
            $tbodyA.html('');
            $tbodyD.html('');

            $.each(list, function (index, item) {
                var payKV = `<tr><td>${item.PAY_EDESC}</td><td>${item.AMOUNT}</td></tr>`;
                switch (item.PAY_TYPE_FLAG) {
                    case "A":
                        $tbodyA.append(payKV);
                        break;
                    case "D":
                        $tbodyD.append(payKV);
                        break;
                }
            });
        };
        var changePaySlip = function () {
            app.serverRequest('', {'PERIOD_DT_CODE': $periodDtCode.val(), 'SALARY_TYPE': $salaryType.val()}).then(function (response) {
                render(response.data);
            }, function (error) {
                console.log('error', error);
            });
        };
        $periodDtCode.on('change', function () {
            changePaySlip();
        });
        $salaryType.on('change', function () {
            changePaySlip();
        });
        changePaySlip();
    });
})(window.jQuery, window.app);