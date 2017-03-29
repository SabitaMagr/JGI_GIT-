(function ($, app) {
    'use strict';
    $(document).ready(function () {
        // Index.init();
        // Index.initCalendar();

        if (!$().fullCalendar) {
            return;
        }

        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        var h = {};

        if ($('#calendar').width() <= 400) {
            $('#calendar').addClass("mobile");
            h = {
                left: 'title, prev, next',
                center: '',
                right: 'today,month,agendaWeek,agendaDay'
            };
        } else {
            $('#calendar').removeClass("mobile");
            if (Metronic.isRTL()) {
                h = {
                    right: 'title',
                    center: '',
                    left: 'prev,next,today,month,agendaWeek,agendaDay'
                };
            } else {
                h = {
                    left: 'title',
                    center: '',
                    right: 'prev,next,today,month,agendaWeek,agendaDay'
                };
            }
        }

        $('#calendar').fullCalendar('destroy'); // destroy the calendar
        $('#calendar').fullCalendar({ //re-initialize the calendar
            disableDragging : false,
            header: h,
            editable: true,
            events: [{
                title: 'All Day',
                start: new Date(y, m, 1),
                backgroundColor: Metronic.getBrandColor('yellow')
            }, {
                title: 'Long Event',
                start: new Date(y, m, d - 5),
                end: new Date(y, m, d - 2),
                backgroundColor: Metronic.getBrandColor('blue')
            }, {
                title: 'Repeating Event',
                start: new Date(y, m, d - 3, 16, 0),
                allDay: false,
                backgroundColor: Metronic.getBrandColor('red')
            }, {
                title: 'Repeating Event',
                start: new Date(y, m, d + 6, 16, 0),
                allDay: false,
                backgroundColor: Metronic.getBrandColor('green')
            }, {
                title: 'Meeting',
                start: new Date(y, m, d+9, 10, 30),
                allDay: false
            }, {
                title: 'Lunch',
                start: new Date(y, m, d, 14, 0),
                end: new Date(y, m, d, 14, 0),
                backgroundColor: Metronic.getBrandColor('grey'),
                allDay: false
            }, {
                title: 'Birthday',
                start: new Date(y, m, d + 1, 19, 0),
                end: new Date(y, m, d + 1, 22, 30),
                backgroundColor: Metronic.getBrandColor('purple'),
                allDay: false
            }, {
                title: 'Click for Google',
                start: new Date(y, m, 28),
                end: new Date(y, m, 29),
                backgroundColor: Metronic.getBrandColor('yellow'),
                url: 'http://google.com/'
            }]
        });
    });
})(window.jQuery, window.app);