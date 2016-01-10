/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/NodeList-data",["./_base/kernel","./query","./_base/lang","./_base/array","./dom-attr"],function(f,h,i,k,j){var a=h.NodeList,c={},l=0,g=function(d){var b=j.get(d,"data-dojo-dataid");b||(b="pid"+l++,j.set(d,"data-dojo-dataid",b));return b},n=f._nodeData=function(d,b,m){var e=g(d),a;c[e]||(c[e]={});arguments.length==1&&(a=c[e]);typeof b=="string"?arguments.length>2?c[e][b]=m:a=c[e][b]:a=i.mixin(c[e],b);return a},o=f._removeNodeData=function(d,b){var a=g(d);c[a]&&(b?delete c[a][b]:delete c[a])};
f._gcNodeData=function(){var a=h("[data-dojo-dataid]").map(g),b;for(b in c)k.indexOf(a,b)<0&&delete c[b]};i.extend(a,{data:a._adaptWithCondition(n,function(a){return a.length===0||a.length==1&&typeof a[0]=="string"}),removeData:a._adaptAsForEach(o)});return a});