# Partikkel kjøpsknapp

## Bruker-guide

* Legg til partikkel_buy_button.css på web-side
* Legg til partikkel_buy_button.js på bunnen av web-siden
* Legg til div-tag med id 'partikkel-button-wrapper'. Dersom div-tag blir lagt på annen side enn artikkelen, f.eks. på betalingsmurside, så må artikkel-url inkluderes i data-attributt:
`<div id="partikkel-button-wrapper" data-url="http://www.avisa.no/artikkelen.html" />`
* Legg til iFrame-tag. Her foregår sjekk om artikkel er kjøpt av bruker
`html<iFrame id="purchased-check" width="0" height="0"/>`

Script og css er også tilgjengelig her: https://www.partikkel.io/external/buttons/

## Bygging av script
* 'npm install'
* 'gulp'
* Javascript og css skal ligge klarti dist-folder. NB! Miljøspesifikk bygging er ikke på plass, så output er hardkodet med testmiljø-adresser.
