<?PHP

/****************************************************************************************************/
/*   FlatMP - Modulo per FlatNuke                    	                                              */
/*   ==================================================                                             */
/*                                                                                                  */
/*   Copyright (c) 2009 by ZEB-DEMON                                                                */
/*   http:// www.ZEB-DEMON.info                                                                     */
/*                                                                                                  */
/*   Flatnux adaptation by LWANGAMAN                                                                */
/*   http://johnrdorazio.altervista.org                                                             */
/*                                                                                                  */
/*   This program is free software. You can redistribute it and/or modify it under the terms of     */
/*   the GNU General Public License (ver.2) as published by the Free Software Foundation.           */
/****************************************************************************************************/

# previene che blocco sia eseguito direttamente e redirige a index.php
if ( strpos(strtolower($_SERVER['SCRIPT_NAME']),strtolower(basename(__FILE__))) )
{
	header("Location: ../../index.php");
	die("...");
}

/*
$req=$_SERVER["REQUEST_URI"];

if(strstr($req,"myforum="))
	die(_NONPUOI); 
*/

// importo i file necessari
echo "<script type=\"text/javascript\" src=\"/sections/".$_FN['vmod']."/mp_scripts.js\"></script>";
include_once("sections/".$_FN['vmod']."/mp_functions.php");
				
/****************************************************************************************************/
/**********************************************FLAT-MP***********************************************/
/****************************************************************************************************/

if(isset($_POST))
   $postMB=&$_POST;			// a partire dalla 4.1.0
else
   $postMB=&$HTTP_POST_VARS ;	// fino alla 4.1.0
 
$mailbox=isset($postMB['mailbox']) ? $postMB['mailbox'] : $mailbox=isset($_GET['mailbox']) ? $_GET['mailbox'] : "Inbox";   //la cartella in cui mi trovo
$page=isset($postMB['page']) ? $postMB['page'] : $page=isset($_GET['page']) ? $_GET['page'] : 1;   //la cartella in cui mi trovo

// alcune variabili
$id = isset($_GET['id']) ? stripslashes(htmlspecialchars($_GET['id'])) : "";
$file=$_FN['datadir']."/mailboxes/".$_FN['user']."/$mailbox/$id.php";
$folder=$_FN['datadir']."/mailboxes/".$_FN['user']."/$mailbox";

// upgrade messaggi xml in php
$mbox = opendir($folder); 

while ($mp = readdir($mbox))
	{
		if (($mp != ".") && ($mp != "..") && (substr($mp, -4, 4) == ".xml"))
			{
				$mp_old=$folder."/".$mp;
				$id=substr($mp,0,-4);			
				$mp_new=$folder."/".$id.".php";
								
				rename($mp_old,$mp_new);
				@unlink($mp);
			}
	}
closedir($mbox);

$op=isset($_GET['op']) ? $_GET['op'] : null;
switch($op) // inizio opzioni
{

/**********************************************/
/********************MAIN PAGE*****************/
/**********************************************/

default :

main_page($mailbox,$_FN['vmod'],$_FN['user'],$page,$_FN['datadir']); // stampa pagina principale

break; // end main page

/**********************************************/
/********************MP OPTZ*******************/
/**********************************************/

case "mp_optz":

mp_optz($mailbox,$_FN['vmod'],$_FN['user'],$_FN['datadir']); // opzioni FlatMP

break; // end mp_optz

/**********************************************/
/******************MP OPTZ SAVE****************/
/**********************************************/

case "mp_optz_save":

mp_optz_save($_FN['vmod'],$_FN['user'],$_FN['datadir']); // salva opzioni FlatMP

break; // end mp_optz_save

/**********************************************/
/**********************MP ADD******************/
/**********************************************/

case "mp_add":
 
mp_add($_FN['vmod'],$_FN['user'],$mailbox); // inserisce un nuovo messaggio

break; // end mp_add

/**********************************************/
/**********************MP SEND*****************/
/**********************************************/

case "mp_send":

mp_send($_FN['vmod'],$_FN['datadir'],$mailbox); // invia un messaggio
	
break; // end mp_send

/**********************************************/
/*********************MP REPLY*****************/
/**********************************************/

case "mp_reply":

mp_reply($file,$_FN['vmod'],$_FN['user'],$mailbox); // risponde ad messaggio

break; // end mp_reply

/**********************************************/
/********************MP FORWARD****************/
/**********************************************/

case "mp_forward":

mp_forward($file,$_FN['vmod'],$_FN['user'],$mailbox); // inoltra un messaggio

break; // end mp_forward

/**********************************************/
/*********************MP READ******************/
/**********************************************/

case "mp_read":

mp_read($id,$file,$_FN['vmod'],$mailbox); // legge un messaggio

break; // end mp_read

/**********************************************/
/********************MP DELETE*****************/
/**********************************************/

case "mp_delete":

mp_delete($file,$_FN['vmod'],$mailbox); // cancella un messaggio

break; // end mp_delete

/**********************************************/
/********************MP EMPTY******************/
/**********************************************/

case "mp_empty":

mp_empty($folder,$_FN['vmod'],$mailbox); // cancella tutti i messaggi di una cartella

break; // end mp_empty

/**********************************************/
/**********************CP ADD******************/
/**********************************************/

case "cp_add":

cp_add($_FN['vmod'],$_FN['user']); // inserisce nuova cartella personale

break; // end cp_add

/**********************************************/
/********************CP RENAME*****************/
/**********************************************/

case "cp_rename":

mp_move($_FN['vmod'],$_FN['user']); // rinomina una cartella ad un'altra

break; // end cp_move

/**********************************************/
/********************CP CREATE*****************/
/**********************************************/

case "cp_create":

cp_create($_FN['vmod'],$_FN['user'],$folder,$_FN['datadir']); // crea o rinomina cartella personale

break; // end cp_create

/**********************************************/
/********************CP DELETE*****************/
/**********************************************/

case "cp_delete":

cp_delete($folder,$_FN['vmod']); // cancella una cartella personale

break; // end cp_delete

/**********************************************/
/*********************CP MOVE******************/
/**********************************************/

case "cp_move":

mp_move($_FN['user'],$folder1,$folder2,$_FN['datadir']); // sposta tutti i file di una cartella ad un'altra

break; // end cp_move

} // Switch

//module_copyright("FlatMP","1.1.0 beta","ZEB-DEMON","zeb.demon@gmail.com", "www.ZEB-DEMON.info", "Gpl version 2.0");
?>