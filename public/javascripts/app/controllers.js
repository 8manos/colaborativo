/* Controllers */
var controllers = angular.module('colaborativo.controllers', []);

controllers.controller('IndexController', function ($scope, socket) {
    $scope.posts = [];
    socket.on('tweet', function (data) {
      $scope.posts.unshift(data);
    });
});

controllers.controller('AppCtrl', function ($scope, socket) {
  $scope.title = "conectando...";
  socket.on('tweet', function (data) {
    $scope.title = data.id;
  });
});