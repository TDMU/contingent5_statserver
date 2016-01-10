/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/Stateful",["./_base/kernel","./_base/declare","./_base/lang","./_base/array"],function(g,i,d,h){return g.declare("dojo.Stateful",null,{postscript:function(a){a&&d.mixin(this,a)},get:function(a){return this[a]},set:function(a,c){if(typeof a==="object"){for(var b in a)this.set(b,a[b]);return this}b=this[a];this[a]=c;this._watchCallbacks&&this._watchCallbacks(a,b,c);return this},watch:function(a,c){var b=this._watchCallbacks;if(!b)var g=this,b=this._watchCallbacks=function(a,c,e,d){var f=
function(b){if(b)for(var b=b.slice(),d=0,f=b.length;d<f;d++)try{b[d].call(g,a,c,e)}catch(h){console.error(h)}};f(b["_"+a]);d||f(b["*"])};!c&&typeof a==="function"?(c=a,a="*"):a="_"+a;var e=b[a];typeof e!=="object"&&(e=b[a]=[]);e.push(c);return{unwatch:function(){e.splice(h.indexOf(e,c),1)}}}})});