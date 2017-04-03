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

        // Fetch all the calendar events
        app.pullDataById(document.calendarUrl, {}).then(function (response) {

        }, function(err) {

        });

        $('#calendar').fullCalendar('destroy'); // destroy the calendar
        $('#calendar').fullCalendar({ //re-initialize the calendar
            disableDragging : false,
            navLinks: true,
            eventLimit: true,
            header: h,
            editable: false,
            events: [{
                title: 'All Day Event',
                start: '2017-04-01'
                }, {
                    title: 'Long Event',
                    start: '2017-04-07',
                    end: '2017-04-10'
                }, {
                    id: 999,
                    title: 'Repeating Event',
                    start: '2017-04-09T16:00:00'
                }, {
                    id: 999,
                    title: 'Repeating Event',
                    start: '2017-04-16T16:00:00'
                }, {
                    title: 'Conference',
                    start: '2017-04-11',
                    end: '2017-04-13'
                }, {
                    title: 'Meeting',
                    start: '2017-04-12T10:30:00',
                    end: '2017-04-12T12:30:00'
                }, {
                    title: 'Lunch',
                    start: '2017-04-12T12:00:00'
                },
                {
                    title: 'Meeting',
                    start: '2017-04-12T14:30:00'
                }, {
                    title: 'Happy Hour',
                    start: '2017-04-12T17:30:00'
                },
                {
                    title: 'Dinner',
                    start: '2017-04-12T20:00:00'
                }, {
                    title: 'Birthday Party',
                    start: '2017-04-13T07:00:00'
                }, {
                    title: 'Click for Google',
                    url: 'http://google.com/',
                    start: '2017-04-28'
                }
            ]
        });
    });

    /*************** BIRTHDAY TAB CLICK EVENT ***************/
    $('.ln-nav-tab-birthday').on('click', function(e) {
        e.preventDefault();
        $('.ln-birthday').removeClass('active');
        $('.ln-birthday a').attr('aria-expanded', 'false');
        $(this).attr('aria-expanded', 'true');
        $(this).parent('li').addClass('active');
        if ($(this).is('#ln-birthday-today')) {
            $('#tab-birthday-upcoming').hide().removeClass('active');
            $('#tab-birthday-today').show().addClass('active');
        }
        else {
            $('#tab-birthday-today').hide().removeClass('active');
            $('#tab-birthday-upcoming').show().addClass('active');
        }
    });
    // setTimeout(function() {
    //     $('.upcomingholidays-loading').remove();
    //     $('.upcomingholidays .feeds').css('visibility', 'visible');
    // }, 3000);

})(window.jQuery, window.app);