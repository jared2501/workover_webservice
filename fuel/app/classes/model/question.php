<?php

class Model_Question extends Orm\Model {
	protected static $_table_name = 'questions';
	
	protected static $_properties = array(
		'id',
		'title' => array(
			'data_type' => 'varchar',
			'default' => ''
		),
		'description' => array(
			'data_type' => 'varchar',
			'default' => ''
		),
		'difficulty' => array(
			'data_type' => 'varchar',
			'default' => ''
		),
		'created_at' => array(
			'data_type' => 'int',
		),
		'updated_at' => array(
			'data_type' => 'int',
		),
	);
	
	protected static $_has_many = array(
		'sections' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Section',
			'key_to' => 'question_id',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'answers' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Answer',
			'key_to' => 'question_id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);
	
	protected static $_created_at = 'created_at';
	protected static $_updated_at = 'updated_at';
	protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => false,
        ),
    );
	
	protected $section_count;
	
	public static function total_count($where = array()) {
		$query = DB::select(DB::expr('COUNT(*) as count'))->from(self::$_table_name);
		
		if(!empty($where)) $query->where($where);
		
		$result = $query->execute()->current();
		return $result['count'];
	}
	
	public static function get_list($limit, $offset, $where = array()) {
		$query = DB::select('id', 'title')->from(self::$_table_name)->limit($limit)->offset($offset);
		
		if(!empty($where))
			$query->where($where);
		
		return $query->execute();
	}
	
	
	public static function make($question, $sections, $answers) {
		if(empty($question))
			return false;
		
		$q = self::forge($question);
		
		if(!empty($sections) && is_array($sections)) {
			$i = 1;
			foreach($sections as $section) {
				$section['number'] = $i++;
				$s = Model_Section::forge($section);
				$q->sections[] = $s;

				if(!empty($answers) && is_array($answers)) {
					foreach($answers as $answer) {
						$a = Model_Answer::forge($answer);
						$q->answers[] = $a;
					}
				}
			}
		}
		
		if($q->save()) {
			return $q;
		} else {
			return false;
		}
	}
	
	
	public function section_count() {
		if(empty($this->section_count)) {
			$result = DB::select(DB::expr('COUNT(*) as count'))->from('sections')->where('question_id', $this->id)->execute()->current();
			$this->section_count = $result['count'];
		}
		
		return $this->section_count;
	}
	
	public function edit($question, $sections, $answers) {
		$this->title = $question['title'];
		$this->description = $question['description'];
		$this->difficulty = $question['difficulty'];
		
		foreach($sections as $key => $section) {
			if(isset($section['id'])) {
				$s = Model_Section::find($section['id']);
				$s->body = $section['body'];
				$s->title = $section['title'];
				$s->number = $key+1;
				$s->save();
			} else {
				$s = Model_Section::forge($section);
				$this->sections[] = $s;
			}
		}
		
		if(isset($answers)) {
			foreach($this->answers as $ans) {
				$ans->delete();
			}

			$this->answers = array();
			
			foreach($answers as $answer) {
				$a = Model_Answer::forge($answer);
				$this->answers[] = $a;
			}
		}
		
		if($this->save()) {
			return $this;
		} else {
			return false;
		}
	}

	public function to_array($custom = false, $recurse = false) {
		$arr = parent::to_array($custom, $recurse);
		if(!empty($arr['sections'])) {
			$arr['sections'] = array_values($arr['sections']);
		}
		return $arr;
	}
}