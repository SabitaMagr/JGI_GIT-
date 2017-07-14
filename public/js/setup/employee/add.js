(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var addrPermZoneId = $('#addrPermZoneId');
        var addrPermDistrictId = $('#addrPermDistrictId');
        var addrPermVdcMunicipalityId = $('#addrPermVdcMunicipalityId');

        if (addrPermZoneId.val() !== null) {
            if (typeof document.address !== 'undefined' && document.address.length !== 0 && typeof document.address.addrPermZoneId !== 'undefined') {
                addrPermZoneId.val(document.address.addrPermZoneId).trigger('change');
            }
            app.fetchAndPopulate(document.urlDistrict, addrPermZoneId.val(), addrPermDistrictId, function () {
                if (addrPermDistrictId.val() !== null) {
                    if (typeof document.address !== 'undefined') {
                        addrPermDistrictId.val(document.address.addrPermDistrictId).trigger('change');
                    }
                    app.fetchAndPopulate(document.urlMunicipality, addrPermDistrictId.val(), addrPermVdcMunicipalityId, function () {

                        addrPermZoneId.on('change', function () {
                            app.fetchAndPopulate(document.urlDistrict, addrPermZoneId.val(), addrPermDistrictId, function () {
                                if (addrPermDistrictId.val() !== null) {
                                    app.pullDataById(document.urlMunicipality, {id: addrPermDistrictId.val()}).then(function (data) {
                                        var nameList = [];
                                        $.each(data, function (key, item) {
                                            nameList.push(item);
                                        });
                                        addrPermVdcMunicipalityId.val("");
                                        addrPermVdcMunicipalityId.autocomplete({
                                            source: nameList
                                        });
                                    }, function (error) {
                                        console.log("Error fetching Districts", error);
                                    });
                                }
                            });
                        });

                        addrPermDistrictId.on('change', function () {
                            app.pullDataById(document.urlMunicipality, {id: addrPermDistrictId.val()}).then(function (data) {
                                var nameList = [];
                                $.each(data, function (key, item) {
                                    nameList.push(item);
                                });
                                addrPermVdcMunicipalityId.val("");
                                addrPermVdcMunicipalityId.autocomplete({
                                    source: nameList
                                });
                            }, function (error) {
                                console.log("Error fetching Districts", error);
                            });


                        });

                    });
                }
            });
        }

        var addrTempZoneId = $('#addrTempZoneId');
        var addrTempDistrictId = $('#addrTempDistrictId');
        var addrTempVdcMunicipality = $('#addrTempVdcMunicipality');

        if (addrTempZoneId.val() !== null) {
            if (typeof document.address !== 'undefined' && document.address.length !== 0 && typeof document.address.addrTempZoneId !== 'undefined') {
                addrTempZoneId.val(document.address.addrTempZoneId).trigger('change');
            }
            app.fetchAndPopulate(document.urlDistrict, addrTempZoneId.val(), addrTempDistrictId, function () {
                if (addrTempDistrictId.val() !== null) {
                    if (typeof document.address !== 'undefined') {
                        addrTempDistrictId.val(document.address.addrTempDistrictId).trigger('change');
                    }
                    app.fetchAndPopulate(document.urlMunicipality, addrTempDistrictId.val(), addrTempVdcMunicipality, function () {

                        addrTempZoneId.on('change', function () {
                            app.fetchAndPopulate(document.urlDistrict, addrTempZoneId.val(), addrTempDistrictId, function () {
                                if (addrTempDistrictId.val() !== null) {
                                    app.pullDataById(document.urlMunicipality, {id: addrTempDistrictId.val()}).then(function (data) {
                                        var nameList = [];
                                        $.each(data, function (key, item) {
                                            nameList.push(item);
                                        });
                                        addrTempVdcMunicipality.val("");
                                        addrTempVdcMunicipality.autocomplete({
                                            source: nameList
                                        });
                                    }, function (error) {
                                        console.log("Error fetching Districts", error);
                                    });
                                }
                            });
                        });

                        addrTempDistrictId.on('change', function () {
                            app.pullDataById(document.urlMunicipality, {id: addrTempDistrictId.val()}).then(function (data) {
                                var nameList = [];
                                $.each(data, function (key, item) {
                                    nameList.push(item);
                                });
                                addrTempVdcMunicipality.val("");
                                addrTempVdcMunicipality.autocomplete({
                                    source: nameList
                                });
                            }, function (error) {
                                console.log("Error fetching Districts", error);
                            });


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

    });

})(window.jQuery, window.app);


