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
			'methods' => array (
				'getUploadFileParams'
			),
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

	/**
	 * upload関数のテスト
	 * アップロードファイル1つ、2件を正常登録
	 */
	public function testUploadOKFile() {
		// 一時保存されるアップロードファイル
		$postFileName = 'testUploadOKFile.txt';
		$tmpFileName = tempnam('/tmp', $postFileName);
		// アップロードされてきた体でファイルを作成しておく
		file_put_contents($tmpFileName, array (
			"ほげ\n",
			"12345\n"
		));
		// POSTされるフォームデータ
		$uploadFormData = array (
			array (
				'name' => $postFileName,
				'tmp_name' => $tmpFileName
			)
		);
		// フォームデータ取得関数は、上で用意したフォームデータを返すようにスタブにする
		$this->controller->expects($this->any())
			->method('getUploadFileParams')
			->will($this->returnValue($uploadFormData));
		// テスト実行
		$result = $this->testAction('/todo_lists/upload.json', array (
			'method' => 'post'
		));
		// 結果取得 / 確認
		$result = $this->vars['response'];
		$expected = '2件のTODOを登録しました。';
		$this->assertEquals($expected, $result);
	}

	/**
	 * upload関数のテスト
	 * アップロードファイル2つ、3件を正常登録, 1件はバリデーションエラー
	 */
	public function testUploadOKandNGFile() {
		//一時保存されるアップロードファイル1
		$postFileName1 = 'testUploadOKandNGFile1.txt';
		$tmpFileName1 = tempnam('/tmp', $postFileName1);
		//アップロードされてきた体でファイルを作成しておく
		file_put_contents($tmpFileName1, array (
			"ほげ\n",
			"12345\n",
			"12345\n"
		));
		//一時保存されるアップロードファイル2
		$postFileName2 = 'testUploadOKandNGFile2.txt';
		$tmpFileName2 = tempnam('/tmp', $postFileName2);
		//アップロードされてきた体でファイルを作成しておく
		file_put_contents($tmpFileName2, array (
			"ふが\n",
			"12345\n" //これは重複でエラーになる
		));
		// POSTされるフォームデータ
		$uploadFormData = array (
			array (
				'name' => $postFileName1,
				'tmp_name' => $tmpFileName1
			),
			array (
				'name' => $postFileName2,
				'tmp_name' => $tmpFileName2
			)
		);

		//TodoListControllerを生成
		//フォームデータ取得関数は、上で用意したフォームデータを返すようにスタブする
		$this->controller->expects($this->any())
			->method('getUploadFileParams')
			->will($this->returnValue($uploadFormData));
		//テスト実行
		$result = $this->testAction('/todo_lists/upload.json', array (
			'method' => 'post'
		));
		//結果取得 / 確認
		$result = $this->vars['response'];
		$this->assertEquals('3件のTODOを登録しました。', $result['errors'][0][0]);
		$this->assertEquals('以下のエラーが発生しました。', $result['errors'][1][0]);
		$this->assertEquals('file:testUploadOKandNGFile1.txt - line: 3: 同じ内容のTODOが既に登録されています。', $result['errors'][2][0]);
		$this->assertEquals('file:testUploadOKandNGFile2.txt - line: 2: 同じ内容のTODOが既に登録されています。', $result['errors'][3][0]);
	}
}