/**
 * Created by ukesh on 9/12/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
    app.addDatePicker(
        $("#startDate"),
        $("#endDate")
    );

    });
})(window.jQuery,window.app);