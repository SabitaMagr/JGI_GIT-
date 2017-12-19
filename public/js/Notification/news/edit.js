(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("newsDate", "nepaliDate");
        app.datePickerWithNepali("newsExpiryDate", "nepaliDateExpiry");        
        //to change edit values
        $('#companyId').val(document.companyEditVal).trigger('change.select2');
        $('#branchId').val(document.branchEditVal).trigger('change.select2');
        $('#departmentId').val(document.departmentEditVal).trigger('change.select2');
        $('#designationId').val(document.designationEditVal).trigger('change.select2');
        
        var employeeDataArray=document.employeeEditVal.split(",");
        $("#employeeId").val(employeeDataArray).trigger('change');

        var myDropzone;
        var $fileListTable = $('#fileDetailsTbl');

        var fileDetails = function () {
            window.app.pullDataById(document.pullNewsFile, {
            }).then(function (success) {
                $fileListTable.find("tr:not(:first)").remove();
                $.each(success.data, function (index, value) {
                    console.log(value);
                    $fileListTable.append("<tr><td>" + value.FILE_NAME + "</td><td><button class=' btn btn-danger deleteFile' type='button' data-id=" + value.NEWS_FILE_ID + ">DELETE</button></td><tr>");
                });

            }, function (failure) {
            });
        }

        fileDetails();


        $fileListTable.on("click", "td .deleteFile", function () {
            var selectedDeleteBtnId = $(this).attr('data-id');
            window.app.pullDataById(document.deleteFile, {
                 id: selectedDeleteBtnId
            }).then(function (success) {
                fileDetails();
            }, function (failure) {
            });

        });



        var fileData = {
            newsTypeId: null,
            filePath: null,
            editMode: false,
            fileName: null
        };

        Dropzone.autoDiscover = false;


        $('#addDocument').on('click', function () {
            $('#uploadErr').hide();
            console.log(document.uploadUrl);
            $('#documentUploadModel').modal('show');
            myDropzone = new Dropzone("#dropZoneContainer", {
                url: document.uploadUrl,
                autoProcessQueue: false,
                maxFiles: 1,
                addRemoveLinks: true
            });
        });


        $('#uploadSubmitBtn').on('click', function () {
            if (myDropzone.files.length == 0) {
                $('#uploadErr').show();
                return;
            } else {
                $('#uploadErr').hide();
            }
            $('#documentUploadModel').modal('hide');
            myDropzone.processQueue();
            myDropzone.on("success", function (file, success) {
                console.log(success);
//            console.log("Upload Image Response ", success);
                if (success.success) {
                    imageUpload(success.data);
                }
            });

            myDropzone.on("complete", function (file) {
                location.reload();
            });

        });



        $('#uploadCancelBtn').on('click', function () {
            $('#documentUploadModel').modal('hide');
        });

        var imageUpload = function (data) {
            console.log(data);
            window.app.pullDataById(document.pushNewsFileLink, {
                'newsTypeId': null,
                'filePath': data.fileName,
                'fileName': data.oldFileName
            }).then(function (success) {
                if (success.data != null) {
                    console.log('added sucessfully');
                }
            }, function (failure) {
            });
        }




    });
})(window.jQuery, window.app);