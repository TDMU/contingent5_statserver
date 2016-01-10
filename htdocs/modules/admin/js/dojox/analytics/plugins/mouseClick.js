//>>built
define("dojox/analytics/plugins/mouseClick",["dojo/_base/lang","../_base","dojo/_base/window","dojo/on"],function(f,g,h,i){return g.plugins.mouseClick=new function(){this.addData=f.hitch(g,"addData","mouseClick");this.onClick=function(b){this.addData(this.trimEvent(b))};i(h.doc,"click",f.hitch(this,"onClick"));this.trimEvent=function(b){var e={},a;for(a in b)switch(a){case "target":case "originalTarget":case "explicitOriginalTarget":var d=["id","className","nodeName","localName","href","spellcheck",
"lang"];e[a]={};for(var c=0;c<d.length;c++)b[a][d[c]]&&(d[c]=="text"||d[c]=="textContent"?b[a].localName!="HTML"&&b[a].localName!="BODY"&&(e[a][d[c]]=b[a][d[c]].substr(0,50)):e[a][d[c]]=b[a][d[c]]);break;case "clientX":case "clientY":case "pageX":case "pageY":case "screenX":case "screenY":e[a]=b[a]}return e}}});