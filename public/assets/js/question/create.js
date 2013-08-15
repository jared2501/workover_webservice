var app = angular.module('workover-webservice', []);

app.controller('QuestionCtrl', function($scope, $timeout, $http) {
	$scope.question = {
		id: window.QUESTION.id,
		title: window.QUESTION.title,
		description: window.QUESTION.description,
		difficulty: window.QUESTION.difficulty,
	};
	$scope.sections = window.SECTIONS;
	$scope.options = window.OPTIONS;

	function swap(array_object, index_a, index_b) {
		var temp = array_object[index_a];
		array_object[index_a] = array_object[index_b];
		array_object[index_b] = temp;
	}

	$scope.addSection = function() {
		$scope.sections.push({title: ''});
	};

	$scope.removeSection = function(section) {
		if(confirm('Are you sure you want to delete this section?')) {
			var index = $scope.sections.indexOf(section);
			$scope.sections.splice(index, 1);
		}
	};

	// By up, we mean down in index!
	$scope.moveSectionUp = function(section) {
		var index = $scope.sections.indexOf(section);

		if(index > 0) {
			swap($scope.sections, index, index - 1);
		}
	};

	$scope.moveSectionDown = function(section) {
		var index = $scope.sections.indexOf(section);
		var length = $scope.sections.length;

		if(index < (length - 1)) {
			swap($scope.sections, index, index + 1);
		}
	};

	$scope.parseToMarkup = function() {
		$scope.$broadcast('wysiwyg:parse-to-markup', $scope.options);
	};

	$scope.parseToInput = function() {
		$scope.$broadcast('wysiwyg:parse-to-input', $scope.options);
	}

	$scope.submit = function() {
		if($scope.options.length > 0) {
			$scope.$broadcast('wysiwyg:pre-submit', $scope.options);
			
			$http.post('', {
				'question': $scope.question,
				'sections': $scope.sections,
				'answers': $scope.options
			}).then(function(response){
				console.log(response.data);
			});
		}
	}
});