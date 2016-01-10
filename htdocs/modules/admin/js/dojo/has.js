/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/has",["require"],function(b){var a=b.has||function(){},b=navigator.userAgent;a.add("dom-addeventlistener",!!document.addEventListener);a.add("touch","ontouchstart"in document);a.add("device-width",screen.availWidth||innerWidth);a.add("agent-ios",!!b.match(/iPhone|iP[ao]d/));a.add("agent-android",b.indexOf("android")>1);a.clearElement=function(a){a.innerHTML="";return a};a.normalize=function(c,b){var d=c.match(/[\?:]|[^:\?]*/g),f=0,e=function(b){var c=d[f++];if(c==":")return 0;else{if(d[f++]==
"?")return!b&&a(c)?e():(e(!0),e(b));return c||0}};return(c=e())&&b(c)};a.load=function(a,b,d){a?b([a],d):d()};return a});