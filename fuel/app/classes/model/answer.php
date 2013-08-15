<?php

class Model_Answer extends Orm\Model {
	protected static $_table_name = 'answers';
	protected static $_belongs_to = array('question');
	
	public static function unique_count($question_id, $section_number) {
		$result = DB::select(DB::expr('COUNT(DISTINCT `key`) as count'))->from(self::$_table_name)->where(array(
			array('question_id', '=', $question_id),
			array('section_number', '=', $section_number),
		))->execute()->current();
		return $result['count'];
	}
	
	public static function check_answer($question_id, $section_number, $input) {
		if(empty($question_id) || empty($section_number))
			return false;
		
		$count = self::unique_count($question_id, $section_number);
		
		// Fringe case that section has not answers
		if($count == 0) {
			if(count($input) == 0 || empty($input))
				return true;
			else
				return false;
		}
		
		if($count != count($input) || !is_array($input))
			return false;
		
		$query = DB::select(DB::expr('COUNT(*) as count'))->from(self::$_table_name)->and_where_open()->where(array(
			array('question_id', '=', $question_id),
			array('section_number', '=', $section_number),
		));
		
		$query->and_where_open();
		foreach($input as $key=>$value) {
			$query->or_where_open()->where(array(
				array('key', '=', $key),
				array('value', '=', $value),
			))->or_where_close();
		}
		$query->and_where_close()->and_where_close();
		$result = $query->execute()->current();
			
		if($result['count'] == count($input))
			return true;
		
		return false;
	}
}