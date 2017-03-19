angular.module('hris', [])
        .controller('assetIssuecontroller', function ($scope, $http) {
            
            $scope.astChange = function () {
                
                 window.app.pullDataById(document.restfulUrl, {
                action: 'pullMenuDetail',
                data: {id: $scope.employee}

            }).then(function (success) {
                console.log(success);
            }, function (error) {
                console.log(error);

            })
            };
            
            
            
            
            
            
//            $scope.rdClick = function (){
//            console.log($scope.rdChk);
//                if($scope.rdChk==true){
//                    $scope.rdChk.prop("disabled", false);
//                }
//                else{
//                    $scope.rdChk.prop("disabled", true);
//                }
//            };

           


        });