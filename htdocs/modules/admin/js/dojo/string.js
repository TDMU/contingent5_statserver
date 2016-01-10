/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/string",["./_base/kernel","./_base/lang"],function(c,f){f.getObject("string",!0,c);c.string.rep=function(a,b){if(b<=0||!a)return"";for(var d=[];;){b&1&&d.push(a);if(!(b>>=1))break;a+=a}return d.join("")};c.string.pad=function(a,b,d,e){d||(d="0");a=String(a);b=c.string.rep(d,Math.ceil((b-a.length)/d.length));return e?a+b:b+a};c.string.substitute=function(a,b,d,e){e=e||c.global;d=d?f.hitch(e,d):function(a){return a};return a.replace(/\$\{([^\s\:\}]+)(?:\:([^\s\:\}]+))?\}/g,function(a,c,
g){a=f.getObject(c,!1,b);g&&(a=f.getObject(g,!1,e).call(e,a,c));return d(a,c).toString()})};c.string.trim=String.prototype.trim?f.trim:function(a){for(var a=a.replace(/^\s+/,""),b=a.length-1;b>=0;b--)if(/\S/.test(a.charAt(b))){a=a.substring(0,b+1);break}return a};return c.string});