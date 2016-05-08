(function () {
    function inIframe () {
        try {
            return window.self !== window.top;
        } catch (e) {
            return true;
        }
    }

	function checkLocation() {
		var iFrame = document.getElementById('purchased-check');

        try {
    		if(iFrame.contentWindow.location.hostname === window.location.hostname) {
    			var buyButton = document.getElementById('partikkel_buy_button');
    			buyButton.childNodes[0].innerText = 'LES NÅ';
                window.clearTimeout(this.partikkelTimerId);
                return;
    		}
        } catch (e) { // Nothing to do, probably no access to iFrame location due to cross-origin ..   
        }

        if(this.partikkelCountdown === 0) {
            window.clearTimeout(this.partikkelTimerId);
        }
        else {
            this.partikkelCountdown--;
        }
	}

    function insertHtml() {
        if(inIframe()) {
            return;
        }
        var partikkelDiv = document.getElementById('partikkel-button-wrapper');
        var purchasedIFrame = document.getElementById('purchased-check');
        var returnUrl = partikkelDiv.dataset.url || window.location;
        var url = 'https://test.partikkel.io/particket/access/?url=' + returnUrl;
        
        if(partikkelDiv) {        	
        	var html = '<div id="partikkel_buy_button" class="box_partikkel_buy"><a href="' + url + '" class="clearUnderline">KJØP NÅ</a></div>';      
            partikkelDiv.innerHTML = html;
            this.partikkelCountdown = 20;
            this.partikkelTimerId = window.setInterval(checkLocation, 100);
        }
        if(purchasedIFrame) {
            purchasedIFrame.src = url;
        }
    }

    window.setTimeout(insertHtml, 1);
}());