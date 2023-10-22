<?php header('Content-Type: application/javascript');?>
'use strict';

// the storeController contains two objects:
// - store: contains the product list
// - cart: the shopping cart object
// - detailsprod: contains the details product
storeApp.controller('storeController', function ($scope, $routeParams, DataService,$http) {

    // get store and cart from service
    $scope.detailsprod = DataService.detailsprod;
    $scope.store = DataService.store;
    $scope.cart = DataService.cart;     
 
	
	 
    if ($routeParams.productCode != null) {
        $scope.product = $scope.store.getProduct($routeParams.productCode);
        
        $http.get('https://<?php echo $_SERVER['SERVER_NAME'];?>/demo/productoptions/?parentsku='+$routeParams.productCode).
    		success(function(data, status, headers, config) {
      		$scope.detail = data.productoptions;
      		 
      		
    }).
    error(function(data, status, headers, config) {
      // log error
    });
        
        
        
      //$scope.detail = $scope.detailsprod.getDetail($routeParams.productCode,$http,$scope);
    }
});
