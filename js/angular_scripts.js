var app = angular.module('myApp', []);
app.controller('coursesCtrl', function($scope, $http) {
   $http.get("./php-scripts/getCS-courses.php")
   .then(function (response) {
    $scope.courses = response.data;
  });
});