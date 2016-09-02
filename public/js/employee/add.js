/**
 * Created by ukesh on 8/29/16.
 */

function pullDataById(url, id) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: url,
            data: {id: id},
            type: 'POST',
            error: function (error) {
                reject(error);
            },
            success: function (data) {
                resolve(data);
            }

        });
    });
}

function populateSelectElement(element, data) {
    element.html('');
    for (key in data) {
        element.append($('<option>', {value: key, text: data[key]}));
    }
    var keys = Object.keys(data);
    if (keys.length > 0) {
        element.select2('val', keys[0]);
    }
}

function fetchAndPopulate(url, id, element, callback) {
    pullDataById(url, id).then(function (data) {
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


    // $('#finishBtn').on('click', function () {
    //     $('#submit').click();
    // });
    if(typeof document.currentTab!=="undefined"){
        // $('[href="#tab'+document.currentTab+'"]').click();
        $('#rootwizard').bootstrapWizard('show',parseInt(document.currentTab)-1);
    }
    // $('#formEmployee').validate({rules: {'form-employeeCode': 'required'}, messages: {'form-employeeCode': "ee"}});


    var format="d-M-yyyy";
    $("#employeeBirthDate").datepicker({
        format: format,
        autoclose:true
    });
    $("#famSpouseBirthDate").datepicker({
        format: format,
        autoclose:true
    });
    $("#famSpouseWeddingAnniversary").datepicker({
        format: format,
        autoclose:true
    });
    $("#idDrivingLicenseExpiry").datepicker({
        format: format,
        autoclose:true
    });
    $("#idCitizenshipIssueDate").datepicker({
        format: format,
        autoclose:true
    });
    $("#idPassportExpiry").datepicker({
        format: format,
        autoclose:true
    });
    $("#joinDate").datepicker({
        format: format,
        autoclose:true
    });
});
