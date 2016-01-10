/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/regexp",["./_base/kernel","./_base/lang"],function(c,d){d.getObject("regexp",!0,c);c.regexp.escapeString=function(a,b){return a.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g,function(a){if(b&&b.indexOf(a)!=-1)return a;return"\\"+a})};c.regexp.buildGroupRE=function(a,b,d){if(!(a instanceof Array))return b(a);for(var f=[],e=0;e<a.length;e++)f.push(b(a[e]));return c.regexp.group(f.join("|"),d)};c.regexp.group=function(a,b){return"("+(b?"?:":"")+a+")"};return c.regexp});