window.nepaliCalendar = (function ($) {
    "use strict";
    var bsadMap = {
        "2074":
                {
                    '01': {from: "2017-04-14", to: "2017-05-14"},
                    '02': {from: "2017-05-15", to: "2017-06-14"},
                    '03': {from: "2017-06-15", to: "2017-07-15"},
                    '04': {from: "2017-07-16", to: "2017-08-16"},
                    '05': {from: "2017-08-17", to: "2017-09-16"},
                    '06': {from: "2017-09-17", to: "2017-10-17"},
                    '07': {from: "2017-10-18", to: "2017-11-16"},
                    '08': {from: "2017-11-17", to: "2017-12-15"},
                    '09': {from: "2017-12-16", to: "2018-01-14"},
                    '10': {from: "2018-01-15", to: "2018-02-12"},
                    '11': {from: "2018-02-13", to: "2018-03-14"},
                    '12': {from: "2018-03-15", to: "2018-04-13"}
                }
    };
    var weekdaytemplate = {
        1: [],
        2: [],
        3: [],
        4: [],
        5: [],
        6: [],
        7: []
    };

    var weekday = null;
    var months = {
        '01': 'Baishakh',
        '02': 'Jestha',
        '03': 'Asar',
        '04': 'Shrawan',
        '05': 'Bhadau',
        '06': 'Aswin',
        '07': 'Kartik',
        '08': 'Mansir',
        '09': 'Poush',
        '10': 'Magh',
        '11': 'Falgun',
        '12': 'Chaitra',
    }
    var formatDate = function (date) {
        var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;
        return [year, month, day].join('-');
    }

    var getYears = function () {
        return Object.keys(bsadMap);
    };
    var getMonths = function () {
        return months;
    };

    var getCalendar = function (year, month) {
        var weekday = $.extend(true, {}, weekdaytemplate);
        var monthData = bsadMap[year][month];
        var fromDate = new Date(monthData['from']);
        var toDate = new Date(monthData['to']);
        var timeDiff = Math.abs(toDate.getTime() - fromDate.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
        var f = false;
        var j = 0;
        var loopDate = new Date();
        for (var i = 0; i < 50; i++) {
            var index = i % 7;
            if (!f) {
                if (index === fromDate.getDay()) {
                    f = true;
                } else {
                    weekday[index + 1].push({});
                }
            }

            if (f) {
                if (j == diffDays) {
                    break;
                }
                loopDate.setTime(fromDate.getTime() + j * 86400000);
                weekday[index + 1].push({date: formatDate(loopDate), day: j + 1});
                j++;
            }

        }
        return weekday;
    };
    return {
        getCalendar: getCalendar,
        bsadMap: bsadMap,
        getYears: getYears,
        getMonths: getMonths
    };
})(window.jQuery);
(function ($, app, nc) {
    'use strict';
    $(document).ready(function () {
        var $nepaliCalendar = $('#nepaliCalendar');
        var $content = $('#nc-content');
        var $sunday = $content.find('#nc-sunday');
        var $monday = $content.find('#nc-monday');
        var $tuesday = $content.find('#nc-tuesday');
        var $wednesday = $content.find('#nc-wednesday');
        var $thrusday = $content.find('#nc-thrusday');
        var $friday = $content.find('#nc-friday');
        var $saturday = $content.find('#nc-saturday');

        var template = `
        <div class='nc-date'>
            <table class="table table-condensed" style="inherit">
                <tr>
                    <td colspan="2" class="day" style="font-size:10px;">
                    <td>
                </tr>
                <tr>
                    <td colspan="2" class="status" style="font-size:10px;">
                    <td>
                </tr>
                <tr>
                    <td class="in-time" style="font-size:10px;"></td>
                    <td class="out-time" style="font-size:10px;"></td>
                </tr>
            </table>
        </div>`;


        var $year = $('#nc-year');
        var $month = $('#nc-month');

        var years = nc.getYears();
        var months = nc.getMonths();

        $month.on('change', function () {
            loadCalendar($year.val(), $month.val());
        });



        app.pullDataById(document.restfulUrl, {action: 'getServerDateBS'}).then(function (response) {
            var currentDate = response.data.CURRENT_DATE;
            var currentYear = currentDate.split('-')[0];
            var currentMonth = currentDate.split('-')[1];
            $year.html('');
            for (var i in years) {
                if (years[i] == currentYear) {
                    $year.append($("<option selected='selected'></option>").val(years[i]).text(years[i]));
                } else {
                    $year.append($("<option></option>").val(years[i]).text(years[i]));
                }
            }
            $month.html('');
            for (var i in months) {
                if (i == currentMonth) {
                    $month.append($("<option selected='selected'></option>").val(i).text(months[i]));
                } else {
                    $month.append($("<option></option>").val(i).text(months[i]));
                }
            }
            loadCalendar(currentYear, currentMonth);
        });

        var loadCalendar = function (year, month) {
            var monthData = nc.getCalendar(year, month);
            $sunday.html('');
            $monday.html('');
            $tuesday.html('');
            $wednesday.html('');
            $thrusday.html('');
            $friday.html('');
            $saturday.html('');

            var sundayData = monthData[1];
            for (var i = 0; i < sundayData.length; i++) {
                var $template = $(template);
                $template.attr('date', sundayData[i].date || " - ");
                $template.find('.day').append(sundayData[i].day || " - ");
                $sunday.append($template);
            }
            var mondayData = monthData[2];
            for (var i = 0; i < mondayData.length; i++) {
                var $template = $(template);
                $template.attr('date', mondayData[i].date || " - ");
                $template.find('.day').append(mondayData[i].day || " - ");
                $monday.append($template);
            }
            var tuesdayData = monthData[3];
            for (var i = 0; i < tuesdayData.length; i++) {
                var $template = $(template);
                $template.attr('date', tuesdayData[i].date || " - ");
                $template.find('.day').append(tuesdayData[i].day || " - ");
                $tuesday.append($template);
            }
            var wednesdayData = monthData[4];
            for (var i = 0; i < wednesdayData.length; i++) {
                var $template = $(template);
                $template.attr('date', wednesdayData[i].date || " - ");
                $template.find('.day').append(wednesdayData[i].day || " - ");
                $wednesday.append($template);
            }
            var thrusdayData = monthData[5];
            for (var i = 0; i < thrusdayData.length; i++) {
                var $template = $(template);
                $template.attr('date', thrusdayData[i].date || " - ");
                $template.find('.day').append(thrusdayData[i].day || " - ");
                $thrusday.append($template);
            }
            var fridayData = monthData[6];
            for (var i = 0; i < fridayData.length; i++) {
                var $template = $(template);
                $template.attr('date', fridayData[i].date || " - ");
                $template.find('.day').append(fridayData[i].day || " - ");
                $friday.append($template);
            }
            var saturdayData = monthData[7];
            for (var i = 0; i < saturdayData.length; i++) {
                var $template = $(template);
                $template.attr('date', saturdayData[i].date || " - ");
                $template.find('.day').append(saturdayData[i].day || " - ");
                $saturday.append($template);
            }
            var m = nc.bsadMap[year][month];
            app.pullDataById(document.calendarJsonFeedUrl, {'startDate': m.from, 'endDate': m.to}).then(function (response) {
                $.each(response, function (key, value) {
                    var $date = $nepaliCalendar.find('[date=' + value.ATTENDANCE_DT + ']');
                    $date.find('.in-time').html(value.IN_TIME);
                    $date.find('.out-time').html(value.OUT_TIME);
                    $date.find('.status').html(value.ATTENDANCE_STATUS);
                    if (value.OVERALL_STATUS == 'DO') {
                        $date.css('background-color', '#ADFF2F');
                        $date.children().css('background-color', '#ADFF2F');
                    } else if (value.OVERALL_STATUS == 'HD' || value.OVERALL_STATUS == 'WD') {
                        $date.css('background-color', '#eaea2a');
                        $date.children().css('background-color', '#eaea2a');
                    } else if (value.OVERALL_STATUS == 'LV' || value.OVERALL_STATUS == 'LP') {
                        $date.css('background-color', '#a7aeaf');
                        $date.children().css('background-color', '#a7aeaf');
                    } else if (value.OVERALL_STATUS == 'TN' || value.OVERALL_STATUS == 'TP') {
                        $date.css('background-color', '#39c7b8');
                        $date.children().css('background-color', '#39c7b8');
                    } else if (value.OVERALL_STATUS == 'TV' || value.OVERALL_STATUS == 'VP') {
                        $date.css('background-color', '#e89c0a');
                        $date.children().css('background-color', '#e89c0a');
                    } else if (value.OVERALL_STATUS == 'AB') {
                        $date.css('background-color', '#cc0000');
                        $date.children().css('background-color', '#cc0000');
                    }
                });
            }, function (error) {

            });
        };
    });
})(window.jQuery, window.app, window.nepaliCalendar);