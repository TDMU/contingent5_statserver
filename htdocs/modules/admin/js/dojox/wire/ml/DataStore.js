//>>built
define(["dijit","dojo","dojox","dojo/require!dijit/_Widget,dojox/wire/_base"],function(f,b,g){b.provide("dojox.wire.ml.DataStore");b.require("dijit._Widget");b.require("dojox.wire._base");b.declare("dojox.wire.ml.DataStore",f._Widget,{storeClass:"",postCreate:function(){this.store=this._createStore()},_createStore:function(){if(!this.storeClass)return null;var a=g.wire._getClass(this.storeClass);if(!a)return null;for(var b={},e=this.domNode.attributes,d=0;d<e.length;d++){var c=e.item(d);if(c.specified&&
!this[c.nodeName])b[c.nodeName]=c.nodeValue}return new a(b)},getFeatures:function(){return this.store.getFeatures()},fetch:function(a){return this.store.fetch(a)},save:function(a){this.store.save(a)},newItem:function(a){return this.store.newItem(a)},deleteItem:function(a){return this.store.deleteItem(a)},revert:function(){return this.store.revert()}})});