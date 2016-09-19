/**
 * Created by ukesh on 9/15/16.
 */
(function ($, app) {
    'use strict';
<<<<<<< HEAD
    // var datatable = $('#tableWithSearch').dataTable().api();
    // var employeeId = $('#employeeId');
    //
    // employeeId.on("change", function () {
    //     var id = $(this).val();
    //     app.pullDataById(document.url, {action: 'assignList', id: id}).then(function (data) {
    //         console.log(data);
    //
    //     }, function (error) {
    //         console.log(error);
    //
    //     });
    // });
    //
    // var viewEmployees=$('#viewEmployees');
    // var leaveId=$('#leaveId');
    // var branchId=$('#branchId');
    // var departmentId=$('#departmentId');
    // var genderId=$('#genderId');
    // var designationId=$('#designationId');
    //
    // var leavelist={};
    //


=======
    var datatable = $('#tableWithSearch').dataTable().api();
    var employeeId = $('#employeeId');

    employeeId.on("change", function () {
        var id = $(this).val();
        app.pullDataById(document.url, {action: 'assignList', id: id}).then(function (data) {
            console.log(data);

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

    var leavelist={};
>>>>>>> c78677e1b1eb1c0a690efeff4036ee1d4d1bc85a

})(window.jQuery, window.app);