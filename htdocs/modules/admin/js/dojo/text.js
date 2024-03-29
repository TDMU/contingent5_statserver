/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/text",["./_base/kernel","require","./has","./_base/xhr"],function(n,o,q,p){var h;h=function(a,c,b){p("GET",{url:a,sync:!!c,load:b})};var e={},i=function(a){if(a){var a=a.replace(/^\s*<\?xml(\s)+version=[\'\"](\d)*.(\d)*[\'\"](\s)*\?>/im,""),c=a.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);c&&(a=c[1])}else a="";return a},l={},g={};n.cache=function(a,c,b){var d;typeof a=="string"?/\//.test(a)?(d=a,b=c):d=o.toUrl(a.replace(/\./g,"/")+(c?"/"+c:"")):(d=a+"",b=c);a=b!=void 0&&typeof b!="string"?
b.value:b;b=b&&b.sanitize;return typeof a=="string"?(e[d]=a,b?i(a):a):a===null?(delete e[d],null):(d in e||h(d,!0,function(a){e[d]=a}),b?i(e[d]):e[d])};return{dynamic:!0,normalize:function(a,c){var b=a.split("!"),d=b[0];return(/^\./.test(d)?c(d):d)+(b[1]?"!"+b[1]:"")},load:function(a,c,b){var a=a.split("!"),d=a.length>1,j=a[0],f=c.toUrl(a[0]),a=l,k=function(a){b(d?i(a):a)};j in e?a=e[j]:f in c.cache?a=c.cache[f]:f in e&&(a=e[f]);if(a===l)if(g[f])g[f].push(k);else{var m=g[f]=[k];h(f,!c.async,function(a){e[j]=
e[f]=a;for(var b=0;b<m.length;)m[b++](a);delete g[f]})}else k(a)}}});