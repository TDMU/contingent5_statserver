//>>built
define(["dijit","dojo","dojox","dojo/require!dojo/data/ItemFileReadStore"],function(l,e){e.provide("dojox.data.demos.stores.LazyLoadJSIStore");e.require("dojo.data.ItemFileReadStore");e.declare("dojox.data.demos.stores.LazyLoadJSIStore",e.data.ItemFileReadStore,{constructor:function(){},isItemLoaded:function(b){if(this.getValue(b,"type")==="stub")return!1;return!0},loadItem:function(b){var c=b.item;this._assertIsItem(c);var k=this.getValue(c,"name"),h=this.getValue(c,"parent"),a="";h&&(a+=h+"/");
a+=k+"/data.json";var f=this,a=e.xhrGet({url:a,handleAs:"json-comment-optional"});a.addCallback(function(g){delete c.type;delete c.parent;for(var a in g)c[a]=e.isArray(g[a])?g[a]:[g[a]];f._arrayOfAllItems[c[f._itemNumPropName]]=c;g=f.getAttributes(c);for(a in g)for(var j=c[g[a]],i=0;i<j.length;i++){var d=j[i];typeof d==="object"&&d.stub&&(d={type:["stub"],name:[d.stub],parent:[k]},h&&(d.parent[0]=h+"/"+d.parent[0]),f._arrayOfAllItems.push(d),d[f._storeRefPropName]=f,d[f._itemNumPropName]=f._arrayOfAllItems.length-
1,j[i]=d)}b.onItem&&b.onItem.call(b.scope?b.scope:e.global,c)});a.addErrback(function(a){b.onError&&b.onError.call(b.scope?b.scope:e.global,a)})}})});