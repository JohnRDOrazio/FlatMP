<?PHP

/****************************************************************************************************/
/*   FlatMP Include - Plugin di FlatMP              	                                            */
/*   ==================================================                                             */
/*                                                                                                  */
/*   Copyright (c) 2009 by ZEB-DEMON                                                                */
/*   http://www.ZEB-DEMON.info                                                                      */
/*                                                                                                  */
/*   This program is free software. You can redistribute it and/or modify it under the terms of     */
/*   the GNU General Public License (ver.2) as published by the Free Software Foundation.           */
/****************************************************************************************************/

global $_FN;

// imposto il nome della sezione
$section=find_section("FlatMP");

// importo i file necessari
include_once("sections/$section/mp_functions.php");

// verifica l'esistenza della cartella mailboxes altrimenti la crea
if (!file_exists($_FN['datadir']."/mailboxes"))
	mkdir($_FN['datadir']."/mailboxes");
	
// verifica l'esistenza della cartella dell'utente altrimenti la crea
if (!file_exists($_FN['datadir']."/mailboxes/".$_FN['user']))
	mp_first($_FN['user'],$_FN['admin']);
	
// verifica l'esistenza del file delle opzioni altrimenti lo crea
if (!file_exists($_FN['datadir']."/mailboxes/".$_FN['user']."/mp_config.php")&&$_FN['user']!="")
	mp_config($_FN['user'],$section,$_FN['datadir']);
	
// visualizzo il plugin nel blocco	
if((user_can_view_section($section)))
		echo "&#187;&nbsp;<a href=\"index.php?mod=".$section."\">"._MP."  (".mp_count($_FN['user'],$_FN['datadir']).")</a>";
else
		echo "<br />"._LOGINMP;
?>
<br>
<br>