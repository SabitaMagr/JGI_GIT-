(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#holidayTable").kendoGrid({
            excel: {
                fileName: "HolidayList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.holidays,
                pageSize: 20
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: {
                input: true,
                numeric: false
            },
            dataBound: gridDataBound,
            columns: [
                {field: "HOLIDAY_ENAME", title: "Holiday Name", template: "<span>#: (HOLIDAY_ENAME == null) ? '-' : HOLIDAY_ENAME #</span>"},
                {title: "Start Date",
                    columns: [{
                            field: "START_DATE",
                            title: "English",
                            template: "<span>#: (START_DATE == null) ? '-' : START_DATE #</span>"
                        }, {field: "START_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (START_DATE_N == null) ? '-' : START_DATE_N #</span>"
                        }]},
                {title: "End Date",
                    columns: [{
                            field: "END_DATE",
                            title: "English",
                            template: "<span>#: (END_DATE == null) ? '-' : END_DATE #</span>"},
                            {field: "END_DATE_N",
                             title: "Nepali",
                             template: "<span>#: (END_DATE_N == null) ? '-' : END_DATE_N #</span>"
                         }]},
                {field: "HALF_DAY", title: "Interval", template: "<span>#: (HALF_DAY == null) ? '-' : HALF_DAY #</span>"}
                ,
            ]
        });
        app.searchTable('holidayTable', ['HOLIDAY_ENAME', 'START_DATE', 'START_DATE_N', 'END_DATE', 'END_DATE_N', 'HALF_DAY']);
        app.pdfExport(
                'holidayTable',
                {
                    'HOLIDAY_ENAME': 'Holiday',
                    'START_DATE': 'Start Date(AD),',
                    'START_DATE_N': 'Start Date(BS),',
                    'END_DATE': 'End Date(AD)',
                    'END_DATE_N': 'End Date(BS)',
                    'HALF_DAY': 'HALF_DAY'

                });
        function gridDataBound(e) {
            var grid = e.sender;
            if (grid.dataSource.total() == 0) {
                var colCount = grid.columns.length;
                $(e.sender.wrapper)
                        .find('tbody')
                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
            }
        }
        ;
        $("#export").click(function (e) {
            var grid = $("#holidayTable").data("kendoGrid");
            grid.saveAsExcel();
        });
    }
    );
}
)(window.jQuery, window.app);
