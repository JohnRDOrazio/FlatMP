/****************************************************************************************************/
/*   FlatMP Javascript 2011        	                                                                */
/*   ==================================================                                             */
/*                                                                                                  */
/*   by LWANGAMAN <donjohn.fmmi@gmail.com>                                                          */
/*   http://johnrdorazio.altervista.org                                                             */
/*                                                                                                  */
/*   This program is free software. You can redistribute it and/or modify it under the terms of     */
/*   the GNU General Public License (ver.2) as published by the Free Software Foundation.           */
/****************************************************************************************************/


// dynamically add javascript or css to the page
var inject = function(type,src){
  hd = document.getElementsByTagName("head")[0];
  if (type=="css") { 
    el = document.createElement("link");
    el.type = "text/css";
    el.rel = "stylesheet";
    el.href = src;
    hd.appendChild(el);
  }
  if (type=="js") { 
    el = document.createElement("script");
    el.type = "text/javascript";
    el.src = src;
    hd.appendChild(el);
  } 
}

inject("css","/sections/none_FlatMP/style.css"); 

if (typeof jQuery == 'undefined') {  
    // jQuery is not loaded
    inject("js","http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");  
}

if (typeof $.fn.dragCheck == 'undefined') {
/*******************************************************************
*  jQuery dragCheck checkboxes plugin                              *
*  Author: Lwangaman <donjohn.fmmi@gmail.com>                      *
*  Website: http://johnrdorazio.altervista.org                     *
*  jQuery: http://plugins.jquery.com/project/dragCheck             *
*******************************************************************/
$.fn.dragCheck = function(selector){
  
  if (selector===false) 
    return this.find('*').andSelf().add(document).unbind('.dc').removeClass('dc-selected') 
      .filter(':has(:checkbox)').css({MozUserSelect: '', cursor: ''}); 
     
  else  
    return this.each(function(){  
       
      // if a checkbox is clicked this will be set to 
      // it's checked state (true||false), otherwise null 
      var mdown = null; 
 
      // get the specified container, or children if not specified 
      $(this).find(selector||'> *').filter(':has(:checkbox)').each(function(){ 
             
        // highlight all already checked boxes 
        if ( $(this).find(':checkbox:checked').length ) 
           $(this).addClass('dc-selected'); 
            
      }) 
       .bind('mouseover.dc', function(){  
        
         // if a checkbox was clicked and mouse button bein held down 
         if (mdown != null){ 
           // set this container's checkbox to the 
           // same state as the one first clicked 
           $(this).find(':checkbox')[0].checked = mdown; 
           // add the highlight class 
           $(this).toggleClass('dc-selected', mdown); 
         } 
          
      })  
       .bind('mousedown.dc', function(e){ 
          
         // find this container's checkbox 
         var t = e.target; 
         if ( !$(t).is(':checkbox') ) 
           t = $(this).find(':checkbox')[0]; 
 
         // switch it's state (click event will be canceled later) 
         t.checked = !t.checked; 
         // set the value to which other hovered 
         // checkboxes will be set while the mouse is down 
         mdown = t.checked; 
            
         // highlight this one according to it's state 
         $(this).toggleClass('dc-selected', mdown); 
           
      }) 
       
      // avoid text selection 
       .bind('selectstart.dc', function(){ 
         return false; 
      }).css({ 
        MozUserSelect:'none', 
        cursor: 'default' 
      }) 
       
      // cancel the click event on the checkboxes because 
      // we already switched it's checked state on mousedown  
       .find(':checkbox').bind('click.dc', function(){ 
         return false; 
      });  
 
      // clear the mdown var if the mouse button is released 
      // anywhere on the page 
      $(document).bind('mouseup.dc', function(){  
        mdown = null; 
      }); 
 
    }); 
  
};  
}

$(document).ready(function(){

  // set width of right column
  rightboxwidth = $("#mp-mailboxtitle").width() - $("#mp-left-col").width();
  $("#mp-right-col").css({width:rightboxwidth});
  // set width of right column on every window resize
  $(window).resize(function(){
    rightboxwidth = $("#mp-mailboxtitle").width() - $("#mp-left-col").width();
    $("#mp-right-col").css({width:rightboxwidth});
  });

  // onclick handler for each message row
  $(".mex-click").click(function(){
    location.href="index.php?"+$(this).parent().attr("id");  
  });

  $("#mptable").dragCheck("td:not(.notthisone)");
  $("#mpcheckall").click(function(){
    $(":checkbox.mpcheckbox").attr('checked', this.checked);
  });
}); 
    