/**
 * Created by punam on 9/18/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
        var holidayId = $("#holidayId");
        var branchId = $("#branchId");

        holidayId.on('change', function () {
            app.fetchAndPopulate(document.urlBranchList, holidayId.val(),branchId);
        });

    });
})(window.jQuery,window.app);


