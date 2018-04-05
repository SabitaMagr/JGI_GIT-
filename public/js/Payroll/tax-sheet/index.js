(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var months = null;
        var $year = $('#fiscalYearId');
        var $month = $('#monthId');
        var $employeeId = $('#employeeId');
        var $viewBtn = $('#viewBtn');
        var $additionList = $('#additionList');
        var $deductionList = $('#deductionList');
        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
            months = monthList;
        });
        app.setEmployeeSearch($employeeId);
        var showTaxSheet = function ($data) {
            $.each($data, function (index, item) {
                switch (item['PAY_TYPE_FLAG']) {
                    case 'A':
                        
                        break;

                    case 'D':

                        break;
                }
            });
        }
        $viewBtn.on('click', function () {
            var monthId = $month.val();
            var employeeId = $employeeId.val();
            app.serverRequest('', {monthId: monthId, employeeId: employeeId}).then(function (response) {
                showTaxSheet(response.data);
            }, function (error) {

            });
        });

    });
})(window.jQuery, window.app);