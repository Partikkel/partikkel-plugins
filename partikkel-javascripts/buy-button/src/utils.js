var Spinner = require('spin.js');

var Utils = {
	inIframe: function() {
        try {
            return window.self !== window.top;
        } catch (e) {
            return true;
        }
    },

	checkLocation: function () {
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
        var partikkelNoPriceDiv = document.getElementById('partikkel-button-noprice-wrapper');
        var purchasedIFrame = document.getElementById('purchased-check');
        var articelUrl = (partikkelDiv && partikkelDiv.dataset && partikkelDiv.dataset.url) ? partikkelDiv.dataset.url : window.location;
        var buyUrl = 'https://test.partikkel.io/particket/access/?url=' + articelUrl;
        var checkUrl = 'https://test.partikkel.io/particket/checkaccess/?url=' + articelUrl;
        var priceUrl = 'https://test.partikkel.io/api/open/article/url?url=' + articelUrl;
        
        if(partikkelDiv) {        	
        	var html = '<a id="partikkel_buy_button" class="partikkel_buy_button with_price" href="' + buyUrl + '" />';  
            partikkelDiv.innerHTML = html; 
            var spinner = new Spinner({});
            spinner.spin(partikkelDiv.firstChild);          
        }
        if(partikkelNoPriceDiv) {
            var html = '<a id="partikkel_buy_button_plain" class="partikkel_buy_button with_price" href="' + buyUrl + '" />';  
            partikkelNoPriceDiv.innerHTML = html;
            var spinner = new Spinner({});
            spinner.spin(partikkelNoPriceDiv.firstChild);
        }
        if (partikkelDiv || partikkelNoPriceDiv) {            
            this.setIntervalX(this.checkLocation, this.checkPrice, spinner, priceUrl, 100, 20);

            if(purchasedIFrame) {
                purchasedIFrame.src = checkUrl;
                purchasedIFrame.style.visibility = 'hidden';
            }
        }
    },

    setIntervalX: function (callback, checkPrice, spinner, priceUrl, delay, repetitions) {
        var x = 0;
        var buyButton = document.getElementById('partikkel_buy_button');
        var buyButtonPlain = document.getElementById('partikkel_buy_button_plain');

        var intervalID = window.setInterval(function () {

           var success = callback();

           if(success) {
                spinner.stop();
                if(buyButton) {
                    buyButton.innerText = 'LES NÅ';
                }
                if(buyButtonPlain) {
                    buyButtonPlain.innerText = 'LES NÅ';
                }
                window.clearInterval(intervalID);
                return;
           }

           if (++x === repetitions) {
                spinner.stop();
                if(buyButton) {
                    checkPrice(priceUrl, buyButton);
                }
                if(buyButtonPlain) {
                    buyButtonPlain.innerText = 'KJØP NÅ';
                }
                window.clearInterval(intervalID);
           }
        }, delay);
    },

    checkPrice: function(url, buyButton) {
        var xmlhttp;

        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == XMLHttpRequest.DONE ) {
                if(xmlhttp.status == 200){
                    buyButton.innerText = 'KJØP (' + JSON.parse(xmlhttp.response).price + ' kr)';
                }               
                else {
                    buyButton.innerText = 'KJØP NÅ';
                }
            }
        }

        xmlhttp.open("GET", url, true);
        xmlhttp.setRequestHeader('Content-Type', 'application/json');
        xmlhttp.send();
    }
};

module.exports = Utils;
