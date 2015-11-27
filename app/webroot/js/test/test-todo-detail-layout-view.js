define(function(require){
	var sinon = require('sinon');
	var TodoDetailLayoutView = require('views/todo-detail-layout-view');
	var TodoModel = require('models/todo-model');
	var loginTest = require('test/test-login');
	return function () {
		describe("TODO詳細の表示データ取得テスト", function () {
			it('ログインチェック', function (done) {
				loginTest(done);
			});

			it("Todoとユーザ一覧取得", function (done) {
				var layoutView = _createTodoDetailLayoutView();
				sinon.stub(layoutView, 'showItem', function (todoModel) {
					todoModel.should.be.ok;
					layoutView.showItem.restore();
					done();
				});
				layoutView.render();
			});

			//utility
			function _createTodoDetailLayoutView(data) {
				//テンプレート
				var template = '<div><div></div></div>';
				//modelとtemplateを渡してviewを生成
				var view = new TodoDetailLayoutView({
					template: $(template),
					modelId: 1
				});
				return view;
			}
		});
	}
});

