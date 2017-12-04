(function ($, app) {
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);

        var $shiftAssignTable = $('#shiftAssignTable');
        var $search = $('#search');
        var $bulkActionDiv = $('#bulkActionDiv');
        var $shiftId = $('#shiftId');
        var $fromDate = $('#fromDate');
        var $nepaliFromDate = $('#nepaliFromDate');
        var $toDate = $('#toDate');
        var $nepaliToDate = $('#nepaliToDate');
        var $bulkEdit = $('#bulkEdit');


        var grid = app.initializeKendoGrid($shiftAssignTable, [
            {field: "COMPANY_NAME", title: "Company", width: 150},
            {field: "BRANCH_NAME", title: "Branch", width: 150},
            {field: "DEPARTMENT_NAME", title: "Department", width: 150},
            {field: "POSITION_NAME", title: "Position", width: 150},
            {field: "DESIGNATION_TITLE", title: "Designation", width: 150},
            {field: "FULL_NAME", title: "Name", width: 150},
            {field: "SHIFT_ENAME", title: "Shift", width: 150},
            {title: "From", columns: [
                    {field: "FROM_DATE_AD", title: "AD", width: 75},
                    {field: "FROM_DATE_BS", title: "BS", width: 75},
                ]},
            {title: "To", columns: [
                    {field: "TO_DATE_AD", title: "AD", width: 75},
                    {field: "TO_DATE_BS", title: "BS", width: 75},
                ]},
        ], null, {id: 'ID', atLast: true, fn: function (selected) {
                if (selected) {
                    $bulkActionDiv.show();
                } else {
                    $bulkActionDiv.hide();
                }
            }});

        app.searchTable('shiftAssignTable', ['COMPANY_NAME', 'BRANCH_NAME', 'DEPARTMENT_NAME', 'POSITION_NAME', 'DESIGNATION_TITLE', 'FULL_NAME', 'SHIFT_ENAME', 'FROM_DATE_AD', 'FROM_DATE_BS', 'TO_DATE_AD', 'TO_DATE_BS'], true);
        app.pdfExport(
                'shiftAssignTable',
                {
                    'COMPANY_NAME': 'Company',
                    'BRANCH_NAME': 'Branch',
                    'DEPARTMENT_NAME': 'Department',
                    'POSITION_NAME': 'Position',
                    'DESIGNATION_TITLE': 'Designation',
                    'FULL_NAME': 'Name',
                    'SHIFT_ENAME': 'Shift',
                    'FROM_DATE_AD': 'From(AD)',
                    'FROM_DATE_BS': 'From(BS)',
                    'TO_DATE_AD': 'To(AD)',
                    'TO_DATE_BS': 'To(BS)',
                }
        );

        $search.on('click', function () {
            grid.clearSelected();
            $shiftId.val(-1).trigger('change.select2');
            $fromDate.val('');
            $nepaliFromDate.val('');
            $toDate.val('');
            $nepaliToDate.val('');
            $bulkActionDiv.hide();
            var search = document.searchManager.getSearchValues();
            app.pullDataById(document.listWS, search).then(function (response) {
                app.renderKendoGrid($shiftAssignTable, response.data);
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        app.populateSelect($shiftId, document.shiftList, 'SHIFT_ID', 'SHIFT_ENAME', 'Select Shift', -1);

        $bulkEdit.on('click', function () {
            var shiftId = $shiftId.val();
            if (shiftId == -1) {
                app.showMessage("Select Shift First.", "error");
                $shiftId.focus();
                return;
            }
            var fromDate = $fromDate.val();
            if (fromDate == "") {
                app.showMessage("From Date is required.", "error");
                $fromDate.focus();
                return;
            }
            var employeeShift = grid.getSelected();
            var toDate = $toDate.val();
            var employeeShiftIds = [];

            for (var i in employeeShift) {
                employeeShiftIds.push(employeeShift[i]['ID']);
            }
            (function (employeeIdList) {
                var counter = 0;
                var length = employeeIdList.length;
                var addShift = function (employeeId) {
                    app.pullDataById(document.editWs, {
                        shiftId: shiftId,
                        fromDate: fromDate,
                        toDate: toDate,
                        employeeIds: [employeeId]
                    }).then(function (response) {
                        NProgress.set((counter + 1) / length);
                        if (!response.success) {
                            app.showMessage("Shift Assign Edit for Employee Id : " + employeeId + "Failed.", 'error');
                        }
                        counter++;
                        if (counter < length) {
                            addShift(employeeIdList[counter]);
                        } else {
                            app.showMessage("Shift Assign Edited.");
                            $search.trigger('click');
                        }
                    }, function (error) {

                    });

                };
                NProgress.start();
                addShift(employeeIdList[counter]);


            })(employeeShiftIds);

        });


    });
})(window.jQuery, window.app);
