(function ($, app) {
    'use strict';
    const userId = "USER_ID";
    const enableNotification = "ENABLE_NOTIFICATION";
    const enableEmail = "ENABLE_EMAIL";
    const constraint = ['N', 'Y'];

    $(document).ready(function () {
        var $enableNotificaion = $('#enableNotification');
        var $enableEmail = $('#enableEmail');

        $enableNotificaion.on('change', function () {
//            var $this = $(this);
//            app.pullDataById(document.updateSettingUrl, {
//               
//            }).then(function (success) {
//                console.log('success', success);
//                $n.prop('checked', success[enableNotification] == constraint[1]);
//                $e.prop('checked', success[enableEmail] == constraint[1]);
//            }, function (failure) {
//                console.log('failure', failure);
//            });
        });

        $enableEmail.on('change', function () {
//            var $this = $(this);
        });

        (function ($n, $e) {
            app.pullDataById(document.settingUrl, {
                test: 'test'
            }).then(function (success) {
                console.log('success', success);
                $n.prop('checked', success[enableNotification] == constraint[1]);
                $e.prop('checked', success[enableEmail] == constraint[1]);
            }, function (failure) {
                console.log('failure', failure);
            });
        })($enableNotificaion, $enableEmail);
    });
})(window.jQuery, window.app);