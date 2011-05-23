<?PHP

/****************************************************************************************************/
/*   FlatMP Functions - Funzioni di FlatMP            	                                            */
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

// funzione che conta i messaggi non letti
function mp_count($myforum,$datadir)
	{
		$mp=0;
		$handle = opendir("$datadir/mailboxes/$myforum/Inbox");
	
		while ($file = readdir($handle))
			{
				if (!( $file=="." OR $file==".." ))
					{
						$fp=fopen("$datadir/mailboxes/$myforum/Inbox/$file","r");
						$string=fread($fp,filesize("$datadir/mailboxes/$myforum/Inbox/$file"));
						fclose($fp);
						$read=get_xml_element("fm:read",$string);
						if($read==0)
							$mp+=1;
					}
			}	
		closedir($handle);
		return($mp);
	}
// funzione che modifica il file delle opzioni
function mp_optz_save($modname,$myforum,$datadir)
	{
		$fp=fopen("$datadir/mailboxes/$myforum/mp_config.php","w");

		fwrite($fp,"<"."?xml version='1.0' ?".">
<fm:options>
	<fm:mail>".$_POST['mail']."</fm:mail>
  <fm:mexpp>".$_POST['mexpp']."</fm:mexpp>
</fm:options>");
		fclose($fp);
		
		echo "<br /><br /><br /><b>"._ASPETTAMP."</b>";
		$time=1;
		header("Refresh:".$time.";URL=index.php?mod=$modname");
	}
// funzione che crea il file delle opzioni
function mp_config($myforum,$section,$datadir)
	{
		copy("sections/$section/mp_config.php","$datadir/mailboxes/$myforum/mp_config.php");
	}
// funzione che crea il messaggio di benvenuto, le cartelle utente e il file delle opzioni
function mp_first($myforum,$admin,$section,$datadir)
	{
		mkdir("$datadir/mailboxes/$myforum");
		mkdir("$datadir/mailboxes/$myforum/Inbox");
		mkdir("$datadir/mailboxes/$myforum/Outbox");

		global $_FN;
    $myid=time();
		$mytitle=_BENVENUTOMP; 
		$mysender=$admin;
		$myrecipient=$myforum;
		$mybody=_PRIMOMP;
    $modname=$section;
	
		// scrivo il msg di benvenuto per il nuovo utente	
		mp_create("$myforum/Inbox",$myid,0,$mysender,$myrecipient,$mytitle,$mybody,$modname,$_FN['datadir']);
	}

// funzione che ricava gli utenti dal flat-database
function users_list()
	{ 
    $db = new XMLDatabase("fndatabase","misc");
    $query = "SELECT username FROM users ORDER BY username";
    $userlist = $db->Query($query);
		// effettuo il listing dell'array ordinato
		$list="";
    foreach($userlist as $utente) {
			$list.="<option>".$utente['username']."</option>";
			}
		return ($list);
	}

// funzione che stampa la main page
function main_page($mailbox,$modname,$myforum,$page,$datadir)
	{
		// preparo la lista messaggi
		$folder="$datadir/mailboxes/$myforum/$mailbox";
		$handle=opendir($folder);
    $modlist = array();
    global $_FN;
    $fuso_orario = $_FN['jet_lag'];
    
		while($file=readdir($handle))
			{
				if (!($file=="." or $file=="..") and (!preg_match("/^\./",$file) and ($file!="CVS")))
					{
						$modlist[]=$file;
					}
			}
		closedir($handle);

		rsort($modlist);
		
		// Calcolo il numero di pagine
		if (!file_exists("$datadir/mailboxes/{$_FN['user']}/mp_config.php"))
			mp_config($_FN['user'],$modname,$datadir);
		
		$string=get_file("$datadir/mailboxes/{$_FN['user']}/mp_config.php");
		$num_mp=get_xml_element("fm:mexpp",$string);
		
		$tot_mp=sizeof($modlist);  // ho dovuto correggere, altrimenti se sizeof($modlist)=1, then $tot_mp = 0!; 
		$modulo=0;
		if(($tot_mp%$num_mp)>0)
			$modulo=1;
		$pages=intval($tot_mp/$num_mp)+$modulo;
		$a = $page*$num_mp;
    $b = $a-($num_mp-1);
    if($page==$pages){ $a=$tot_mp; }    
    $mexstr = "<span style='font-weight:bold;'>".$b." - ".$a."</span> of <span style='font-weight:bold;'>".$tot_mp."</span>";
    $pagstr = "<span>Page ".$page." of ".$pages." </span>";

 		if($mailbox=="Inbox"){
			echo "<div id='mp-mailboxtitle'>"._RICEVUTIMP."</div>";
      $inbox = "<span style='color:#000000;font-weight:bold;'>"._RICEVUTIMP."</span><span style='font-weight:bold;'> (".mp_count($_FN['user'],$_FN['datadir']).")</span>";
      $outbox = "<a href=\"index.php?mod=".$modname."&amp;mailbox=Outbox\" style='color:#FFFFFF;font-weight:bold;'>"._INVIATIMP."</a>";
      }
		else{
			echo "<div id='mp-mailboxtitle'>"._INVIATIMP."</div>";
      $inbox = "<a href=\"index.php?mod=".$modname."&amp;mailbox=Inbox\" style='color:#FFFFFF;font-weight:bold;'>"._RICEVUTIMP."<span style='font-weight:bold;'> (".mp_count($_FN['user'],$_FN['datadir']).")</span></a>";
      $outbox = "<span style='color:#000000;font-weight:bold;'>"._INVIATIMP."</span>";
      }
?>
<div id="mp-wrap">
  <div id="mp-left-col">
<?php 
  echo "<div class='mp-left-col-menu-item' style='height:60px;'></div>";
  echo "<div class='mp-left-col-menu-item'><button class='mp-button' onclick='location.href=\"index.php?mod=$modname&amp;op=mp_add&amp;mailbox=$mailbox\"'>"._INVIAMP."</button></div>";
  echo "<div class='mp-left-col-menu-item ".(($mailbox=="Inbox") ? "mp-menu-item-selected" : "" )."'>".$inbox."</div>";
  echo "<div class='mp-left-col-menu-item ".(($mailbox=="Outbox") ? "mp-menu-item-selected" : "" )."'>".$outbox."</div>";
?>
  </div>
  <div id="mp-right-col">
<?php
		echo "<div id=\"mptable-header\">"; 
      echo "<div style=\"width:5%;\" class=\"mptable-header-element notthisone\"><input type=\"checkbox\" id=\"mpcheckall\"></div>";
      echo "<div class=\"mptable-header-element\"><button class='mp-button' onclick=\"location.reload();\">"._AGGIORNAMP."</button></div>";
      echo "<div class=\"mptable-header-element\"><button class='mp-button' onclick=\"if(confirm('"._SICUROTUTTIMP."')){location.href='index.php?mod=$modname&amp;op=mp_empty&amp;mailbox=$mailbox'}\">"._ELIMINATUTTIMP."</button></div>";
      echo "<div class=\"mptable-header-element\" style=\"float:right;padding:3px;\"><a href=\"index.php?mod=$modname&amp;op=mp_optz&amp;mailbox=$mailbox\"><img alt='"._OPZIONI1MP."' src='images/mp/gear-29340234.png' /></a></div>";
      //echo "<div class=\"mptable-header-element\">".substr(_RISPONDIMP, 0, 1)."</div>";
      //echo "<div class=\"mptable-header-element\">".substr(_INOLTRAMP, 0, 1)."</div>";
      //echo "<div class=\"mptable-header-element\">".substr(_ELIMINAMP, 0, 1)."</div>";    
  		echo "<div style='clear:both;text-align:right;padding:10px;'>";
  		if($page>1){
  			echo "<a href='index.php?mod=$modname&amp;mailbox=$mailbox&amp;page=1' style='color:Blue;'> &#60;&#60; Newest </a><span> | </span>";
  			echo "<a href='index.php?mod=$modname&amp;mailbox=$mailbox&amp;page=".($page-1)."' style='color:Blue;'> &#60; Newer </a><span> | </span>";
        }
      echo $mexstr; //$pagstr;
      if($page<$pages){
  			echo "<span> | </span><a href='index.php?mod=$modname&amp;mailbox=$mailbox&amp;page=".($page+1)."' style='color:Blue;'> Older &#62; </a>";
  			echo "<span> | </span><a href='index.php?mod=$modname&amp;mailbox=$mailbox&amp;page=".($pages)."' style='color:Blue;'> Oldest &#62;&#62; </a>";
        }
      echo "</div>";
    echo "</div>";
    
    echo "<table id=\"mptable\">";
		echo "<tr>";
    echo "<td style=\"width:5%;\"></td>";   //checkbox
		echo "<td style=\"width:22%;\"></td>";  //DESTINATARIO-MITTENTE
		echo "<td style=\"width:45%;\"></td>";  //OGGETTO
		echo "<td style=\"width:22%;\"></td>";  //DATA-ORA
		echo "<td style=\"width:2%;\"></td>";   //R
		echo "<td style=\"width:2%;\"></td>";   //I
		echo "<td style=\"width:2%;\"></td>";   //E
		echo "</tr>";
					
    if(sizeof($modlist)>0){
      $x=0;
  		for ($i=0+($num_mp*($page-1)); $i<($num_mp*$page) && $i<sizeof($modlist); $i++)
  			{
  				$string=get_file("$datadir/mailboxes/$myforum/$mailbox/$modlist[$i]");
  				$read=get_xml_element("fm:read",$string);
  				$title=get_xml_element("fm:title",$string);
  				$sender=get_xml_element("fm:sender",$string);
  				$recipient=get_xml_element("fm:recipient",$string);
  				$body=get_xml_element("fm:body",$string);
  				$id=str_replace(".php","",$modlist[$i]);
  				$file="$datadir/mailboxes/$myforum/$mailbox/$modlist[$i].php";
          $readstyle = $read==0 ? "font-weight:bold;" : "";
  				$readclass = $read==0 ? "unread" : "read";
          echo "<tr class='mex-line $readclass' id='mod&#61;$modname&amp;op&#61;mp_read&amp;id&#61;$id&amp;mailbox&#61;$mailbox'>"; 
          echo "<td><input type=\"checkbox\" id=\"check$x\" class=\"mpcheckbox\"></td>";
          ++$x;
  				if($mailbox=="Inbox")
  					echo "<td style='$readstyle' class='mex-click'>$sender</td>";
  				else
  					echo "<td style='$readstyle' class='mex-click'>To: $recipient</td>";	
  				echo "<td style='$readstyle' class='mex-click'>$title</td>";
  				echo "<td style='$readstyle' class='mex-click'>".date("d/",$id+(3600*$fuso_orario)).date("m/",$id+(3600*$fuso_orario)).date("Y - ",$id+(3600*$fuso_orario)).date("H:",$id+(3600*$fuso_orario)).date("i",$id+(3600*$fuso_orario))."</td>";
  				echo "<td style='text-align:center;'><a href=\"index.php?mod=$modname&amp;op=mp_reply&amp;id=$id&amp;mailbox=$mailbox\"><img src=\"images/mp/reply.png\" border=\"0\"></a></td>";
  				echo "<td style='text-align:center;'><a href=\"index.php?mod=$modname&amp;op=mp_forward&amp;id=$id&amp;mailbox=$mailbox\"><img src=\"images/mp/forward.png\" border=\"0\"></a></td>";
  				$pagevar = ($page!=0) ? "&amp;page=".$page : ""; 
          echo "<td style='text-align:center;'><a href=\"index.php?mod=$modname&amp;op=mp_delete&amp;id=$id&amp;mailbox=$mailbox".$pagevar."\" onclick=\"return confirm('"._SICURO1MP."')\"><img src=\"images/mp/delete.png\" border=\"0\"></a></td>";
  				echo "</tr>";
  			}
    }
		echo "</table>";
?>
  </div>
</div>
<?php
		echo "<div style='clear:both;text-align:right;padding:10px;margin:10px 0px;'>";
		if($page>1){
			echo "<a href='index.php?mod=$modname&amp;mailbox=$mailbox&amp;page=1' style='color:Blue;'> &#60;&#60; Newest </a><span> | </span>";
			echo "<a href='index.php?mod=$modname&amp;mailbox=$mailbox&amp;page=".($page-1)."' style='color:Blue;'> &#60; Newer </a><span> | </span>";
      }
    echo $mexstr; //$pagstr;
    if($page<$pages){
			echo "<span> | </span><a href='index.php?mod=$modname&amp;mailbox=$mailbox&amp;page=".($page+1)."' style='color:Blue;'> Older &#62; </a>";
			echo "<span> | </span><a href='index.php?mod=$modname&amp;mailbox=$mailbox&amp;page=".($pages)."' style='color:Blue;'> Oldest &#62;&#62; </a>";
      }
    echo "</div>";
	}

// funzione per visualizzare le opzioni
function mp_optz($mailbox,$modname,$myforum,$datadir)
	{
		$string=get_file("$datadir/mailboxes/$myforum/mp_config.php");
		
		$mail=get_xml_element("fm:mail",$string);
    $mexpp=get_xml_element("fm:mexpp",$string);
    		
		if ($mail==1)
			{
				$check_mail1="checked='checked'";
				$check_mail0="";
			}
		else
			{
				$check_mail1="";
				$check_mail0="checked='checked'";
			}

		echo "<div style='padding:20px;'>";
		echo "<form name='mp_config_MPSAVE' method='post' action='index.php?mod=".$modname."&amp;op=mp_optz_save'>";
    echo "<fieldset>";
		echo "<legend>"._OPZIONI2MP."</legend>";
		echo "<span>"._OPTMAIL."</span><br />";
    echo "<label for='mailyes'>"._MPYES."</label><input id='mailyes' name='mail' type='radio' value='1' ".$check_mail1.">";
		echo "<label for='mailno'>"._MPNO."</label><input id='mailno' name='mail' type='radio' value='0' ".$check_mail0.">";
		echo "<br /><br />";
    echo "<label for='mexpp'>Messaggi per pagina: </label><input id='mexpp' name='mexpp' value='$mexpp' type='number' min='2' />";
    echo "</fieldset>";
		echo "<input type='submit' value="._MPSAVE.">";
		echo "</form>";
		echo "</div>";
		echo "<img src=\"images/mp/back.png\" border=\"0\">&nbsp;<a href=\"index.php?mod=$modname&amp;mailbox=$mailbox\">"._INDIETROMP."</a>";
	}
// funzione per inviare nuovo messaggio
function mp_add($modname,$myforum,$mailbox)
	{
		$rec="list";
		$obj="";
		$txt="";
		mp_fill($modname,$myforum,$rec,$obj,$txt,$mailbox);
	}
// funzione (javascript) che controlla che titolo e corpo messaggio non siano vuoti
 ?>
<script type="text/javascript" language="javascript">
	function validate()
		{
			if(document.getElementsByName('title')[0].value=='')
				{
					alert('<?php echo _MANCAOGGETTOMP ?>');
					document.getElementsByName('title')[0].focus();
					return false;
				}
			if(document.getElementsByName('mp-body')[0].value=='')
				{
					alert('<?php echo _MANCATESTOMP ?>');
					document.getElementsByName('mp-body')[0].focus();
					return false;
				}
			return true;
		}
</script>
<?
// funzione per compilare il messaggio
function mp_fill($modname,$myforum,$rec,$obj,$txt,$mailbox)
	{
		if($rec=="list")
			$rec="<select name=\"recipient\" onChange=\"MM_jumpMenu('parent',this,0)\">".users_list()."</select>";
		else
			$rec="<input name=\"recipient\" type=\"text\" size=\"25\" value=\"$rec\" onmouseover=\"document.getElementsByName('recipient')[0].focus()\" readonly=\"true\">";
		
		if($txt!="")
			$txt="&#xD;&#xD;&#xD;--------------------------------------------------------------------&#xD;$txt";
		 ?>
		<form name="mp_send" method="post" action="index.php?mod=<?php echo $modname ?>&amp;op=mp_send&amp;mailbox=<?php echo $mailbox ?>" onsubmit="return validate();">
			<table width="100%" border="0">
				<tr>
					<td width="15%">
						<div align="right"><strong><?php echo _MITTENTEMP ?>:</strong></div>
					</td>
					<td width="85%">
						<input name="sender" type="text" size="25" value="<?php echo $myforum ?>" onmouseover="document.getElementsByName('sender')[0].focus()" readonly="true">
					</td>
				</tr>
				<tr>
					<td width="15%">
						<div align="right"><strong><?php echo _DESTINATARIOMP ?>:</strong></div>
					</td>
					<td width="85%">
						<?php echo $rec ?>
					</td>
				</tr>
				<tr> 
					<td width="15%">
						<div align="right"><strong><?php echo _OGGETTOMP ?>:</strong></div>
					</td>
					<td width="85%">
						<input id="mp_object" name="title" type="text" value="<?php echo $obj ?>" onmouseover="document.getElementsByName('title')[0].focus()">
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
						<br />
						<?
							bbcodes_js();
              bbcodes_panel("mp-body","formatting"); echo "<br />";
							bbcodes_panel("mp-body","emoticons"); echo "<br />";
						 ?>
					</td>
				</tr>		
				<tr> 
					<td valign="top">
						<div align="right"><strong><?php echo _TESTOMP ?>:</strong></div>
					</td>
					<td>
						<textarea id="mp-body" name="mp-body" rows="7"><?php echo $txt ?></textarea></td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
						<p><input type="submit" name="Submit" value="<?php echo _SUBMITMP ?>"></p>
					</td>
				</tr>
			</table>
		</form>
		<?php
		echo "<img src=\"images/mp/back.png\" border=\"0\">&nbsp;<a href=\"index.php?mod=$modname&amp;mailbox=$mailbox\">"._INDIETROMP."</a>";
	}

// funzione che crea i messaggi
function mp_create($path,$id,$read,$sender,$recipient,$title,$body,$modname,$datadir)
	{
		$fp=fopen("$datadir/mailboxes/$path/$id.php","w");

		fwrite($fp,"<"."?xml version='1.0' ?".">
		<!DOCTYPE fm:messages SYSTEM \"www.ZEB-DEMON.info\">
		<fm:messages xmlns:fm=\"www.ZEB-DEMON.info\">
			<fm:read>$read</fm:read>
			<fm:sender>$sender</fm:sender>
			<fm:recipient>$recipient</fm:recipient>
			<fm:title>$title</fm:title>
			<fm:body>$body</fm:body>
		</fm:messages>");
		fclose($fp);
		
		if (!file_exists("$datadir/mailboxes/$recipient/mp_config.php"))
			mp_config($recipient,$modname,$datadir);
		
		$string=get_file("$datadir/mailboxes/$recipient/mp_config.php");
		$mail=get_xml_element("fm:mail",$string);
		
		if ($mail==1&&$read==0)
			mp_sendmail($recipient,$sender,$title); //invio notifica via mail
	}

//funzione che invia notifica via mail
function mp_sendmail($recipient,$sender,$title)
	{
		global $_FN;
		$uservalues = get_user($recipient);
		$url="http://".$_SERVER['SERVER_NAME'];
		$to=$uservalues['email'];
		$message=$recipient." "._TESTO1MAIL." ".$sender." "._TESTO2MAIL." ".$url;
		$object=_OGGETTOMAIL.": ".$title." ".$_FN['sitename'];
		$from="FROM: ".$_FN['sitename']." <noreply@noreply>\r\nX-Mailer: Flatnux on PHP/".phpversion();
		
		mail($to,$object,$message,$from);
	}
	
// funzione che legge i messaggi
function mp_read($id,$file,$modname,$mailbox)
	{	
		global $_FN;
    $fuso_orario=$_FN['jet_lag'];
    $string=get_file($file);
		$title=get_xml_element("fm:title",$string);
		$sender=get_xml_element("fm:sender",$string);
		$recipient=get_xml_element("fm:recipient",$string);
		$body=get_xml_element("fm:body",$string);
		$data=date("d",$id+(3600*$fuso_orario))."/".date("m",$id+(3600*$fuso_orario))."/".date("Y - ",$id+(3600*$fuso_orario)).date("H:",$id+(3600*$fuso_orario)).date("i",$id+(3600*$fuso_orario));

		// se siamo in Inbox segno il messaggio come letto
		if($mailbox=="Inbox")
			mp_create("$recipient/$mailbox",$id,1,$sender,$recipient,$title,$body,$modname,$_FN['datadir']);

 		echo "<span style='font-weight:bold;'>$title</span>";
		echo "<br /><br />";
		echo "$body";			
		echo "<br /><br /><div style=\"text-align:left;\"><hr />";
		echo _DAMP." <a href=\"index.php?mod=login&user=$sender\"><b>$sender</b></a> ".$data." </div><br />";

		// indietro - rispondi - inoltra - elimina
		echo "<img src=\"images/mp/back.png\" border=\"0\">&nbsp;<a href=\"index.php?mod=$modname&amp;mailbox=$mailbox\">"._INDIETROMP."</a>&nbsp;&nbsp;";
		echo "<img src=\"images/mp/reply.png\" border=\"0\">&nbsp;<a href=\"index.php?mod=$modname&amp;op=mp_reply&amp;id=$id&amp;mailbox=$mailbox\">"._RISPONDIMP."</a>&nbsp;&nbsp;";
		echo "<img src=\"images/mp/forward.png\" border=\"0\">&nbsp;<a href=\"index.php?mod=$modname&amp;op=mp_forward&amp;id=$id&amp;mailbox=$mailbox\">"._INOLTRAMP."</a>&nbsp;&nbsp;";
		echo "<img src=\"images/mp/delete.png\" border=\"0\">&nbsp;<a href=\"index.php?mod=$modname&amp;op=mp_delete&amp;id=$id&amp;mailbox=$mailbox\" onclick=\"return confirm('"._SICURO1MP."')\">"._ELIMINAMP."</a>";
	}
	
// funzione per inviare i messaggi
function mp_send($modname,$datadir,$mailbox)
	{
		if(isset($_POST))
			$postArray=&$_POST;				// a partire dalla 4.1.0
		else
			$postArray=&$HTTP_POST_VARS;	// fino alla 4.1.0

		$myid=time();   
		$mybody = stripslashes($postArray['mp-body']);
		$mybody = str_replace("\n", "<br />", $mybody);
		$mybody = tag2html($mybody, "home");

		// eliminiamo tutti i tag html per una maggiore sicurezza
		$mytitle = stripslashes(htmlspecialchars($postArray['title']));
		$mysender = stripslashes(htmlspecialchars($postArray['sender']));
		$myrecipient = stripslashes(htmlspecialchars($postArray['recipient']));

		// se il titolo del messaggio è vuoto incorro in un errore
		if($mytitle == "")
			{
				echo "<br/>";
				echo _MANCAOGGETTOMP."<br/><br/><img src=\"images/mp/back.png\" border=\"0\">&nbsp;<a href=\"index.php?mod=$modname&mailbox=$mailbox\">"._INDIETROMP."</a><br /><br />";
			}
		else
			{
				if (!file_exists("$datadir/mailboxes/$myrecipient/Inbox")) // se l'utente non ha creato le sue cartelle, le creo
					{
						mkdir("$datadir/mailboxes/$myrecipient");
						mkdir("$datadir/mailboxes/$myrecipient/Inbox");
						mkdir("$datadir/mailboxes/$myrecipient/Outbox");
					}
			
				mp_create("$myrecipient/Inbox",$myid,0,$mysender,$myrecipient,$mytitle,$mybody,$modname,$datadir);
				mp_create("$mysender/Outbox",$myid,1,$mysender,$myrecipient,$mytitle,$mybody,$modname,$datadir);
		
				echo "<br /><b>"._HOINVIATOMP." $myrecipient</b>";
				echo "<br /><br /><br /><b>"._ASPETTAMP."</b>";
				$time=1;
				header("Refresh:".$time.";URL=index.php?mod=$modname&mailbox=$mailbox");
			}
	}

// funzione per rispondere ai messaggi
function mp_reply($file,$modname,$myforum,$mailbox)
	{
		global $_FN;
    $fuso_orario=$_FN['jet_lag'];
		$fp=fopen($file,"r"); // apro il file a cui rispondere in sola lettura

		$string=fread($fp,filesize($file));

		fclose($fp); // chiudo il file

		$title=get_xml_element("fm:title",$string);
		$sender=get_xml_element("fm:sender",$string);
		$recipient=$myforum;
		$body=get_xml_element("fm:body",$string);
		$id=$_GET['id'];
		$data=date("d",$id+(3600*$fuso_orario))."/".date("m",$id+(3600*$fuso_orario))."/".date("Y - ",$id+(3600*$fuso_orario)).date("H:",$id+(3600*$fuso_orario)).date("i",$id+(3600*$fuso_orario));

		// impedisco si crei una serie di re:re:re: 
		$title=str_replace("re: ","",$title);

		$rec=$sender;
		$obj="re: $title";
		$txt="$sender $data&#xD;&#xD;$body&#xD;";
		
		mp_fill($modname,$myforum,$rec,$obj,$txt,$mailbox);
	}

// funzione per inoltrare i messaggi
function mp_forward($file,$modname,$myforum,$mailbox)
	{
		global $_FN;
    $fuso_orario=$_FN['jet_lag'];
		$fp=fopen($file,"r"); // apro il file da inoltrare in sola lettura

		$string=fread($fp,filesize($file));

		fclose($fp); // chiudo il file

		$title=get_xml_element("fm:title",$string);
		$sender=get_xml_element("fm:sender",$string);
		$recipient=$myforum;
		$body=get_xml_element("fm:body",$string);
		$id=$_GET['id'];
		$data=date("d",$id+(3600*$fuso_orario))."/".date("m",$id+(3600*$fuso_orario))."/".date("Y - ",$id+(3600*$fuso_orario)).date("H:",$id+(3600*$fuso_orario)).date("i",$id+(3600*$fuso_orario));

		// impedisco si crei una serie di i:i:i: 
		$title=str_replace("i: ","",$title);
		
		$rec="list";
		$obj="i: $title";
		$txt="$sender $data&#xD;&#xD;$body&#xD;";
		
		mp_fill($modname,$myforum,$rec,$obj,$txt,$mailbox);
	}
	
// funzione che cancella un singolo messaggio
function mp_delete($file,$modname)
	{
		unlink($file);
		$mailbox = isset($_GET['mailbox']) ? "&mailbox=".$_GET['mailbox'] : "";
    $page = isset($_GET['page']) ? "&page=".$_GET['page'] : "";
		Header("Location: index.php?mod=".$modname.$mailbox.$page);
		die();
	}

// funzione che cancella tutti i messaggi di una cartella
function mp_empty($folder,$modname)
	{
		$handle = opendir($folder);
		while ($file = readdir($handle))
			{
				if(!( $file=="." OR $file==".."))
					{
						unlink("$folder/$file");
					}
			}	
		closedir($handle);
		
		Header("Location: index.php?mod=$modname");
		die();
	}
// funzione per creare una nuova cartella
function cp_add($modname,$myforum)
	{
		$what="add";
		$name=_NOMECARTELLAMP;
		$submit=_SUBMITNUOVACP;
		
		cp_fill($modname,$myforum);
	}
// funzione per rinominare una nuova cartella
function cp_rename($modname,$myforum)
	{
		$what="rename";
		$name=_NUOVONOMECARTELLAMP;
		$submit=_SUBMITRINOMINACP;
		
		cp_fill($modname,$myforum);
	}
// funzione per compilare il nome cartella personale (incoming..)
function cp_fill($modname,$myforum)
	{
		 ?>
		<br/>
		<form name="cp_create" method="post" action="index.php?mod=<?php echo $modname ?>&amp;op=cp_create" onsubmit="return validate_cp();">
			<input name="what" type="hidden" value="<?php echo $what ?>">
			<table width="100%" border="0">
				<tr>
					<td width="15%">
						<div align="right"><strong><?php echo $name ?>:</strong></div>
					</td>
					<td width="85%">
						<input name="name" type="text" size="25" onmouseover="document.getElementsByName('name')[0].focus()" readonly="true">
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
						<p><input type="submit" name="Submit" value="<?php echo $submit ?>"></p>
						<p>&nbsp; </p>
					</td>
				</tr>
			</table>
		</form>
		<?php
		echo "<br /><br /><img src=\"images/mp/back.png\" border=\"0\">&nbsp;<a href=\"index.php?mod=$modname&amp;mailbox=$mailbox\">"._INDIETROMP."</a>";
	}
// funzione che crea la cartella personale (incoming..)
function cp_create($modname,$myforum,$folder,$datadir)
	{
		if(isset($_POST))
			$postArray=&$_POST;				// a partire dalla 4.1.0
		else
			$postArray=&$HTTP_POST_VARS;	// fino alla 4.1.0
  
		$myfolder = stripslashes(htmlspecialchars($postArray['name']));
		$what = stripslashes(htmlspecialchars($postArray['what']));
		
		if($what=="add")
			{
				mkdir("$datadir/mailboxes/$myforum/$myfolder");
				echo "<br /><b>"._CARTELLACREATAMP." $myfolder</b>";
			}
		else
			{
				rename($folder,"$datadir/mailboxes/$myforum/$myfolder");
				echo "<br /><b>"._CARTELLARINOMINATAMP." $myfolder</b>";
			}
			
		echo "<br /><br /><br /><b>"._ASPETTAMP."</b>";		
		$time=1;
		header('Refresh:'.$time.';'."index.php?mod=$modname");	
	}
// funzione che cancella una cartella ed il suo contenuto (incoming..)
function cp_delete($folder,$modname)
	{	
		mp_empty($folder,$modname);
		rmdir($folder);
		
		Header("Location: index.php?mod=$modname");
		die();
	}
// funzione per spostare messaggi da una cartella ad un'altra (incoming..)
function mp_move($myforum,$folder1,$folder2,$datadir)
	{
		$handle=opendir("$datadir/mailboxes/$myforum/$folder1");
		while($file=readdir($handle))
			{
				if(!( $file=="." OR $file==".." OR is_dir($file)))
					{
						copy("$datadir/mailboxes/$myforum/$folder1/".$file, "$datadir/mailboxes/$myforum/$folder2/".$file);
						unlink ("$datadir/mailboxes/$myforum/$folder1/".$file);
					}
			}	
		closedir($handle);
	}
?>