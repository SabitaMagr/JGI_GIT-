(function ($, app) {
    'use strict';
    $(document).ready(function () {
        window.app.floatingProfile.setDataFromRemote(document.employeeId);
    });
})(window.jQuery, window.app);