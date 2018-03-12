(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();


        var $cutomerSelect = $('#customerSelect');
        var $monthSelect = $('#monthSelect');
        app.populateSelect($cutomerSelect, document.customerList, 'CUSTOMER_ID', 'CUSTOMER_ENAME', 'Select An Customer', '');
        app.populateSelect($monthSelect, document.monthList, 'MONTH_ID', 'MONTH_EDESC', 'Select An Month', '');


        var cols = [];

        cols.push({
            field: 'FULL_NAME',
            title: "Name",
            locked: true,
            template: '<span>#:FULL_NAME#</span>',
            width: 100
        });

        cols.push({
            field: 'LOCATION_NAME',
            title: "Location",
            locked: true,
            template: '<span>#:LOCATION_NAME#</span>',
            width: 100
        });

        function CustomSelectElement(container, options) {

            var guid = kendo.guid();
            $('<select id="' + guid + '" name="' + options.field + '"><option>PR<option>AB</option><option>HO</option></select>').appendTo(container);
        }


        for (var i = 1; i < 33; i++) {
            var temp = 'C' + i;
            cols.push({
                field: temp,
                title: "" + i,
                template: '<span>#: ' + temp + ' #</span>',
                width: 50,
                editor: CustomSelectElement
            });
        }

        cols.push({
            locked: true,
            command: ["edit"],
            title: "&nbsp;",
            width: 100
        });


        $('#viewBtn').on('click', function () {
            var selectedCustomerVal = $cutomerSelect.val();
            var selectedMonthVal = $monthSelect.val();

            if (selectedCustomerVal == '' || selectedMonthVal == '') {
                app.errorMessage('Customer or Month is not selected ', 'error');
                return;
            }

            $("#grid").empty();

            var crudServiceBaseUrl = "https://demos.telerik.com/kendo-ui/service",
                    dataSource = new kendo.data.DataSource({
                        transport: {
                            read: function (e) {
                                app.serverRequest(document.pullCustomerMonthlyAttendanceUrl, {
                                    customerId: selectedCustomerVal,
                                    monthId: selectedMonthVal

                                }
                                ).then(function (response) {

                                    e.success(response.data);
                                });


                            },
                            update: function (e) {
                                var rowData = e.data.models[0];

                                app.serverRequest(document.updateEmpContractAttendnace, {
                                    customerId: selectedCustomerVal,
                                    monthId: selectedMonthVal,
                                    kendoData: rowData

                                }
                                ).then(function (response) {
                                    console.log(response.success);
                                    if (response.success == true) {
                                        e.success();
                                    }
                                });

                            },
                            parameterMap: function (options, operation) {
                                if (operation !== "read" && options.models) {
                                    return {models: kendo.stringify(options.models)};
                                }
                            }
                        },
                        batch: true,
                        pageSize: 100,
                        schema: {
                            model: {
                                id: "CONTRACT_ID",
                                fields: {
//                                        CONTRACT_ID: { editable: false, nullable: true },
                                    FULL_NAME: {editable: false, nullable: true},
                                    LOCATION_NAME: {editable: false, nullable: true},
//                                    C1: {},
                                }
                            }
                        }
                    });


            $("#grid").kendoGrid({
                dataSource: dataSource,
                height: 450,
                scrollable: true,
                columns: cols,
//                        columns: [
//                            "ProductName",
//                            { field: "UnitPrice", title: "Unit Price", format: "{0:c}", width: "120px" },
//                            { field: "UnitsInStock", title:"Units In Stock", width: "120px" },
//                            { field: "Discontinued", width: "120px", editor: customBoolEditor },
//                            { field: "Discontinued", width: "120px", editor: customBoolEditor },
//                            { command: ["edit", "destroy"], title: "&nbsp;", width: "250px" }],
//                editable: true
                editable: "inline"
            });


        });






    });
})(window.jQuery);