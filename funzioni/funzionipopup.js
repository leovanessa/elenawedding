// JavaScript Document
var POINT=function(x,y)
{this.x=x||0;this.y=y||0;return this}; 

//funzione per aprire un pop up di fianco al link che lo apre
function apri_pop_up_foto(path, chiave, larghezza, altezza) {
	eval("document.getElementById('div_pop_up').style.visibility='hidden'")
	eval("document.getElementById('img_pop_up').style.visibility='hidden'")
	
	eval("document.getElementById('img_pop_up').src='"+path+"'")
	
	pos_top = parseInt(y)-(parseInt(altezza)/2)
	pos_left = parseInt(x)+larghezza
	eval("document.getElementById('div_pop_up').style.top='"+ pos_top +"px'")
	eval("document.getElementById('div_pop_up').style.left='"+ pos_left +"px'")

	eval("document.getElementById('div_pop_up').style.visibility='visible'")
	eval("document.getElementById('img_pop_up').style.visibility='visible'")
}

function getEventCoords(e){ 
	var rv=new POINT(e.pageX||e.clientX||0, e.pageY||e.clientY||0); 
	
	if (typeof e.pageX=="undefined"){ 
	 if (document.documentElement&& (document.documentElement.scrollTop||document.documentElement.scrollLeft)){ 
		 rv.x+=document.documentElement.scrollLeft; 
		 rv.y+=document.documentElement.scrollTop; 
	 }
	 else if (document.body&&(document.body.scrollTop||document.body.scrollHeight)){ 
		 rv.x+=document.body.scrollLeft; 
		 rv.y+=document.body.scrollTop; 
	 } 
	} 
	
	x=rv.x;
	y=(rv.y)+5;
}

