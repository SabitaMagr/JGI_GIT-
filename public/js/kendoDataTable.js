(function ($) {
    'use strict';
    $('#dataTable').kendoGrid({height: 300,
        groupable: false,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        }});
})(window.jQuery);