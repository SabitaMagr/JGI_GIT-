(function ($, app) {
    $(document).ready(function () {
        $("select#employeeId").select2();
        $('form').bind('submit', function () {
            $(this).find(':disabled').removeAttr('disabled');
        });
    });
})(window.jQuery, window.app);

