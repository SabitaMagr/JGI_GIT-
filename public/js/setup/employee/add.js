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

                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });


})(window.jQuery, window.app);


angular.module("hris", [])
    .controller('qualificationController', function ($scope, $http) {

        //for qualification detail [add and delete]
        $scope.degreeList = [];
        $scope.universityList = [];
        $scope.programList = [];
        $scope.courseList = [];
        $scope.employeeQualificationList = [];
        $scope.rankType = [
            {"id": "GPA", "name": "GPA"},
            {"id": "PER", "name": "Percentage"},
        ];
        var employeeId = angular.element(document.getElementById('employeeId')).val();

        $scope.qualificationFormList = [];

        window.app.pullDataById(document.urlQualificationDtl, {
            action: 'pullAcademicDetail',
            data: {
                'employeeId': employeeId
            }
        }).then(function (success) {
            $scope.$apply(function () {
                var data = success.data;

                $scope.counter = '';
                $scope.degreeList = data.degreeList;
                $scope.universityList = data.universityList;
                $scope.programList = data.programList;
                $scope.courseList = data.courseList;
                $scope.employeeQualificationList = data.employeeQualificationList;
                var num = data.employeeQualificationList.length;

                if (num == 0) {
                    $scope.counter = 1;
                    $scope.qualificationFormList.push({
                        id: 0,
                        academicDegreeId: $scope.degreeList[0],
                        academicUniversityId: $scope.universityList[0],
                        academicProgramId: $scope.programList[0],
                        academicCourseId: $scope.courseList[0],
                        rankType: $scope.rankType[0],
                        rankValue: 0,
                        passedYr: "",
                        checkbox: "checkboxq0",
                        checked: false
                    });
                } else {
                    $scope.counter = num;
                    for(var j=0; j<num; j++){
                        if (data.employeeQualificationList[j].rankType == 'GPA') {
                            var rankType = $scope.rankType[0];
                        } else if (data.employeeQualificationList[j].rankType == 'PER') {
                            var rankType = $scope.rankType[1];
                        }

                        $scope.qualificationFormList.push({
                            id: data.employeeQualificationList[j].id,
                            academicDegreeId: data.employeeQualificationList[j].degreeDtl,
                            academicUniversityId: data.employeeQualificationList[j].universityDtl,
                            academicProgramId: data.employeeQualificationList[j].programDtl,
                            academicCourseId: data.employeeQualificationList[j].courseDtl,
                            rankType: rankType,
                            rankValue: data.employeeQualificationList[j].rankValue,
                            passedYr: data.employeeQualificationList[j].passedYr,
                            checkbox: "checkboxq" + j,
                            checked: false
                        });
                    }
                }
                $scope.view = function () {
                    $scope.qualificationFormList.push({
                        id: 0,
                        academicDegreeId: $scope.degreeList[0],
                        academicUniversityId: $scope.universityList[0],
                        academicProgramId: $scope.programList[0],
                        academicCourseId: $scope.courseList[0],
                        rankType: $scope.rankType[0],
                        rankValue: 0,
                        passedYr: "",
                        checkbox: "checkboxq" + $scope.counter,
                        checked: false
                    });
                    $scope.counter++;
                }

                $scope.addQualification = function () {
                    var qualificationRecord = $scope.qualificationFormList;
                    console.log(qualificationRecord);
                    for(var l=0; l<$scope.qualificationFormList.length; l++){
                        var rankValue = $scope.qualificationFormList[l].rankValue;
                        var passedYr = $scope.qualificationFormList[l].passedYr;

                        console.log(rankValue,passedYr);
                    }

                    window.app.pullDataById(document.urlQualificationDtl, {
                        action: 'submitQualificationDtl',
                        data: {
                            "qualificationRecord": qualificationRecord,
                            'employeeId': employeeId
                        }
                    }).then(function (success) {
                        $scope.$apply(function () {
                            console.log(success.data);
                        });

                    }, function (failure) {
                        console.log(failure);
                    });
                }

                $scope.delete = function () {
                    var tempC = 0;
                    var length = $scope.qualificationFormList.length;
                    for (var i = 0; i < length; i++) {
                        if ($scope.qualificationFormList[i - tempC].checked) {
                            var id = $scope.qualificationFormList[i - tempC].id;
                            if (id != 0) {
                                window.app.pullDataById(document.urlQualificationDtl, {
                                    action: 'deleteQualificationDtl',
                                    data: {
                                        "id": id
                                    }
                                }).then(function (success) {
                                    $scope.$apply(function () {
                                        console.log(success.data);
                                    });

                                }, function (failure) {
                                    console.log(failure);
                                });
                            }
                            $scope.qualificationFormList.splice(i - tempC, 1);
                            tempC++;
                        }
                    }
                }
            });
        }, function (failure) {
            console.log(failure);
        });

        //for document including both image as well as file upload and remove
        $scope.fileTypes=[];
        $scope.employeeFileUploadFormList = [];

        window.app.pullDataById(document.urlQualificationDtl,{
            action: 'pullFileTypeList'
        }).then(function(success){
            $scope.$apply(function(){
               //console.log(success.data);
                $scope.fileTypes=success.data;

                $scope.employeeFileUploadFormList.push({
                    fileType:$scope.fileTypes[0],
                    filePath:[],
                    remarks:""
                });

                $scope.employeeFileRow = function(){
                    $scope.employeeFileUploadFormList.push({
                        fileType:$scope.fileTypes[0],
                        filePath:[],
                        remarks:""
                    });
                }
                $scope.addDocumentList = function(){
                    var record = $scope.employeeFileUploadFormList;
                    console.log(record);

                }

                $scope.deleteEmployeeFile = function(){
                    console.log("hellow this is employee File remove");
                }

            });
        },function(failure){
           console.log(failure);
        });

    });
