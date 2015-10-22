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
		$files = $this->getUploadFileParams();
		$owner = $this->Auth->user()['id'];
		$numTodos = 0;
		$errors = array ();
		foreach ( $files as $file ) {
			$fileName = $file['name'];
			$filePath = $file['tmp_name'];
			$todos = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$assignee = $owner;
			$lineNo = 1;
			foreach ( $todos as $todo ) {
				$data = array ();
				$data['todo'] = $todo;
				$data['status'] = 0;
				$data['owner'] = $owner;
				$data['assignee'] = $assignee;
				$res = $this->TodoList->save($data);
				if ($res) {
					$numTodos++;
				} else {
					if (count($this->TodoList->validationErrors) > 0) {
						foreach ( $this->TodoList->validationErrors as $validationErrorsOfLine ) {
							$title = 'file:' . $fileName . ' - line: ' . $lineNo . ': ';
							foreach ( $validationErrorsOfLine as $validationError ) {
								$errors[] = array (
												$title . $validationError
								);
							}
						}
					}
				}
				$this->TodoList->create();
				$lineNo++;
			}
		}
		if (count($errors) > 0) {
			$this->TodoList->validationErrors = $errors;
			$response = $this->editResponse(false);
			array_unshift($response['errors'], array (
							'以下のエラーが発生しました。'
			));
			if ($numTodos > 0) {
				array_unshift($response['errors'], array (
								$numTodos . '件のTODOを登録しました。'
				));
			}
		} else {
			$response = $numTodos . '件のTODOを登録しました。';
		}
		$this->set(compact('response'));
		$this->set('_serialize', 'response');
	}

	protected function getUploadFileParams(){
		return $this->request->params['form'];
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
