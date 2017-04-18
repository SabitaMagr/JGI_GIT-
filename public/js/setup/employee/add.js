(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var addrPermZoneId = $('#addrPermZoneId');
        var addrPermDistrictId = $('#addrPermDistrictId');
        var addrPermVdcMunicipalityId = $('#addrPermVdcMunicipalityId');

        if (addrPermZoneId.val() !== null) {
            if (typeof document.address !== 'undefined') {
                addrPermZoneId.val(document.address.addrPermZoneId).trigger('change');
            }
            app.fetchAndPopulate(document.urlDistrict, addrPermZoneId.val(), addrPermDistrictId, function () {
                if (addrPermDistrictId.val() !== null) {
                    if (typeof document.address !== 'undefined') {
                        addrPermDistrictId.val(document.address.addrPermDistrictId).trigger('change');
                    }
                    app.fetchAndPopulate(document.urlMunicipality, addrPermDistrictId.val(), addrPermVdcMunicipalityId, function () {
                        if (typeof document.address !== 'undefined') {
                            addrPermVdcMunicipalityId.val(document.address.addrPermVdcMunicipalityId).trigger('change');
                        }

                        addrPermZoneId.on('change', function () {
                            app.fetchAndPopulate(document.urlDistrict, addrPermZoneId.val(), addrPermDistrictId, function () {
                                if (addrPermDistrictId.val() !== null) {
                                    app.fetchAndPopulate(document.urlMunicipality, addrPermDistrictId.val(), addrPermVdcMunicipalityId);
                                }
                            });
                        });

                        addrPermDistrictId.on('change', function () {
                            app.fetchAndPopulate(document.urlMunicipality, addrPermDistrictId.val(), addrPermVdcMunicipalityId);
                        });

                    });
                }
            });
        }

        var addrTempZoneId = $('#addrTempZoneId');
        var addrTempDistrictId = $('#addrTempDistrictId');
        var addrTempVdcMunicipality = $('#addrTempVdcMunicipality');

        if (addrTempZoneId.val() !== null) {
            if (typeof document.address !== 'undefined') {
                addrTempZoneId.val(document.address.addrTempZoneId).trigger('change');
            }
            app.fetchAndPopulate(document.urlDistrict, addrTempZoneId.val(), addrTempDistrictId, function () {
                if (addrTempDistrictId.val() !== null) {
                    if (typeof document.address !== 'undefined') {
                        addrTempDistrictId.val(document.address.addrTempDistrictId).trigger('change');
                    }
                    app.fetchAndPopulate(document.urlMunicipality, addrTempDistrictId.val(), addrTempVdcMunicipality, function () {
                        if (typeof document.address !== 'undefined') {
                            addrTempVdcMunicipality.val(document.address.addrTempVdcMunicipalityId).trigger('change');
                        }

                        addrTempZoneId.on('change', function () {
                            app.fetchAndPopulate(document.urlDistrict, addrTempZoneId.val(), addrTempDistrictId, function () {
                                if (addrTempDistrictId.val() !== null) {
                                    app.fetchAndPopulate(document.urlMunicipality, addrTempDistrictId.val(), addrTempVdcMunicipality);
                                }
                            });
                        });

                        addrTempDistrictId.on('change', function () {
                            app.fetchAndPopulate(document.urlMunicipality, addrTempDistrictId.val(), addrTempVdcMunicipality);
                        });


                    });
                }
            });
        }



        $('#finishBtn').on('click', function () {
            if (typeof document.urlEmployeeList !== 'undefined') {
                location.href = document.urlSetupComplete;
            }
        });
        if (typeof document.currentTab !== "undefined") {
            $('#rootwizard').bootstrapWizard('show', parseInt(document.currentTab) - 1);
        }


        $('#filePath').on('change', function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    var previewUpload = $('#previewUpload');
                    previewUpload.attr('src', e.target.result);
                    if (previewUpload.hasClass('hidden')) {
                        previewUpload.removeClass('hidden');
                    }

                };
                reader.readAsDataURL(this.files[0]);
            }
        });
        $('form').bind('submit', function () {
            $(this).find(':disabled').removeAttr('disabled');
        });

//        var inputFieldId = "employeeCode";
//        var formId = "form1";
//        var tableName =  "HRIS_EMPLOYEES";
//        var columnName = "EMPLOYEE_CODE";
//        var checkColumnName = "EMPLOYEE_ID";
//        var selfId = $("#employeeId").val();
//        if (typeof(selfId) == "undefined"){
//            selfId=0;
//        }
//        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId);
//
//        
    });




})(window.jQuery, window.app);


