
<!DOCTYPE html>
<html>
<script src= "http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<body>

<div ng-app="myApp" ng-controller="customersCtrl"> 

<table>
  <tr ng-repeat="x in names">
    <td>{{ x.name }}</td>
    <td>{{ x.sku }}</td>
  </tr>
</table>

</div>

<script>
var app = angular.module('myApp', []);
app.controller('customersCtrl', function($scope, $http) {
    $http.get("http://store.vineos.io/productoptions/?parentsku=wsd001")
    .success(function (response) {$scope.names = response.productoptions;});
});
</script>

</body>
</html>
