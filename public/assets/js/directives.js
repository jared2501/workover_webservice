angular.module('workover-webservice')
.directive("wysiwyg", function ($timeout) {
	var linkFn = function (scope, el, attr, ngModel) {
		ngModel.$render = function() {
			if(!!ngModel.$viewValue) {
				el.html(ngModel.$viewValue);
			} else {
				ngModel.$setViewValue(el.html());
			}
		};

		el.aloha();

		Aloha.bind('aloha-smart-content-changed', function (e, eventArgument) {
			ngModel.$setViewValue(el.html());
		});
	}
	return {
		require: '?ngModel',
		link: linkFn,
		restrict: 'A'
	};
})
.directive("wysiwygParser", function () {
	var inputs = 'input, select, textarea';
	var syntax = {
		"open" : "[",
		"close" : "]",
		"option_split" : ",",
		"correct": {
			"open": "(",
			"close": ")"
		}
	}

	// Finds all the inputs and converts them to their markup
	var toMarkup = function(html) {
		return $('<div>'+html+'</div>').find(inputs).each(function(){ // Add a root node and parse html and find each input...

			var $this = $(this);

			if($this.is('select')) {
				
				var type = 'select';
				var options = [];

				$this.children('option:not(:first)').each(function(){ // Get all the options except the first!
					options.push($(this).text());
				});
				
				$this.replaceWith(type + syntax.open + options.join(',') + syntax.close +
					syntax.correct.open + $this.prop('selectedIndex') + syntax.correct.close);
			}

		}).end().html();
	};

	var toInput = function(html) {
		var start;
		for (start = html.indexOf('select['); start > 0; start = html.indexOf('select[')) {
			var length = 0;
			var correctIndex = 0;
			var extraLengthForCorrectIndex = 0;
			var temp = html.split('');
			for(var i = start; i < temp.length; i++) {
				if(temp[i] == ']' && temp[i-1] != '\\') {
					length = i + 1 - start;
					break;
				}
			}

			// Grab the correct option
			if(temp[i+1] == syntax.correct.open) {
				for(var j = i; j < temp.length; j++) {
					if(temp[j] == syntax.correct.close) {
						correctIndex = parseInt(html.substr(i+2, j-i-2));
						extraLengthForCorrectIndex = j - i;
						break;
					}
				}
			}
			
			if(length == 0)
				break;

			var tempOptionsString = '';
			var tempOptionsArr = html.substr(start+7, length-7-1).split(',')
			for(var i = 0; i < tempOptionsArr.length; i++) {
				var selected = (correctIndex == i + 1)? 'selected' : '';
				tempOptionsString = tempOptionsString + '<option value="' + (i+1) + '" '+ selected +'>' +
					tempOptionsArr[i] + '</option>';
			}

			var spliceStr = '<select><option value="0">(Select an Option)</option>' + tempOptionsString + '</select>';
			
			var temp = html.split('');
			temp.splice(start, length+extraLengthForCorrectIndex, spliceStr);
			html = temp.join('');
		}

		return html;
	}

	var getOptions = function(html, options) {
		options.length = 0;
		// Find all options
		$inputs = $('.sections-area').find(inputs).not('.exclude-wysiwyg');
		
		// Determine a suitable nextNameNum
		var nextNameNum = 0;
		$inputs.each(function(){
			var tempNum = parseInt($(this).attr('name'));

			if(tempNum > nextNameNum) {
				nextNameNum = $(this).attr('name');
			} else if(tempNum == nextNameNum) {
				nextNameNum++;
			}
		});
		
		// If they dont have a name, name them start from nextName and push them to list of optons
		$inputs.each(function(){
			var $this = $(this);

			if(!$this.attr('name')) {
				$this.attr('name', nextNameNum);
				nextNameNum++;
			}

			var answer = 0;

			if($this.is('select')) {
				answer = $this.prop('selectedIndex');
			}

			options.push({'key': $this.attr('name'), 'value': answer});
		});
	}

	return {
		require: '?ngModel',
		link: function($scope, el, attrs, ngModel) {
			$scope.$on('wysiwyg:parse-to-input', function(e, options){
				el.html(toInput(el.html()));
				getOptions(el.html(), options);

				Aloha.trigger('aloha-smart-content-changed');

				$(inputs).not('.exclude-wysiwyg').prop('disabled', true);
			});

			$scope.$on('wysiwyg:parse-to-markup', function(e, options){
				el.html(toMarkup(el.html()));
				
				Aloha.trigger('aloha-smart-content-changed');

				options.length = 0;
			});

			$scope.$on('wysiwyg:pre-submit', function(e, options){
				el.html(toInput(el.html()));
				getOptions(el.html(), options);

				Aloha.trigger('aloha-smart-content-changed');
			});
		}
	};
})
.directive('placeholder', function($timeout){
	jQuery.support.placeholder = (function(){
		var i = document.createElement('input');
		return 'placeholder' in i;
	})();

	if (jQuery.support.placeholder) {
		return {};
	}
	return {
		link: function(scope, elm, attrs){
		if (attrs.type === 'password') {
			return;
		}
		$timeout(function(){
			elm.val(attrs.placeholder).focus(function(){
				if ($(this).val() == $(this).attr('placeholder')) {
					$(this).val('');
				}
			}).blur(function(){
				if ($(this).val() == '') {
					$(this).val($(this).attr('placeholder'));
				}
			});
		});
		}
	}
})
.directive('quickedit', function(){
	return {
		scope: {
			'quickedit': '='
		},
		link: function(scope, elem, attrs){
			if(scope.quickedit) {
				elem.text(scope.quickedit);
			}

			var quickedit = function() {
				$('<input></input>')
				.val(elem.text())
				.attr('class', elem.attr('class'))
				.attr('style', elem.attr('style'))
				.insertAfter(elem)
				.focus()
				.blur(function(){
					var $input = $(this);

					elem
					.text($input.val())
					.show();
					
					$input.remove();
					scope.quickedit = $input.val();
					console.log(scope.quickedit);
				});

				elem.hide();
			};

			elem.click(quickedit);
		}
	};
});






/*

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








.directive('optionWatcher', function($timeout){
	var inputs = 'input, select, textarea';
	

	return {
		scope: {
			'options': '=optionWatcher'
		},
		link: function($scope, elem, attrs) {
			
		}
	}
})*/