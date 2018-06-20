<?php 
function stampa_cb_specializzazioni($array_specializzazioni,$quali_sel)
{
	if($quali_sel!="")
	{// trasformo la stringa di numeri separati dal ":" in un array
		$array_spec_lab = explode(":",$quali_sel);
	}
	
	foreach($array_specializzazioni as $chiave => $valore)
	{
		print "<input type=\"checkbox\" name=\"spec[]\" value=\"".$chiave."\"";
		if($quali_sel!="" && in_array($chiave,$array_spec_lab))
			print " checked";
		print "> ".$valore."<br />";
		
	}
}

function stampa_testo_specializzazioni($array_specializzazioni,$quali_sel,$carattere_pre)
{
	if($quali_sel!="")
	{// trasformo la stringa di numeri separati dal ":" in un array
		$array_spec_lab = explode(":",$quali_sel);
	}
	
	foreach($array_specializzazioni as $chiave => $valore)
	{
		if($quali_sel!="" && in_array($chiave,$array_spec_lab))
			print $carattere_pre.$valore."<br />";
	}
}

// PARAMETRO:	chiave citta
// RESTITUISCE:	[nome citta], [sigla provincia], [nome provincia esteso], [chiave regione], [nome regione esteso]
function dati_italia_dacitta($chiave_citta)
{
	$query = "select * from italia_citta inner join (italia_province inner join italia_regioni on italia_province.id_regione=italia_regioni.id_regione) on italia_citta.provincia=italia_province.sigla where italia_citta.id_citta=".$chiave_citta;
	$res = mysql_query($query);
	$row = mysql_fetch_array($res);
	
	$arr_risp = array();
	$arr_risp[0]=$row["citta"];
	$arr_risp[1]=$row["sigla"];
	$arr_risp[2]=$row["provincia"];
	$arr_risp[3]=$row["id_regione"];
	$arr_risp[4]=$row["regione"];
	
	return $arr_risp;
}

function nomeregione_daid($chiave_regione)
{
	$query_reg = "select regione from italia_regioni where id_regione=".$chiave_regione;
	$res_reg = mysql_query($query_reg);
	$row_reg = mysql_fetch_array($res_reg);
	return ($row_reg["regione"]);
}
?>