mainApp.config(
    ['$routeProvider', '$locationProvider', '$stateProvider', '$urlRouterProvider', '$resourceProvider',
        function ($routeProvider, $locationProvider, $stateProvider, $urlRouterProvider, $resourceProvider) {

            $resourceProvider.defaults.stripTrailingSlashes = false;

            $urlRouterProvider.otherwise('/learner/models/');

            $stateProvider.state('all-attempt-list', {
                url: '/learner/models/',
                templateUrl: BASE_CONFIG.urls.partials.learner + '/partial-attempt-list.html'
            });

            $stateProvider.state('attempt-list', {
                url: '/learner/model/:modelId',
                templateUrl: BASE_CONFIG.urls.partials.learner + '/partial-attempt-list.html'
            });

            $stateProvider.state('attempt', {
                url: '/learner/attempt/:attemptId',
                templateUrl: BASE_CONFIG.urls.partials.learner + '/partial-attempt.html'
            });

            $stateProvider.state('attempt.pair-items', {
                url: '/pair-items/:itemId',
                templateUrl: BASE_CONFIG.urls.partials.learner + '/partial-pair-items.html'
            });

            $stateProvider.state('attempt.group-items', {
                url: '/group-items/:itemId',
                templateUrl: BASE_CONFIG.urls.partials.learner + '/partial-group-items.html'
            });

            $stateProvider.state('attempt.multiple-choice', {
                url: '/multiple-choice/:itemId',
                templateUrl: BASE_CONFIG.urls.partials.learner + '/partial-multiple-choice.html'
            });
        }
    ]
);
