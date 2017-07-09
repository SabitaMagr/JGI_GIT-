(function ($, app, appraisalCustom) {
    'use strict';
    $(document).ready(function () {

        $(function () {
            $("#printable").children("div.portlet-body").css({'padding':'0px'});
            $('.print').on('click', function () {
                $("#printable").children("div.custom-box").css({'border':'none', 'box-shadow' : 'none'});
                $("#printable").children("div.portlet-body").css({'padding':'0px'});
                $("#printable").children("div.md-radio-inline").css({'margin':'0px !important'});
                //unwrap();
                $('.tab-pane').show();
                $('.nav-tabs').hide();
                $('.appRemarks').remove();
                $("#printable").find('input,select,textarea').each(function () {
                    var val ="";
                    if ($(this).attr('type') !== 'hidden' && $(this).attr('type') !== 'radio' && $(this).attr('type') !== 'checkbox' && !$(this).is("select")) {
                        val = $(this).val();
                        var innertext = $('<span />', {
                            text: val,
                            style:'font-size:11px'
                        });
                        $(this).replaceWith(innertext);
                        
                    }else if($(this).attr('type')==='radio' || $(this).attr('type')==='checkbox'){
                        if($(this).prop("checked") === true){
                            val = $(this).parent('div').children('label').text();
                            var parentId = $(this).parent("div").parent("div").parent("div");
                            var innertext = $('<span />', {
                                text: val,
                                style:'font-size:11px'
                            });
                            parentId.append(innertext);
                        }
                        $(this).parent('div').parent('div').remove();
                    }else if($(this).is('select')){
                            val = $(this).find("option:selected").text();
                            var parentId = $(this).parent("div");
                            var innertext = $('<span />', {
                                text: val,
                                style:'font-size:11px'
                            });
                            parentId.prepend(innertext);
                        $(this).remove();
                    }
                });
                $("#printable").print({
                    globalStyles: true,
                    append : " ",
                    title: null,
                    prepend : " ",
                    deferred: $.Deferred().done(function() {
                        App.blockUI({target: "#hris-page-content"});
                        window.location.reload();
                        App.unblockUI("#hris-page-content");
                      })
                });
            });
        });
        app.setLoadingOnSubmit("hrAppraisalReview");
    });
})(window.jQuery, window.app, window.appraisalCustom);