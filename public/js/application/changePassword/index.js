(function ($, app) {
    'use strict';

    $(document).ready(function () {
        var $oldPassword = $('#oldPassword');
        var $newPassword = $('#newPassword');
        var $reNewPassword = $('#reNewPassword');

//        function checkRequiredField() {
//          
//        }

        $(document).on('click', '#changePwdBtn', function () {


            app.pullDataById(document.restfulUrl, {
                action: 'pullCurUserPwd',
                data: {}
            }).then(function (sucess) {
                console.log(sucess.data);
                console.log($oldPassword);
                if (sucess.data != $oldPassword.val()) {
                    $('#oldPwdErr').text('Old Password is not Correct');
                    if ($newPassword.val() !== $reNewPassword.val()) {
                        $('#pwdMisMatchErr').text('Do Not Match With New Password');
                    } else {
                        $('#pwdMisMatchErr').text('');
                    }
                } else {
                    $('#oldPwdErr').text('');
                    if ($newPassword.val() !== $reNewPassword.val()) {
                        $('#pwdMisMatchErr').text('Do Not Match With New Password');
                    } else {
                        $('#pwdMisMatchErr').text('');

                        app.pullDataById(document.restfulUrl, {
                            action: 'updateCurUserPwd',
                            data: {
                                'newPassword': $newPassword.val()
                            }}).then(function (e) {
                            console.log(e);
                        });

                    }

                }
            });

        });





    });
}
)(window.jQuery, window.app);

