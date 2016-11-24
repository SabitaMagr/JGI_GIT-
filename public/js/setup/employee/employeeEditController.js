(function () {
    'use strict';
    angular.module("hris", ['ui.bootstrap'])
            .controller('qualificationController', function ($scope, $uibModal, $log, $document) {

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
                        console.log(success);
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
                            for (var j = 0; j < num; j++) {
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
                            for (var l = 0; l < $scope.qualificationFormList.length; l++) {
                                var rankValue = $scope.qualificationFormList[l].rankValue;
                                var passedYr = $scope.qualificationFormList[l].passedYr;

                                console.log(rankValue, passedYr);
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

                $scope.upload = function () {

                };

                //for document including both image as well as file upload and remove
                $scope.profilePictureId = document.profilePictureId;
                $scope.fileTypes = document.fileTypes;
                $scope.file = {
                    fileCode: null,
                    fileTypeCode: '',
                    filePath: '',
                    editMode: false
                };
                $scope.dropZone;
                Dropzone.options.myAwesomeDropzone = {
                    maxFiles: 1,
                    acceptedFiles: 'image/*',
                    init: function () {
                        $scope.$apply(function () {
                            $scope.dropZone = this;
                            console.log($scope.dropZone);
                        }.bind(this));

                        this.on("success", function (file, success) {

                            $scope.$apply(function () {
                                if (success.success) {
                                    $scope.file.filePath = success.data.fileName;
                                }
                            });
                        });
                    }
                };
                console.log($scope.profilePictureId);
                if ($scope.profilePictureId != -1) {
                    window.app.pullDataById(document.urlQualificationDtl, {
                        action: 'pullEmployeeFile',
                        data: {
                            'employeeFileId': $scope.profilePictureId
                        }
                    }).then(function (success) {
                        $scope.$apply(function () {
                            console.log(success.data);
                            if (success.data != null) {
                                $scope.file.fileCode = success.data['FILE_CODE'];
                                $scope.file.fileTypeCode = success.data['FILETYPE_CODE'];
                                $scope.file.filePath = success.data['FILE_PATH'];
                            }
                        });

                    }, function (failure) {
                        console.log(failure);
                    });
                }

                $scope.viewImage = function (image) {
                    return document.basePath + "/uploads/" + image;
                };

                $scope.post = function () {

                    window.app.pullDataById(document.urlQualificationDtl, {
                        action: 'pushEmployeeFile',
                        data: {
                            'fileCode': $scope.file.fileCode,
                            'fileTypeCode': $scope.file.fileTypeCode,
                            'filePath': $scope.file.filePath,
                            'employeeId': document.employeeId
                        }
                    }).then(function (success) {
                        $scope.$apply(function () {
                            console.log(success.data);
                            if (success.data != null) {
                                $scope.file.editMode = false;
                                $scope.file.fileCode = success.data.fileCode
                            }
                        });

                    }, function (failure) {
                        console.log(failure);
                    });
                };
                $scope.edit = function () {
                    $scope.file.editMode = true;
                    $scope.dropZone.removeAllFiles();
                };


                $scope.employeeDocuments = [];
                $scope.drop = function (fileCode, key) {
                    window.app.pullDataById(document.urlQualificationDtl, {
                        action: 'dropEmployeeFile',
                        data: {
                            'fileCode': fileCode
                        }
                    }).then(function (success) {
                        $scope.$apply(function () {
                            console.log(success);
                            $scope.employeeDocuments.splice(key, 1);
                        });

                    }, function (failure) {
                        console.log(failure);
                    });
                };

                $scope.addDocument = function () {
                    var modalInstance = $uibModal.open({
                        ariaLabelledBy: 'modal-title',
                        ariaDescribedBy: 'modal-body',
                        templateUrl: 'myModalContent.html',
                        resolve: {
                            fileTypes: function () {
                                return $scope.fileTypes;
                            }
                        },
                        controller: function ($scope, $uibModalInstance, fileTypes) {
                            $scope.ok = function () {
                                $uibModalInstance.close();
                            };

                            $scope.cancel = function () {
                                $uibModalInstance.dismiss('cancel');
                            };

                            $scope.fileTypes = fileTypes;
                            $scope.fileTypeCode = null;
                        }
                    });

                    modalInstance.rendered.then(function () {
                        console.log("sdf");
                        var myDropzone = new Dropzone("#dropZoneContainer", {url: document.restfulUrl});
                        my.on("success", function (file, success) {

                            $scope.$apply(function () {
                                    console.log(success);
                            });
                        });
                    });
                };

                window.app.pullDataById(document.urlQualificationDtl, {
                    action: 'pullEmployeeFileByEmpId',
                    data: {
                        'employeeId': document.employeeId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        $scope.employeeDocuments = success.data;
                        console.log($scope.employeeDocuments);
                    });

                }, function (failure) {
                    console.log(failure);
                });



            });
})();

