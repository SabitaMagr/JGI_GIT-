(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
//        
        var $table = $('#table');
        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);

        var $search = $('#search');
        var columns = [{title: "Code", field: "EMPLOYEE_CODE", width: 80},
            {title: "Employee", field: "FULL_NAME", width: 100},
            {title: "Sunday", field: "SUN", width: 80},
            {title: "Monday", field: "MON", width: 80},
            {title: "Tuesday", field: "TUE", width: 80},
            {title: "Wednesday", field: "WED", width: 80},
            {title: "Thursday", field: "THU", width: 80},
            {title: "Friday", field: "FRI", width: 80},
            {title: "Saturday", field: "SAT", width: 80}
        
        ];
        
        var fields = {
            'EMPLOYEE_CODE': {editable: false},
            'FULL_NAME': {editable: false},
            'SUN': {type: "number"},
            'MON': {type: "number"},
            'TUE': {type: "number"},
            'WED': {type: "number"},
            'THU': {type: "number"},
            'FRI': {type: "number"},
            'SAT': {type: "number"},
        };

        var kendoConfig = {
            dataSource: {
                transport: {
                    type: "json",
                    read: {
                        url: document.getWeeklyRosterListLink,
                        type: "POST",
                    },
//                    update: {
//                        url: data.pvmUpdateLink,
//                        type: "POST",
//                    },
                    parameterMap: function (options, operation) {

//                        if (operation === "read") {
//                            selectedMonth = $month.val();
//                            var companyId = $company.val();
//                            var groupId = $group.val();
//                            return {
//                                monthId: selectedMonth,
//                                companyId: (companyId === undefined || companyId == '-1') ? null : companyId,
//                                groupId: (groupId === undefined || groupId == '-1') ? null : groupId
//                            };
//                        }
//                        if (operation !== "read" && options.models) {
//                            console.log(options.models);
//                            return {
//                                monthId: selectedMonth,
//                                models: kendo.stringify(options.models)};
//                        }


                    }
                },
                batch: true,
                schema: {
                    model: {
                        id: "EMPLOYEE_ID",
                        fields: fields
                    }
                },
                pageSize: 20
            },
            pageable: true,
            height: 550,
            toolbar: ["save", "cancel"],
            columns: columns,
            editable: true
        };



        $search.on('click', function () {

            if (typeof $table.data('kendoGrid') === 'undefined') {
                $table.kendoGrid(kendoConfig);
            } else {
                $table.data('kendoGrid').dataSource.read();
                $table.data('kendoGrid').refresh();
            }

        });





    });
})(window.jQuery, window.app);