/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/_base/array",["./kernel","../has","./lang"],function(n,q,o){function j(a){return i[a]=new Function("item","index","array",a)}function l(a){var d=!a;return function(f,b,c){var g=0,e=f&&f.length||0,h;e&&typeof f=="string"&&(f=f.split(""));typeof b=="string"&&(b=i[b]||j(b));if(c)for(;g<e;++g){if(h=!b.call(c,f[g],g,f),a^h)return!h}else for(;g<e;++g)if(h=!b(f[g],g,f),a^h)return!h;return d}}function m(a){var d=1,f=0,b=0;a||(d=f=b=-1);return function(c,g,e,h){if(h&&d>0)return k.lastIndexOf(c,
g,e);var h=c&&c.length||0,i=a?h+b:f;e===p?e=a?f:h+b:e<0?(e=h+e,e<0&&(e=f)):e=e>=h?h+b:e;for(h&&typeof c=="string"&&(c=c.split(""));e!=i;e+=d)if(c[e]==g)return e;return-1}}var i={},p,k;k={every:l(!1),some:l(!0),indexOf:m(!0),lastIndexOf:m(!1),forEach:function(a,d,f){var b=0,c=a&&a.length||0;c&&typeof a=="string"&&(a=a.split(""));typeof d=="string"&&(d=i[d]||j(d));if(f)for(;b<c;++b)d.call(f,a[b],b,a);else for(;b<c;++b)d(a[b],b,a)},map:function(a,d,f,b){var c=0,g=a&&a.length||0,b=new (b||Array)(g);g&&
typeof a=="string"&&(a=a.split(""));typeof d=="string"&&(d=i[d]||j(d));if(f)for(;c<g;++c)b[c]=d.call(f,a[c],c,a);else for(;c<g;++c)b[c]=d(a[c],c,a);return b},filter:function(a,d,f){var b=0,c=a&&a.length||0,g=[],e;c&&typeof a=="string"&&(a=a.split(""));typeof d=="string"&&(d=i[d]||j(d));if(f)for(;b<c;++b)e=a[b],d.call(f,e,b,a)&&g.push(e);else for(;b<c;++b)e=a[b],d(e,b,a)&&g.push(e);return g},clearCache:function(){i={}}};o.mixin(n,k);return k});