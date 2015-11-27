require([
	'mocha',
	'chai',
	'marionette',
], function (mocha, chai) {
	require([
		'js/test/tests.js'
	], function () {
		chai.should();
		mocha.run();
	});
});
