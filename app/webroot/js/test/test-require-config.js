require.paths.mocha = 'lib/mocha';
require.paths.chai = 'lib/chai';
require.paths.sinon = 'lib/sinon-1.17.2';
require.shim.mocha = {
	init: function () {
		this.mocha.setup('bdd');
		return this.mocha;
	}
};
