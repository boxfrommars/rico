var humanServices = angular.module('humanServices', ['ngResource']);

humanServices.factory('Human', ['$resource', function($resource){
    return $resource('api/humans/:id', { id: '@id' }, {
        query: {method: 'GET', isArray:true},
        update: {method: 'PUT'}
    });
}]);

var humanApp = angular.module('humanApp', ['ngRoute', 'ngResource', 'humanServices']);

humanApp
    .controller('HumanIndexController', function ($scope, $http, Human) {
        $scope.humans = Human.query();

        $scope.remove = function (human) {
            human.$delete(function(){
                $scope.humans.splice($scope.humans.indexOf($scope.human), 1);
                alert('deleted');
            });
        }
    })
    .controller('HumanViewController', function ($scope, $http, $routeParams, Human) {
        $scope.human = Human.get({id: $routeParams.id});

        $scope.save = function() {
            $scope.human.$update(function () { alert('saved!'); });
        }
    })
    .controller('HumanCreateController', function ($scope, $http, $routeParams, Human) {
        $scope.human = new Human();

        $scope.save = function() {
            $scope.human.$save(function () { alert('saved!'); });
        }
    });


humanApp.config(['$routeProvider', function ($routeProvider) {
    $routeProvider.
        when('/humans', {
            controller: 'HumanIndexController',
            templateUrl: 'assets/example-app/templates/human.index.html'
        }).
        when('/humans/create', {
            controller: 'HumanCreateController',
            templateUrl: 'assets/example-app/templates/human.view.html'
        }).
        when('/humans/:id', {
            controller: 'HumanViewController',
            templateUrl: 'assets/example-app/templates/human.view.html'
        }).
        otherwise({
            redirectTo: '/humans'
        });
}
]).run();
