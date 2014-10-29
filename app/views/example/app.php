<!doctype html>
<html lang="en" ng-app="humanApp">
<head>
    <meta charset="utf-8">
    <title>Angular App</title>
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.css">
    <script src="/assets/vendor/angular/angular.js"></script>
    <script src="/assets/vendor/angular/angular-route.js"></script>
    <script src="/assets/vendor/angular/angular-resource.js"></script>
    <script src="/assets/example-app/controllers.js"></script>
    <style>
        .grid-actions {
            text-align: right;
            min-width: 80px;
        }
        .grid-actions a {
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="container" ng-view></div>

</body>
</html>