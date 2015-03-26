var app = app || {};

//router
(function(app) {
	app.TodoRouter = Backbone.Marionette.AppRouter.extend({
		//コントローラをインスタンス化
		controller: new app.TodoController(),
		//ルーティング設定
		appRoutes : {
			'' 					: 'todoLists',
			'todo-lists' 		: 'todoLists',
			'todo-lists/:id' 	: 'todoDetail'
		},
	});
})(app);