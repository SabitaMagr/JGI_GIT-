/**
 * Created by ukesh on 9/2/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function(){
        app.addDatePicker($("#startDate"),$("#endDate"));
    });

})(window.jQuery,window.app);