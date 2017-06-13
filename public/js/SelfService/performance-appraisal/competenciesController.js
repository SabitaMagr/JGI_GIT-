angular.module('competenciesModule', ['use', 'ngMessages'])
        .controller("competenciesController", function ($scope, $http) {
            $scope.competenciesList = [];
            var employeeId = parseInt(angular.element(document.getElementById('employeeId')).val());
            var appraisalId = parseInt(angular.element(document.getElementById('appraisalId')).val());
            $scope.competenciesTemplate = {
                counter: 1,
                sno: 0,
                title: "",
                checkbox: "checkboxc0",
                checked: false
            };
            $scope.counter = "";
            $scope.viewCompetenciesList = function () {
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAppraisalCompetenciesList',
                    data: {
                        'employeeId': employeeId,
                        'appraisalId': appraisalId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        var appraisalCompetenciesList = success.data;
                        var appraisalCompetenciesNum =(typeof success.data!=="undefined")?success.data.length:0;
                        console.log(appraisalCompetenciesList);
                        if (appraisalCompetenciesNum > 0) {
                            $scope.counter = appraisalCompetenciesNum;
                            for (var j = 0; j < appraisalCompetenciesNum; j++) {
                                $scope.competenciesList.push(angular.copy({
                                    counter: (j + 1),
                                    sno: appraisalCompetenciesList[j].SNO,
                                    title: appraisalCompetenciesList[j].TITLE,
                                    checkbox: "checkboxc" + j,
                                    checked: false
                                }));
                            }
                        } else {
                            $scope.counter = 1;
                            $scope.competenciesList.push(angular.copy($scope.competenciesTemplate));
                        }
                    });
                }, function (failure) {
                    console.log(failure);
                });
            }
            if ((typeof employeeId == 'undefined' || employeeId !== 0) && (typeof appraisalId == 'undefined' || appraisalId !== 0)) {
                $scope.viewCompetenciesList();
            } else {
                $scope.counter = 1;
                $scope.competenciesList.push(angular.copy($scope.competenciesTemplate));
            }
            $scope.sumAllTotal = function (list) {
                var total = 0;
                angular.forEach(list, function (item) {
                    var total1 = parseInt(item.weight);
                    total += parseInt(total1);
                });
                if (total > 100) {
                    $scope.sumTotal = true;
                } else {
                    $scope.sumTotal = false;
                }
            }
            $scope.addCompetencies = function () {
                console.log("hellow");
                $scope.competenciesList.push(angular.copy({
                    counter: parseInt($scope.counter + 1),
                    sno: 0,
                    title: "",
                    checkbox: "checkboxc" + $scope.counter,
                    checked: false
                }));
                $scope.counter++;
            }
            $scope.deleteCompetencies = function () {
                var tempId = 0;
                var length = $scope.competenciesList.length;
                for (var i = 0; i < length; i++) {
                    if ($scope.competenciesList[i - tempId].checked) {
                        var sno = $scope.competenciesList[i - tempId].sno;
                        if (sno != 0) {
                            window.app.pullDataById(document.restfulUrl, {
                                action: "deleteAppraisalCompetencies",
                                data: {
                                    "sno": sno
                                }
                            }).then(function (success) {
                                $scope.$apply(function () {
                                    console.log(success.data);
                                });
                            }, function (failure) {
                                console.log(failure);
                            });
                        }
                        $scope.competenciesList.splice(i - tempId, 1);
                        tempId++;
                    }
                }
            }
            $scope.submitCompetenciesForm = function () {
                console.log("form is going to be submitted");
                if ($scope.competenciesForm.$valid) {
                    console.log("hellow");
                    console.log($scope.competenciesList);
                    App.blockUI({target: "#hris-page-content"});
                    window.app.pullDataById(document.restfulUrl, {
                        action: "submitAppraisalCompetencies",
                        data: {
                            competenciesList: $scope.competenciesList,
                            employeeId: employeeId,
                            appraisalId: appraisalId
                        },
                    }).then(function (success) {
                        $scope.$apply(function () {
                            console.log(success);
                            $('.nav-tabs a[href="#portlet_tab2_1"]').tab('show');
//                            $scope.competenciesList = [];
//                            $scope.viewCompetenciesList();
                            App.unblockUI("#hris-page-content");
                        });
                    }, function (failure) {
                        App.unblockUI("#hris-page-content");
                        console.log(failure);
                    });
                }
            }
        });
        
//        angular.module("hris", ["competenciesModule"]);