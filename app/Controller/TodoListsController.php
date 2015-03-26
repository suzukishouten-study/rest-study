<?php

App::uses('AppController', 'Controller');

class TodoListsController extends AppController {

	public function index() {
		$res = $this->TodoList->find('all');
		$this->set(compact('res'));
		$this->set('_serialize', 'res');
	}

	public function view($id = null) {
		$res = $this->TodoList->findById($id);
		$this->set(compact('res'));
		$this->set('_serialize', 'res');
	}

	public function add() {
		$data = $this->request->data;
		$res = $this->TodoList->save($data);
		$this->set(compact('res'));
		$this->set('_serialize', 'res');
	}
	public function delete($id) {
		$res = $this->TodoList->delete($id, false);
		$this->set(compact('res'));
		$this->set('_serialize', 'res');
	}

	public function edit($id) {
		$this->TodoList->id = $id;
		$data = $this->request->data;
		$res = $this->TodoList->save($this->request->data);
		$res = !empty($res);
		$this->set(compact('res'));
		$this->set('_serialize', 'res');
	}

}
