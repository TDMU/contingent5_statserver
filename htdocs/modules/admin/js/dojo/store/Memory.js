/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/store/Memory",["../_base/declare","./util/QueryResults","./util/SimpleQueryEngine"],function(f,g,h){return f("dojo.store.Memory",null,{constructor:function(a){for(var b in a)this[b]=a[b];this.setData(this.data||[])},data:null,idProperty:"id",index:null,queryEngine:h,get:function(a){return this.data[this.index[a]]},getIdentity:function(a){return a[this.idProperty]},put:function(a,b){var c=this.data,e=this.index,d=this.idProperty,d=b&&"id"in b?b.id:d in a?a[d]:Math.random();if(d in e){if(b&&
b.overwrite===!1)throw Error("Object already exists");c[e[d]]=a}else e[d]=c.push(a)-1;return d},add:function(a,b){(b=b||{}).overwrite=!1;return this.put(a,b)},remove:function(a){var b=this.index,c=this.data;if(a in b)return c.splice(b[a],1),this.setData(c),!0},query:function(a,b){return g(this.queryEngine(a,b)(this.data))},setData:function(a){var d;a.items?(this.idProperty=a.identifier,d=this.data=a.items,a=d):this.data=a;this.index={};for(var b=0,c=a.length;b<c;b++)this.index[a[b][this.idProperty]]=
b}})});