define(function(require){
	var TodoItemView = require('views/todo-item-view');
	var TodoModel = require('models/todo-model');
	var loginTest = require('test/test-login');
	return function(){
		describe("TODO一覧表示の削除リンク、詳細リンク、完了チェックボックスの表示テスト", function () {
			describe("ログインチェック", function () {
				it('ログインチェック', function (done) {
					loginTest(done);
				});
			});

			describe("オーナかつ担当者の場合", function () {
				it("削除リンク O / 詳細リンク O / チェック O", function () {
					itemView = _createAndRenderTodoItemView(true, true);
					//削除リンクが表示されている
					itemView.ui.removeLink.css('display').should.be.equals('');
					//詳細リンク表示されている
					itemView.ui.detailLink.css('display').should.be.equals('');
					//完了チェックボックスが押せる
					itemView.ui.checkBox.prop('disabled').should.be.equals(false);
				});
			});

			describe("オーナだが担当者ではない場合", function () {
				it("削除リンク O / 詳細リンク O / チェック O", function () {
					var itemView = _createAndRenderTodoItemView(true, false);
					//削除リンクが表示されている
					itemView.ui.removeLink.css('display').should.be.equals('');
					//詳細リンク表示されている
					itemView.ui.detailLink.css('display').should.be.equals('');
					//完了チェックボックスが押せる
					itemView.ui.checkBox.prop('disabled').should.be.equals(false);
				});
			});

			describe("オーナではないが担当者の場合", function () {
				it("削除リンク X / 詳細リンク O / チェック O", function () {
					var itemView = _createAndRenderTodoItemView(false, true);
					//削除リンクが非表示(display=none)になっている
					itemView.ui.removeLink.css('display').should.be.equals('none');
					//詳細リンク表示されている
					itemView.ui.detailLink.css('display').should.be.equals('');
					//完了チェックボックスが押せる
					itemView.ui.checkBox.prop('disabled').should.be.equals(false);
				});
			});
			describe("オーナでもなく担当者でもない場合", function () {
				it("削除リンク X / 詳細リンク X / チェック X", function () {
					var itemView = _createAndRenderTodoItemView(false, false);
					//削除リンクが非表示(display=none)になっている
					itemView.ui.removeLink.css('display').should.be.equals('none');
					//詳細リンクが非表示(display=none)になっている
					itemView.ui.detailLink.css('display').should.be.equals('none');
					//完了チェックボックスが押せない(disabled=true)
					itemView.ui.checkBox.prop('disabled').should.be.equals(true);
				});
			});
			//utility
			function _createAndRenderTodoItemView(owned, assigned){
				//サーバから受信するデータ
				data = {
					TodoList: {
						id: "1",
						todo: "do somothing",
						status: "0",
						owned: owned,
						assigned: assigned
					},
					Owner: {
						id: "1",
						name: "anonymous"
					},
					Assignee: {
						id: "1",
						name: "anonymous"
					}
				};
				//サーバからのデータをparseしてmodel生成
				var model = new TodoModel(data, {parse: true});
				//テンプレート。テスト対象のみ
				var template =
					'<div>' +
					'  <a class="remove-link">削除</a>' +
					'  <a class="detail-link">詳細</a>' +
					'  <input type="checkbox" class="toggle"></input>' +
					'</div>';
				//modelとtemplateを渡してviewを生成
				var view = new TodoItemView({
					model: model,
					//template : $(template)
				});
				//表示までしてからreturn
				view.template = $(template);
				view.render();

				return view;
			}
		});
	}
});

