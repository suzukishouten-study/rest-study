var app = app || {};

//Todo一覧表示用ビュー
(function(app) {
	app.TodoCompositeView = Backbone.Marionette.CompositeView.extend({
		template: '#todo-composite-template',

		childView : app.TodoItemView,

		childViewContainer : 'tbody',

		ui : {
			addTodo : '#addTodo',
			newTodo : '#new-todo'
		},

		events : {
			'click @ui.addTodo' : 'onCreateTodo',
		},

		initialize: function(){
			_.bindAll( this, 'onCreatedSuccess' );
		},

		onCreateTodo : function(e) {
			this.collection.create(this.newAttributes(), {
		          silent:  true ,
		          success: this.onCreatedSuccess
			});
			this.ui.newTodo.val('');
		},

		newAttributes : function() {
			return {
				todo : this.ui.newTodo.val().trim(),
				status : 0
			};
		},

		onCreatedSuccess : function(){
			this.collection.fetch({ reset : true });
		},

	});
})(app);
