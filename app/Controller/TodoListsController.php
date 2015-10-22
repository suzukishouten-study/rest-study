<?php

App::uses('AppController', 'Controller');

class TodoListsController extends AppController {
	private $fields = array (
		'TodoList.id',
		'TodoList.todo',
		'TodoList.status',
		'Owner.id',
		'Owner.name',
		'Assignee.id',
		'Assignee.name'
	);

	public function index() {
		$query = array (
			'fields' => $this->fields,
			'order' => "TodoList.id"
		);
		$res = $this->TodoList->find('all', $query);
		// 整形
		if (count($res) > 0) {
		    $loginUserId = $this->Auth->user()['id'];
			foreach ( $res as $key => $row ) {
			    //「ログインユーザがオーナである」フラグ
				$res[$key]['TodoList']['owned'] = $row['Owner']['id'] === $loginUserId;
			    //「ログインユーザが担当である」フラグ
				$res[$key]['TodoList']['assigned'] = $row['Assignee']['id'] === $loginUserId;
			}
		}
		$this->set(compact('res'));
		$this->set('_serialize', 'res');
	}

	public function view($id = null) {
		$res = $this->TodoList->findById($id, $this->fields);
		$this->set(compact('res'));
		$this->set('_serialize', 'res');
	}

	public function add() {
		$data = $this->request->data;
		$data['owner'] = $this->Auth->user()['id'];
		$res = $this->TodoList->save($data);
		$response = $this->editResponse($res);
		$this->set(compact('response'));
		$this->set('_serialize', 'response');
	}
	public function delete($id) {
		//オーナかどうかチェック
		if(!$this->TodoList->isOwner($id)){
			$this->setStatusValidationError();
			$response = $this->editErrors('オーナのみ削除可能です。');
		}else{
			$res = $this->TodoList->delete($id, false);
			$response = $this->editResponse($res);
		}
		$this->set(compact('response'));
		$this->set('_serialize', 'response');
	}

	public function edit($id) {
		$this->TodoList->id = $id;
		$data = $this->request->data;
		$res = $this->TodoList->save($this->request->data);
		$res = !empty($res);
		$response = $this->editResponse($res);
		$this->set(compact('response'));
		$this->set('_serialize', 'response');
	}

	public function download() {
		//id順で一覧取得
		$query = array (
			'fields' => $this->fields,
			'order' => "TodoList.id"
		);
		$res = $this->TodoList->find('all', $query);
		// CSVファイルに整形
		if ($res && is_array($res)) {
			$fp = fopen('php://temp', 'w+');
			//タイトル
			$fields = array('id', 'todo', 'status', 'owner' ,'assignee');
			fputcsv($fp, $fields);
			//データ
			foreach ( $res as $record ) {
			    $fields = array();
			    $fields[] = $record['TodoList']['id'];
			    $fields[] = $record['TodoList']['todo'];
			    $fields[] = $record['TodoList']['status'];
			    $fields[] = $record['Owner']['name'];
			    $fields[] = $record['Assignee']['name'];
				fputcsv($fp, $fields);
			}
			//ポインタを先頭に
			rewind($fp);
			//読み込み
			$content = stream_get_contents($fp);
			//このままだとエンコーディングはUTF-8, 改行コードはLFとなり、
			//Excelでひらけないので、開きたい場合は下記コメントインしてエンコーディングをSJIS-winにする
			//$content =  mb_convert_encoding($content, 'sjis-win', 'UTF-8');
			fclose($fp);
			//Viewを使用しない
			$this->autoRender = false;
			//ダウンロードファイル名を設定
			$this->response->download('todo.csv');
			$this->response->type('csv');
			$this->response->body($content);
		}
	}

	public function upload() {
		$fileUploadParams = $this->getUploadFileParams();
		$loginUserId = $this->getLoginUserId();
		$owner = $loginUserId;
		$assignee = $loginUserId;
		$errors = array();
		$numRegists = $this->registerFilesAsTodos($fileUploadParams, $owner, $assignee, $errors);
		$response = $this->editUploadResponse($numRegists, $errors);
		$this->set(compact('response'));
		$this->set('_serialize', 'response');
	}

	//アップロードのPOSTデータを取得する
	protected function getUploadFileParams(){
		return $this->request->params['form'];
	}

	//ログイン中ユーザのIDを取得する
	protected function getLoginUserId(){
		return $this->Auth->user()['id'];
	}

	//アップロードされたファイル群を読み込んでTODOとしてDBに登録する
	private function registerFilesAsTodos($fileUploadParams, $owner, $assignee, &$errors){
		$numRegists = 0;
		//$errors = array();
		foreach ( $fileUploadParams as $fileUploadParam ) {
			$fileName = $fileUploadParam['name'];
			$filePath = $fileUploadParam['tmp_name'];
			$todos = $this->readUploadTodoFile($filePath);
			$numRegists += $this->registerTodos($fileName, $todos, $owner, $assignee, $errors);
		}
		return $numRegists;
	}

	// アップロードされたファイルを読み込んで配列に格納して返す
	protected function readUploadTodoFile($filePath){
		return file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}

	// 配列に格納されたTODOをDBに登録する
	private function registerTodos($fileName, $todos, $owner, $assignee, &$errors){
		$numRegists = 0;
		$lineNo = 1;
		foreach ( $todos as $todo ) {
			$record = array ();
			$record['todo'] = $todo;
			$record['status'] = 0;
			$record['owner'] = $owner;
			$record['assignee'] = $assignee;
			$res = $this->TodoList->save($record);
			if ($res) {
				$numRegists++;
			} else {
				$validationErrors = $this->TodoList->validationErrors;
				if (count($validationErrors) > 0) {
					$this->formatValidationErrorMessage($fileName, $lineNo, $validationErrors, $errors);
				}
			}
			$this->TodoList->create();
			$lineNo++;
		}
		return $numRegists;
	}

	// バリデーションエラーの内容を整形する
	private function formatValidationErrorMessage($fileName, $lineNo, $validationErrors, &$errors){
		foreach ( $validationErrors as $validationErrorsOfLine ) {
			$title = 'file:' . $fileName . ' - line: ' . $lineNo . ': ';
			foreach ( $validationErrorsOfLine as $validationError ) {
				$errors[] = array (
					$title . $validationError
				);
			}
		}
	}

	// アップロード処理結果のメッセージをクライアント向けに整形する
	private function editUploadResponse($numRegists, $errors){
		if (count($errors) > 0) {
			$this->TodoList->validationErrors = $errors;
			$response = $this->editResponse(false);
			array_unshift($response['errors'], array (
				'以下のエラーが発生しました。'
			));
			if ($numRegists > 0) {
				array_unshift($response['errors'], array (
					$numRegists . '件のTODOを登録しました。'
				));
			}
		} else {
			$response = $numRegists . '件のTODOを登録しました。';
		}
		return $response;
	}

	//レスポンスを編集
	private function editResponse($res){
		if($res){
			$response = $res;
		}else{
			$this->setStatusValidationError();
			$respnse = array();
			if(count($this->TodoList->validationErrors) > 0){
				$response = $this->editErrors($this->TodoList->validationErrors);
			}else{
				$response = $this->editErrors('エラーが発生しました。');
			}
		}
		return $response;
	}

	//バリデーションエラー時はレスポンスを400に設定
	private function setStatusValidationError(){
		$this->response->statusCode(400);
	}

	//エラーメッセージを編集
	private function editErrors($errors){
		if(is_array($errors)){
			$res['errors'] = $errors;
		}else{
			$res['errors']  = array('error' => array($errors));
		}
		return $res;
	}

}
