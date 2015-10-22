<?php
App::uses('AppModel', 'Model');
App::uses('User', 'Model');
App::uses('AuthComponent',  'Controller/Component');

class TodoList extends AppModel {
	public $belongsTo = array (
		'Owner' => array (
			'className' => 'User',
			'foreignKey' => 'owner',
		),
		'Assignee' => array (
			'className' => 'User',
			'foreignKey' => 'assignee'
		)
	);
	public $validate = array (
		//TODOのチェック
		'todo' => array (
			//文字数
			'rule1' => array (
				'rule' => array(
					'between', 1, 200
				),
				'message' => '1〜200文字までで入力して下さい。'
			),
			//同内容禁止
			'rule2' => array (
				'rule' => 'isUnique',
				'message' => '同じ内容のTODOが既に登録されています。'
			)
		),
		//ステータスのチェック
		'status' => array (
			'rule1' => array (
				//0 or 1 (正規表現)
// 				'rule' => array(
// 					'custom',
// 					'/^[01]$/'
// 				),
				//0 or 1 (候補値列挙)
				'rule' => array(
					'inList',
					array(0, 1)
				),
				'message' => 'ステータスは0(未完了)か1(完了)で入力して下さい。'
			),
		),
		//担当者のチェック
		'assignee' => array (
			//idがusersテーブルに存在するか
			'rule1' => array (
				'rule' => array(
					'existsUser'
				),
				'message' => '担当者に指定したユーザが存在しません。'
			),
		),
		//権限
		'id' => array (
			//自分がオーナ、まはた担当者かチェック
			'rule1' => array (
				'rule' => array(
					'isOwnerOrAssignee'
				),
				'message' => 'オーナ、または担当者のみ更新可能です。'
			),
		)
	);

	//独自バリデーションルール
	//担当者として指定されたIDがusersテーブルに存在するかチェックする
	public function existsUser($userId){
		$userModel = ClassRegistry::init('User');
		$count = $userModel->find('count', array('conditions'=>array('id'=>$userId), 'recursive' => -1));
		return $count > 0;
	}

	//独自バリデーションルール
	//自分がオーナまたは担当者かチェックする
	public function isOwnerOrAssignee($id){
		if(!isset($id)){
			//新規登録の場合は関係なし
			return true;
		}
		$me = AuthComponent::user();
		$todo = $this->findById($id);
		if($todo){
			if($todo['TodoList']['owner'] === $me['id']
				|| $todo['TodoList']['assignee'] === $me['id']){
				return true;
			}
		}
		return false;
	}

	//独自バリデーションルール
	//自分がオーナかチェックする
	public function isOwner($id){
		if(!isset($id)){
			//新規登録の場合は関係なし
			return true;
		}
		$me = AuthComponent::user();
		$todo = $this->findById($id);
		if($todo){
			if($todo['TodoList']['owner'] === $me['id']){
				return true;
			}
		}
		return false;
	}
}
