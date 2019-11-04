(function ($, app) {
    'use strict';
    $(document).ready(function () {


        if (!Object.assign) {
          Object.defineProperty(Object, 'assign', {
            enumerable: false,
            configurable: true,
            writable: true,
            value: function(target) {
              if (target === undefined || target === null) {
                throw new TypeError('Cannot convert first argument to object');
              }

              var to = Object(target);
              for (var i = 1; i < arguments.length; i++) {
                var nextSource = arguments[i];
                if (nextSource === undefined || nextSource === null) {
                  continue;
                }
                nextSource = Object(nextSource);

                var keysArray = Object.keys(nextSource);
                for (var nextIndex = 0, len = keysArray.length; nextIndex < len; nextIndex++) {
                  var nextKey = keysArray[nextIndex];
                  var desc = Object.getOwnPropertyDescriptor(nextSource, nextKey);
                  if (desc !== undefined && desc.enumerable) {
                    to[nextKey] = nextSource[nextKey];
                  }
                }
              }
              return to;
            }
          });
        }


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
        $("#assignFlatValueBtn").hide();

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
                columns.push({field: "EMPLOYEE_ID", title: "ID", width: 80, hidden: true, locked: true});
                columns.push({field: "EMPLOYEE_CODE", title: "Code", width: 80, locked: true});
                columns.push({field: "FULL_NAME", title: "Name", width: 90, locked: true});
                let totalRow = {};
                totalRow = {...totalRow, ...response.data[0]};
                let counter = 1;
                for(let i in response.data[0]){
                    totalRow[i] = '';
                    if(counter > 3){
                        columns.push({field: i, title: response.columns[counter-4].FLAT_EDESC, width: 160,
                template: '<input type="number" class="'+i+'" value="#: '+i+'||""#" style="height:17px;">'});
                    }
                    counter++; 
                }
                response.data.push(totalRow);
                app.initializeKendoGrid($table, columns);
                app.renderKendoGrid($table, response.data);
                $("#searchFieldDiv").show();
                $("#assignFlatValueBtn").show();
            }, function (error) {
                console.log(error);
            });
        });

        // kendo.data.DataSource.prototype.dataFiltered = function () {
        //     var filters = this.filter();
        //     var allData = this.data();
        //     var query = new kendo.data.Query(allData);
        //     return query.filter(filters).data;
        // }

        $table.on('input', 'input', function(e){
            var grid = $table.data("kendoGrid");
            var row = $(e.target).closest("tr");
            var dataItem = grid.dataItem(row);
            var updatedValue = this.value;
            //var data = $table.data("kendoGrid").dataItem($(e.target).closest("tr"));
            var key = this.className;
            dataItem[key] = updatedValue;
            //var dataSource = grid.dataSource.dataFiltered();
            
            if(row.is(":last-child")){
                //var elms = document.getElementsByClassName(key);
                //for (var i = 0; i < elms.length; i++) {
                   //elms[i].setAttribute("value", updatedValue);
                //}
                $("."+key).val(updatedValue);
                var dataSource = grid.dataSource;
                $.each(grid.items(), function(index, item) {
                    var uid = $(item).data("uid");
                    var dataItem = dataSource.getByUid(uid);
                    dataItem[key] = updatedValue;
                    var index = changedValues.findIndex(function(x){
                        return x.employeeId == dataItem.EMPLOYEE_ID && x.flatValue == key;
                    });
                    if(index == -1){ 
                        changedValues.push({employeeId: dataItem.EMPLOYEE_ID, flatValue: key, flatId: key.substring(2)}); 
                    }
                });
                return;
            }
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
