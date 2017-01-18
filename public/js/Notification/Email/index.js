(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var cctemplate = ' <div class="row"> <div class="col-sm-6"> <div class="form-group"> <input name="ccEmail[]" type="text" class="form-control" placeholder="Email" value=""> </div></div><div class="col-sm-6"> <div class="form-group"> <input name="ccName[]" type="text" class="form-control" placeholder="Name" value=""> </div></div></div>';
        var bcctemplate = ' <div class="row"> <div class="col-sm-6"> <div class="form-group"> <input name="bccEmail[]" type="text" class="form-control" placeholder="Email" value=""> </div></div><div class="col-sm-6"> <div class="form-group"> <input name="bccName[]" type="text" class="form-control" placeholder="Name" value=""> </div></div></div>';

        $('.summernote')
                .each(function () {
                    var $this = $(this);
                    var temp = $this.parent().find("input[name='description']").val();
                    $this.summernote({height: 300,
                        minHeight: null,
                        maxHeight: null,
                        focus: true
                    });
                    $this.summernote('code', temp);
                });
        $('.form').each(function () {
            var $this = $(this);
            $this.submit(function (e) {
                $this = $(this);
                var summernote = $this.find('.summernote');
                var message = $this.find("input[name='description']");
                $(message).val(summernote.summernote('code'));
                return true;
            });
        });

        $('.btnAddCC').each(function () {
            var $this = $(this);
            $this.on('click', function () {
                $(this).parent().parent().parent().append(cctemplate);

            });
        });
        $('.btnAddBCC').each(function () {
            var $this = $(this);
            $this.on('click', function () {
                $(this).parent().parent().parent().append(bcctemplate);

            });
        });
    });

})(window.jQuery, window.app);