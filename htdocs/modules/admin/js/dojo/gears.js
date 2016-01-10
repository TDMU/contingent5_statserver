/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/gears",["./_base/kernel","./_base/lang","./_base/sniff"],function(b,c,d){c.getObject("gears",!0,b);b.gears._gearsObject=function(){var a,b=c.getObject("google.gears");if(b)return b;if(typeof GearsFactory!="undefined")a=new GearsFactory;else if(d("ie"))try{a=new ActiveXObject("Gears.Factory")}catch(e){}else if(navigator.mimeTypes["application/x-googlegears"])a=document.createElement("object"),a.setAttribute("type","application/x-googlegears"),a.setAttribute("width",0),a.setAttribute("height",
0),a.style.display="none",document.documentElement.appendChild(a);if(!a)return null;c.setObject("google.gears.factory",a);return c.getObject("google.gears")};b.gears.available=!!b.gears._gearsObject()||0;return b.gears});