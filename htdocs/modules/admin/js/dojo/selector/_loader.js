/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/selector/_loader",["../has","require"],function(b,g){var c=document.createElement("div");b.add("dom-qsa2.1",!!c.querySelectorAll);b.add("dom-qsa3",function(){try{return c.innerHTML="<p class='TEST'></p>",c.querySelectorAll(".TEST:empty").length==1}catch(a){}});var d;return{load:function(a,c,e){var f=g,a=a=="default"?b("config-selectorEngine")||"css3":a,a=a=="css2"||a=="lite"?"./lite":a=="css2.1"?b("dom-qsa2.1")?"./lite":"./acme":a=="css3"?b("dom-qsa3")?"./lite":"./acme":a=="acme"?"./acme":
(f=c)&&a;if(a.charAt(a.length-1)=="?")var a=a.substring(0,a.length-1),h=!0;if(h&&(b("dom-compliant-qsa")||d))return e(d);f([a],function(b){a!="./lite"&&(d=b);e(b)})}}});