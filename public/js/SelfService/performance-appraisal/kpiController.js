angular.module('kpiModule', ['use', 'ngMessages'])
        .controller("kpiController", function ($scope, $http) {
            $scope.KPIList = [];
            var employeeId = parseInt(angular.element(document.getElementById('employeeId')).val());
            var appraisalId = parseInt(angular.element(document.getElementById('appraisalId')).val());
            $scope.KPITemplate = {
                counter: 1,
                sno: 0,
                title: "",
                successCriteria: "",
                weight: "",
                checkbox: "checkboxq0",
                checked: false
            };
            $scope.counter = "";
            $scope.viewKPIList = function () {
                window.app.pullDataById(document.restfulUrl, {
                    action: 'pullAppraisalKPIList',
                    data: {
                        'employeeId': employeeId,
                        'appraisalId': appraisalId
                    }
                }).then(function (success) {
                    $scope.$apply(function () {
                        var appraisalKPIList = success.data;
                        var appraisalKPINum = (typeof success.data!=="undefined")?success.data.length:0;;
                        console.log(appraisalKPIList);
                        if (appraisalKPINum > 0) {
                            $scope.counter = appraisalKPINum;
                            for (var j = 0; j < appraisalKPINum; j++) {
                                $scope.KPIList.push(angular.copy({
                                    counter: (j + 1),
                                    sno: appraisalKPIList[j].SNO,
                                    title: appraisalKPIList[j].TITLE,
                                    successCriteria: appraisalKPIList[j].SUCCESS_CRITERIA,
                                    weight: parseInt(appraisalKPIList[j].WEIGHT),
                                    checkbox: "checkboxq" + j,
                                    checked: false
                                }));
                            }
                        } else {
                            $scope.counter = 1;
                            $scope.KPIList.push(angular.copy($scope.KPITemplate));
                        }
                    });
                }, function (failure) {
                    console.log(failure);
                });
            }
            if ((typeof employeeId == 'undefined' || employeeId !== 0) && (typeof appraisalId == 'undefined' || appraisalId !== 0)) {
                $scope.viewKPIList();
            } else {
                $scope.counter = 1;
                $scope.KPIList.push(angular.copy($scope.KPITemplate));
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
            $scope.addKPI = function () {
                console.log("hellow");
                $scope.KPIList.push(angular.copy({
                    counter: parseInt($scope.counter + 1),
                    sno: 0,
                    title: "",
                    successCriteria: "",
                    weight: "",
                    checkbox: "checkboxq" + $scope.counter,
                    checked: false
                }));
                $scope.counter++;
            }
            $scope.deleteKPI = function () {
                var tempId = 0;
                var length = $scope.KPIList.length;
                for (var i = 0; i < length; i++) {
                    if ($scope.KPIList[i - tempId].checked) {
                        var sno = $scope.KPIList[i - tempId].sno;
                        if (sno != 0) {
                            window.app.pullDataById(document.restfulUrl, {
                                action: "deleteAppraisalKPI",
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
                        $scope.KPIList.splice(i - tempId, 1);
                        tempId++;
                    }
                }
            }
            $scope.submitKPIForm = function () {
                console.log("form is going to be submitted");
                if ($scope.KPIForm.$valid) {
                    console.log("hellow");
                    App.blockUI({target: "#hris-page-content"});
                    window.app.pullDataById(document.restfulUrl, {
                        action: "submitAppraisalKPI",
                        data: {
                            KPIList: $scope.KPIList,
                            employeeId: employeeId,
                            appraisalId: appraisalId
                        },
                    }).then(function (success) {
                        $scope.$apply(function () {
                            console.log(success);
                            $('.nav-tabs a[href="#portlet_tab2_COM"]').tab('show');
//                            $scope.KPIList = [];
//                            $scope.viewKPIList();
                            App.unblockUI("#hris-page-content");
                        });
                    }, function (failure) {
                        App.unblockUI("#hris-page-content");
                        console.log(failure);
                    });
                }
            }
        });

angular.module("hris", ["kpiModule", "competenciesModule"]);