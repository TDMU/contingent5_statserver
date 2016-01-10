/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/main",["./_base/kernel","./has","require","./_base/sniff","./_base/lang","./_base/array","./ready","./_base/declare","./_base/connect","./_base/Deferred","./_base/json","./_base/Color","./has!dojo-firebug?./_firebug/firebug","./_base/browser","./_base/loader"],function(b,g,c,h,d,e,f){b.config.isDebug&&c(["./_firebug/firebug"]);var a=b.config.require;a&&(a=e.map(d.isArray(a)?a:[a],function(a){return a.replace(/\./g,"/")}),b.isAsync?c(a):f(1,function(){c(a)}));return b});