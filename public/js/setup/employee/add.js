(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var addrPermZoneId = $('#addrPermZoneId')
        var addrPermDistrictId = $('#addrPermDistrictId');
        var addrPermVdcMunicipalityId = $('#addrPermVdcMunicipalityId');

        if (addrPermZoneId.val() != null) {
            app.fetchAndPopulate(document.urlDistrict, addrPermZoneId.val(), addrPermDistrictId, function () {
                if (addrPermDistrictId.val() != null) {
                    app.fetchAndPopulate(document.urlMunicipality, addrPermDistrictId.val(), addrPermVdcMunicipalityId);
                }
            });
        }

        addrPermZoneId.on('change', function () {
            app.fetchAndPopulate(document.urlDistrict, addrPermZoneId.val(), addrPermDistrictId, function () {
                if (addrPermDistrictId.val() != null) {
                    app.fetchAndPopulate(document.urlMunicipality, addrPermDistrictId.val(), addrPermVdcMunicipalityId);
                }

            });
        });

        addrPermDistrictId.on('change', function () {
            app.fetchAndPopulate(document.urlMunicipality, addrPermDistrictId.val(), addrPermVdcMunicipalityId);
        });


        var addrTempZoneId = $('#addrTempZoneId')
        var addrTempDistrictId = $('#addrTempDistrictId');
        var addrTempVdcMunicipality = $('#addrTempVdcMunicipality');

        if (addrTempZoneId.val() != null) {
            app.fetchAndPopulate(document.urlDistrict, addrTempZoneId.val(), addrTempDistrictId, function () {
                if (addrTempDistrictId.val() != null) {
                    app.fetchAndPopulate(document.urlMunicipality, addrTempDistrictId.val(), addrTempVdcMunicipality);
                }
            });
        }

        addrTempZoneId.on('change', function () {
            app.fetchAndPopulate(document.urlDistrict, addrTempZoneId.val(), addrTempDistrictId, function () {
                if (addrTempDistrictId.val() != null) {
                    app.fetchAndPopulate(document.urlMunicipality, addrTempDistrictId.val(), addrTempVdcMunicipality);
                }
            });
        });

        addrTempDistrictId.on('change', function () {
            app.fetchAndPopulate(document.urlMunicipality, addrTempDistrictId.val(), addrTempVdcMunicipality);
        });


        $('#finishBtn').on('click', function () {
            if (typeof document.urlEmployeeList !== 'undefined') {
                location.href = document.urlEmployeeList;
            }
        });
        if (typeof document.currentTab !== "undefined") {
            // $('[href="#tab'+document.currentTab+'"]').click();
            $('#rootwizard').bootstrapWizard('show', parseInt(document.currentTab) - 1);
        }
        // $('#formEmployee').validate({rules: {'form-employeeCode': 'required'}, messages: {'form-employeeCode': "ee"}});


        app.addDatePicker(
            $("#employeeBirthDate"),
            $("#joinDate"),
            $("#idPassportExpiry"),
            $("#idCitizenshipIssueDate"),
            $("#idDrivingLicenseExpiry"),
            $("#famSpouseWeddingAnniversary"),
            $("#famSpouseBirthDate"),
            $("#startDate"),
            $("#endDate")
        );

        $('#filePath').on('change', function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    var previewUpload = $('#previewUpload');
                    previewUpload.attr('src', e.target.result);
                    if (previewUpload.hasClass('hidden')) {
                        previewUpload.removeClass('hidden');
                    }

                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });


})(window.jQuery, window.app);

