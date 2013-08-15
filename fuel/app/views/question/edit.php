<?php echo Form::open(array( 'id' => 'question-form', 'class' => 'form-horizontal', 'name' => 'question')) ?>
	<input type="hidden" name="question.id" value="<?php echo $question->id; ?>"/>
	<?php echo Form::fieldset_open(array(), 'Question Properties') ?>			
		<div class="control-group ">
			<label for="question.title" class="control-label">Question Title</label>
			<div class="controls">
				<input type="text" id="title" name="question.title" class="span12" class="input-xxlarge required" required="required" value="<?php echo $question->title; ?>"/>
			</div>
		</div>
		<div class="control-group">
			<label for="question.description" class="control-label">Description</label>
			<div class="controls">
				<textarea type="text" id="description" name="question.description" class="span12" class="input-xxlarge" style="height: 150px;"><?php echo $question->description; ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label for="question.difficulty" class="control-label">Difficulty</label>
			<div class="controls">
				<?php echo Form::select('question.difficulty', $question->difficulty, array('easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard')); ?>
			</div>
		</div>
	<?php echo Form::fieldset_close() ?>
	
	<?php echo Form::fieldset_open(array(), 'Section Properties') ?>
		
		<div class="section-edit-container" data-section-count="<?php echo count($question->sections); ?>">
			<?php foreach($question->sections as $section): ?>
				<div class="section" data-section-id="<?php echo $section->id; ?>">
					<div class="section-edit-handle"></div>
					<h4 class="aloha-editable section-title"><?php echo $section->title; ?></h4>
					<div class="aloha-editable section-body"><?php echo $section->body; ?></div>
					<div class="section-edit-actions btn-group btn-group-vertical"><button class="btn section-edit-edittable"><i class="icon-edit"></i></button> <button class="btn section-edit-show"><i class="icon-film"></i></button></div>
				</div>
			<?php endforeach; ?>
		</div>
		
	<?php echo Form::fieldset_close() ?>
	
	<div class="form-actions">
		<?php echo Form::submit('Submit', null, array('class' => 'btn btn-primary', 'id' => 'question-form-submit')) ?>
	</div>
<?php echo Form::close() ?>


<script>
	var inputs = 'input, select, label, textarea';
	
	(function( $ ){
		var inputs = 'input, select, label, textarea';
		var syntax = {
			"open" : "[",
			"close" : "]",
			"option_split" : ","
		}
		
		var methods = {
			tostring: function() {
				return this.each(function() {
					if($(this).is(inputs)) {
						if($(this).is('select')) {
							var type = 'select';
							
							var options = new Array();
							$(this).children('option:not(:first)').each(function(){
								options.push($(this).text());
							});
							
							$(this).replaceWith(type+syntax.open+options.join(',')+syntax.close);
						}
					}
				});
			},
			
			toinput: function() {
				return this.each(function() {					
					var html = $(this).html();
					var start;
					for (start = html.indexOf('select['); start > 0; start = html.indexOf('select[')) {
						var length = 0;
						var temp = html.split('');
						for(var i = start; i < temp.length; i++) {
							if(temp[i] == ']' && temp[i-1] != '\\') {
								length = i + 1 - start;
								break;
							}
						}
						
						if(length == 0)
							break;

						var tempOptionsString = '';
						var tempOptionsArr = html.substr(start+7, length-7-1).split(',')
						for(var i = 0; i < tempOptionsArr.length; i++) {
							tempOptionsString = tempOptionsString + '<option value="' + (i+1) + '">' + tempOptionsArr[i] + '</option>';
						}

						var spliceStr = '<select><option value="0">(Select an Option)</option>' + tempOptionsString + '</select>';
						
						var temp = html.split('');
						temp.splice(start, length, spliceStr);
						html = temp.join('');
					}
					
					$(this).html(html);
				});
			}
		}
		
		$.fn.simpleParse = function( method ) {
			// Method calling logic
			if ( methods[method] ) {
				return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
			} else if ( typeof method === 'object' || ! method ) {
				return methods.init.apply( this, arguments );
			} else {
				$.error( 'Method ' +  method + ' does not exist on jQuery.simpleParse' );
			}    
		};
	})( jQuery );
	
	
	// For form errors
	$('.help-inline').closest('.control-group').addClass('error');
	
	// For form submit
	$('#question-form').submit(function(){
		if($('.section-edit-container').data('section-count') < 1) {
			alert('Please add at least one section to this question');
			return false;
		}
		
		var sections = {};
		var notFilledOutAnswers = false
		
		$('.section-edit-container').children('.section').each(function(index, value){
			var $secitionBody = $(this).children('.section-body');
			var answers = [];
			var i = 0;
			$secitionBody.find('input, select, label, textarea').each(function(){
				// If we have an option on (Please select an option) or an input is blank
				if($(this).val() < 1 || $(this).val() == '') {
					alert('Please fill out the correct answers for the sections');
					notFilledOutAnswers = true;
					return false;
				}
				answers.push({
					key: i,
					value: $(this).val()
				});
				
				$(this).attr('name', 'answer[' + i + ']');
				i++;
			});
	
			if(notFilledOutAnswers) {
				return false;
			}
				
			sections[index] = {
				'id': $(this).data('sectionId'),
				'title': $(this).children('.section-title').html(),
				'body': $secitionBody.html(),
				'answers': answers
			};
		});
		
		if(notFilledOutAnswers) {
			return false;
		}
		
		var form = $(this).toDeepJson();
		form['question']['sections'] = sections;
		
		
		$.ajax({
			data: form,
			type: 'POST',
			success: function(response) {
				console.log(response);
				if(response.result == true) {
					wrapperNavigator.success(); // Will always exist and should be called when user is done
				} else {
					$('#question-form-submit').val('Submit').attr("disabled", false);
					alert('Sorry, there was an error!');
				}
			}
		});
		
		
		$('#question-form-submit').val('Submitting...').attr("disabled", "disabled");
		return false;
	});
	
	
	// Make the whole sections sortable
	$('.section-edit-container').sortable({
		handle: ".section-edit-handle",
		connectWith : ".section-edit-container"
	});
	$('.section-edit-delete').live('click', function(){
		$('.section-edit-container').data('section-count', $('.section-edit-container').data('section-count')-1);
		$(this).closest('.section').remove();
	});
	$('.section-edit-edittable').live('click', function(){
		$(this).closest('.section').find('.section-body').find(inputs).simpleParse('tostring');
		return false;
	});
	$('.section-edit-show').live('click', function(){
		$(this).closest('.section').find('.section-body').simpleParse('toinput');
		return false;
	});
	
	
	
	
	// Get the add section button working
	var toaddd = '<div class="section"><div class="section-edit-handle"></div><h4 class="aloha-editable">New Section</h4><table><tr><td><div class="aloha-editable">zxzxc</div></td></tr><tr><td>asfasd</td></tr><tr><td>dsgfsd</td></tr></table><div class="section-edit-actions"><a class="btn btn-small section-edit-delete"><i class="icon-remove-sign"></i> Delete</a></div></div>';
	function add_section() {
		$('.section-edit-container').data('section-count', $('.section-edit-container').data('section-count')+1);
		var rows = parseInt($('#section-edit-addrows').val());
		var cols = parseInt($('#section-edit-addcols').val());
		
		if(rows < 1 || isNaN(rows) || isNaN(cols) || cols < 1) {
			alert('Invalid rows or columns! Please enter an integer greater than 0');
			return false;
		}
		
		$('.section-edit-container').append(addSection(rows, cols));
		Aloha.jQuery('.aloha-editable').aloha();
	};
	Aloha.ready(function() {
		$('.section-edit-add').click(add_section);
		
		// $('.aloha-table td').live('click', function(){
			// $(this).find(inputsStr).remove()
		// });
		
		// Aloha.bind('aloha-table-selection-changed', function(event, editable) {
			// alert();
        // });		
	});
	Aloha.jQuery('.aloha-editable').aloha();
	
	// Table building
	function buildTableStr(rows, cols) {
		var tableStr = '<table class="table table-bordered section-table">';
		
		// tableStr += '<thead>';
			// var rowStr = '<tr>';
			
			// for(var j=0; j<rows; j++) {
				// rowStr += $.tmpl('<th>#{cell}</th>', {'cell':'<input class="span1" type="text" >'});
			// }
			// rowStr += '</tr>';
			
			// tableStr += rowStr;
		// tableStr += '</thead>';
			tableStr += '<tbody>';
			
				for(var i=0; i<rows; i++) {
					var rowStr = '<tr>';
					for(var j=0; j<cols; j++) {
						rowStr += $.tmpl('<td>#{cell}</td>', {'cell':'&nbsp;'});
					}
					rowStr += '</tr>';
					
					tableStr += rowStr;
				}
				
			tableStr += '</tbody>';
		tableStr += '</table>';
		return tableStr;
	}
	function addSection(rows, cols) {
		return $.tmpl('<div class="section">#{handle}#{heading}#{table}#{actions}</div>', {
			'handle': '<div class="section-edit-handle"></div>',
			'heading': '<h4 class="aloha-editable section-title">New Section</h4>',
			'table': '<div class="aloha-editable section-body"><p>Text can go above the table!</p>'+buildTableStr(rows, cols)+'<p>Or below the table!</p></div>',
			'actions': '<div class="section-edit-actions btn-group btn-group-vertical"><button class="btn section-edit-delete"><i class="icon-remove-sign"></i></button> <button class="btn section-edit-edittable"><i class="icon-edit"></i></button> <button class="btn section-edit-show"><i class="icon-film"></i></button></div>',
		});
	}
	
	
	// So the dropw down doesnt collapse
	$('.dropdown-table').click(function(e){
		e.preventDefault();
		return false;
	}).keypress(function(e){
		if ( e.which == 13 ) {
			e.preventDefault();
			$('.section-edit-add').trigger('click');
			return false;
		}
	});
</script>