<?php header('Content-Type: application/javascript');?>
<?php
include("../../main_oauth.php");
?>
'use strict';

// App Module: the name YourStore matches the ng-app attribute in the main <html> tag
// the route provides parses the URL and injects the appropriate partial page

var storeApp = angular.module('YourStore', ['ngRoute', 'ngAnimate','ui.bootstrap']).
  config(['$routeProvider', function($routeProvider) {
  $routeProvider.
      when('/store', {
        templateUrl: 'partials/store',
        controller: 'storeController'
      }).
      when('/products/:productCode', {
        templateUrl: 'partials/product',
        controller: 'storeController'
      }).
      when('/cart', {
        templateUrl: 'partials/shoppingCart',
        controller: 'storeController'
    }).
     when('/customerService', {
        templateUrl: 'partials/customerService',
        controller: 'storeController'
    }).
      otherwise({
        redirectTo: '/store'
    });

}]);
//We already have a limitTo filter built-in to angular,
//let's make a startFrom filter
storeApp.filter('startFrom', function () {
    return function (input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});


angular.module('YourStore').controller('TabsCustomerService', function ($scope, $window) {
  $scope.tabs = [
    { title:'Dynamic Title 1', content:'Dynamic content 1' },
    { title:'Dynamic Title 2', content:'Dynamic content 2', disabled: true }
  ];

  $scope.alertMe = function() {
    setTimeout(function() {
      $window.alert('You\'ve selected the alert tab!');
    });
  };
});

// create a data service that provides a store and a shopping cart that
// will be shared by all views (instead of creating fresh ones for each view).
storeApp.factory("DataService", function () {

    // create store
    var myStore = new store();
    var storeDetails = new detailsprod();
    // create shopping cart
    var myCart = new shoppingCart("YourStore");

    // enable PayPal checkout
    // note: the second parameter identifies the merchant; in order to use the 
    // shopping cart with PayPal, you have to create a merchant account with 
    // PayPal. You can do that here:
    // https://www.paypal.com/webapps/mpp/merchant
    myCart.addCheckoutParameters("PayPal", "<?php echo _m('paypal_email');?>");

    // return data object with store and cart
    return {
        store: myStore,
        cart: myCart,
        detailsprod: storeDetails
    };
});