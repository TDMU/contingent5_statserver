/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/uacss",["./dom-geometry","./_base/lang","./ready","./_base/sniff","./_base/window"],function(h,i,j,a,f){var d=f.doc.documentElement,b=a("ie"),k=a("opera"),c=Math.floor,l=a("ff"),f=h.boxModel.replace(/-/,""),b={dj_ie:b,dj_ie6:c(b)==6,dj_ie7:c(b)==7,dj_ie8:c(b)==8,dj_ie9:c(b)==9,dj_quirks:a("quirks"),dj_iequirks:b&&a("quirks"),dj_opera:k,dj_khtml:a("khtml"),dj_webkit:a("webkit"),dj_safari:a("safari"),dj_chrome:a("chrome"),dj_gecko:a("mozilla"),dj_ff3:c(l)==3};b["dj_"+f]=!0;var e="",g;for(g in b)b[g]&&
(e+=g+" ");d.className=i.trim(d.className+" "+e);j(90,function(){if(!h.isBodyLtr()){var a="dj_rtl dijitRtl "+e.replace(/ /g,"-rtl ");d.className=i.trim(d.className+" "+a+"dj_rtl dijitRtl "+e.replace(/ /g,"-rtl "))}});return a});