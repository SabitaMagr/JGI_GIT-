(function ($, app) {
    'use strict';

    var $oldPassword = $('#oldPassword');
    var $newPassword = $('#newPassword');
    var $reNewPassword = $('#reNewPassword');

    console.log(document.oldPassword);
    $('form').submit(function () {
        if ($oldPassword.val() != document.oldPassword) {
            $('#oldPwdErr').text('Type your old Password correctly');
            if ($newPassword.val() != $reNewPassword.val()) {
                $('#pwdMisMatchErr').text('Do Not Match With New Password');
            }
            return false;
        } else {
            $('#oldPwdErr').text('');
        }

        if ($newPassword.val() !== $reNewPassword.val()) {
            $('#pwdMisMatchErr').text('Do Not Match With New Password');
            return false;
        } else {
            $('#pwdMisMatchErr').text('');
        }
    });





})(window.jQuery, window.app);

