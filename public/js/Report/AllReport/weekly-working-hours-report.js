(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.datePickerWithNepali('fromDate', 'nepaliFromDate');
        var $employeeTable = $('#employeeTable');
        var $search = $('#search');
    

        app.initializeKendoGrid($employeeTable, [
            {field: "EMPLOYEE_CODE", title: "Code", locked: true, width: 100},
            {field: "FULL_NAME", title: "Employee", locked: true, width: 150},
            {field: "DEPARTMENT_NAME", title: "Department", locked: true, width: 100},
            {field: "ASSIGNED_HOUR", title: "Assigned Hours", locked: true, width: 100},
            {field: "TOTAL_HOURS_WORKED", title: "Total No. Of Hours", locked: true, width: 100},
            {title: "No. Of Hours Worked In A Week", columns: [
                {field: "SUN_WH", title: "Sunday", locked: true, width: 100},
                {field: "MON_WH", title: "Monday", locked: true, width: 100},
                {field: "TUE_WH", title: "Tuesday", locked: true, width: 100},
                {field: "WED_WH", title: "Wednesday", locked: true, width: 100},
                {field: "THU_WH", title: "Thursday", locked: true, width: 100},
                {field: "FRI_WH", title: "Friday", locked: true, width: 100},
                {field: "SAT_WH", title: "Saturday", width: 100}
            ]}
            /*{field: "LEVEL_NO", title: "Level", width: 150},
            {field: "LOCATION_EDESC", title: "Location", width: 150},
            {field: "FUNCTIONAL_TYPE_EDESC", title: "Functional Type", width: 150},
            {field: "FUNCTIONAL_LEVEL_EDESC", title: "Functional Level", width: 150}*/
        ]);

        app.searchTable('employeeTable', ['EMPLOYEE_CODE', 'FULL_NAME'], false);
  
        var map = {
            'EMPLOYEE_ID': 'Employee Id',
            'EMPLOYEE_CODE': 'Employee Code',
            //'TITLE': 'Title',
            'FULL_NAME': 'Employee',
            //'GENDER_NAME': 'Gender',
            'BIRTH_DATE_AD': 'Birth Date(AD)',
            //'BIRTH_DATE_BS': 'Birth Date(BS)',
            'JOIN_DATE_AD': 'Join Date(AD)',
            'AGE': 'Age',
            'SERVICE_DURATION': 'Service Duration'
        }; 

        $('#excelExport').on('click', function () {
            app.excelExport($employeeTable, map, 'Birthday Employee List.xlsx');        
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($employeeTable, map, 'Birthday Employee List.pdf');
        });
       
        $search.on('click', function () {
            var responseData;
            var data = document.searchManager.getSearchValues();
            var fromDate = $('#fromDate').val();
            data.fromDate = fromDate; 
            app.serverRequest(document.pullEmployeeListForEmployeeTableLink, data).then(function (response) { 
                if (response.success) {
                    responseData = response.data;
                   
                    for(var i = 0; i < responseData.length; i++){
                        responseData[i].TOTAL_HOURS_WORKED = (parseInt(responseData[i].SUN_WH) || 0)+(parseInt(responseData[i].MON_WH) || 0)+(parseInt(responseData[i].TUE_WH) || 0)+(parseInt(responseData[i].WED_WH) || 0)+(parseInt(responseData[i].THU_WH) || 0)+(parseInt(responseData[i].FRI_WH) || 0)+(parseInt(responseData[i].SAT_WH) || 0);
                    }

                    app.renderKendoGrid($employeeTable, responseData);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });  
        });
    });  
})(window.jQuery, window.app);