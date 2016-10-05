/**
 * Created by punam on 10/4/16.
 */
angular.module('hris',[])
    .controller('recommedApproverController',function($scope,$http){

        $scope.change = function(){
            var employeeId =  angular.element(document.getElementById("employeeId")).val();

            window.app.pullDataById(document.url, {
                action: 'pullRecommendApproveList',
                employeeId: employeeId
            }).then(function (success) {
                $scope.$apply(function () {

                    $scope.recommenderOptions=success.recommender;
                    $scope.recommenderSelected=$scope.recommenderOptions[0];

                    $scope.approverOptions=success.approver;
                    $scope.approverSelected=$scope.approverOptions[0];

                });
            }, function (failure) {
                console.log(failure);
            });
        };;
    });
