(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("newsDate", "nepaliDate");

        var myDropzone;
        
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
            myDropzone.processQueue();
            
        myDropzone.on("success", function (file, success) {
            console.log(success);
//            console.log("Upload Image Response ", success);
            if (success.success) {
                imageUpload(success.data);
            }
        });
        
        myDropzone.on("complete", function(file) {
              myDropzone.removeAllFiles()
//              myDropzone.removeFile(file);
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