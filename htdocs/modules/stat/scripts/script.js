var ns6;
var curMenu = null;
var hideTimerID = null;
var overpopupmenu = false;

function init() {
  ns6 = document.getElementById && !document.all;
}    

    function getAbsolutePos(el) {
      var r = { x: el.offsetLeft, y: el.offsetTop };
      if (el.offsetParent) {
        var tmp = getAbsolutePos(el.offsetParent);
        r.x += tmp.x;
        r.y += tmp.y;
      }
      return r;
    };

  function stopTimer() {
    if (hideTimerID != null) {
      clearTimeout(hideTimerID);
      hideTimerID = null
    }  
  }
    
  function startTimer() {
    stopTimer();
    hideTimerID = setTimeout("hidemenu()", 1000);
  }

  function setOver(val) {
    overpopupmenu = val;
    if(!overpopupmenu) startTimer();
  }
  
  function showmenu(elem, id)
  {
    if (curMenu != null) hidemenu(null, true);
  
      curMenu = document.getElementById('sm_' + id);
      var elemPos = getAbsolutePos(elem);
      if (ns6)
      {
        curMenu.style.left = elemPos.x;
        curMenu.style.top = elemPos.y + 20;
      } 
      else
      {
        curMenu.style.pixelLeft = elemPos.x;
        curMenu.style.pixelTop = elemPos.y + 20;
      }
      
    curMenu.style.display = "";
    startTimer();
    return true;
  }

  function hidemenu(e, force) {
    if ((force || !overpopupmenu) && curMenu != null) {
//      if(document.getElementById && !document.all) { //firefox
//        if (e != null && e.target.tagName == 'A') return false;
//      }
      stopTimer();
      curMenu.style.display = "none";
      curMenu = null;
      return true;
    }
    else return false;  
  }

  window.onload = init;
//  if (window.captureEvents) window.captureEvents(Event.CLICK);
//  document.captureEvents(Event.MOUSEDOWN);
//  document.onmousedown = hidemenu; Не работает выбор файла в IE