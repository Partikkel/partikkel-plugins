(function () {
    function insertHtml() {
    	var timepass = false;
        var partikkelDiv = document.getElementById('partikkel-paid');

        if(!partikkelDiv) {
        	timepass = true;
        	var partikkelDiv = document.getElementById('partikkel-timepass');
        }
        
        if(partikkelDiv) {
            var html = '<div class="partikkel-fixed"><a href="https://www.partikkel.io/minside/">';
            html += timepass ? '<div class="partikkel-floating-timepass">' : '<div class="partikkel-floating">';
            html += '<img src="https://www.partikkel.io/images/logo-invert-transparent.svg" class="partikkel-icon" />';
            if(timepass) {
            	html += '<img src="https://www.partikkel.io/images/clock.svg" class="partikkel-clock" />';
            }
            html += '<span>BETALT</span></div></a></div>';

            partikkelDiv.innerHTML = html;	            
        }
    }
    setTimeout(insertHtml(), 1);
}());