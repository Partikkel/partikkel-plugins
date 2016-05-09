var domready = require('domready'),
	utils = require('./utils.js');

//module.exports = function () {
//    window.setTimeout(Utils.insertHtml(), 1);
//};

domready(function () {
    //module.exports = function () {
    	utils.insertHtml();
    //};
});