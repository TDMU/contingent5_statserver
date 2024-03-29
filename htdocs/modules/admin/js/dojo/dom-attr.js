/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/dom-attr",["exports","./_base/sniff","./_base/lang","./dom","./dom-style","./dom-prop"],function(e,j,l,g,m,h){function f(a,c){var b=a.getAttributeNode&&a.getAttributeNode(c);return b&&b.specified}var k={innerHTML:1,className:1,htmlFor:j("ie"),value:1},i={classname:"class",htmlfor:"for",tabindex:"tabIndex",readonly:"readOnly"};e.has=function(a,c){var b=c.toLowerCase();return k[h.names[b]||c]||f(g.byId(a),i[b]||c)};e.get=function(a,c){var a=g.byId(a),b=c.toLowerCase(),d=h.names[b]||c,e=
k[d];value=a[d];if(e&&typeof value!="undefined")return value;if(d!="href"&&(typeof value=="boolean"||l.isFunction(value)))return value;b=i[b]||c;return f(a,b)?a.getAttribute(b):null};e.set=function(a,c,b){a=g.byId(a);if(arguments.length==2){for(var d in c)e.set(a,d,c[d]);return a}d=c.toLowerCase();var f=h.names[d]||c,j=k[f];if(f=="style"&&typeof b!="string")return m.set(a,b),a;if(j||typeof b=="boolean"||l.isFunction(b))return h.set(a,c,b);a.setAttribute(i[d]||c,b);return a};e.remove=function(a,c){g.byId(a).removeAttribute(i[c.toLowerCase()]||
c)};e.getNodeProp=function(a,c){var a=g.byId(a),b=c.toLowerCase(),d=h.names[b]||c;if(d in a&&d!="href")return a[d];b=i[b]||c;return f(a,b)?a.getAttribute(b):null}});