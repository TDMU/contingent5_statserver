/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/store/Observable",["../_base/kernel","../_base/lang","../_base/Deferred","../_base/array"],function(t,l,m,p){return l.getObject("dojo.store",!0).Observable=function(c){function n(a,i){var b=c[a];b&&(c[a]=function(c){if(f)return b.apply(this,arguments);f=!0;try{var a=b.apply(this,arguments);m.when(a,function(a){i(typeof a=="object"&&a||c)});return a}finally{f=!1}})}var j=[],q=0;c.notify=function(a,c){q++;for(var b=j.slice(),e=0,g=b.length;e<g;e++)b[e](a,c)};var s=c.query;c.query=function(a,
i){var i=i||{},b=s.apply(this,arguments);if(b&&b.forEach){var e=l.mixin({},i);delete e.start;delete e.count;var g=c.queryEngine&&c.queryEngine(a,e),n=q,o=[],f;b.observe=function(a,e){o.push(a)==1&&j.push(f=function(a,f){m.when(b,function(b){var j=b.length!=i.count,d,l;if(++n!=q)throw Error("Query is out of date, you must observe() the query prior to any data modifications");var m,k=-1,h=-1;if(f!==void 0){d=0;for(l=b.length;d<l;d++){var r=b[d];if(c.getIdentity(r)==f){m=r;k=d;(g||!a)&&b.splice(d,1);
break}}}if(g){if(a&&(g.matches?g.matches(a):g([a]).length))d=k>-1?k:b.length,b.splice(d,0,a),h=p.indexOf(g(b),a),b.splice(d,1),i.start&&h==0||!j&&h==b.length?h=-1:b.splice(h,0,a)}else a&&!i.start&&(h=k>=0?k:c.defaultIndex||0);if((k>-1||h>-1)&&(e||!g||k!=h)){j=o.slice();for(d=0;b=j[d];d++)b(a||m,k,h)}})});return{cancel:function(){var b=p.indexOf(o,a);b>-1&&(o.splice(b,1),o.length||j.splice(p.indexOf(j,f),1))}}}}return b};var f;n("put",function(a){c.notify(a,c.getIdentity(a))});n("add",function(a){c.notify(a)});
n("remove",function(a){c.notify(void 0,a)});return c}});