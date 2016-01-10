/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/back",["./_base/kernel","./_base/lang","./_base/sniff","./dom","./dom-construct","./_base/window","require"],function(f,d,k,y,z,A,t){function g(){var a=c.pop();if(a){var b=c[c.length-1];!b&&c.length==0&&(b=l);b&&(b.kwArgs.back?b.kwArgs.back():b.kwArgs.backButton?b.kwArgs.backButton():b.kwArgs.handle&&b.kwArgs.handle("back"));e.push(a)}}function i(){var a=e.pop();a&&(a.kwArgs.forward?a.kwArgs.forward():a.kwArgs.forwardButton?a.kwArgs.forwardButton():a.kwArgs.handle&&a.kwArgs.handle("forward"),
c.push(a))}function m(a,b,c){return{url:a,kwArgs:b,urlHash:c}}function o(a){a=a.split("?");return a.length<2?null:a[1]}function u(){var a=(f.config.dojoIframeHistoryUrl||t.toUrl("./resources/iframe_history.html"))+"?"+(new Date).getTime();p=!0;if(j)k("webkit")?j.location=a:window.frames[j.name].location=a;return a}function B(){if(!q){var a=c.length,b=n();(b===v||window.location.href==w)&&a==1?g():e.length>0&&e[e.length-1].urlHash===b?i():a>=2&&c[a-2]&&c[a-2].urlHash===b&&g()}}d.getObject("back",!0,
f);var d=f.back,n=d.getHash=function(){var a=window.location.hash;a.charAt(0)=="#"&&(a=a.substring(1));return k("mozilla")?a:decodeURIComponent(a)},r=d.setHash=function(a){a||(a="");window.location.hash=encodeURIComponent(a)},w=typeof window!=="undefined"?window.location.href:"",v=typeof window!=="undefined"?n():"",l=null,x=null,s=null,j=null,e=[],c=[],p=!1,q=!1;d.goBack=g;d.goForward=i;d.init=function(){if(!y.byId("dj_history")){var a=f.config.dojoIframeHistoryUrl||t.toUrl("./resources/iframe_history.html");
f._postLoad?console.error("dojo.back.init() must be called before the DOM has loaded. If using xdomain loading or djConfig.debugAtAllCosts, include dojo.back in a build layer."):document.write('<iframe style="border:0;width:1px;height:1px;position:absolute;visibility:hidden;bottom:0;right:0;" name="dj_history" id="dj_history" src="'+a+'"></iframe>')}};d.setInitialState=function(a){l=m(w,a,v)};d.addToHistory=function(a){e=[];var b=null,d=null;if(!j)f.config.useXDomain&&!f.config.dojoIframeHistoryUrl&&
console.warn("dojo.back: When using cross-domain Dojo builds, please save iframe_history.html to your domain and set djConfig.dojoIframeHistoryUrl to the path on your domain to iframe_history.html"),j=window.frames.dj_history;s||(s=z.create("a",{style:{display:"none"}},A.body()));if(a.changeUrl){b=""+(a.changeUrl!==!0?a.changeUrl:(new Date).getTime());if(c.length==0&&l.urlHash==b){l=m(d,a,b);return}else if(c.length>0&&c[c.length-1].urlHash==b){c[c.length-1]=m(d,a,b);return}q=!0;setTimeout(function(){r(b);
q=!1},1);s.href=b;if(k("ie")){var d=u(),g=a.back||a.backButton||a.handle,h=function(a){n()!=""&&setTimeout(function(){r(b)},1);g.apply(this,[a])};if(a.back)a.back=h;else if(a.backButton)a.backButton=h;else if(a.handle)a.handle=h;var i=a.forward||a.forwardButton||a.handle,h=function(a){n()!=""&&r(b);i&&i.apply(this,[a])};if(a.forward)a.forward=h;else if(a.forwardButton)a.forwardButton=h;else if(a.handle)a.handle=h}else k("ie")||x||(x=setInterval(B,200))}else d=u();c.push(m(d,a,b))};d._iframeLoaded=
function(a,b){var d=o(b.href);d==null?c.length==1&&g():p?p=!1:c.length>=2&&d==o(c[c.length-2].url)?g():e.length>0&&d==o(e[e.length-1].url)&&i()};return f.back});