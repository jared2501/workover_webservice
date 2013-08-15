<?php $question = (isset($question)) ? json_encode($question) : '{title: "", description: "", difficulty: ""}'; ?>
<?php $sections = (isset($sections)) ? json_encode($sections) : '[{}]'; ?>
<?php $answers = (isset($answers)) ? json_encode($answers) : '[]'; ?>

<script>
	window.QUESTION = <?php echo $question; ?>;
	window.SECTIONS = <?php echo $sections; ?>;
	window.OPTIONS = <?php echo $answers; ?>;
</script>


<div ng-app="workover-webservice">
	<div ng-controller="QuestionCtrl">
		<form class="form-horizontal">
			<fielset>
				<legend>Question Body</legend>
				<div class="control-group ">
					<label for="title" class="control-label">Question Title</label>
					<div class="controls">
						<input ng-model="question.title" class="input-block-level required exclude-wysiwyg" required="required"/>
					</div>
				</div>
				<div class="control-group">
					<label for="description" class="control-label">Description</label>
					<div class="controls">
						<div id="bob" wysiwyg ng-model="question.description" class="input-block-level required exclude-wysiwyg" required="required"></div>
					</div>
				</div>
				<div class="control-group">
					<label for="difficulty" class="control-label">Difficulty</label>
					<div class="controls">
						<select ng-model="question.difficulty" class="exclude-wysiwyg">
							<option value="easy">Easy</option>
							<option value="medium">Medium</option>
							<option value="hard">Hard</option>
						</select>
					</div>
				</div>
			</fieldset>
			
			<fieldset style="padding: 15px;" class="sections-area">
				<legend style="margin: 0;">Sections</legend>
				
				<div ng-repeat="section in sections">

					<div style="padding: 10px;">
						<div class="clearfix">
							<h4 class="pull-left" quickedit="section.title">Section Title <small>(click to edit)</small></h4>

							<div class="btn-group pull-right">
								<a class="btn"
									ng-click="moveSectionUp(section)"
									ng-show="$index > 0">
										<i class="icon-chevron-up"></i>
								</a>

								<a class="btn"
									ng-click="moveSectionDown(section)"
									ng-show="$index < (sections.length-1)">
										<i class="icon-chevron-down"></i>
								</a>

								<a class="btn" ng-click="parseToInput()"><i class="icon-film"></i></a>
								<a class="btn" ng-click="parseToMarkup()"><i class="icon-edit"></i></a>

								<a class="btn" ng-click="removeSection(section)"><i class="icon-remove"></i></a>
							</div>
						</div>

						<div wysiwyg wysiwyg-parser ng-model="section.body" class="input-block-level exclude-wysiwyg">
							<p>Enter your section body here</p>
							<table class="table table-bordered">
								<tr><td>Cell 1</td><td>This is a table</td></tr>
								<tr><td>Cell 2</td><td>select[Option 1]</td></tr>
							</table>
							<p>Text can below as well</p>
						</div>
					</div>

				</div>

				<div class="form-actions">
					<a
						class="btn btn-primary"
						href=""
						ng-click="submit()"
						ng-class="{disabled: options.length < 1}"
						ng-disabled="options.length < 1">
							Submit Question
					</a>

					<a class="btn btn-success pull-right" href="" ng-click="addSection()">
						<i class="icon-plus icon-white"></i> Add Section
					</a>
				</div>
			</fieldset>
		</form>
	</div>
</div>
