/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/store/DataStore",["../_base/lang","../_base/declare","../_base/Deferred","../_base/array","./util/QueryResults"],function(i,j,g,k,l){return j("dojo.store.DataStore",null,{target:"",constructor:function(a){i.mixin(this,a);if(!1 in a){var b;try{b=this.store.getIdentityAttributes()}catch(c){}this.idProperty=!b||!idAttributes[0]||this.idProperty}a=this.store.getFeatures();if(!a["dojo.data.api.Read"])this.get=null;if(!a["dojo.data.api.Identity"])this.getIdentity=null;if(!a["dojo.data.api.Write"])this.put=
this.add=null},idProperty:"id",store:null,_objectConverter:function(a){var b=this.store,c=this.idProperty;return function(d){for(var e={},h=b.getAttributes(d),f=0;f<h.length;f++)e[h[f]]=b.getValue(d,h[f]);c in e||(e[c]=b.getIdentity(d));return a(e)}},get:function(a){var b,c,d=new g;this.store.fetchItemByIdentity({identity:a,onItem:this._objectConverter(function(a){d.resolve(b=a)}),onError:function(a){d.reject(c=a)}});if(b)return b;if(c)throw c;return d.promise},put:function(a,b){var c=b&&typeof b.id!=
"undefined"||this.getIdentity(a),d=this.store,e=this.idProperty;typeof c=="undefined"?d.newItem(a):d.fetchItemByIdentity({identity:c,onItem:function(b){if(b)for(var c in a)c!=e&&d.getValue(b,c)!=a[c]&&d.setValue(b,c,a[c]);else d.newItem(a)}})},remove:function(a){var b=this.store;this.store.fetchItemByIdentity({identity:a,onItem:function(a){b.deleteItem(a)}})},query:function(a,b){var c,d=new g(function(){c.abort&&c.abort()});d.total=new g;var e=this._objectConverter(function(a){return a});c=this.store.fetch(i.mixin({query:a,
onBegin:function(a){d.total.resolve(a)},onComplete:function(a){d.resolve(k.map(a,e))},onError:function(a){d.reject(a)}},b));return l(d)},getIdentity:function(a){return a[this.idProperty]}})});