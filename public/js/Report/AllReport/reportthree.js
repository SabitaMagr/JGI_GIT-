(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        var $tableContainer = $("#reportTable");


        var months = null;
        var $year = $('#fiscalYearId');
        var $month = $('#monthId');
        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
            months = monthList;
        });

        var $search = $('#search');
        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['monthCodeId'] = $month.val();
                console.log(data);
            app.serverRequest('', data).then(function (response) {
                app.renderKendoGrid($table, response.data);
                console.log(response);
            }, function (error) {

            });
        });





    });
})(window.jQuery, window.app);