/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/dnd/autoscroll",["../main","../window"],function(a){a.getObject("dnd",!0,a);a.dnd.getViewport=a.window.getBox;a.dnd.V_TRIGGER_AUTOSCROLL=32;a.dnd.H_TRIGGER_AUTOSCROLL=32;a.dnd.V_AUTOSCROLL_VALUE=16;a.dnd.H_AUTOSCROLL_VALUE=16;a.dnd.autoScroll=function(e){var d=a.window.getBox(),i=0,b=0;if(e.clientX<a.dnd.H_TRIGGER_AUTOSCROLL)i=-a.dnd.H_AUTOSCROLL_VALUE;else if(e.clientX>d.w-a.dnd.H_TRIGGER_AUTOSCROLL)i=a.dnd.H_AUTOSCROLL_VALUE;if(e.clientY<a.dnd.V_TRIGGER_AUTOSCROLL)b=-a.dnd.V_AUTOSCROLL_VALUE;
else if(e.clientY>d.h-a.dnd.V_TRIGGER_AUTOSCROLL)b=a.dnd.V_AUTOSCROLL_VALUE;window.scrollBy(i,b)};a.dnd._validNodes={div:1,p:1,td:1};a.dnd._validOverflow={auto:1,scroll:1};a.dnd.autoScrollNodes=function(e){for(var d,i,b,f,g,h,j=0,k=0,c=e.target;c;){if(c.nodeType==1&&c.tagName.toLowerCase()in a.dnd._validNodes){b=a.getComputedStyle(c);f=b.overflow.toLowerCase()in a.dnd._validOverflow;g=b.overflowX.toLowerCase()in a.dnd._validOverflow;h=b.overflowY.toLowerCase()in a.dnd._validOverflow;if(f||g||h)d=
a._getContentBox(c,b),i=a.position(c,!0);if(f||g){b=Math.min(a.dnd.H_TRIGGER_AUTOSCROLL,d.w/2);g=e.pageX-i.x;if(a.isWebKit||a.isOpera)g+=a.body().scrollLeft;j=0;g>0&&g<d.w&&(g<b?j=-b:g>d.w-b&&(j=b),c.scrollLeft+=j)}if(f||h){f=Math.min(a.dnd.V_TRIGGER_AUTOSCROLL,d.h/2);h=e.pageY-i.y;if(a.isWebKit||a.isOpera)h+=a.body().scrollTop;k=0;h>0&&h<d.h&&(h<f?k=-f:h>d.h-f&&(k=f),c.scrollTop+=k)}if(j||k)return}try{c=c.parentNode}catch(l){c=null}}a.dnd.autoScroll(e)};return a.dnd});