/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/currency",["./_base/kernel","./_base/lang","./_base/array","./number","./i18n","./i18n!./cldr/nls/currency","./cldr/monetary"],function(b,f,g,c,h,j,i){f.getObject("currency",!0,b);b.currency._mixInDefaults=function(a){a=a||{};a.type="currency";var b=h.getLocalization("dojo.cldr","currency",a.locale)||{},c=a.currency,e=i.getData(c);g.forEach(["displayName","symbol","group","decimal"],function(a){e[a]=b[c+"_"+a]});e.fractional=[!0,!1];return f.mixin(e,a)};b.currency.format=function(a,d){return c.format(a,
b.currency._mixInDefaults(d))};b.currency.regexp=function(a){return c.regexp(b.currency._mixInDefaults(a))};b.currency.parse=function(a,d){return c.parse(a,b.currency._mixInDefaults(d))};return b.currency});