/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/data/util/simpleFetch",["dojo/_base/lang","dojo/_base/window","./sorter"],function(c,i,k){c=c.getObject("dojo.data.util.simpleFetch",!0);c.fetch=function(b){b=b||{};if(!b.store)b.store=this;var c=this;this._fetchItems(b,function(e,a){var b=a.abort||null,f=!1,g=a.start?a.start:0,j=a.count&&a.count!==Infinity?g+a.count:e.length;a.abort=function(){f=!0;b&&b.call(a)};var h=a.scope||i.global;if(!a.store)a.store=c;a.onBegin&&a.onBegin.call(h,e.length,a);a.sort&&e.sort(k.createSortFunction(a.sort,
c));if(a.onItem)for(var d=g;d<e.length&&d<j;++d){var l=e[d];f||a.onItem.call(h,l,a)}a.onComplete&&!f&&(d=null,a.onItem||(d=e.slice(g,j)),a.onComplete.call(h,d,a))},function(b,a){a.onError&&a.onError.call(a.scope||i.global,b,a)});return b};return c});