(function () {
    function insertHtml() {
        var partikkelDiv = document.getElementById('partikkel-paid');
        
        if(partikkelDiv) {
            var html = '<div class="partikkel-fixed"><a href="https://test.partikkel.io/minside/"><div class="partikkel-floating"><img src="https://test.partikkel.io/images/logo-invert.svg" class="partikkel-icon" /><span>BETALT</span></div></a></div>';
            partikkelDiv.innerHTML = html;
        }
    }
    setTimeout(insertHtml(), 1);
}());