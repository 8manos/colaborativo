var directives = angular.module('colaborativo.directives', []);
directives.directive('hello', function () {
    return {
        restrict: 'E',
        template: '<p>Hello from directive</p>'
    };
});