(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var address = document.address || {};
        var addrPermZoneId = $('#addrPermZoneId');
        var addrPermDistrictId = $('#addrPermDistrictId');
        var addrPermVdcMunicipalityId = $('#addrPermVdcMunicipalityId');
        var addrTempZoneId = $('#addrTempZoneId');
        var addrTempDistrictId = $('#addrTempDistrictId');
        var addrTempVdcMunicipality = $('#addrTempVdcMunicipality');

        /*
         * 
         */
        var onChangePermZone = function (zoneId) {
            if (zoneId == null) {
                app.populateSelectElement(addrPermDistrictId, []);
                onChangePermDistrict(null);
                return;
            }
            app.pullDataById(document.urlDistrict, {id: zoneId}).then(function (data) {
                app.populateSelectElement(addrPermDistrictId, data, address['addrPermDistrictId']);
                onChangePermDistrict(addrPermDistrictId.val());
            }, function (error) {
                console.log("url=>" + document.urlDistrict, error);
            });
        };

        var onChangePermDistrict = function (districtId) {
            if (districtId == null) {
                addrPermVdcMunicipalityId.val('');
                addrPermVdcMunicipalityId.autocomplete({
                    source: []
                });
                return;
            }

            app.pullDataById(document.urlMunicipality, {id: districtId}).then(function (data) {
                var nameList = [];
                var value = "";
                $.each(data, function (key, item) {
                    nameList.push(item);
                    if (address['addrPermVdcMunicipalityId'] == key) {
                        value = item;
                    }
                });
                addrPermVdcMunicipalityId.val(value);
                addrPermVdcMunicipalityId.autocomplete({
                    source: nameList
                });
            }, function (error) {
                console.log("url=>" + document.urlMunicipality, error);
            });
        };

        var onChangeTempZone = function (zoneId) {
            if (zoneId == null) {
                app.populateSelectElement(addrTempDistrictId, []);
                onChangeTempDistrict(null);
                return;
            }
            app.pullDataById(document.urlDistrict, {id: zoneId}).then(function (data) {
                app.populateSelectElement(addrTempDistrictId, data, address['addrTempDistrictId']);
                onChangeTempDistrict(addrTempDistrictId.val());
            }, function (error) {
                console.log("url=>" + document.urlDistrict, error);
            });
        };

        var onChangeTempDistrict = function (districtId) {
            if (districtId == null) {
                addrTempVdcMunicipality.val('');
                addrTempVdcMunicipality.autocomplete({
                    source: []
                });
                return;
            }

            app.pullDataById(document.urlMunicipality, {id: districtId}).then(function (data) {
                var nameList = [];
                var value = "";
                $.each(data, function (key, item) {
                    nameList.push(item);
                    if (address['addrTempVdcMunicipalityId'] == key) {
                        value = item;
                    }
                });
                addrTempVdcMunicipality.val(value);
                addrTempVdcMunicipality.autocomplete({
                    source: nameList
                });
            }, function (error) {
                console.log("url=>" + document.urlMunicipality, error);
            });
        };


        /*
         * 
         */
        addrPermZoneId.on('change', function () {
            var $this = $(this);
            onChangePermZone($this.val());
        });

        addrPermDistrictId.on('change', function () {
            var $this = $(this);
            onChangePermDistrict($this.val());
        });

        addrTempZoneId.on('change', function () {
            var $this = $(this);
            onChangeTempZone($this.val());
        });

        addrTempDistrictId.on('change', function () {
            var $this = $(this);
            onChangeTempDistrict($this.val());
        });

        onChangePermZone(addrPermZoneId.val());
        onChangeTempZone(addrTempZoneId.val())



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


