<!--
function stampa_data()
{
	var data = new Date();
	
	mese = data.getMonth();
	if(parseInt(mese)<9)
		mese = "0" + mese

	data_da_stampare = data.getDate() + " | " + mese + " | " + data.getFullYear() + " &nbsp;&nbsp;h. " + data.getHours() + "." + data.getMinutes();
	document.getElementById('cella_data').innerHTML = data_da_stampare
	
	setTimeout(stampa_data,60000)
}
//-->
