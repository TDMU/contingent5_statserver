/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/_base/window",["./kernel","../has","./sniff"],function(a,d){a.doc=this.document||null;a.body=function(){return a.doc.body||a.doc.getElementsByTagName("body")[0]};a.setContext=function(b,c){a.global=e.global=b;a.doc=e.doc=c};a.withGlobal=function(b,c,d,i){var j=a.global;try{return a.global=e.global=b,a.withDoc.call(null,b.document,c,d,i)}finally{a.global=e.global=j}};a.withDoc=function(b,c,h,i){var j=a.doc,l=a.isQuirks,m=a.isIE,f,g,k;try{a.doc=e.doc=b;a.isQuirks=d.add("quirks",a.doc.compatMode==
"BackCompat",!0,!0);if(d("ie")&&(k=b.parentWindow)&&k.navigator)f=parseFloat(k.navigator.appVersion.split("MSIE ")[1])||void 0,(g=b.documentMode)&&g!=5&&Math.floor(f)!=g&&(f=g),a.isIE=d.add("ie",f,!0,!0);h&&typeof c=="string"&&(c=h[c]);return c.apply(h,i||[])}finally{a.doc=e.doc=j,a.isQuirks=d.add("quirks",l,!0,!0),a.isIE=d.add("ie",m,!0,!0)}};var e={global:a.global,doc:a.doc,body:a.body,setContext:a.setContext,withGlobal:a.withGlobal,withDoc:a.withDoc};return e});