<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>シンプルTODOアプリ</title>
</head>
<body>
	<!-- コンテンツ -->
	<div id="main"></div>

	<!-- TODO一覧表示のレイアウトテンプレート -->
	<script type="text/template" id="todo-layout-template">
	<h1>TODOリスト</h1>
	<div id="todo-lists"></div>
	</script>

	<!-- TODO一覧表示のテンプレート -->
	<script type="text/template" id="todo-composite-template">
	<textarea style="width:300px;height:50px"id="new-todo" placeholder="Todo?" autofocus></textarea>
	<input type="button" id="addTodo" value="追加">
	<hr>
	<div>
		<table border="1" width="350px">
			<tbody></tbody>
		</table>
	</div>
	</script>

	<!-- TODO一行分のテンプレート（上のtbody部分に挿入される） -->
	<script type="text/template" id="todo-item-template">
	<td><input type="checkbox" class="toggle" <%- status === '1' ? 'checked' : '' %>></td>
	<td style="margin:0px">
		<span class="todo-edit" style="margin:0px"><%- todo %></span>
	</td>
	<td>
		<a class="remove-link" href="#">削除</a>
		<a class="detail-link" href="#todo-lists/<%- id %>">詳細</a>
	</td>
	</script>

	<!-- 詳細画面のレイアウトテンプレート -->
	<script type="text/template" id="todo-detail-layout-template">
	<div id="todo-item"></div>
	</script>

	<!-- 詳細画面の表示内容テンプレート -->
	<script type="text/template" id="todo-detail-item-template">
	<h2>Todo #<%- id %></h2>
	<div>
	<textarea style="width:300px;height:50px" id="edit-todo" autofocus placeholder="Todo?"><%- todo %></textarea>
	<input type="button" id="updateTodo" value="更新"></input>
	<input type="button" id="updateCancel" value="キャンセル"></input>
	</div>
	</script>

	<!-- js(library) -->
	<script src="js/lib/jquery-2.1.3.min.js" type="text/javascript"></script>
	<script src="js/lib/underscore-min.js" type="text/javascript"></script>
	<script src="js/lib/backbone-min.js" type="text/javascript"></script>
	<script src="js/lib/backbone.marionette.min.js" type="text/javascript"></script>

	<!-- js(application) -->
	<!--   model   -->
	<script src="js/models/todo-model.js" type="text/javascript"></script>
	<!--   collection   -->
	<script src="js/collections/todo-collection.js" type="text/javascript"></script>
	<!--   view   -->
	<script src="js/views/todo-item-view.js" type="text/javascript"></script>
	<script src="js/views/todo-detail-item-view.js" type="text/javascript"></script>
	<script src="js/views/todo-detail-layout-view.js" type="text/javascript"></script>
	<script src="js/views/todo-composite-view.js" type="text/javascript"></script>
	<script src="js/views/todo-layout-view.js" type="text/javascript"></script>
	<!--   controller   -->
	<script src="js/routers/controller.js" type="text/javascript"></script>
	<!--   router   -->
	<script src="js/routers/router.js" type="text/javascript"></script>
	<!--   application   -->
	<script src="js/app.js" type="text/javascript"></script>
	<!--   entry point   -->
	<script src="js/main.js" type="text/javascript"></script>

</body>
</html>