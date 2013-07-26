//init app module and declare it's dependencies on other modules
var app = angular.module('colaborativo', ['colaborativo.controllers']);
app.config(['$routeProvider', function ($routeProvider) {
    $routeProvider.when('/', {
        templateUrl: 'partials/index',
        controller: 'IndexController'
    }).otherwise({
        redirectTo: '/'
    });
}]).config(['$locationProvider', function($locationProvider) {
    $locationProvider.html5Mode(true);
}]);