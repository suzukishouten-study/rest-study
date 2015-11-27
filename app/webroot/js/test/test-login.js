define(function (require) {
	var UserModel = require('models/user-model');

	var userModel = new UserModel();
	return function login(done){
		if (!userModel.isLoggedIn()) {
			userModel.login(
				'yamada',   //DB登録データに合わせる
				'test',		//DB登録データに合わせる
				function success(message) {
					console.info("logged in!")
					done();
				},
				function error(message) {
					done('login error! ' + '\n' + message);
				}
			)
		} else {
			//ログイン済みなら終了
			console.info('ログイン済み');
			done();
		}
	}
});

