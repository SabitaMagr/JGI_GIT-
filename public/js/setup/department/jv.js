(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#jvTable');
        app.initializeKendoGrid($table, [
            {field: "PAY_ID", title: "Pay ID", width: 150, hidden: true, template: '<input type="text" value="#: PAY_ID||""#" title="JV" name="payId[]" style="height:17px;">'},
            {field: "PAY_EDESC", title: "Pay Edesc", width: 150},
            {field: "JV_NAME", title: "JV Name", width: 120, template: '<input type="text" value="#: JV_NAME||""#" title="JV" name="jvName[]" style="height:17px;">'},
            {field: "FLAG", title: "Flag", width: 150, template: '<select class="selectpicker"   data-style="btn-new" name="flag[]"><option value="N" #if(FLAG == "N"){#selected#}#>N</option><option value="Y" #if(FLAG == "Y"){#selected#}#>Y</option></select>'}
            // {field: "BRANCH_NAME", title: "Branch", width: 150}
        ]);

        app.searchTable('jvTable', ['PAY_EDESC', 'JV_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'PAY_EDESC': 'PAY_EDESC',
                'JV_NAME': 'JV_NAME'
            }, 'Department List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'PAY_EDESC': 'PAY_EDESC',
                'JV_NAME': 'JV_NAME'
            }, 'Department List');
        });

        $("#submit").on('click', function(){
            var payId = [], jvName = [], flag = [];
            $("input[name='payId[]']").each(function() {
                payId.push($(this).val());
            });
            $("input[name='jvName[]']").each(function() {
                jvName.push($(this).val());
            });
            $("select[name='flag[]']").each(function() {
                flag.push($(this).val());
            });
            
            var data = {
                deptId : $("#deptId").val(),
                flag : flag, 
                payId : payId,
                jvName : jvName
            }
            app.serverRequest(document.jvUpdateLink, data).then(function (response) {
                if (response.success) {
                    app.showMessage('Success', 'success');
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);