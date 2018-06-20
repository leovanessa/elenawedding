<?php
//funzione per la cancellazione di un immagine se non condivisa
function cancella_imm($chiave_annuncio, $cont_fk_tabella)
{
	$query_md5="select cont_file_md5 from contenuti where cont_fk_tabella= ".$cont_fk_tabella." and cont_fk_record=".$chiave_annuncio.";";
	$ris = mysqli_query($con, $query_md5)or die("query recupero nome file già associato fallita");
	$row_md5=mysqli_fetch_array($ris);
	$cont_file_md5_vecchio=$row_md5["cont_file_md5"];
	$sql_con="select cont_chiave from contenuti where cont_file_md5 = '".$cont_file_md5_vecchio."' ;";
	$ris_con=mysqli_query($con, $sql_con) or die ("query controllo univocità file fallita");
	$num=mysqli_num_rows($ris_con);
	if ($num==1)
		{
			$sql_per="select contb_percorso_front from contenuti_tabelle where contb_chiave=".$cont_fk_tabella.";";
			$ris_per=mysqli_query($con, $sql_per)or die("query recupero percorso fallita");
			$row_per=mysqli_fetch_array($ris_per);
			$percorso = $row_per["contb_percorso_front"];
			crea_percorso($percorso);
			@unlink($percorso.$cont_file_md5_vecchio);
			@unlink($percorso."big/".$cont_file_md5_vecchio);
		}	

}

/* *********************************************************************************************************************************** */

// funzione per la sostituzione delgi apici e delle lettere particolari in codice HTML
function aggiusta_post($stringa)
{
	if(isset($stringa) && $stringa!="")
	{
		//$stringa=htmlentities($stringa,ENT_QUOTES);
		$stringa=str_replace("'", "&#39;",$stringa);		
		$stringa=nl2br($stringa);
	}
	else
		$stringa="";
			
	return $stringa;
}

// parametri= [id del record selezionato], [azione: "up" o "down"], [nome tabella], [nome campo chiave], [nome campo sorting], [condizioni di ricerca del massimo valore di sorting nella tabella]
function cambia_ordinamento_campi($chiave_record_sel, $azione, $nome_tabella, $nome_campo_chiave, $nome_campo_sorting, $condizione)
{
	$id = $chiave_record_sel;

	$fai_update=true;
	
	// Recupero il max valore di sorting
	$max_sorting = restituisci_max_sorting($nome_tabella, $nome_campo_sorting, $condizione);
	
	//recupero la posizione in elenco
	$querySort = "select * from ".$nome_tabella." where ".$nome_campo_chiave." = " . $id;
	$resSort = mysqli_query($con, $querySort);
	$array = mysqli_fetch_array($resSort);
	if ($azione == "up")
	{
		$sort = $array[$nome_campo_sorting] - 1;
		if($sort==0)
		{
			$sort = 1;
			$fai_update=false;
		}
		$sortaltro = $array[$nome_campo_sorting];
		$pos = $array[$nome_campo_sorting] - 1;
		$updatedue = "update ".$nome_tabella." set ".$nome_campo_sorting." = " . $sortaltro . " where ".$nome_campo_sorting." = " . $pos. " and ".$condizione;
	}
	else
	{
		$sort = $array[$nome_campo_sorting] + 1;
		if($sort > $max_sorting)
		{
			$sort = $max_sorting;
			$fai_update=false;
		}
		$sortaltro = $array[$nome_campo_sorting];
		$pos = $array[$nome_campo_sorting] + 1;
		$updatedue = "update ".$nome_tabella." set ".$nome_campo_sorting." = " . $sortaltro . " where ".$nome_campo_sorting." = " . $pos. " and ".$condizione;
	}
	
	//faccio l'update dei due record
	$updateuno = "update ".$nome_tabella." set ".$nome_campo_sorting." = " . $sort . " where ".$nome_campo_chiave." = " . $id;
	//print $updateuno."<br>";
	//print $updatedue;
	if($fai_update)
	{
		mysqli_query($con, $updatedue);
		mysqli_query($con, $updateuno);
	}
}

//funzione che cancella un'immagine, se presente e non condivisa
function cancella_immagine($con, $nome_tabella, $nome_campo_chiave, $valore_campo_chiave, $nome_campo_immagine, $path_immagine)
{
	// vedo se c'è l'immagine
	$query = "select ".$nome_campo_immagine." from ".$nome_tabella." where ".$nome_campo_chiave."=".$valore_campo_chiave;
	$res = mysqli_query($con, $query);
	
	while($row = mysqli_fetch_array($res))
			{
	
	if($row[$nome_campo_immagine]!="")
	{// controllo se l'immagine è condivisa
		$arr_imm_DB = explode(":",$row[$nome_campo_immagine]);
		if(!isset($arr_imm_DB[1]))
			$nome_cerca = $row[$nome_campo_immagine];
		else
			$nome_cerca = $arr_imm_DB[1];
			
		$query_controlla = "select * from ".$nome_tabella." where ".$nome_campo_chiave."<>".$valore_campo_chiave." and ".$nome_campo_immagine." like '%".$nome_cerca."%'";
		$res_controlla = mysqli_query($con, $query_controlla);
		$num_controlla = mysqli_num_rows($res_controlla);
		
		if($num_controlla==0)
			@unlink($path_immagine.$nome_cerca);
			
			//echo $path_immagine.$nome_cerca;
			
	}
	}
}

//funzione che cancella un'immagine, se presente e non condivisa
function cancella_contenuto($valore_chiave_contenuto)
{
	$query_files = "select * from contenuti inner join contenuti_tabelle on contenuti.cont_fk_tabella=contenuti_tabelle.contb_chiave where contenuti.cont_chiave=".$valore_chiave_contenuto;
	$res_files = mysqli_query($con, $query_files);
	
	$row_file = mysqli_fetch_array($res_files);
	// controllo se l'immagine è condivisa
	$nome_cerca = $row_file["cont_file_md5"];
	
	$query_controlla = "select * from contenuti where cont_chiave<>".$row_file["cont_chiave"]." and cont_file_md5 like '%".$nome_cerca."%' and cont_fk_tabella=".$row_file["cont_fk_tabella"];
	//print $query_controlla; 
	$res_controlla = mysqli_query($con, $query_controlla);
	$num_controlla = mysqli_num_rows($res_controlla);
	
	if($num_controlla==0)
	{
		@unlink($row_file["contb_percorso"].$nome_cerca);
		@unlink($row_file["contb_percorso"]."big/".$nome_cerca);		
	}
	// cancello anche l'eventuale immagine collegata al contenuto
	cancella_contenuto_2(7,$valore_chiave_contenuto);

	cancella_record("contenuti","cont_chiave","cont_sorting","cont_fk_record=".$row_file["cont_fk_record"]. " and cont_fk_tabella=".$row_file["cont_fk_tabella"],$row_file["cont_chiave"]);
}

//funzione che cancella un'immagine, se presente e non condivisa
function cancella_contenuto_2($valore_chiave_cont_tabelle, $valore_campo_chiave)
{
	$query_files = "select * from contenuti inner join contenuti_tabelle on contenuti.cont_fk_tabella=contenuti_tabelle.contb_chiave where contenuti.cont_fk_record=".$valore_campo_chiave." and contenuti.cont_fk_tabella=".$valore_chiave_cont_tabelle;
	//print $query_files;
	$res_files = mysqli_query($con, $query_files);
	
	while($row_file = mysqli_fetch_array($res_files))
	{
		// controllo se l'immagine è condivisa
		$nome_cerca = $row_file["cont_file_md5"];
		
		$query_controlla = "select * from contenuti where cont_chiave<>".$row_file["cont_chiave"]." and cont_file_md5 like '%".$nome_cerca."%' and cont_fk_tabella=".$row_file["cont_fk_tabella"];
		//print $query_controlla."<br>";
		$res_controlla = mysqli_query($con, $query_controlla);
		$num_controlla = mysqli_num_rows($res_controlla);
		
		if($num_controlla==0)
		{
			@unlink($row_file["contb_percorso"].$nome_cerca);
			@unlink($row_file["contb_percorso"]."big/".$nome_cerca);		
		}
		// cancello anche l'eventuale immagine collegata al contenuto
		cancella_contenuto_2(7,$row_file["cont_chiave"]);

		cancella_record("contenuti","cont_chiave","cont_sorting","cont_fk_record=".$row_file["cont_fk_record"]. " and cont_fk_tabella=".$row_file["cont_fk_tabella"],$row_file["cont_chiave"]);
	}
}

//funzione che cancella un record da una tbl
function cancella_record($nome_tabella, $nome_campo_chiave, $nome_campo_sorting, $condizioni_ordinamento, $id)
{
	if(isset($nome_campo_sorting) && $nome_campo_sorting!="")
	{
		// Recupero il max valore di sorting
		$max_sorting = restituisci_max_sorting($nome_tabella, $nome_campo_sorting, $condizioni_ordinamento." and ".$nome_campo_chiave." = ".$id);
		$query = "update ".$nome_tabella." set ".$nome_campo_sorting."=".$nome_campo_sorting."-1 where ".$condizioni_ordinamento." and ".$nome_campo_sorting.">".$max_sorting;
		//print $query;
		mysqli_query($con, $query);
	}
	$cancella = "delete from " . $nome_tabella . " where " . $nome_campo_chiave . " = " . $id;
	mysqli_query($con, $cancella);
}

function crea_codice_random_annuncio($lunghezza)
{
	$codicelungo = md5(rand() . date("F j, Y, g:i a s") . "ADIGIHGIDGHISGHOIDSHFOISDFJREIUWWIFVCUIGFDJDIDS");
	if($lunghezza=="")
		$lunghezza=18;
	$codicegiusto = substr($codicelungo, 0, $lunghezza);
	
	//controllo che sia univoco
	$select = "select us_aut_cod from usati where us_aut_cod = '" . $codicegiusto . "'";
	$res = mysqli_query($con, $select);
	
	$num = mysqli_num_rows($res);
	
	if ($num > 0)
	{
		crea_codice_random_annuncio();
	}
	
	mysqli_free_result($res);
	
	return $codicegiusto;
}

function crea_codice_random_utente($lunghezza)
{
	$codicelungo = md5(rand() . date("F j, Y, g:i a s") . "ADIGIHGIDGHISGHOIDSHFOISDFJREIUWWIFVCUIGFDJDIDS");
	if($lunghezza=="")
		$lunghezza=18;
	$codicegiusto = substr($codicelungo, 0, $lunghezza);
	
	//controllo che sia univoco
	$select = "select ut_aut_cod from utenti where ut_aut_cod = '" . $codicegiusto . "'";
	$res = mysqli_query($con, $select);
	
	$num = mysqli_num_rows($res);
	
	if ($num > 0)
	{
		crea_codice_random_utente();
	}
	
	mysqli_free_result($res);
	
	return $codicegiusto;
}

// funzione per creare immagine dimensionata, se è jpg e più grande delle dimensioni definite
function crea_immagine($percorso,$percorso_new,$nome_foto_senzaest,$tipofile,$WIDTH_DEFINITA,$HEIGHT_DEFINITA)
{
	//collegamento all'immagine uploadata
	$mainImage = imagecreatefromjpeg($percorso."/".$nome_foto_senzaest.".".$tipofile);
	
	//dimensioni dell'immagine uploadata
	$width = imagesx($mainImage);
	$height = imagesy($mainImage);
	
	if($width>$WIDTH_DEFINITA || $height>$HEIGHT_DEFINITA)
	{// ridimensioni l'immagine
		$rapporto = $width/$height;
			
		if($HEIGHT_DEFINITA=="")
			$HEIGHT_DEFINITA = ($WIDTH_DEFINITA/$rapporto); // se il parametro altezza è vuoto, l'immagine verrà creata con larghezza fissa e altezza in base alle proporzioni
		else if($WIDTH_DEFINITA=="")
			$WIDTH_DEFINITA = ($HEIGHT_DEFINITA*$rapporto); // se il parametro larghezza è vuoto, l'immagine verrà creata con altezza fissa e larghezza in base alle proporzioni
		
		$myThumb = imagecreatetruecolor($WIDTH_DEFINITA, $HEIGHT_DEFINITA);
	
		$rapporto_def=$WIDTH_DEFINITA/$HEIGHT_DEFINITA;
		
		if($rapporto_def >= $rapporto)
			crea_img_dabase($nome_foto_senzaest, $percorso, $percorso_new, $width, $height, $myThumb, $mainImage, $WIDTH_DEFINITA,$HEIGHT_DEFINITA);
		elseif($rapporto_def < $rapporto)
			crea_img_daaltezza($nome_foto_senzaest, $percorso, $percorso_new, $width, $height, $myThumb, $mainImage, $WIDTH_DEFINITA,$HEIGHT_DEFINITA);
			
		imagedestroy($myThumb);
		//unlink($percorso."/".$nome_foto_senzaest.".".$tipofile);
	}
	//faccio pulizia nelle immagini
	imagedestroy($mainImage);
	

	
}

//********************	funzione che crea la thumb tenendo fissa l'altezza		*********************************
function crea_img_daaltezza($nome_file, $percorso_foto, $percorso_foto_new, $width, $height, $myThumb, $mainImage, $WIDTH_DEFINITA,$HEIGHT_DEFINITA)
{
	$baseThumb=$WIDTH_DEFINITA;
	$hThumb=$HEIGHT_DEFINITA;

	//fisso l'altezza e recupero le dimensioni della thumb
	$w = ($hThumb * $width) / $height;
	//echo "base in proporzione: " . $w;
	$diff = ($w - $baseThumb) / 2;
	$x = $diff;
	
	$myThumb_temp = imagecreatetruecolor($w, $hThumb);
	
	//creo la prima immagine piccolina, rispettando le proporzioni
	$img_temp = $percorso_foto."/".$nome_file . "_tmp.jpg";
	
	imagecopyresampled($myThumb_temp, $mainImage, 0, 0, 0, 0, $w, $hThumb, $width, $height);
	imagejpeg($myThumb_temp, $img_temp, 100);
	
	//creo l'immagine delle dimensioni giuste, tagliando l'eccedenza!
	//$nuovonome = md5($nome_file);
	$img_thumb = $percorso_foto_new."/".$nome_file . ".jpg";
	imagecopyresampled($myThumb, $myThumb_temp, 0, 0, $x, 0, $baseThumb, $hThumb, $baseThumb, $hThumb);
	imagejpeg($myThumb, $img_thumb, 100);
	
	imagedestroy($myThumb_temp);
	
	//cancello l'immagine intermedia
	@unlink($img_temp);
}

//********************	funzione che crea la thumb tenendo fissa la base		*********************************
function crea_img_dabase($nome_file, $percorso_foto, $percorso_foto_new, $width, $height, $myThumb, $mainImage, $WIDTH_DEFINITA,$HEIGHT_DEFINITA)
{
	$baseThumb=$WIDTH_DEFINITA;
	$hThumb=$HEIGHT_DEFINITA;

	//fisso la base e recupero le dimensioni della thumb
	$h = ($baseThumb * $height) / $width;
	$diff = ($h - $hThumb) / 2;
	$y = $diff;
	
	$myThumb_temp = imagecreatetruecolor($baseThumb, $h);
	
	//creo la prima immagine piccolina, rispettando le proporzioni
	$img_temp = $percorso_foto."/".$nome_file . "_tmp.jpg";
	
	imagecopyresampled($myThumb_temp, $mainImage, 0, 0, 0, 0, $baseThumb, $h, $width, $height);
	imagejpeg($myThumb_temp, $img_temp, 100);
	
	
	//creo l'immagine delle dimensioni giuste, tagliando l'eccedenza!
	//$nuovonome = md5($nome_file);
	$img_thumb = $percorso_foto_new."/".$nome_file . ".jpg";
	imagecopyresampled($myThumb, $myThumb_temp, 0, 0, 0, $y, $baseThumb, $hThumb, $baseThumb, $hThumb);
	imagejpeg($myThumb, $img_thumb, 100);
	
	imagedestroy($myThumb_temp);
	
	//cancello l'immagine intermedia
	@unlink($img_temp);
}


function crea_percorso($path)
{
	if(!$path{strlen($path-1)} == "/")
		$path .= "/";
		
	$arr_path = explode("/",$path);
	for($i=0; $i<sizeof($arr_path); $i++)
	{
		if($arr_path[$i]!=".." && $arr_path[$i]!="")
		{
			$path_crea="";
			for($y=0; $y<=$i; $y++)
			{
				$path_crea .= $arr_path[$y]."/";
			}
			if (!is_dir($path_crea))
				@mkdir($path_crea, 0777); // creo la directory		
		}
	}
}
// funzioni che controlla se un valore passato è presente in una stringa con valori separati da [due punti]
function controlla_campi_duepunti($valore_da_cercare, $stringa_in_cui_cercare) {
	$arr = explode(":", $stringa_in_cui_cercare);
	$trovato=0;
	foreach($arr as $valore)
	{
		if($valore==$valore_da_cercare)
			$trovato++;
	}
	if($trovato>0)
		return true;
	else
		return false;
}


// funzione per criptare l'id del contenuto
function cripta_chiave($chiave) 
{
	 $codicecriptato = (($chiave * 2058171) + (2511 * 4452));
	 $temp = md5($codicecriptato);
	 $primameta = substr($temp, 0, 16);
	 $secondameta = substr($temp, 16, 31);
	 $codicecriptato = $primameta . $codicecriptato . $secondameta;
	 return $codicecriptato;
}

// funzione per de-criptare l'id del contenuto
function decripta_chiave($chiavecriptata)
{
	$finestr = strlen($chiavecriptata) - 32;
	$codice = substr($chiavecriptata, 16, $finestr);
	$chiave = ($codice - (2511 * 4452)) / 2058171;
	return $chiave;
}

function dimmi_tema_inhome($chiave_argomento)
{
	$query = "select * from temi_home where tmhm_tema_fk =" . $chiave_argomento ;
	$res = mysqli_query($con, $query);
	$num = mysqli_num_rows($res);
	if($num==0)
		$ritorno="no";
	else
		$ritorno="si";
	return $ritorno;
}

//********	funzione per stampare data in ita o eng, con mesi in lettere o numeri. $date deve essere nella forma 'YYYY-MM-DD', cioè date("Y-m-d"), con o senza i trattini				 ****************
function dimmi_data($date, $come, $lingua, $separatore) {
	$date = str_replace("-","",$date);
	$giorno=substr($date,6,2);
	$mese_cifra=substr($date,4,2);
	$anno=substr($date,0,4);
	
	if($come!="cifre")
	{
		if($lingua=="ita")
			$arr_mesi= array ('Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre');
		else if($lingua=="eng")
			$arr_mesi=array ('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

		if($giorno{0}==0)
			$giorno=$giorno{1};

		if($mese_cifra{0}==0)
			$mese_cifra=$mese_cifra{1};
		$indice=$mese_cifra-1;
		$mese=$arr_mesi[$indice];
		
		if($come=="testo_short")
			$mese=substr($mese,0,3);
	}
	else
		$mese=$mese_cifra;
	
	if($lingua=="ita")
		return $giorno.$separatore.$mese.$separatore.$anno;
	else
		return $anno.$separatore.$mese.$separatore.$giorno;
}

// $date deve essere in un formato di data in Inglese
function dimmi_giorno_settimana($date, $come, $lingua) 
{
	if($lingua=="ita")
		$arr_giorni_settimana= array ('Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato');
	else
		$arr_giorni_settimana= array ('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Wednesday', 'Friday', 'Saturday');
		
	$str_time=strtotime($date);
	$int_giorno = date("w",$str_time);
	
	$giorno=$arr_giorni_settimana[$int_giorno];
	
	if($come=="testo_short")
		$giorno=substr($giorno,0,3);
	
	return $giorno;
}

//funzione che mi restituisce le dimensioni di una immagine
function dimmi_dimensioni_img($perc)
{
	$myImage = imagecreatefromjpeg($perc);
	$width = imagesx($myImage);
	$height = imagesy($myImage);
	
	imagedestroy($myImage);
	
	return $width . ":" . $height;
}


function dimmi_nome_autore($chiave_autore)
{
	$query = "select * from utenti where ut_chiave =" . $chiave_autore ;
	$res = mysqli_query($con, $query);
	$row = mysqli_fetch_array($res);
	$nome_cognome=$row["ut_nome"]."&nbsp;".$row["ut_cognome"];
	
	if($row["ut_tipo"]==1)
		$color="red";
	else
		$color="black";
	return "<span style=\"color: ".$color."\">".$nome_cognome."</span>";
}


function dimmi_nome_autore_front($chiave_autore)
{
	$query = "select * from utenti where ut_chiave=".$chiave_autore ;
	$res = mysqli_query($con, $query);
	$row = mysqli_fetch_array($res);
	$nome_cognome=ucfirst($row["ut_nome"])."&nbsp;".ucfirst($row["ut_cognome"]);
	
	return $nome_cognome;
}

//********************	Funzione che restituisce il nome di un file, senza l'estensione
function dimminomefile($file)
{
	$file_name= basename($file);  //restituisce il nome del file
	
	$lunghezzastringa= strlen ($file_name);
	$posizionedelpunto = strpos($file_name,".");
	$nomefile=substr($file_name,0,$posizionedelpunto);
	
	return $nomefile;
}

// funzione per scrivere il nome di un sito, data la stringa conprendente l'http://
function dimmi_nome_sito($str_link)
{
	$pos_spezza_inizio=strpos($str_link,"//");
	$str_link=substr($str_link,($pos_spezza_inizio+2),strlen($str_link));
	//adesso la stringa inizia da dopo il '//'
	$pos_spezza_fine=strpos($str_link,"/");
	if($pos_spezza_fine===false)
		$pos_spezza_fine = strlen($str_link);
	$str_link=substr($str_link,0,($pos_spezza_fine));
	//adesso la stringa finisce prima del primo '/'
	//print $str_link;
	return $str_link;
}

function dimmi_nome_tipo_utente($tipo_utente)
{
	if($tipo_utente==1)
		$str = "Amministratori";
	else
		$str = "Collaboratori";
		
	return $str; 
}

function dimmi_mime_file($tipofile)
{
	$query_tipo = "select * from files_tipi where fl_tipo='".$tipofile."'";
	$res_tipo = mysqli_query($con, $query_tipo);
	if(mysqli_num_rows($res_tipo)==0)
		$ritorno = false;
	else
	{	
		$row_tipo = mysqli_fetch_array($res_tipo);
		$ritorno = $row_tipo["fl_mime"];
	}	
	return $ritorno;
}

function dimmi_titolo_tema($chiave_categoria)
{
	$query = "select * from temi where tm_chiave =" . $chiave_categoria ;
	$res = mysqli_query($con, $query);
	$num = mysqli_num_rows($res);
	if($num==0)
		$ritorno=false;
	else
	{
		$row = mysqli_fetch_array($res);
		$ritorno = $row["tm_titolo"];
	}
	return $ritorno;
}

function dimmi_quante_news_home()
{
	$query = "select * from news_quante_home";
	$res = mysqli_query($con, $query);
	$num = mysqli_num_rows($res);
	if($num==0)
		$ritorno=5;
	else
	{
		$row = mysqli_fetch_array($res);
		$ritorno = $row["nw_quante"];
	}
	return $ritorno;
}

function formatta_data($data, $come, $lingua, $separatore_data, $separatore_ora)
{
	$data = str_replace("-","",$data);
	$data = str_replace(":","",$data);
	
	$giorno=substr($data,6,2);
	$mese_cifra=substr($data,4,2);
	$anno=substr($data,0,4);

	$ora=substr($data,8,2);
	$minuti=substr($data,10,2);
	$secondi=substr($data,12,2);
	
	if($come!="cifre")
	{
		if($lingua=="ita")
		{
			if($come=="testo_long")
				$mesi= array ('Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre');
			else
				$mesi=array ('Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic');
		}
		else
		{
			if($come=="testo_long")
				$mesi=array ('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
			else
				$mesi=array ('Jan', 'Feb', 'Mar', 'Apr', 'Mayg', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		}
		if($mese_cifra{0}==0)
			$mese_cifra=$mese_cifra{1};
		$indice=$mese_cifra-1;
		$mese=$mesi[$indice];
	}
	else
		$mese=$mese_cifra;
	
	$oggi=date("Ymd");
	$giorno_ieri=date("d")-1;
	if($giorno_ieri<10)
		$giorno_ieri = "0".$giorno_ieri;
	$ieri=date("Ym").$giorno_ieri;

	if(substr($data,0,8) == $oggi)
		$data_return = "Oggi ";
	else if(substr($data,0,8) == $ieri)
		$data_return = "Ieri ";
	else
	{
		if($lingua=="ita")
			$data_return = $giorno.$separatore_data.$mese.$separatore_data.$anno;
		else
			$data_return = $anno.$separatore_data.$mese.$separatore_data.$giorno;
	}
	
	if(isset($ora) && isset($minuti) && isset($secondi) && $separatore_ora!="no")
		$data_return .= " ".$ora.$separatore_ora.$minuti.$separatore_ora.$secondi;
		
	return $data_return;
}


//funzione per creare a random una stringa
function GetRandomString($length) 
{
	$template="1234567890abcdefghijklmnopqrstuvwxyz1234567890abcdefghijklmnopqrstuvwxyz";
	$rndstring="";
	
	settype($length, "integer");
	settype($rndstring, "string");
	settype($a, "integer");
	settype($b, "integer");
	
	for ($a = 0; $a < $length; $a++) {
	   $b = rand(0, strlen($template) - 1);
	   $rndstring .= $template[$b];
	}
	
	return $rndstring;
}

function inserimento_univoco($tabella,$codizioni)
{
	$query = "select * from ". $tabella . " where " . $codizioni ;
	//print $query;

	$res = mysqli_query($con, $query);

	$num = mysqli_num_rows($res);
	
	if($num==0)
		return true;
	else
		return false;
}

//funzione anti-spamming
function preprocessHeaderField($value)
{
  $spamming = "no";
  $valore = $value;
  
  //Remove line feeds
  if(is_numeric(strpos($value,"\\r")))
  {
	$spamming = "si";
  }
  if(is_numeric(strpos($value,"\\n")))
  {
	$spamming = "si";
  }
	$ret = str_replace("\\r", "", $value);
	$ret = str_replace("\\n", "", $ret);
  //Remove injected headers
  $find = array("/bcc\:/i",
				"/Content\-Type\:/i",
				"/Mime\-Type\:/i",
				"/cc\:/i",
				"/to\:/i");
	$lunprima = strlen ($ret);
	
	$ret = preg_replace($find, "", $ret);
	
	$lundopo = strlen ($ret);
	
	if($lunprima>$lundopo)
	{
		$spamming = "si";
	}

	if($spamming=="si")
	{
			 $headers  = "MIME-Version: 1.0\n";
			 $headers .= "Content-type: text/html; charset=iso-8859-1\n";		 
			 $headers .= "From: msavojardo@webworking.it <msavojardo@webworking.it>\nReply-To: msavojardo@webworking.it\n";
			 
			 $subject = "Spamming su portale jazz";
			 $messaggio = $valore;
		 mail ("msavojardo@webworking.it", $subject, $messaggio, $headers);
	}
	
  return $ret;
}

/* **************** FUNZIONE CHE RESTITUISCE IL MAX VALORE DI SORTING				*********************** */
function restituisci_max_sorting($nome_tabella, $nome_campo, $condizioni) {
	$query = "SELECT MAX(".$nome_campo.") FROM ".$nome_tabella." WHERE ".$condizioni;
	$res = mysqli_query($con, $query);
	$risp = mysqli_fetch_array($res);

	return($risp[0]);
}

function stampa_importanza($chiave_imp)
{
	$query = "select * from importanza where i_chiave=" . $chiave_imp ;
	$res = mysqli_query($con, $query);
	$row = mysqli_fetch_array($res);
	
	return "<span style=\"" .$row["i_stile"]. "\">" .$row["i_descrizione"]. "</span>";
}

// parametri: [chiave], [chiave tabella], [larghezza], [altezza], [stile], [pop-up]
function stampa_immagine_contenuto($chiave, $chiave_tabella, $larghezza, $altezza, $stile, $link_pop_up)
{
	$query_cont_img = "select * from contenuti inner join contenuti_tabelle on contenuti.cont_fk_tabella=contenuti_tabelle.contb_chiave where contenuti.cont_fk_record=".$chiave." and cont_fk_tabella=".$chiave_tabella;
	//print $query_cont_img;
	$res_cont_img = mysqli_query($con, $query_cont_img);
	$row_cont_img = mysqli_fetch_array($res_cont_img);
	if($row_cont_img["cont_file_md5"] != "")
		$immagine_associata=true;
	else
		$immagine_associata=false;
	
	$url_imm = $row_cont_img["contb_percorso_front"].$row_cont_img["cont_file_md5"];
	if($immagine_associata && is_file($url_imm))
	{
		//$url_imm = $row_cont_img["contb_percorso_front"].$row_cont_img["cont_file_md5"];
		$estensione = tipo_file($row_cont_img["cont_file_md5"]);
		$larghezza = $larghezza;
		$altezza_big = $larghezza;

		if($estensione=="jpg" || $estensione=="jpeg")
		{
			$arr_dim = explode(":",dimmi_dimensioni_img($url_imm));
			if(isset($arr_dim[0]) && $arr_dim[0]<$larghezza)
			{
				$larghezza = $arr_dim[0];
				$alt = $arr_dim[1];
			}
			else
				$alt = 70;
		}
		// calcolo la dimensione dell'immagine grande
		$url_imm_big = $row_cont_img["contb_percorso_front"]."big/".$row_cont_img["cont_file_md5"];
		if(is_file($url_imm_big))
		{
			if($estensione=="jpg" || $estensione=="jpeg")
			{
				$arr_dim = explode(":",dimmi_dimensioni_img($url_imm_big));
				if(isset($arr_dim[0]))
					$altezza_big = $arr_dim[1];
				else
					$altezza_big = 200;
			}
		}

		/* se l'immagine è troppo grande setto l'altezza a 350 affinchè il pop-up sia posizionato non troppo in alto*/
		if($altezza_big>700)
			$altezza_big=350;
		if(is_file($url_imm))
		{
			$stampa = "";
			if(is_file($url_imm_big) && isset($link_pop_up) && $link_pop_up=="si")
				$stampa .= "<a href=\"#\"  onClick=\"getEventCoords(event); apri_pop_up_foto('".$url_imm_big."','".$row_cont_img["cont_chiave"]."', 5, ".$altezza_big."); return false; /* il return false impedisce alla pagina di tornare in alto*/ \">";
			$stampa .=  "<img src=\"".$url_imm."\" border=\"0\" width=\"".$larghezza."\" alt=\"".$row_cont_img["cont_titolo"]."\" title=\"".$row_cont_img["cont_titolo"]."\" style=\"".$stile."\">";
			if(is_file($url_imm_big) && isset($link_pop_up) && $link_pop_up=="si")
				$stampa .=  "</a>";
			return $stampa;				
		}
		else
			return "niente";
	}
	else
		return "";
}

// funzione per stampare menu a tendina anni auto-aggiornante
function stampa_option_anni($anno_partenza, $num_anni_futuri, $campo_evidenziare) {
	$anno_attuale = date("Y");
	$codice="<option value=\"\"></option>";
	/*if($campo_evidenziare=="")
		$campo_evidenziare=date("Y");*/

	for($i=$anno_partenza; $i<=$anno_attuale+$num_anni_futuri; $i++)
	{
		$codice .= "<option value=\"$i\"";
		if(strtolower($campo_evidenziare)==strtolower($i))
			$codice .= " selected=\"selected\"";
		$codice .= ">$i</option>";
	}
	print $codice;
}

function stampa_option_data($end, $campo_evidenziare) {
	$codice="<option value=\"\"></option>";
	
	/*if($campo_evidenziare=="")
	{
		if($end==31)
			$campo_evidenziare=date("d");
		else
			$campo_evidenziare=date("m");
	}*/
		
	for($i=1; $i<=$end; $i++)
	{
		if($i<10)
			$zero = "0";
		else
			$zero = "";
		$codice .= "<option value=\"".$zero.$i."\"";
		if($campo_evidenziare==$i)
			$codice .= " selected=\"selected\"";
		$codice .= ">".$zero.$i."</option>";
	}
	print $codice;
}

//********************	funzione che restituisce l'estensione di un file		*********************************
function tipo_file($userfile_name)
{
	$file_name= basename($userfile_name);  //restituisce il nome del file
	
	$lunghezzastringa= strlen ($file_name);
	$posizionedelpunto = strpos($file_name,".");
	$nomefile=substr($file_name,0,$posizionedelpunto);
	$ncaratteri = ($lunghezzastringa - 1 - $posizionedelpunto);
	$tipofile=substr($file_name,$posizionedelpunto + 1,$ncaratteri);  //tipo del file caricato
	
	$tipofile = strtolower($tipofile);
	
	return $tipofile;
}

// parametri: [nome_tabella], [nome_campo_chiave], [valore_campo_chiave], [nome_campo_sorting], [condizioni]
function update_sorting_altri($nome_tabella, $nome_campo_chiave, $valore_campo_chiave, $nome_campo_sorting, $condizioni)
{
	// recupero il valore di sorting del record
	$query = "select ".$nome_campo_sorting." from ".$nome_tabella." where ".$nome_campo_chiave."=".$valore_campo_chiave;
	$res = mysqli_query($con, $query);
	if(mysqli_num_rows($res)==0)
	{// il record è già stato cancellato
		$query_sorting="update ".$nome_tabella." set ".$nome_campo_sorting."=".$nome_campo_sorting."-1 where ".$condizioni;
	}
	else
	{
		$arr = mysqli_fetch_array($res);
		$query_sorting="update ".$nome_tabella." set ".$nome_campo_sorting."=".$nome_campo_sorting."-1 where ".$condizioni." and ".$nome_campo_sorting." > ".$arr[$nome_campo_sorting];
	}
	//print $query_sorting; 
	mysqli_query($con, $query_sorting);
}

?>