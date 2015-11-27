//詳細画面用レイアウトビュー
define(function(require) {
	var TodoDetailItemView = require('views/todo-detail-item-view');
	var TodoModel = require('models/todo-model');
	var UserCollection = require('collections/user-collection');

	var TodoDetailLayoutView = Marionette.LayoutView.extend({
		//テンプレート
		template : '#todo-detail-layout-template',

		regions : {
			itemRegion : '#todo-item',
		},

		onRender: function () {
			//Todoを取得
 			this.todoModel = new TodoModel({
 				id : this.options.modelId
 			});
 			var todoFetching = this.todoModel.fetch();
			//ユーザ一覧取得
			var userCollection = new UserCollection();
			var userFetching = userCollection.fetch();
			$.when(
				todoFetching,
				userFetching
			).done(function(){
				this.showItem(this.todoModel, userCollection);
			}.bind(this));
		},

		showItem: function (todoModel, userCollection) {
 			this.itemRegion.show(new TodoDetailItemView({
 				model : todoModel,
 				userList : userCollection.models
 			}));
 		},

	});
	return TodoDetailLayoutView;
});
