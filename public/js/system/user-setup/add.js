/**
 * Created by root on 10/18/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
      
        var employeeId = $("#employeeID").val();
        window.app.floatingProfile.setDataFromRemote(employeeId);
        
        $("#employeeID").on("change",function(){
           var employeeId = $(this).val();
           window.app.floatingProfile.setDataFromRemote(employeeId);
        });
        
        $('select').select2();
    });
})(window.jQuery,window.app);
