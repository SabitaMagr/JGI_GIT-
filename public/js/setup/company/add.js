(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var formId = "company-form";
        var inputFieldId = "form-companyName";
        var tableName = "HRIS_COMPANY";
        var columnName = "COMPANY_NAME";
        var checkColumnName = "COMPANY_ID";
        var $logo = $("#form-logo");
        var selfId = $("#companyId").val();
        if (typeof (selfId) == "undefined") {
            selfId = 0;
        }
        window.app.checkUniqueConstraints(inputFieldId, formId, tableName, columnName, checkColumnName, selfId);
        window.app.checkUniqueConstraints("form-companyCode", formId, tableName, "COMPANY_CODE", checkColumnName, selfId);

        var $myAwesomeDropzone = $('#my-awesome-dropzone');
        var $uploadedImage = $('#uploadedImage');

        var imageData = {
            fileCode: null,
            fileName: null,
            oldFileName: null
        };

        var toggle = function () {
            if (imageData.fileName == null) {
                $myAwesomeDropzone.show();
                $uploadedImage.hide();
            } else {
                $($uploadedImage.children()[0]).attr('src', document.basePath + "/uploads/" + imageData.fileName);
                $myAwesomeDropzone.hide();
                $uploadedImage.show();
            }
        }
        toggle();

        var dropZone = null;
        Dropzone.options.myAwesomeDropzone = {
            maxFiles: 1,
            acceptedFiles: 'image/*',
            autoProcessQueue: false,
            addRemoveLinks: true,
            init: function () {
                dropZone = this;
                this.on('success', function (file, success) {
                    imageData = success.data;
                    $logo.val(imageData.fileCode);
                    toggle();
                });
            }
        };
        $('#uploadFile').on('click', function () {
            dropZone.processQueue();
        });




        $(formId).on('submit', function (e) {
            e.preventDefault();
        });
    });
})(window.jQuery, window.app);
