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

if (typeof(jQuery) == 'undefined') {  
    // jQuery is not loaded
    inject("js","http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");  
}

if (typeof($.fn.dragCheck) == 'undefined') {
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
      var mdown = null; 
      $(this).find(selector||'> *').filter(':has(:checkbox)').each(function(){ 
        if ( $(this).find(':checkbox:checked').length ) 
           $(this).parent("tr").addClass('dc-selected'); 
      }) 
       .bind('mouseover.dc', function(){  
         if (mdown != null){ 
           $(this).find(':checkbox')[0].checked = mdown; 
           $(this).parent("tr").toggleClass('dc-selected', mdown); 
         } 
      })  
       .bind('mousedown.dc', function(e){ 
         var t = e.target; 
         if ( !$(t).is(':checkbox') ) 
           t = $(this).find(':checkbox')[0]; 
         t.checked = !t.checked; 
         mdown = t.checked; 
         $(this).parent("tr").toggleClass('dc-selected', mdown); 
      }) 
       .bind('selectstart.dc', function(){ 
         return false; 
      }).css({ 
        MozUserSelect:'none', 
        cursor: 'default' 
      }) 
       .find(':checkbox').bind('click.dc', function(){ 
         return false; 
      });  
      $(document).bind('mouseup.dc', function(){  
        mdown = null; 
      }); 
    }); 
};  
}

$(document).ready(function(){

  // set width of right column
  if($.support.boxModel){
    extrawidth = parseInt($("#mp-right-col").css("padding-left")) + parseInt($("#mp-right-col").css("padding-right")) + parseInt($("#mp-right-col").css("margin-left")) + parseInt($("#mp-right-col").css("border-left-width")) + parseInt($("#mp-right-col").css("border-right-width"));
  }
  else{
    extrawidth = parseInt($("#mp-right-col").css("padding-right")) + parseInt($("#mp-right-col").css("margin-left")) - parseInt($("#mp-right-col").css("padding-left")) - parseInt($("#mp-right-col").css("border-left-width")) - parseInt($("#mp-right-col").css("border-right-width"));  
  }
  rightboxwidth = $("#mp-mailboxtitle").width() - $("#mp-left-col").width() + extrawidth;
  rightboxwidth += "px";
  $("#mp-right-col").css({width:rightboxwidth});
  // set width of right column on every window resize
  $(window).resize(function(){
    rightboxwidth = $("#mp-mailboxtitle").width() - $("#mp-left-col").width() + extrawidth;
    rightboxwidth += "px";
    $("#mp-right-col").css({width:rightboxwidth});
  });

  // onclick handler for each message row
  $(".mex-click").click(function(){
    location.href="index.php?"+$(this).parent().attr("id");  
  });

  $("#mptable").dragCheck("td:not(.notthisone)");

  //add shift-click functionality
  $(":checkbox.mpcheckbox").click(function(ev){
    if(!ev.shiftKey) {
      last = this;
    } 
    else {
      var start = $(this).parents("tr").index()-1;
      var end = $(last).parents("tr").index();  
      $(':checkbox.mpcheckbox').slice(Math.min(start,end),Math.max(start,end)).prop("checked",last.checked).parents("tr").toggleClass("dc-selected",last.checked);
      last = this;
    }  
  });
  $("#mpcheckall").click(function(){
    chkstate = this.checked;
    $(":checkbox.mpcheckbox").prop('checked', chkstate).each(function(){
      $(this).parents("tr").toggleClass("dc-selected",chkstate);
    }); 
  });
});