/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/_base/event",["./kernel","../on","../has","../dom-geometry"],function(c,b,d,e){if(b._fixEvent){var f=b._fixEvent;b._fixEvent=function(a,b){(a=f(a,b))&&e.normalizeEvent(a);return a}}c.fixEvent=function(a,c){if(b._fixEvent)return b._fixEvent(a,c);return a};c.stopEvent=function(a){d("dom-addeventlistener")||a&&a.preventDefault?(a.preventDefault(),a.stopPropagation()):(a=a||window.event,a.cancelBubble=!0,b._preventDefault.call(a))};return{fix:c.fixEvent,stop:c.stopEvent}});