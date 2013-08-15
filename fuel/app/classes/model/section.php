<?php


class Model_Section extends Orm\Model {
	protected static $_table_name = 'sections';
	
	protected static $_properties = array(
		'id',
		'question_id',
		'number',
		'title' => array( //column name
			'type' => 'varchar',
			'default' => ''
		),
		'body' => array(
			'type' => 'text',
			'default' => ''
		),
		'created_at' => array(
			'data_type' => 'int',
		),
		'updated_at' => array(
			'data_type' => 'int',
		),
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
	
	protected static $_belongs_to = array('question');
	
	public function get_previous_attemps($user_id) {
		$sub = Model_Submission::find('last', array(
			'where' => array(
				array('question_id', '=', $this->question_id),
				array('user_id', '=', $user_id),
				array('section_number', '=', $this->number),
			),
			'order_by' => array('submission_number' => 'desc')
		));
		
		if(empty($sub)) {
			return 0;
		}
		
		return $sub->submission_number;
	}
}