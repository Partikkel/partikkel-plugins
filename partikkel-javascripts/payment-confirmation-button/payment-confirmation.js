(function () {
    function insertHtml() {
        var partikkelDiv = document.getElementById('partikkel-paid');
        
        if(partikkelDiv) {
            var html = '<div class="partikkel-fixed"><a href="https://www.partikkel.io/minside/"><div class="partikkel-floating"><img src="https://www.partikkel.io/images/logo-invert.svg" class="partikkel-icon" /><span>BETALT</span></div></a></div>';
            partikkelDiv.innerHTML = html;
        }
    }
    setTimeout(insertHtml(), 1);
}());