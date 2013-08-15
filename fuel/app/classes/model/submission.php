<?php


class Model_Submission extends Orm\Model {
	protected static $_table_name = 'submissions';
	
	public static function up_to_section($question_id, $user_id) {
		$sub = self::find('last', array(
			'where' => array(
				array('question_id', '=', $question_id),
				array('user_id', '=', $user_id),
				array('correct', '=', true),
			),
			'order_by' => array('section_number' => 'desc')
		));
		
		if(empty($sub))
			return 0;
		
		return $sub->section_number + 1;
	}
	
	public static function make_submission($question_id, $section_number, $user_id, $input, $correct = false) {
		if(empty($question_id) or empty($section_number) or empty($user_id))
			return null;
		
		$prev_sub = self::find('last', array(
			'where' => array(
				array('question_id', '=', $question_id),
				array('section_number', '=', $section_number),
				array('user_id', '=', $user_id),
			),
			'order_by' => array('submission_number' => 'desc')
		));
		
		$submission_number = 1;
		
		if(!empty($prev_sub)) {
			$submission_number = $prev_sub->submission_number + 1;
		}
		
		$props = array(
			'question_id' => $question_id,
			'user_id' => $user_id,
			'section_number' => $section_number,
			'submission_number' => $submission_number,
			'serialized_submission' => serialize($input),
			'correct' => $correct,
		);
		
		if(self::forge($props)->save())
			return $submission_number;
		
		return null;
	}
}