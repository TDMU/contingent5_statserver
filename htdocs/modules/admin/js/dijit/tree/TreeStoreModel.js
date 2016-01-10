//>>built
define("dijit/tree/TreeStoreModel",["dojo/_base/array","dojo/aspect","dojo/_base/declare","dojo/_base/json","dojo/_base/lang"],function(h,j,k,l,f){return k("dijit.tree.TreeStoreModel",null,{store:null,childrenAttrs:["children"],newItemIdAttr:"id",labelAttr:"",root:null,query:null,deferItemLoadingUntilExpand:!1,constructor:function(a){f.mixin(this,a);this.connects=[];a=this.store;if(!a.getFeatures()["dojo.data.api.Identity"])throw Error("dijit.Tree: store must support dojo.data.Identity");if(a.getFeatures()["dojo.data.api.Notification"])this.connects=
this.connects.concat([j.after(a,"onNew",f.hitch(this,"onNewItem"),!0),j.after(a,"onDelete",f.hitch(this,"onDeleteItem"),!0),j.after(a,"onSet",f.hitch(this,"onSetItem"),!0)])},destroy:function(){for(var a;a=this.connects.pop();)a.remove()},getRoot:function(a,b){this.root?a(this.root):this.store.fetch({query:this.query,onComplete:f.hitch(this,function(b){if(b.length!=1)throw Error(this.declaredClass+": query "+l.stringify(this.query)+" returned "+b.length+" items, but must return exactly one item");
this.root=b[0];a(this.root)}),onError:b})},mayHaveChildren:function(a){return h.some(this.childrenAttrs,function(b){return this.store.hasAttribute(a,b)},this)},getChildren:function(a,b,c){var g=this.store;if(g.isItemLoaded(a)){for(var d=[],e=0;e<this.childrenAttrs.length;e++)var m=g.getValues(a,this.childrenAttrs[e]),d=d.concat(m);var i=0;this.deferItemLoadingUntilExpand||h.forEach(d,function(a){g.isItemLoaded(a)||i++});i==0?b(d):h.forEach(d,function(a,e){g.isItemLoaded(a)||g.loadItem({item:a,onItem:function(a){d[e]=
a;--i==0&&b(d)},onError:c})})}else{var n=f.hitch(this,arguments.callee);g.loadItem({item:a,onItem:function(a){n(a,b,c)},onError:c})}},isItem:function(a){return this.store.isItem(a)},fetchItemByIdentity:function(a){this.store.fetchItemByIdentity(a)},getIdentity:function(a){return this.store.getIdentity(a)},getLabel:function(a){return this.labelAttr?this.store.getValue(a,this.labelAttr):this.store.getLabel(a)},newItem:function(a,b,c){var g={parent:b,attribute:this.childrenAttrs[0]},d;this.newItemIdAttr&&
a[this.newItemIdAttr]?this.fetchItemByIdentity({identity:a[this.newItemIdAttr],scope:this,onItem:function(e){e?this.pasteItem(e,null,b,!0,c):(d=this.store.newItem(a,g))&&c!=void 0&&this.pasteItem(d,b,b,!1,c)}}):(d=this.store.newItem(a,g))&&c!=void 0&&this.pasteItem(d,b,b,!1,c)},pasteItem:function(a,b,c,g,d){var e=this.store,f=this.childrenAttrs[0];b&&h.forEach(this.childrenAttrs,function(c){if(e.containsValue(b,c,a)){if(!g){var d=h.filter(e.getValues(b,c),function(b){return b!=a});e.setValues(b,c,
d)}f=c}});if(c)if(typeof d=="number"){var i=e.getValues(c,f).slice();i.splice(d,0,a);e.setValues(c,f,i)}else e.setValues(c,f,e.getValues(c,f).concat(a))},onChange:function(){},onChildrenChange:function(){},onDelete:function(){},onNewItem:function(a,b){b&&this.getChildren(b.item,f.hitch(this,function(a){this.onChildrenChange(b.item,a)}))},onDeleteItem:function(a){this.onDelete(a)},onSetItem:function(a,b){if(h.indexOf(this.childrenAttrs,b)!=-1)this.getChildren(a,f.hitch(this,function(b){this.onChildrenChange(a,
b)}));else this.onChange(a)}})});