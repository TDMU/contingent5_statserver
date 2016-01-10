/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/i18n",["./_base/kernel","require","./has","./_base/array","./_base/lang","./_base/xhr"],function(d,l,m,p,k){var m=d.i18n={},n=/(^.*(^|\/)nls)(\/|$)([^\/]*)\/?([^\/]*)/,q=function(a,b,c,i){for(var h=[c+i],b=b.split("-"),e="",f=0;f<b.length;f++)e+=(e?"-":"")+b[f],(!a||a[e])&&h.push(c+e+"/"+i);return h},g={},r=d.getL10nName=function(a,b,c){c=c?c.toLowerCase():d.locale;a="dojo/i18n!"+a.replace(/\./g,"/");b=b.replace(/\./g,"/");return/root/i.test(c)?a+"/nls/"+b:a+"/nls/"+c+"/"+b},o=function(a,
b,c,i,h,e){a([b],function(f){var j=g[b+"/"]=k.clone(f.root),d=q(!f._v1x&&f,h,c,i);a(d,function(){for(var a=1;a<d.length;a++)g[d[a]]=j=k.mixin(k.clone(j),arguments[a]);g[b+"/"+h]=j;e&&e(k.delegate(j))})})};load=function(a,b,c){var a=n.exec(a),i=a[1]+"/",h=a[5]||a[4],e=i+h,f=(a=a[5]&&a[4])||d.locale,j=e+"/"+f;a?g[j]?c(g[j]):o(b,e,i,h,f,c):(a=d.config.extraLocale||[],a=k.isArray(a)?a:[a],a.push(f),p.forEach(a,function(a){o(b,e,i,h,a,a==f&&c)}))};var s=new Function("bundle","var __preAmdResult, __amdResult; function define(bundle){__amdResult= bundle;} __preAmdResult= eval(bundle); return [__preAmdResult, __amdResult];"),
t=function(a,b){var c=[];d.forEach(a,function(a){var b=l.toUrl(a+".js");if(g[b])c.push(g[b]);else{try{var e=l(a);if(e){c.push(e);return}}catch(f){}d.xhrGet({url:b,sync:!0,load:function(a){a=s(a);c.push(g[b]=a[0]?/nls\/[^\/]+\/[^\/]+$/.test(b)?a[0]:{root:a[0],_v1x:1}:a[1])},error:function(){c.push(g[b]={})}})}});b.apply(null,c)};m.getLocalization=function(a,b,c){var d,a=r(a,b,c).substring(10);load(a,!l.isXdUrl(l.toUrl(a+".js"))?t:l,function(a){d=a});return d};m.normalizeLocale=function(a){a=a?a.toLowerCase():
d.locale;a=="root"&&(a="ROOT");return a};return k.mixin(m,{dynamic:!0,normalize:function(a,b){var c=n.exec(a)[1];return/^\./.test(c)?b(c)+"/"+a.substring(c.length):a},load:load,cache:function(a,b){g[a]=b}})});