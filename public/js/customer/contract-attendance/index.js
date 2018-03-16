function setTemplate(temp) {
    var returnvalue = '';
    if (temp == 'AB') {
        returnvalue = 'attendance-color-red';
    } else if (temp == 'PR') {
        returnvalue = 'attendance-color-green';
    }

    return returnvalue;
}
(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();




        var $cutomerSelect = $('#customerSelect');
        var $monthSelect = $('#monthSelect');
        app.populateSelect($cutomerSelect, document.customerList, 'CUSTOMER_ID', 'CUSTOMER_ENAME', 'Select An Customer', '');
        app.populateSelect($monthSelect, document.monthList, 'MONTH_ID', 'MONTH_EDESC', 'Select An Month', '');




        function CustomSelectElement(container, options) {

            var guid = kendo.guid();
            $('<select id="' + guid + '" name="' + options.field + '"><option>PR<option>AB</option><option>HO</option></select>').appendTo(container);
        }



        function GenerateColsForKendo(dayCount) {
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

            for (var i = 1; i <= dayCount; i++) {
                var temp = 'C' + i;
                cols.push({
                    field: temp,
                    title: "" + i,
                    template: '<span class="#: setTemplate(' + temp + ') #">#: ('+temp+' == null) ? "-" : '+temp+' #</span>',
                    width: 35,
                    editor: CustomSelectElement
                });
            }

//            cols.push({
//                locked: true,
//                command: ["edit"],
//                title: "&nbsp;",
//                width: 100
//            });

            return cols;
        }



        $('#viewBtn').on('click', function () {
            var selectedCustomerVal = $cutomerSelect.val();
            var selectedMonthVal = $monthSelect.val();

            if (selectedCustomerVal == '' || selectedMonthVal == '') {
                app.errorMessage('Customer or Month is not selected ', 'error');
                return;
            }

            $("#grid").empty();


            app.serverRequest(document.pullCustomerMonthlyAttendanceUrl, {
                customerId: selectedCustomerVal,
                monthId: selectedMonthVal

            }
            ).then(function (response) {
//                console.log(response);
                console.log(response.data.monthDetails.DAYSCOUNT);
                var cols = [];
                cols = GenerateColsForKendo(response.data.monthDetails.DAYSCOUNT);

                var crudServiceBaseUrl = "https://demos.telerik.com/kendo-ui/service",
                        dataSource = new kendo.data.DataSource({
                            transport: {
                                read: function (e) {
                                    e.success(response.data.attendanceResult);
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
                    editable: "inline"
                });


            });







        });






    });
})(window.jQuery);