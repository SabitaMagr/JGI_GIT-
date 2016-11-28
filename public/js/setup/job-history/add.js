/**
 * Created by punam on 9/28/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.addDatePicker(
            $("#startDate"),
            $("#endDate")
        );
        var employeeId = $("#employeeID").val();
        window.app.floatingProfile.setDataFromRemote(employeeId);
        
        $("#employeeID").on("change",function(){
           var employeeId = $(this).val();
           window.app.floatingProfile.setDataFromRemote(employeeId);
        });
    });
})(window.jQuery,window.app);


