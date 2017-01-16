(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $markAsViewed = $('.markAsViewed');

        $markAsViewed.on('click', function (e) {
            e.preventDefault();
            var $this = $(this);
            app.pullDataById(document.markAsViewed, {
                messageId: $this.attr('message-id')
            }).then(function (success) {
                console.log("markAsViewed res", success);
                if (success.success) {
                    location.reload();
                }

            }, function (failure) {
                console.log("markAsViewed fail", failure);
            });

        });

    });

})(window.jQuery, window.app);