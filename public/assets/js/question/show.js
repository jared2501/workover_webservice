function QuestionCtrl($scope, $timeout, $http) {
	$scope.question = window.QUESTION;

	if(window.SHOW_ANSWERS) {
		
		$scope.question.unhiddenSections = $scope.question.sections;

		$timeout(function(){
			angular.forEach($scope.question.sections, function(section){
				section.correct = true;

				angular.forEach(section.answers, function(value, key){
					var input = $('#section-'+section.id+' input, select, label, textarea').filter('[name="'+key+'"]');
					
					if($(input).is('input:checkbox')) {
						$(input).prop('checked', value);
					} else {
						$(input).val(value);
					}
				});
			});

			var inputs = $('input, select, label, textarea');
			inputs.attr("disabled", "disabled");
		});
		
	} else {
		
		angular.forEach($scope.question.sections, function(value){
			value.submit = function() {
				// Send submission


				// Check answers
				var allCorrect = true;
				var inputs = $('#section-'+this.id+' input, select, label, textarea');

				angular.forEach(inputs, function(input){
					var inputName = $(input).prop('name');
					var inputVal = $(input).val();
					var found = false;
					var correct = false;
					
					angular.forEach(this.answers, function(val, name){
						if(inputName == name) {
							found = true;
							if(val == inputVal) {
								correct = true;
								return;
							}
						}
					});

					if(found == false || correct == true) {
						allCorrect = true;
						return;
					} else {
						allCorrect = false;
						return;
					}
				}, this);

				// Take actions
				if(allCorrect) {
					this.correct = true;

					// Really bad... but time deadline
					var inputs = $('input, select, label, textarea');
					inputs.attr("disabled", "disabled");

					if($scope.question.sections.length > 0) {
						// 1. Move onto next section
						$scope.question.unhiddenSections.push($scope.question.sections.pop());
					} else {
						// 2. Completed all sections
						alert('done!');
					}
				} else {
					// 3. Contains errors, try again
					alert('Your submission contains errors, please try again!');
				}
			}
		});

		if($scope.question.sections && $scope.question.sections.length > 0) {
			$scope.question.unhiddenSections = [$scope.question.sections.pop()];
		}

	}
}