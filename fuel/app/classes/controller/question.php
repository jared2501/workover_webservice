<?php

/**
 * The Question Controller.
 * 
 * @package  app
 * @extends  Controller
 */
 
class Controller_Question extends Controller_Hybrid {
	public $template = 'template';
	
	public function get_show() {
		$question_id = Input::get('question_id', 0);
		
		$question = Model_Question::find($question_id, array('related' => array('sections')));
		
		Casset::js('jquery::jquery.min.js');
		Casset::js('angularjs::angular.min.js');
		Casset::js('bootstrap::bootstrap.min.js');
		Casset::js('core::question/show.js');
		Casset::css('bootstrap::bootstrap.min.css');

		if(empty($question)) {
			$this->template->title = 'Nothing to see here';
			$this->template->content = View::forge('404');
		} else {
			$data['question'] = $question->to_array();
			$data['show_answers'] = (Input::get('show_answers') ? 'true' : 'false' );

			$this->template->title = 'Question '.$question_id;
			$this->template->content = View::forge('question/show', $data, false);
		}
	}
	
	public function get_create() {
		Casset::js('aloha::require.js');
		Casset::js('aloha::vendor/jquery-1.7.2.js');
		Casset::js('aloha::aloha.js', false, 'aloha');
		Casset::set_js_option('aloha', 'attr', array('data-aloha-plugins' => 'common/ui,common/format,common/highlighteditables,common/link'));

		Casset::js('angularjs::angular.min.js');
		Casset::js('bootstrap::bootstrap.min.js');
		
		Casset::css('aloha::aloha.css');
		Casset::css('bootstrap::bootstrap.min.css');
		
		Casset::js('core::question/create.js');
		Casset::js('core::directives.js');
		
		$this->template->title = 'Create a Question';
		$this->template->content = View::forge('question/create');
	}
	
	public function post_create() {
		$question = Input::json('question');
		$sections = Input::json('sections');
		$answers = Input::json('answers');
		
		$result = Model_Question::make($question, $sections, $answers);
		
		if($result) {

			$curl = Request::forge('http://localhost/workover/systems/1/questions/add', 'curl');
			$curl->set_method('post');
			$curl->set_params(array(
				'question_id' => $result->id,
				'title' => $result->title,
				'difficulty' => $result->difficulty
			));
			$curl_result = $curl->execute();

			return $this->response($result);
		} else {
			return $this->response($result, 400);
		}
	}
	
	public function get_edit() {
		$question_id = Input::get('question_id', 0);
		$question = Model_Question::find($question_id, array('related' => array('sections', 'answers')));
		
		Casset::js('aloha::require.js');
		Casset::js('aloha::vendor/jquery-1.7.2.js');
		Casset::js('aloha::aloha.js', false, 'aloha');
		Casset::set_js_option('aloha', 'attr', array('data-aloha-plugins' => 'common/ui,common/format,common/highlighteditables,common/link'));
		
		Casset::js('angularjs::angular.min.js');
		Casset::js('bootstrap::bootstrap.min.js');
		
		Casset::css('aloha::aloha.css');
		Casset::css('bootstrap::bootstrap.min.css');
		
		Casset::js('core::question/create.js');
		Casset::js('core::directives.js');
		
		if(empty($question)) {
			$this->template->title = 'Nothing to see here';
			$this->template->content = View::forge('404');
		} else {
			$q = $question->to_array();

			$data['answers'] = array_values($q['answers']);
			$data['sections'] = array_values($q['sections']);

			unset($q['answers']);
			unset($q['sections']);

			$data['question'] = $q;

			$this->template->title = 'Edit a Question';
			$this->template->content = View::forge('question/create', $data, false);
		}
	}
	
	public function post_edit() {
		$question = Input::json('question');
		$sections = Input::json('sections');
		$answers = Input::json('answers');

		$question_id = Input::json('question.id', 0);
		$q = Model_Question::find($question_id);
		
		if(empty($question)) {
			return $this->response(false, 404);
		}
		
		$result = $q->edit($question, $sections, $answers);
		
		if($result) {
			
			$curl = Request::forge('http://localhost/workover/systems/1/questions/add', 'curl');
			$curl->set_method('post');
			$curl->set_params(array(
				'question_id' => $result->id,
				'title' => $result->title,
				'difficulty' => $result->difficulty
			));
			$curl_result = $curl->execute();
			
			return $this->response($result);
		} else {
			return $this->response($result, 400);
		}
	}
}
