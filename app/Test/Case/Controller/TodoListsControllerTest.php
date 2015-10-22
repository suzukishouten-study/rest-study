<?php
App::uses('AppController', 'Controller');

class TodoListsControllerTest extends ControllerTestCase {
	public $fixtures = array (
		'app.todo_list',
		'app.user'
	);

	/**
	 * 準備
	 * @return Controller
	 */
	public function setUp() {
		parent::setUp();
		$mocks = array (
			'components' => array (
				'Auth' => array (
					'user'
				)
			)
		);
		//TodoListsControllerを生成
		$controller = $this->generate('TodoLists', $mocks);
		//Authコンポーネントのuserメソッドをスタブにする
		$loginUser = array (
			"id" => "1000",
			"username" => "yamada",
			"name" => "yamada"
		);
		$controller->Auth->staticExpects($this->any())
			->method('user')
			->will($this->returnValue($loginUser));
		$this->controller = $controller;
	}

	/**
	 * index関数のテスト
	 */
	public function testIndex() {
		$this->testAction('/todo_lists.json', array (
			'method' => 'get'
		));
		$result = $this->vars['res'];
		$expected = array (
			array (
				"TodoList" => array (
					"id" => "1000",
					"todo" => "牛乳を買う",
					"status" => "1",
					"owned" => true,
					"assigned" => true
				),
				"Owner" => array (
					"id" => "1000",
					"name" => "山田太郎"
				),
				"Assignee" => array (
					"id" => "1000",
					"name" => "山田太郎"
				)
			)
		);
		$this->assertEquals($expected, $result);
	}

	/**
	 * view関数のテスト
	 */
	public function testView() {
		$this->testAction('/todo_lists/1000.json', array (
			'method' => 'get'
		));
		$result = $this->vars['res'];
		$expected = array (
			"TodoList" => array (
				"id" => "1000",
				"todo" => "牛乳を買う",
				"status" => "1"
			),
			"Owner" => array (
				"id" => "1000",
				"name" => "山田太郎"
			),
			"Assignee" => array (
				"id" => "1000",
				"name" => "山田太郎"
			)
		);
		$this->assertEquals($expected, $result);
	}

}