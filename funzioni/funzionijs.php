<script language="javascript" type="text/javascript">
<!--

//FUNZOINI PER IL CONTROLLO DEI MODULI
//funzione che viene richiamata per gestire i controlli al submit dei moduli
function controllaeinviamodulo(nomemodulo,arcampicontrollo)
{
	controllo="yes";
	var i = 0;
	while(i<arcampicontrollo.length && controllo=="yes")
	{
		valuecampo=arcampicontrollo[i];
		arvalori=valuecampo.split(":");
		if(arvalori[0]!="-" && arvalori[2]!="dataobbligatoria" && arvalori[2]!="controlladata" && arvalori[2]!="controlloradiobutton" && arvalori[2]!="obbligatoriomenuatendina")
		{
			if(document.forms[nomemodulo].elements[arvalori[0]].disabled==true)
			{
				document.forms[nomemodulo].elements[arvalori[0]].disabled=false;
				document.forms[nomemodulo].elements[arvalori[0]].style.border="1px solid #04c5fa";
				document.forms[nomemodulo].elements[arvalori[0]].disabled=true;
			}
			else
			{
				document.forms[nomemodulo].elements[arvalori[0]].style.border="1px solid #04c5fa";
			}
		}
		
		switch (arvalori[2])
		{
			case "obbligatorio":
				controllo = obbligatorio(nomemodulo,arvalori[0],arvalori[1]);
				break;
			case "obbligatoriomenuatendina":
				controllo = obbligatoriomenuatendina(nomemodulo,arvalori[0],arvalori[1]);
				break;
			case "file_obbligatorio":
				controllo = file_obbligatorio(nomemodulo,arvalori[0],arvalori[1]);
				break;
			case "controllaprivacy":
				controllo = controllaprivacy(nomemodulo,arvalori[0],arvalori[1]);
				break;
			case "dataobbligatoria":
				controllo = dataobbligatoria(nomemodulo,arvalori[0],arvalori[1]);
				break;
			case "controlladata":
				controllo = controlladata(nomemodulo,arvalori[0],arvalori[1]);
				break;
			case "controlloradiobutton":
				controllo = controlloradiobutton(nomemodulo,arvalori[0],arvalori[1]);
				break;
			case "matricola":
				controllo = controllamatricola(nomemodulo,arvalori[0],arvalori[1]);
				break;
			case "verificaemail":
				controllo_obbl = obbligatorio(nomemodulo,arvalori[0],arvalori[1]);
				if(controllo_obbl=="yes")
				{
					controllo = verificaemail(nomemodulo,arvalori[0],arvalori[1]);
				}
				else
				{
					controllo = "no";
				}
				break;
			case "verificaemail_nonobbligatoria":
				if(document.forms[nomemodulo].elements[arvalori[0]].value!="" && document.forms[nomemodulo].elements[arvalori[0]].value!=null)
				{
					controllo = verificaemail(nomemodulo,arvalori[0],arvalori[1]);
				}
				break;
			case "verificapassword":
				controllo = verificapassword(nomemodulo,arvalori[0],arvalori[1]);
				break;
			case "controllapwd":
				if(document.forms[nomemodulo].acc[0].checked==true)
				{
					controllo = obbligatorio(nomemodulo,'password','password');
				}
				else
				{
					controllo="yes";
				}
				break;
			default:
				alert("errore");
				return false;
				break;
		}
		i++;
	}
	
	if(controllo=="yes")
	{
		return true;
	}
	else
	{
		if(arvalori[0]!="-")
		{
			if(arvalori[2]=="dataobbligatoria" || arvalori[2]=="controlladata")
			{
				document.forms[nomemodulo].elements[arvalori[0] + "_giorno"].focus();
			}
			else if(arvalori[2]!="controlloradiobutton")
			{
				document.forms[nomemodulo].elements[arvalori[0]].style.border="1px solid red";
				document.forms[nomemodulo].elements[arvalori[0]].focus();
			}
		}
		return false;
	}
}
function controllamatricola(nomeform,nomecampo,nomeetichetta)
{
	if((document.forms[nomeform].elements[nomecampo].value=="" || document.forms[nomeform].elements[nomecampo].value==null))
	{
		alert("Il campo '" + nomeetichetta + "' deve contenere 4 caratteri numerici!");
		return "no";
	}
	else
	{
		var stringa = document.forms[nomeform].elements[nomecampo].value;
		var espressione = new RegExp("[0-9][0-9][0-9][0-9]");
		if ((stringa.length!=4)||(!espressione.test(stringa)))
		{
			alert("Il campo '" + nomeetichetta + "' deve contenere 4 caratteri numerici!");
			return "no";
		}
		else
		{
			return "yes";
		}
	}
}

//campo obbligatorio
function obbligatorio(nomeform,nomecampo,nomeetichetta)
{
	tipocampo=document.forms[nomeform].elements[nomecampo].type;

	switch(tipocampo)
	{
		case "select-one":
			return obbligatoriomenuatendina(nomeform,nomecampo,nomeetichetta);
			break;
		default:
			return obbligatoriotext(nomeform,nomecampo,nomeetichetta);
			break;
	}
	if(document.forms[nomeform].elements[nomecampo].value=="" || document.forms[nomeform].elements[nomecampo].value==null)
	{
		alert("Il campo '" + nomeetichetta + "' è obbligatorio!");
		return "no";
	}
	else
	{
		return "yes";
	}
}

function obbligatorio_condizioni(nomeform,nomecampo,nomeetichetta,campo_condizione,valore_campo_condizione)
{
	/*tipocampo=document.forms[nomeform].elements[nomecampo].type;
	switch(tipocampo)
	{
		case "select-one":
			return obbligatoriomenuatendina(nomeform,nomecampo,nomeetichetta);
			break;
		default:
			return obbligatoriotext(nomeform,nomecampo,nomeetichetta);
			break;
	}*/
	if((document.forms[nomeform].elements[nomecampo].value=="" || document.forms[nomeform].elements[nomecampo].value==null) && document.forms[nomeform].elements[campo_condizione].value==valore_campo_condizione )
	{
		alert("Il campo '" + nomeetichetta + "' è obbligatorio!");
		return "no";
	}
	else
	{
		return "yes";
	}
}

//campo obbligatorio
function file_obbligatorio(nomeform,nomecampo,nomeetichetta)
{
	nomecampo_old=nomecampo+"_old"
	tipocampo=document.forms[nomeform].elements[nomecampo].type;

	if((document.forms[nomeform].elements[nomecampo].value=="" || document.forms[nomeform].elements[nomecampo].value==null) && (document.forms[nomeform].elements[nomecampo_old].value==null || document.forms[nomeform].elements[nomecampo_old].value==""))
	{
		alert("Il campo '" + nomeetichetta + "' è obbligatorio!");
		return "no";
	}
	else
	{
		return "yes";
	}
}

function file_obbligatorio_condizione(nomeform,nomecampo,nomeetichetta,campo_condizione,valore_campo_condizione)
{
	nomecampo_old=nomecampo+"_old"

	if((document.forms[nomeform].elements[nomecampo].value=="" || document.forms[nomeform].elements[nomecampo].value==null) && document.forms[nomeform].elements[campo_condizione].value==valore_campo_condizione)
	{
		alert("Il campo '" + nomeetichetta + "' è obbligatorio!");
		return "no";
	}
	else
	{
		return "yes";
	}
}

function controllaprivacy(nomeform,nomecampo,testoalert)
{
	if(document.forms[nomeform].elements[nomecampo].checked==false)
	{
		alert(testoalert);
		return "no";
	}
	else
	{
		return "yes";
	}
}

function obbligatoriotext(nomeform,nomecampo,nomeetichetta)
{
	if(document.forms[nomeform].elements[nomecampo].value=="" || document.forms[nomeform].elements[nomecampo].value==null)
	{
		alert("Il campo '" + nomeetichetta + "' è obbligatorio!");
		return "no";
	}
	else
	{
		return "yes";
	}
}

function obbligatoriomenuatendina(nomeform,nomecampo,nomeetichetta)
{
	if(document.forms[nomeform].elements[nomecampo].selectedIndex==0)
	{
		alert("Il campo '" + nomeetichetta + "' è obbligatorio!");
		return "no";
	}
	else
	{
		return "yes";
	}
}

//data obbligatoria
function dataobbligatoria(nomeform,nomecampo,nomeetichetta)
{
	if(document.forms[nomeform].elements[nomecampo + "_giorno"].selectedIndex==0 && document.forms[nomeform].elements[nomecampo + "_mese"].selectedIndex==0 && document.forms[nomeform].elements[nomecampo + "_anno"].selectedIndex==0)
	{
		alert("Il campo '" + nomeetichetta + "' è obbligatorio!");
		return "no";
	}
	else
	{
		return controlladata(nomeform,nomecampo,nomeetichetta);
	}
}

//controllo dalla correttezza della data
function controlladata(nomeform,nomecampo,nomeetichetta)
{
	if(document.forms[nomeform].elements[nomecampo + "_giorno"].selectedIndex==0 && document.forms[nomeform].elements[nomecampo + "_mese"].selectedIndex==0 && document.forms[nomeform].elements[nomecampo + "_anno"].selectedIndex==0)
	{
		return "yes";
	}
	else if(document.forms[nomeform].elements[nomecampo + "_giorno"].selectedIndex==0 || document.forms[nomeform].elements[nomecampo + "_mese"].selectedIndex==0 || document.forms[nomeform].elements[nomecampo + "_anno"].selectedIndex==0)
	{
		alert("Il campo '" + nomeetichetta + "' non è completo!");
		return "no";
	}
	else
	{
		return controllavaliditadata(nomeform,nomecampo,nomeetichetta);
	}
}

//funzione che controlla la validità di una data
function controllavaliditadata(nomeform,nomecampo,nomeetichetta)
{
	gg=document.forms[nomeform].elements[nomecampo + "_giorno"].selectedIndex;
	if(gg<10)
	{
		gg="0" + gg;
	}
	
	mm=document.forms[nomeform].elements[nomecampo + "_mese"].selectedIndex;
	if(mm<10)
	{
		mm="0" + mm;
	}
	
	aa=document.forms[nomeform].elements[nomecampo + "_anno"].selectedIndex+1999;
	
	strdata=gg+"/"+mm+"/"+aa;
	
	data = new Date(aa,mm-1,gg);
	
	daa=data.getFullYear().toString();
	dmm=(data.getMonth()+1).toString();
	dmm=dmm.length==1?"0"+dmm:dmm
	dgg=data.getDate().toString();
	dgg=dgg.length==1?"0"+dgg:dgg
	dddata=dgg+"/"+dmm+"/"+daa
	if(dddata!=strdata)
	{
      alert("Verificare il campo '" + nomeetichetta + "'");
	  return "no";
	}
	else
	{
		return "yes";
	}
}

//controllo formattazione di un campo mail obbligatorio
function verificaemail(nomeform,nomecampo,nomeetichetta)
{
	if ((document.forms[nomeform].elements[nomecampo].value.indexOf('@')==-1)|| (document.forms[nomeform].elements[nomecampo].value.indexOf('.')==-1))
	{
	 alert("Il campo '" + nomeetichetta + "' non è valido!");
	 return "no";
	}
	else
	{
		valorecampo = document.forms[nomeform].elements[nomecampo].value;
		pos_at = valorecampo.indexOf('@');
		pos_punto = valorecampo.indexOf('.', pos_at);
		len_mail = valorecampo.length;
		
		pos_dominio = pos_punto-pos_at;
		pos_dominiodue = len_mail - pos_punto;
		
		if(pos_at < 2 || (pos_dominio <= 2) || (pos_dominiodue <= 2))
		{
		 alert("Il campo '" + nomeetichetta + "' non è valido!");
		 return "no";
		}
		else
			return "yes";
	}
}


//controllo che venga selezionato almeno un radiobutton
function controlloradiobutton(nomeform,nomecampo,nomeetichetta)
{
	oggetto=document.forms[nomeform].elements[nomecampo];
	nselezionati=0;
	if(isNaN(oggetto.length))
	{
		if(oggetto.checked)
		{
			nselezionati++;
		}
	}
	else
	{
		finoa=oggetto.length;
		for(i=0;i<finoa;i++)
		{
			if(oggetto[i].checked)
			{
				nselezionati++;
			}
		}
	}
	
	if(nselezionati==0)
	{
		alert("Non hai selezionato '" + nomeetichetta + "'!");
		return "no";
	}
	else
	{
		return "yes";
	}
}

//funzione per il controllo dell'inserimetno di una nuova password
function verificapassword(nomeform,nomecampo,nomeetichetta)
{
	if(document.forms[nomeform].elements[nomecampo].value!=document.forms[nomeform].elements[nomecampo + "2"].value)
	{
		alert("La Password di controllo è diversa da quella inserita!");
		return "no";
	}
	else
	{
		return "yes";
	}
}


//FINE FUNZOINI PER IL CONTROLLO DEI MODULI
//*****************************************

//funzione per aprire un pop up di fianco al link che lo apre
function apripopup(oggetto, url, w, h)
{
	valoretop=getPageOffsetTop(oggetto);
	valoreleft=getPageOffsetLeft(oggetto)+oggetto.offsetWidth+20;
	window.open(url, '', 'width=' + w + ', height=' + h + ', top=' + valoretop + ', left=' + valoreleft + ',status=no,location=no,toolbar=no,menubar=no,scrollbars=no');
}

//funzioni correlate per calcoalre le posizioni del link
function getPageOffsetTop(el) {

  var y;

  // Return the x coordinate of an element relative to the page.

  y = el.offsetTop;
  if (el.offsetParent != null)
    y += getPageOffsetTop(el.offsetParent);

  return y;
}

function getPageOffsetLeft(el) {

  var x;

  // Return the x coordinate of an element relative to the page.

  x = el.offsetLeft;
  if (el.offsetParent != null)
    x += getPageOffsetLeft(el.offsetParent);

  return x;
}


//funzione per la gestione nelle news in home page
function settaarginhome(azione,nesima)
{
	if(azione=="rimuovi")
	{
		document.frm.action='argomenti_inhome.asp?rimuovi=' + nesima;
		document.frm.submit();
	}
	else if(azione=="scegli")
	{
		document.frm.action='argomenti_seleziona_elenco.asp?scegli=' + nesima;
		document.frm.submit();
	}
	else
	{
		return false;
	}
}

/* FUNZIONI MIE */
function costruisci_campo_da_select(nome_form,nome_campo) {
	nome_select = eval(nome_campo + "_select")
	with(document.nome_form)
	{
		var valore_campo=""
		for (i=0;i<eval("nome_select.length");i++)
		{
			if(eval("nome_select.options["+i+"].selected"))
				valore_campo = valore_campo+":"+ eval("nome_select.options["+i+"].value")
		}
		// elimino il primo ":"
		valore_campo = valore_campo.substr(1, valore_campo.length)
		// assegno il valore al campo nascosto
		eval("nome_campo.value")=valore_campo
	}
}


function muoviti(pagina)
{
	document.frm_ricerca.action = pagina;
	document.frm_ricerca.submit();
}




-->
</script>
