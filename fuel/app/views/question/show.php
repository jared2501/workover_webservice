<script>
	window.QUESTION = <?php echo json_encode($question); ?>;
	window.SHOW_ANSWERS = <?php echo $show_answers; ?>;
</script>

<div ng-app>
	<div ng-controller="QuestionCtrl">
		<div>
			{{question.description}}
			<p>
				<span class="label label-info">Total Sections: {{question.sections.length}}</span>
				<span class="label label-success">Difficulty: {{question.difficulty}}</span>
			</p>
		</div>

		<div ng-repeat="section in question.unhiddenSections" id="section-{{section.id}}">
			<h4 ng-bind-html-unsafe="section.title"></h4>
			<div ng-bind-html-unsafe="section.body"></div>

			<div class="form-actions" ng-show="!section.correct">
				<button class="btn btn-primary" ng-click="section.submit()">Submit</button>
			</div>
		</div>
	</div>
</div>
