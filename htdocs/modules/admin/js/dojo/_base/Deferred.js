/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/_base/Deferred",["./kernel","./lang"],function(c,e){var l=function(){},q=Object.freeze||function(){};c.Deferred=function(b){function n(a){if(i)throw Error("This deferred has already been resolved");g=a;i=!0;o()}function o(){for(var a;!a&&h;){var d=h;h=h.next;if(a=d.progress==l)i=!1;var f=j?d.error:d.resolved;if(f)try{var b=f(g);b&&typeof b.then==="function"?b.then(e.hitch(d.deferred,"resolve"),e.hitch(d.deferred,"reject"),e.hitch(d.deferred,"progress")):(f=a&&b===void 0,a&&!f&&(j=b instanceof
Error),d.deferred[f&&j?"reject":"resolve"](f?g:b))}catch(c){d.deferred.reject(c)}else j?d.deferred.reject(g):d.deferred.resolve(g)}}var g,i,j,m,h,k=this.promise={};this.resolve=this.callback=function(a){this.fired=0;this.results=[a,null];n(a)};this.reject=this.errback=function(a){j=!0;this.fired=1;n(a);this.results=[null,a];if(!a||a.log!==!1)(c.config.deferredOnError||function(a){console.error(a)})(a)};this.progress=function(a){for(var d=h;d;){var b=d.progress;b&&b(a);d=d.next}};this.addCallbacks=
function(a,b){this.then(a,b,l);return this};k.then=this.then=function(a,b,f){var e=f==l?this:new c.Deferred(k.cancel),a={resolved:a,error:b,progress:f,deferred:e};h?m=m.next=a:h=m=a;i&&o();return e.promise};var p=this;k.cancel=this.cancel=function(){if(!i){var a=b&&b(p);if(!i)a instanceof Error||(a=Error(a)),a.log=!1,p.reject(a)}};q(k)};e.extend(c.Deferred,{addCallback:function(){return this.addCallbacks(e.hitch.apply(c,arguments))},addErrback:function(){return this.addCallbacks(null,e.hitch.apply(c,
arguments))},addBoth:function(){var b=e.hitch.apply(c,arguments);return this.addCallbacks(b,b)},fired:-1});c.Deferred.when=c.when=function(b,c,e,g){if(b&&typeof b.then==="function")return b.then(c,e,g);return c?c(b):b};return c.Deferred});