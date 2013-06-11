function showDiv(divName){
	if (document.getElementById(divName)){
		document.getElementById(divName).style.display = 
		(document.getElementById(divName).style.display == 'none') ? 'block' : 'none';
	}
}
