/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/domReady",["./has"],function(n){function h(b){d?b(1):c.push(b)}var i=this,a=document,j={loaded:1,complete:1},k=typeof a.readyState!="string",d=!!j[a.readyState];if(k)a.readyState="loading";if(!d){var c=[],e=[],f=function(b){b=b||i.event;if(!(d||b.type=="readystatechange"&&!j[a.readyState])){d=1;if(k)a.readyState="complete";for(;c.length;)c.shift()()}},g=function(b,a){b.addEventListener(a,f,!1);c.push(function(){b.removeEventListener(a,f,!1)})};if(!n("dom-addeventlistener")){var g=function(b,
a){a="on"+a;b.attachEvent(a,f);c.push(function(){b.detachEvent(a,f)})},l=a.createElement("div");try{l.doScroll&&i.frameElement===null&&e.push(function(){try{return l.doScroll("left"),1}catch(a){}})}catch(o){}}g(a,"DOMContentLoaded");g(i,"load");"onreadystatechange"in a?g(a,"readystatechange"):k||e.push(function(){return j[a.readyState]});if(e.length){var m=function(){if(!d){for(var a=e.length;a--;)if(e[a]()){f("poller");return}setTimeout(m,30)}};m()}}h.load=function(a,d,c){h(c)};return h});