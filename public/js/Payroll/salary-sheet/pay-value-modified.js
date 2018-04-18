(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        /**/
        var months = null;
        var companyList = null;
        var groupList = null;
        /**/
        var $year = $('#fiscalYearId');
        var $month = $('#monthId');
        var $company = $('#companyId');
        var $group = $('#groupId');
        var $table = $('#table');

        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
            months = monthList;
        }, data.getFiscalYearMonthLink);

        (function ($companyId, link) {
            var onDataLoad = function (data) {
                companyList = data['company'];
                app.populateSelect($companyId, data['company'], 'COMPANY_ID', 'COMPANY_NAME', 'Select Company');
            };
            app.serverRequest(link, {}).then(function (response) {
                if (response.success) {
                    onDataLoad(response.data);
                }
            }, function (error) {

            });
        })($company, data.getSearchDataLink);

        (function ($groupId, link) {
            var onDataLoad = function (data) {
                groupList = data;
                app.populateSelect($groupId, groupList, 'GROUP_ID', 'GROUP_NAME', 'Select Group');
            };
            app.serverRequest(link, {}).then(function (response) {
                if (response.success) {
                    onDataLoad(response.data);
                }
            }, function (error) {

            });
        })($group, data.getGroupListLink);

        var salarySheetChange = function () {
            var monthId = $month.val();
            var companyId = $company.val();
            var groupId = $group.val();

            if (monthId === null && monthId === '') {
                return;
            }
        };
        var columns = [{field: 'FULL_NAME', title: 'Employee Name', width: 150, locked: true}];
        var fields = {};

        $.each(data.ruleList, function (k, v) {
            columns.push({field: v['PAY_ID_COL'], title: v['PAY_EDESC'], width: 100});
            fields[v['PAY_ID_COL']] = {type: "number"};
        });
        columns.push({command: ["edit"], title: "&nbsp;", width: "150px"});

        var kendoConfig = {
            dataSource: {
                transport: {
                    type: "json",
                    read: {
                        url: data.pvmReadLink,
                        type: "POST",
                    },
                    update: {
                        url: '',
                        type: "POST",
                    },
                    parameterMap: function (options, operation) {
                        if (operation === "read") {
                            return {
                                monthId: $month.val(),
                                companyId: $company.val(),
                                groupId: $group.val()
                            };
                        }
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
            columns: columns,
            editable: "inline"
        };
        $table.kendoGrid(kendoConfig);
    });
})(window.jQuery, window.app);