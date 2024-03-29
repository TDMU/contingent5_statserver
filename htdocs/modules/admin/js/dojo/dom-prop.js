/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/dom-prop",["exports","./_base/kernel","./_base/sniff","./_base/lang","./dom","./dom-style","./dom-construct","./_base/connect"],function(f,h,n,o,i,p,j,k){var g={},q=0,l=h._scopeName+"attrid",r={col:1,colgroup:1,table:1,tbody:1,tfoot:1,thead:1,tr:1,title:1};f.names={"class":"className","for":"htmlFor",tabindex:"tabIndex",readonly:"readOnly",colspan:"colSpan",frameborder:"frameBorder",rowspan:"rowSpan",valuetype:"valueType"};f.get=function(a,d){var a=i.byId(a),c=d.toLowerCase();return a[f.names[c]||
d]};f.set=function(a,d,c){a=i.byId(a);if(arguments.length==2&&typeof d!="string"){for(var b in d)f.set(a,b,d[b]);return a}b=d.toLowerCase();b=f.names[b]||d;if(b=="style"&&typeof c!="string")return p.style(a,c),a;if(b=="innerHTML")return n("ie")&&a.tagName.toLowerCase()in r?(j.empty(a),a.appendChild(j.toDom(c,a.ownerDocument))):a[b]=c,a;if(o.isFunction(c)){var e=a[l];e||(e=q++,a[l]=e);g[e]||(g[e]={});var m=g[e][b];if(m)k.disconnect(m);else try{delete a[b]}catch(h){}c?g[e][b]=k.connect(a,b,c):a[b]=
null;return a}a[b]=c;return a}});