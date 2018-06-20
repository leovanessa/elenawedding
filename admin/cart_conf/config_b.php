<?php
//define("PATH_ASSOLUTA", "http://127.0.0.1/comprensorio_alpino/");
define("PATH_ASSOLUTA", "http://www.wwprogetti.com/CAT02/");

define("EMAIL_INFO_PORTALE","roberto_brogi@libero.it");
//define("EMAIL_INFO_PORTALE","info@mondomanfredi.com");

define('FPDF_FONTPATH1','font/');
define('FPDF_FONTPATH','../font/');



define("NOME_PORTALE", "Casabella");
define("NOME_PORTALE_BACK", "CAT2 - Area Amministrativa");
define("NOME_PORTALE_WEB", "sito.com");
define("ANAGRAFICA_PORTALE","Comprensorio Alpino T02 - Anagrafica");

define("TITLE_PORTALE_PRE", "..: ");
define("TITLE_PORTALE", "Casabella");
define("TITLE_PORTALE_POST", " :..");

define("NEWS_WIDTH", 165);
define("NEWS_HEIGHT", 100);

define("GALLERY_WIDTH", 218);
define("GALLERY_HEIGHT", 150);

define("HOME_PAGE_WIDTH", 674);
define("HOME_PAGE_HEIGHT", 500);

define("IMMAGINI_PICCOLE_WIDTH", 75);
define("IMMAGINI_PICCOLE_HEIGHT", 40);



define("MAX_SIZE_UPLOAD_MByte",ini_get("upload_max_filesize"));
define("MAX_SIZE_UPLOAD_Byte",ini_get("upload_max_filesize")*1024*1024);

define("SC_NOVITA","new!");


// definisco la testata ed il piede delle email
define("COLORE_TABELLA","#71b4ea");
$email_testata = "";
$email_piede = "";

$email_testata .= "<html><head><style type=\"text/css\">a {font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal;	font-style: normal;	line-height: normal; color:#666666; text-decoration:underline;}</style></head>\n<body style=\"background-color:#FFFFFF; font-family: Arial, Helvetica, sans-serif; font-size:11px;	font-weight:normal;	font-style: normal;	line-height: normal; color:#666666; margin:0px; padding:2px 3px 2px 3px;\">\n";
$email_testata .= "<table width=\"600\" cellpadding=\"0\" cellspacing=\"0\" border=0>\n";
	$email_testata .= "<tr><td><a href=\"".PATH_ASSOLUTA."/index.php\" target=\"_blank\" title=\"".NOME_PORTALE." - Vai al Sito\"><img src=\"".PATH_ASSOLUTA."img/header_email.jpg\" width=\"600\" border=\"0\" alt=\"".NOME_PORTALE." - Vai al Sito\" ></a></td></tr>";
	$email_testata .= "<tr><td width='100%' style='border:1px solid ".COLORE_TABELLA."; padding:6px 4px;'>";
		$email_testata .= "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"4\">";

				$email_piede .= "\n<tr><td height=\"10\"></td></tr><tr><td style=\"font-size:10px; color:#CCCCCC; text-align:center; border-top:1px solid #b1b5c1; padding:12px 0px 0px 0px;\">".ANAGRAFICA_PORTALE."<br></td></tr>";
		$email_piede .= "</table></td></tr>";
	$email_piede .= "</table>";
$email_piede .= "</body></html>";

define("EMAIL_TESTATA",$email_testata);
define("EMAIL_PIEDE",$email_piede);

// definisco i meta per l'head dell'html
$meta_head = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
$meta_head .= "<meta name=\"author\" content=\"roberto brogi - www.roberto_brogi.it - izzusano@libero.it\">\n";
$meta_head .= "<META name=\"Description\" content=\"\">\n";
$meta_head .= "<META name=\"Keywords\" content=\"\">\n";
$meta_head .= "<meta name=\"DC.title\" content=\"CATO2\">\n";
$meta_head .= "<META NAME=\"robots\" CONTENT=\"INDEX,FOLLOW\">\n";
$meta_head .= "<META NAME=\"robots\" CONTENT=\"ALL\">\n";
$meta_head .= "<meta name=\"copyright\" content=\"Copyright ©2008, CATO2\">\n";
$meta_head .= "<meta http-equiv=\"Content-Language\" content=\"it\">\n";
//$meta_head .= "<link REL=\"SHORTCUT ICON\" HREF=\"img/favicon.ico\">\n";
//$meta_head .= "<link rel=\"icon\" href=\"img/favicon.ico\" />\n";
 
define("META_HEAD",$meta_head);

?>