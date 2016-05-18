var Spinner = require('spin.js');

var Utils = {
	inIframe: function() {
        try {
            return window.self !== window.top;
        } catch (e) {
            return true;
        }
    },

	checkLocation: function (spinner) {
		var iFrame = document.getElementById('purchased-check');

        try {
    		if(iFrame.contentWindow.location.hostname === window.location.hostname) {
                return true;
    		}
        } catch (e) { // Nothing to do, probably no access to iFrame location due to cross-origin ..   
        }
        return false;
	},

    insertHtml: function () {
        if(this.inIframe()) {
            return;
        }
        var partikkelDiv = document.getElementById('partikkel-button-wrapper');
        var purchasedIFrame = document.getElementById('purchased-check');
        var returnUrl = (partikkelDiv.dataset && partikkelDiv.dataset.url) ? partikkelDiv.dataset.url : window.location;
        var buyUrl = 'https://test.partikkel.io/particket/access/?url=' + returnUrl;
        var checkUrl = 'https://test.partikkel.io/particket/checkaccess/?url=' + returnUrl;
        
        if(partikkelDiv) {        	
        	var html = '<div id="partikkel_buy_button" class="box-partikkel-buy"><a href="' + buyUrl + '" class="clearUnderline"></a></div>';      
            partikkelDiv.innerHTML = html;

            var spinner = new Spinner({});
            spinner.spin(partikkelDiv.firstChild);
            
            this.setIntervalX(this.checkLocation, spinner, 100, 20)
        }
        if(purchasedIFrame) {
            purchasedIFrame.src = checkUrl;
        }
    },

    setIntervalX: function (callback, spinner, delay, repetitions) {
        var x = 0;
        var buyButton = document.getElementById('partikkel_buy_button');

        var intervalID = window.setInterval(function () {

           var success = callback();

           if(success) {
                spinner.stop();
                buyButton.firstChild.innerText = 'LES NÅ';
                window.clearInterval(intervalID);
                return;
           }

           if (++x === repetitions) {
                spinner.stop();
                buyButton.firstChild.innerText = 'KJØP NÅ';
                window.clearInterval(intervalID);
           }
        }, delay);
    }
};

module.exports = Utils;
