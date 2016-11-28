/**
 * Created by punam on 9/28/16.
 */
(function ($,app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var scopeArea = function(){
            var parentDesignation = $("#form-parentDesignation").val();
            if(parentDesignation=="" || parentDesignation=="-1"){
                $("#scopeArea").slideUp();
                $("input[name=withinBranch]").attr("disabled","disabled");
                $("input[name=withinDepartment]").attr("disabled","disabled");
            }else{
                $("#scopeArea").slideDown();
                $("input[name=withinBranch]").removeAttr("disabled");
                $("input[name=withinDepartment]").removeAttr("disabled");
            }
        };
        $("#form-parentDesignation").on("change",scopeArea);
        scopeArea();      
    });
})(window.jQuery,window.app);
