var app = app || {};

//controller
(function(app) {
	app.TodoController = Backbone.Marionette.Controller.extend({

		todoLists : function() {
			//Todoレイアウト用ビューにルーティング
			this.nextView(app.TodoLayoutView);
		},

		todoDetail : function(id) {
			this.nextView(app.TodoDetailLayoutView, {modelId : id});
		},

		nextView : function(View, option) {
			app.application.mainRegion.show(new View(option));
		},

	});
})(app);
