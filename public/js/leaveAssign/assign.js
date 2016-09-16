/**
 * Created by ukesh on 9/15/16.
 */
(function ($, app) {
    'use strict';
    var datatable = $('#tableWithSearch').dataTable().api();
    var employeeId = $('#employeeId');

    employeeId.on("change", function () {
        var id = $(this).val();
        app.pullDataById(document.url, {action: 'assignList', id: id}).then(function (data) {
            console.log(data);

            datatable.clear();
            datatable.rows.add([[2, 2, 2, 2, 2, 2], [2, 2, 2, 2, 2, 2], [2, 2, 2, 2, 2, 2]]);
            datatable.draw();
        }, function (error) {
            console.log(error);

        });
    });

    var viewEmployees=$('#viewEmployees');
    var leaveId=$('#leaveId');
    var branchId=$('#branchId');
    var departmentId=$('#departmentId');
    var genderId=$('#genderId');
    var designationId=$('#designationId');
    viewEmployees.on("click",function () {
        app.pullDataById(document.url,{
           action:'pullEmployeeLeave',
            id:{
               leaveId:leaveId.val(),
                branchId:branchId.val(),
                departmentId:departmentId.val(),
                genderId:genderId.val(),
                designationId:designationId.val()
            }
        });
    });



})(window.jQuery, window.app);