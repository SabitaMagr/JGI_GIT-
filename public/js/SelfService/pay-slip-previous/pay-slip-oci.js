(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $periodDtCode = $('#mcode');
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
        var periodDtCodeChange = function ($this) {
            app.serverRequest('', {'PERIOD_DT_CODE': $this.val()}).then(function (response) {
                render(response.data);
            }, function (error) {
                console.log('error', error);
            });
        };
        $periodDtCode.on('change', function () {
            periodDtCodeChange($(this));
        });
        periodDtCodeChange($periodDtCode);




    });
})(window.jQuery, window.app);