<?php
class TodoListFixture extends CakeTestFixture {
	public $import = 'TodoList';
	public $records = array (
		array (
			"id" => 1000,
			"todo" => "牛乳を買う",
			"status" => "1",
			"owner" => 1000,
			"assignee" => 1000
		)
	);
}