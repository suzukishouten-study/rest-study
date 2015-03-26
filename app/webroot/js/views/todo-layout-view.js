var app = app || {};

//Todo一覧表示用レイアウトビュー
(function(app) {
	app.TodoLayoutView = Backbone.Marionette.LayoutView.extend({
		//テンプレート
		template: '#todo-layout-template',

		regions : {
			listRegion : '#todo-lists',
		},

		onRender : function(){
			var todoCollection = new app.TodoCollection();
			this.listenTo(todoCollection , 'reset', this.showTodoList, this);
			todoCollection.fetch({reset : true});
		},

		showTodoList : function(todoCollection){
			this.listRegion.show( new app.TodoCompositeView({
				collection : todoCollection
			}));
		},

	});
})(app);