(function ($, app) {
    "use strict";
    $(document).ready(function () {
        console.log(document.tableName);

        app.checkUniqueConstraints("flatCode",
                "flatValueFormId",
                document.tableName,
                document.flatCodeAttr,
                document.flatIdAttr,
                (typeof document.flatId !== 'undefined') ? document.flatId : 0);
    });
})(window.jQuery, window.app);