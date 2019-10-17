(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();

        var $flatValueId = $("#flatValueId");
        var $fiscalYearId = $("#fiscalYearId");

        var $searchEmployeesBtn = $('#searchEmployeesBtn');
        var $assignFlatValueBtn = $('#assignFlatValueBtn');

        var $table = $('#flatValueDetailTable');
        
        var changedValues = [];

        app.populateSelect($flatValueId, document.flatValues, "FLAT_ID", "FLAT_EDESC", "Select Flat Value");
        app.populateSelect($fiscalYearId, document.fiscalYears, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");

        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);
        $("#searchFieldDiv").hide();

        $searchEmployeesBtn.on('click', function () {
            var flatIdOptions = $("#flatValueId option");
            var flatId = [];
            flatId = $flatValueId.val();
            if(flatId == null || flatId.length == 0){
                flatId = $.map(flatIdOptions ,function(option) {
                    return option.value;
                });
            }
            if ($fiscalYearId.val() == -1) {
                app.showMessage("No fiscal year Selected.", 'error');
                $fiscalYearId.focus();
                return;
            }
            
            $table.empty();
            app.serverRequest(document.getFlatValueDetailWS, {
                flatId: flatId,
                fiscalYearId: $fiscalYearId.val(),
                employeeFilter: document.searchManager.getSearchValues()}).then(function (response) {
                var columns = []; 
                var fields = {
                    'EMPLOYEE_CODE': {editable: false},
                    'FULL_NAME': {editable: false},
                };
                
                columns.push({field: "EMPLOYEE_ID", title: "ID", width: 80, hidden: true, locked: true});
                columns.push({field: "EMPLOYEE_CODE", title: "Code", width: 80, locked: true});
                columns.push({field: "FULL_NAME", title: "Name", width: 90, locked: true});
                let counter = 1;
                for(let i in response.data[0]){
                    if(counter > 3){
                        columns.push({field: i, title: response.columns[counter-4].FLAT_EDESC, width: 160,
                template: '<input type="number" class="'+i+'" value="#: '+i+'||""#" style="height:17px;">'});
                    }
                    counter++; 
                }
                app.initializeKendoGrid($table, columns);
                app.renderKendoGrid($table, response.data);
                $("#searchFieldDiv").show();
                
            }, function (error) {
                console.log(error);
            });
        });

        $table.on('input', 'input', function(e){
            var grid = $table.data("kendoGrid");
            var row = $(e.target).closest("tr");
            var dataItem = grid.dataItem(row);
            //var data = $table.data("kendoGrid").dataItem($(e.target).closest("tr"));
            var key = this.className;
            dataItem[key] = this.value;
            //var index = changedValues.findIndex(x => x.EMPLOYEE_ID==dataItem.EMPLOYEE_ID);
            var index = changedValues.findIndex(function(x){
                return x.employeeId == dataItem.EMPLOYEE_ID && x.flatValue == key;
            });
            if(index == -1){ 
                changedValues.push({employeeId: dataItem.EMPLOYEE_ID, flatValue: key, flatId: key.substring(2)}); 
            }
        });

        $assignFlatValueBtn.on('click', function () {
            var grid = $table.data("kendoGrid");
            var currentData = grid.dataSource._data;
            for(let x = 0; x < changedValues.length; x++){
                var data = currentData.filter(function(item, i) { 
                    return item.EMPLOYEE_ID == changedValues[x].employeeId;
                });
                changedValues[x].value = data[0][changedValues[x].flatValue];
            }
            var fiscalYearId = $fiscalYearId.val();
            app.serverRequest(document.getFlatValueUpdateWS, {data : changedValues, fiscalYearId: fiscalYearId}).then(function(){
                app.showMessage('Operation successfull', 'success');
            }, function (error) {
                console.log(error);
            });
        });
    });
})(window.jQuery, window.app);
