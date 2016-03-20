(function () {
    function insertHtml() {
        var partikkelDiv = document.getElementById('partikkel-paid');
        
        if(partikkelDiv) {
            var html = '<div class="partikkel-fixed"><div class="partikkel-floating"><a href="https://test.partikkel.io/minside/"><img src="https://test.partikkel.io/images/logo-invert.svg" class="partikkel-icon" /></a><span>BETALT</span></div></div>';
            partikkelDiv.innerHTML = html;
        }
    }
    setTimeout(insertHtml(), 1);
}());