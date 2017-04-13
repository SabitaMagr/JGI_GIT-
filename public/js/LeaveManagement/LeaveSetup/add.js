(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var leaveId = $('#leaveId');
        var availableDays = $('#availableDays'), startDate = $('#startDate'), endDate = $('#endDate');
        var assignedLeaves;
        var leaves;
        app.pullDataById(document.urlAssignedLeaves, {action: 'assignedLeaves', id: document.id}).then(function (data) {
            if (data.success) {
                leaves = data.data;
                var tempObj = {};
                for (var index in leaves) {
                    tempObj[leaves[index]['LEAVE_ID']] = leaves[index]['LEAVE_ENAME'];
                }
                app.populateSelectElement(leaveId, tempObj);
                if (leaves.length >= 1) {
                    availableDays.val(leaves[0]['TOTAL_DAYS']);
                }
            }
        }, function (error) {
            console.log(error);
        });

        leaveId.on('change', function () {
            var id = $(this).val();
            var items = leaves.filter(function (value) {
                return value['LEAVE_ID'] == id;
            });
            availableDays.val(items[0]['TOTAL_DAYS']);

        });

        app.addDatePicker(startDate, endDate);
    });

})(window.jQuery, window.app);