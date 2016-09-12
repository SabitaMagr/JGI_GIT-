/**
 * Created by ukesh on 8/29/16.
 */
function fetchAndPopulate(url, id, element, callback) {
    pullDataById(url, {id: id}).then(function (data) {
        populateSelectElement(element, data);
        if (typeof callback !== 'undefined') {
            callback();
        }
    }, function (error) {
        console.log("Error fetching Districts", error);
    });
}

$(document).ready(function () {
    var addrPermZoneId = $('#addrPermZoneId')
    var addrPermDistrictId = $('#addrPermDistrictId');
    var addrPermVdcMunicipalityId = $('#addrPermVdcMunicipalityId');

    if (addrPermZoneId.val() != null) {
        fetchAndPopulate(document.urlDistrict, addrPermZoneId.val(), addrPermDistrictId, function () {
            if (addrPermDistrictId.val() != null) {
                fetchAndPopulate(document.urlMunicipality, addrPermDistrictId.val(), addrPermVdcMunicipalityId);
            }

        });
    }

    addrPermZoneId.on('change', function () {
        fetchAndPopulate(document.urlDistrict, addrPermZoneId.val(), addrPermDistrictId, function () {
            if (addrPermDistrictId.val() != null) {
                fetchAndPopulate(document.urlMunicipality, addrPermDistrictId.val(), addrPermVdcMunicipalityId);
            }

        });
    });

    addrPermDistrictId.on('change', function () {
        fetchAndPopulate(document.urlMunicipality, addrPermDistrictId.val(), addrPermVdcMunicipalityId);
    });


    var addrTempZoneId = $('#addrTempZoneId')
    var addrTempDistrictId = $('#addrTempDistrictId');
    var addrTempVdcMunicipality = $('#addrTempVdcMunicipality');

    if (addrTempZoneId.val() != null) {
        fetchAndPopulate(document.urlDistrict, addrTempZoneId.val(), addrTempDistrictId, function () {
            if (addrTempDistrictId.val() != null) {
                fetchAndPopulate(document.urlMunicipality, addrTempDistrictId.val(), addrTempVdcMunicipality);
            }
        });
    }

    addrTempZoneId.on('change', function () {
        fetchAndPopulate(document.urlDistrict, addrTempZoneId.val(), addrTempDistrictId, function () {
            if (addrTempDistrictId.val() != null) {
                fetchAndPopulate(document.urlMunicipality, addrTempDistrictId.val(), addrTempVdcMunicipality);
            }
        });
    });

    addrTempDistrictId.on('change', function () {
        fetchAndPopulate(document.urlMunicipality, addrTempDistrictId.val(), addrTempVdcMunicipality);
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


    addDatePicker(
        $("#employeeBirthDate"),
        $("#joinDate"),
        $("#idPassportExpiry"),
        $("#idCitizenshipIssueDate"),
        $("#idDrivingLicenseExpiry"),
        $("#famSpouseWeddingAnniversary"),
        $("#famSpouseBirthDate")
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
