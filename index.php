<!-- From Gabriel Ferreira's CodePen (https://codepen.io/gabrielferreira/pen/jMgaLe)-->
<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <title>Drag and Drop Test</title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'><link rel="stylesheet" href="./styles/style.css">
</head>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<script src="./js/angular_scripts.js"></script>
<body>
    
    <h1>DRAG AND DROP</h1>
    <div class="adder">
        <input type="text" class="input" placeholder="Add items in your list"/>
        <span class="add">+</span>
    </div>
    <div ng-app="myApp" ng-controller="coursesCtrl">
        <ul ng-repeat="x in courses">
                <li class="draggable" draggable="true">{{x.course_title}}</li>
        </ul>
    </div>
</body>
<script src="./js/app.js"></script>
</html>

<!-- End From CodePen -->