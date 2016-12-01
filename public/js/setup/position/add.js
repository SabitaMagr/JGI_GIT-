/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

//        $("#form-positionName").on("blur", function () {
//            var positionName = $(this).val();
//            var columnsWidValues = [];
//            columnsWidValues["POSITION_NAME"]=positionName;
//            console.log(columnsWidValues);
//
//            window.app.pullDataById(document.url, {
//                action: 'checkUniqueConstraint',
//                tableName: "HR_POSITIONS",
//                columnsWidValues: columnsWidValues
//            }).then(function (success) {
//                console.log(success.data);
//            }, function (failure) {
//                console.log(failure);
//            });
//
//            alert(positionName);
//        });
    });
})(window.jQuery, window.app);
