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

        var isViewLoading = true;
        $('#calendar').fullCalendar('destroy'); // destroy the calendar
        $('#calendar').fullCalendar({ //re-initialize the calendar
            defaultDate: '2017-03-12',
            disableDragging : false,
            viewRender: function( view, element ) {
                $('#calendar .html-loading').remove();
                var intervalId = setInterval(function() {
                    var $dataHtml = element.find('.fc-content-skeleton');
                    if (!isViewLoading && $dataHtml) {
                        clearInterval(intervalId);
                        $dataHtml.find('tbody').each(function(k,v) {
                            var $attnRow = $(v).find('tr:eq(0)');
                            if ('undefined' != typeof($attnRow)) {
                                $attnRow.find('.fc-title').html(function(i,inOutTxt) {
                                    var inOutTime = inOutTxt.split(' ');
                                    if (inOutTime.length) {
                                        var output = "";
                                        if (inOutTime[0]) {
                                            output += '<span class="fc-title-in" style="background-color:none;padding:1px 3px;">' + inOutTime[0] + '</span>';
                                        }
                                        if (inOutTime[1]) {
                                            output += '<span class="fc-title-out" style="background-color:none;padding:1px 3px;">' + inOutTime[1] + '</span>';
                                        }
                                        if (output) {
                                            return output;
                                        }
                                    }
                                })
                            }
                            var $eventRow = $(v).find('tr:gt(0)');
                            if ('undefined' != typeof($eventRow)) {
                                $eventRow.find('.fc-title').css('color', '#fff');
                            }
                        });
                    }
                }, 100);
            },
            eventLimit: true,
            header: h,
            editable: false,
            cache: false,
            data: {},
            events: {
                url: document.calendarJsonFeedUrl,
                type: 'POST',
                error: function() {

                }
            },
            loading: function(isLoading, view) {
                isViewLoading = isLoading;
            }
        });
    });

    /*************** BIRTHDAY TAB CLICK EVENT ***************/
    $('.tab-pane-birthday').slimScroll({
        height: '298px'
    });

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

    ComponentsPickers.init();

})(window.jQuery, window.app);