var app = app || {};

//Todo一覧表示用コレクション
(function(app) {
	app.TodoCollection = Backbone.Collection.extend({
		url : '/rest-study/todo_lists.json',
		model : app.TodoModel,

		parse : function(response) {
			//コレクションをパース
			console.log("コレクションをパース");
			return response;
		}
	});
})(app);