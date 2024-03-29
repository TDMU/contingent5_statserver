/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/cookie",["./_base/kernel","./regexp"],function(c,h){c.cookie=function(c,f,b){var a=document.cookie,e;if(arguments.length==1)e=(e=a.match(RegExp("(?:^|; )"+h.escapeString(c)+"=([^;]*)")))?decodeURIComponent(e[1]):void 0;else{b=b||{};a=b.expires;if(typeof a=="number"){var d=new Date;d.setTime(d.getTime()+a*864E5);a=b.expires=d}if(a&&a.toUTCString)b.expires=a.toUTCString();var f=encodeURIComponent(f),a=c+"="+f,g;for(g in b)a+="; "+g,d=b[g],d!==!0&&(a+="="+d);document.cookie=a}return e};
c.cookie.isSupported=function(){if(!("cookieEnabled"in navigator))this("__djCookieTest__","CookiesAllowed"),navigator.cookieEnabled=this("__djCookieTest__")=="CookiesAllowed",navigator.cookieEnabled&&this("__djCookieTest__","",{expires:-1});return navigator.cookieEnabled};return c.cookie});