(function ($, app) {
    'use strict';
    const userId = "USER_ID";
    const enableNotification = "ENABLE_NOTIFICATION";
    const enableEmail = "ENABLE_EMAIL";
    const constraint = ['N', 'Y'];

    $(document).ready(function () {
        var $enableNotificaion = $('#enableNotification');
        var $enableEmail = $('#enableEmail');
        var setting = {};

        (function ($n, $e) {
            app.pullDataById(document.settingUrl, {
                test: 'test'
            }).then(function (success) {
//                console.log("setting suc", success);
                setting = success;
                $enableNotificaion.bootstrapSwitch({
                    state: setting[enableNotification] === constraint[1],
                    onSwitchChange: function () {
                        var $this = $(this);
                        app.pullDataById(document.updateSettingUrl, {
                            [enableNotification]: constraint[$this.bootstrapSwitch("state") ? 1 : 0]
                        }).then(function (success) {
                            console.log('success', success);
                        }, function (failure) {
                            console.log('failure', failure);
                        });

                    }});
                $enableEmail.bootstrapSwitch({
                    state: setting[enableEmail] === constraint[1],
                    onSwitchChange: function () {
                        var $this = $(this);
                        app.pullDataById(document.updateSettingUrl, {
                            [enableNotification]: constraint[$this.bootstrapSwitch("state") ? 1 : 0]
                        }).then(function (success) {
                            console.log('success', success);
                        }, function (failure) {
                            console.log('failure', failure);
                        });
                    }});
            }, function (failure) {
                console.log('setting fail', failure);
            });
        })($enableNotificaion, $enableEmail);
    });
})(window.jQuery, window.app);