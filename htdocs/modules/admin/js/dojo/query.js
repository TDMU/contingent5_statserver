/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/query",["./_base/kernel","./has","./dom","./on","./_base/array","./_base/lang","./selector/_loader","./selector/_loader!default"],function(f,k,s,t,i,h,u,r){function p(a,b){var c=function(c,d){if(typeof d=="string"&&(d=s.byId(d),!d))return new b([]);var e=typeof c=="string"?a(c,d):c.orphan?c:[c];if(e.orphan)return e;return new b(e)};c.matches=a.match||function(a,b,d){return c.filter([a],b,d).length>0};c.filter=a.filter||function(a,b,d){return c(b,d).filter(function(b){return i.indexOf(a,
b)>-1})};if(typeof a!="function")var d=a.search,a=function(a,b){return d(b||document,a)};return c}k.add("array-extensible",function(){return h.delegate([],{length:1}).length==1&&!k("bug-for-in-skips-shadowed")});var q=Array.prototype,l=q.slice,v=q.concat,m=i.forEach,n=function(a,b,c){b=[0].concat(l.call(b,0));c=c||f.global;return function(d){b[0]=d;return a.apply(c,b)}},g=function(a){var b=this instanceof e&&k("array-extensible");typeof a=="number"&&(a=Array(a));var c=a&&"length"in a?a:arguments;
if(b||!c.sort){for(var d=b?this:[],g=d.length=c.length,f=0;f<g;f++)d[f]=c[f];if(b)return d;c=d}h._mixin(c,o);c._NodeListCtor=function(a){return e(a)};return c},e=g,o=e.prototype=k("array-extensible")?[]:{};e._wrap=o._wrap=function(a,b,c){a=new (c||this._NodeListCtor||e)(a);return b?a._stash(b):a};e._adaptAsMap=function(a,b){return function(){return this.map(n(a,arguments,b))}};e._adaptAsForEach=function(a,b){return function(){this.forEach(n(a,arguments,b));return this}};e._adaptAsFilter=function(a,
b){return function(){return this.filter(n(a,arguments,b))}};e._adaptWithCondition=function(a,b,c){return function(){var d=arguments,e=n(a,d,c);if(b.call(c||f.global,d))return this.map(e);this.forEach(e);return this}};m(["slice","splice"],function(a){var b=q[a];o[a]=function(){return this._wrap(b.apply(this,arguments),a=="slice"?this:null)}});m(["indexOf","lastIndexOf","every","some"],function(a){var b=i[a];o[a]=function(){return b.apply(f,[this].concat(l.call(arguments,0)))}});h.extend(g,{constructor:e,
_NodeListCtor:e,toString:function(){return this.join(",")},_stash:function(a){this._parent=a;return this},on:function(a,b){var c=this.map(function(c){return t(c,a,b)});c.remove=function(){for(var a=0;a<c.length;a++)c[a].remove()};return c},end:function(){return this._parent?this._parent:new this._NodeListCtor(0)},concat:function(){var a=h.isArray(this)?this:l.call(this,0),b=i.map(arguments,function(a){return a&&!h.isArray(a)&&(typeof g!="undefined"&&a.constructor===g||a.constructor===this._NodeListCtor)?
l.call(a,0):a});return this._wrap(v.apply(a,b),this)},map:function(a,b){return this._wrap(i.map(this,a,b),this)},forEach:function(a,b){m(this,a,b);return this},filter:function(a){var b=arguments,c=this,d=0;if(typeof a=="string"){c=j._filterResult(this,b[0]);if(b.length==1)return c._stash(this);d=1}return this._wrap(i.filter(c,b[d],b[d+1]),this)},instantiate:function(a,b){var c=h.isFunction(a)?a:h.getObject(a),b=b||{};return this.forEach(function(a){new c(b,a)})},at:function(){var a=new this._NodeListCtor(0);
m(arguments,function(b){b<0&&(b=this.length+b);this[b]&&a.push(this[b])},this);return a._stash(this)}});var j=p(r,g);f.query=p(r,function(a){return g(a)});j.load=function(a,b,c){u.load(a,b,function(a){c(p(a,g))})};f._filterQueryResult=j._filterResult=function(a,b,c){return new g(j.filter(a,b,c))};f.NodeList=j.NodeList=g;return j});