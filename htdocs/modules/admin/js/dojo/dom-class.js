/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/dom-class",["./_base/lang","./_base/array","./dom"],function(m,n,g){function i(a){if(typeof a=="string"||a instanceof String){if(a&&!k.test(a))return l[0]=a,l;a=a.split(k);a.length&&!a[0]&&a.shift();a.length&&!a[a.length-1]&&a.pop();return a}if(!a)return[];return n.filter(a,function(a){return a})}var e,k=/\s+/,l=[""],d={};return e={contains:function(a,c){return(" "+g.byId(a).className+" ").indexOf(" "+c+" ")>=0},add:function(a,c){var a=g.byId(a),c=i(c),b=a.className,f,b=b?" "+b+" ":" ";
f=b.length;for(var h=0,e=c.length,d;h<e;++h)(d=c[h])&&b.indexOf(" "+d+" ")<0&&(b+=d+" ");f<b.length&&(a.className=b.substr(1,b.length-2))},remove:function(a,c){var a=g.byId(a),b;if(c!==void 0){c=i(c);b=" "+a.className+" ";for(var f=0,d=c.length;f<d;++f)b=b.replace(" "+c[f]+" "," ");b=m.trim(b)}else b="";a.className!=b&&(a.className=b)},replace:function(a,c,b){a=g.byId(a);d.className=a.className;e.remove(d,b);e.add(d,c);a.className!==d.className&&(a.className=d.className)},toggle:function(a,c,b){a=
g.byId(a);if(b===void 0)for(var c=i(c),d=0,h=c.length,j;d<h;++d)j=c[d],e[e.contains(a,j)?"remove":"add"](a,j);else e[b?"add":"remove"](a,c);return b}}});