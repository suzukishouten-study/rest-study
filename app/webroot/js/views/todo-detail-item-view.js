var app = app || {};

//詳細ビュー
(function(app) {
	app.TodoDetailItemView = Backbone.Marionette.ItemView.extend({

		//テンプレート
		template: "#todo-detail-item-template",

		ui : {
			todoStatus   : '#edit-todo',
			updateButton : '#updateTodo',
			cancelButton : '#updateCancel'
		},

		//DOMイベントハンドラ設定
		events : {
			//更新ボタンクリック時
			'click @ui.updateButton' : 'onUpdateClick',
			//キャンセルボタンクリック時
			'click @ui.cancelButton' : 'onCancelClick',
		},

		//初期化
		initialize: function(){
			_.bindAll( this, 'onSaveSuccess' );
		},

		//更新ボタンクリックのイベントハンドラ
		onUpdateClick : function() {
			//テキストボックスから文字を取得
			var todoString = this.ui.todoStatus.val();
			this.model.save({
				todo : todoString
			}, {
				silent : true,
				success : this.onSaveSuccess,
			});
		},

		//キャンセルボタンクリックのイベントハンドラ
		onCancelClick : function() {
			this.backTodoLists();
		},

		//更新成功
		onSaveSuccess : function() {
			this.backTodoLists();
		},

		//TODOリスト画面に戻る
		backTodoLists : function() {
			Backbone.history.navigate('#todo-lists', true);
		}

	});
})(app);