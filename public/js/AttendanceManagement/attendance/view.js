(function ($,app) {
    'use strict';
    $(document).ready(function () {

        $('select').select2();
        $('#inTime').combodate({
            minuteStep: 1
        });
        $('#outTime').combodate({
            minuteStep: 1
        });

        $('.hour').attr("disabled", true);
        $('.minute').attr("disabled", true);
        $('.ampm').attr("disabled", true);

    });
})(window.jQuery,window.app);

