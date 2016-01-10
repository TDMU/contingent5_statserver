//>>built
define("dojox/mobile/_DataListMixin",["dojo/_base/array","dojo/_base/connect","dojo/_base/declare","dojo/_base/lang","dijit/registry","./ListItem"],function(e,d,g,f,h,i){return g("dojox.mobile._DataListMixin",null,{store:null,query:null,queryOptions:null,buildRendering:function(){this.inherited(arguments);if(this.store){var a=this.store;this.store=null;this.setStore(a,this.query,this.queryOptions)}},setStore:function(a,c,b){if(a!==this.store){this.store=a;this.query=c;this.queryOptions=b;if(a&&a.getFeatures()["dojo.data.api.Notification"])e.forEach(this._conn||
[],d.disconnect),this._conn=[d.connect(a,"onSet",this,"onSet"),d.connect(a,"onNew",this,"onNew"),d.connect(a,"onDelete",this,"onDelete")];this.refresh()}},refresh:function(){this.store&&this.store.fetch({query:this.query,queryOptions:this.queryOptions,onComplete:f.hitch(this,"onComplete"),onError:f.hitch(this,"onError")})},createListItem:function(a){var c={},b=this.store.getLabelAttributes(a),d=b?b[0]:null;e.forEach(this.store.getAttributes(a),function(b){b===d?c.label=this.store.getLabel(a):c[b]=
this.store.getValue(a,b)},this);b=new i(c);a._widgetId=b.id;return b},generateList:function(a){e.forEach(this.getChildren(),function(a){a.destroyRecursive()});e.forEach(a,function(a){this.addChild(this.createListItem(a))},this)},onComplete:function(a,c){this.generateList(a,c)},onError:function(){},onSet:function(){},onNew:function(a){this.addChild(this.createListItem(a))},onDelete:function(a){h.byId(a._widgetId).destroyRecursive()}})});