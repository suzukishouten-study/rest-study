var app = app || {};

//詳細画面用レイアウトビュー
(function(app) {
	app.TodoDetailLayoutView = Backbone.Marionette.LayoutView.extend({
		//テンプレート
		template : '#todo-detail-layout-template',

		regions : {
			itemRegion : '#todo-item',
		},

		onRender : function() {
			var todoModel = new app.TodoModel({
				id : this.options.modelId
			});
			//モデルのサーバからのデータ取得完了時、描画を行う
			this.listenTo(todoModel, 'sync', this.showItem, this);
			//サーバからデータ取得
			todoModel.fetch({
				wait : true
			});
		},

		showItem : function(todoModel) {
			this.itemRegion.show( new app.TodoDetailItemView({
				model : todoModel
			}));
		},

	});
})(app);
