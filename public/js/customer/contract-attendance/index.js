function setTemplate(temp) {
    var returnvalue = '';
    if (temp == 'AB') {
        returnvalue = 'attendance-color-red';
    } else if (temp == 'PR') {
        returnvalue = 'attendance-color-green';
    }

    return returnvalue;
}

function getStausString(status, time, subEmp) {
    var returnvalue = status + ' (' + time + ')';

    if (subEmp != null) {
        returnvalue += ' (' + subEmp + ')';
    }
    return returnvalue;
}


(function ($) {
    'use strict';
    $(document).ready(function () {

        var selectedUId;
        var selectedColumnName;

        var attdAttendanceDate;
        var attdCustomerId;
        var attdContractId;
        var attdEmployeeId;
        var attdLocationId;
        var attdDutyTypeId;
        var attdDesignationId;
        var attdStartTime;
        var attdEndTime;
        $('#addInTime').combodate({
            minuteStep: 1,
        });
        $('#addOutTime').combodate({
            minuteStep: 1,
        });
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
                field: 'DESIGNATION_TITLE',
                title: "Designation",
                locked: true,
                template: '<span>#:DESIGNATION_TITLE#</span>',
                width: 100
            });
            cols.push({
                field: 'LOCATION_NAME',
                title: "Location",
                locked: true,
                template: '<span>#:LOCATION_NAME#</span>',
                width: 100
            });
            cols.push({
                field: 'START_TIME',
                title: "Start Time",
                locked: true,
                template: '<span>#:START_TIME#</span>',
                width: 100
            });
            cols.push({
                field: 'END_TIME',
                title: "End Time",
                locked: true,
                template: '<span>#:END_TIME#</span>',
                width: 100
            });
            cols.push({
                field: 'DUTY_TYPE_NAME',
                title: "Duty Type",
                locked: true,
                template: '<span>#:DUTY_TYPE_NAME#</span>',
                width: 100
            });
            for (var i = 1; i <= dayCount; i++) {
                var temp = 'C' + i;
                var tempStatus = 'C' + i + '_STATUS';
                var tempTime = 'C' + i + '_IN_OUT_TIME';
                var tempSubEmp = 'C' + i + '_SUB_EMP_NAME';
                cols.push({
                    field: temp,
                    title: "" + i,
                    template: '<button data-field="' + i + '"  class="attdBtn #: setTemplate(' + tempStatus + ') #">#: (' + tempStatus + ' == null) ? "-" : getStausString(' + tempStatus + ',' + tempTime + ',' + tempSubEmp + ') #</button>',
//                    template: '<span class="#: setTemplate(' + temp + ') #">#: (' + temp + ' == null) ? "-" : ' + temp + ' #</span>',
                    width: 100,
//                    editor: CustomSelectElement
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
        $('#grid').on("click", ".attdBtn", function () {

            var row = $(this).closest("tr"),
                    grid = $('#grid').data("kendoGrid"),
                    dataItem = grid.dataItem(row);
            selectedColumnName = $(this).attr('data-field');
            var selectedColumnVal = dataItem['C' + selectedColumnName + '_STATUS'];
            selectedUId = dataItem['uid'];

            console.log(dataItem);
            console.log(selectedUId);
            console.log(selectedColumnName);
            console.log(selectedColumnVal);
            if (selectedColumnVal != null) {
                app.serverRequest(document.pullAttendanceAbsentData, {
                    monthStartDate: dataItem['FROM_DATE'],
                    column: selectedColumnName,
                    customerId: dataItem['CUSTOMER_ID'],
                    contractId: dataItem['CONTRACT_ID'],
                    employeeId: dataItem['EMPLOYEE_ID'],
                    locationId: dataItem['LOCATION_ID'],
                    dutyTypeId: dataItem['DUTY_TYPE_ID'],
                    designationId: dataItem['DESIGNATION_ID'],
                    startTime: dataItem['START_TIME'],
                    endTime: dataItem['END_TIME']
                }).then(function (response) {
//                    console.log(response);
                    var attendanceData = response.data;
                    if (attendanceData) {
                        attdAttendanceDate = attendanceData.ATTENDNACE_DATE;
                        attdCustomerId = attendanceData.CUSTOMER_ID;
                        attdContractId = attendanceData.CONTRACT_ID;
                        attdEmployeeId = attendanceData.EMPLOYEE_ID;
                        attdLocationId = attendanceData.LOCATION_ID;
                        attdDutyTypeId = attendanceData.DUTY_TYPE_ID;
                        attdDesignationId = attendanceData.DESIGNATION_ID;
                        attdStartTime = attendanceData.START_TIME;
                        attdEndTime = attendanceData.END_TIME;
                        var attdStatus = attendanceData.STATUS;
                        var attdInTime = attendanceData.IN_TIME;
                        var attdOutTime = attendanceData.OUT_TIME;
                        var attdSubEmp = attendanceData.SUB_EMPLOYEE_ID;
                        $('#attdStatus').val(attdStatus);
                        $('#addInTime').combodate('setValue', attdInTime);
                        $('#addOutTime').combodate('setValue', attdOutTime);
                        app.populateSelect($('#subEmployee'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'No Substitute', '', attdSubEmp);
                        $('#subEmpModal').modal('show');
                    }

                });
            }
        });



        $('#updateAttendanceBtn').on('click', function () {

            var attdStaus = $('#attdStatus').val();
            var attdInTime = $('#addInTime').val()
            var attdOutTime = $('#addOutTime').val()
            var attdSubEmployeeId = $('#subEmployee').val()

            console.log('sdf');
            app.serverRequest(document.updateAttendanceData, {
                attendanceDate: attdAttendanceDate,
                customerId: attdCustomerId,
                contractId: attdContractId,
                employeeId: attdEmployeeId,
                locationId: attdLocationId,
                dutyTypeId: attdDutyTypeId,
                designationId: attdDesignationId,
                startTime: attdStartTime,
                endTime: attdEndTime,
                stauts: attdStaus,
                inTime: attdInTime,
                outTime: attdOutTime,
                subEmployeeId: attdSubEmployeeId
            }).then(function (response) {
                console.log(response);


                var grid = $('#grid').data('kendoGrid');
                var dataItem = grid.dataSource.getByUid(selectedUId);
                console.log(dataItem);
//                console.log(dataItem['C' + selectedColumnName + '_STATUS']);
                
                if(response.success==true){

                dataItem['C' + selectedColumnName + '_STATUS'] = response.data.STATUS;
                dataItem['C' + selectedColumnName + '_IN_OUT_TIME'] = response.data.IN_TIME+'-'+response.data.OUT_TIME;
                dataItem['C' + selectedColumnName + '_SUB_EMP_NAME'] = response.data.SUB_EMPLOYEE;
                grid.refresh();

                $('#subEmpModal').modal('hide');
            }


            }, function (error) {
                console.log(error);
            });

        });
    });
}
)(window.jQuery);