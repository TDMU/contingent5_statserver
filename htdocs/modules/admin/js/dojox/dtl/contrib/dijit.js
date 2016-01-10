//>>built
define("dojox/dtl/contrib/dijit",["dojo/_base/lang","dojo/_base/connect","dojo/_base/array","dojo/query","../_base","../dom","dojo/parser","dojo/_base/sniff"],function(g,j,o,l,h,q,m,p){function k(c){var b=c.cloneNode(!0);p("ie")&&l("script",b).forEach("item.text = this[index].text;",l("script",c));return b}g.getObject("dojox.dtl.contrib.dijit",!0);var d=h.contrib.dijit;d.AttachNode=g.extend(function(c,b){this._keys=c;this._object=b},{render:function(c,b){if(!this._rendered){this._rendered=!0;for(var a=
0,e;e=this._keys[a];a++)c.getThis()[e]=this._object||b.getParent()}return b},unrender:function(c,b){if(this._rendered){this._rendered=!1;for(var a=0,e;e=this._keys[a];a++)c.getThis()[e]===(this._object||b.getParent())&&delete c.getThis()[e]}return b},clone:function(){return new this.constructor(this._keys,this._object)}});d.EventNode=g.extend(function(c,b){this._command=c;for(var a,e=c.split(/\s*,\s*/),d=g.trim,h=[],n=[];a=e.pop();)if(a){var i=null;a.indexOf(":")!=-1?(i=a.split(":"),a=d(i[0]),i=d(i.slice(1).join(":"))):
a=d(a);i||(i=a);h.push(a);n.push(i)}this._types=h;this._fns=n;this._object=b;this._rendered=[]},{_clear:!1,render:function(c,b){for(var a=0,d;d=this._types[a];a++){!this._clear&&!this._object&&(b.getParent()[d]=null);var f=this._fns[a],g;f.indexOf(" ")!=-1&&(this._rendered[a]&&(j.disconnect(this._rendered[a]),this._rendered[a]=!1),g=o.map(f.split(" ").slice(1),function(a){return(new h._Filter(a)).resolve(c)}),f=f.split(" ",2)[0]);this._rendered[a]||(this._rendered[a]=this._object?j.connect(this._object,
d,c.getThis(),f):b.addEvent(c,d,f,g))}this._clear=!0;return b},unrender:function(c,b){for(;this._rendered.length;)j.disconnect(this._rendered.pop());return b},clone:function(){return new this.constructor(this._command,this._object)}});d.DojoTypeNode=g.extend(function(c,b){this._node=c;this._parsed=b;var a=c.getAttribute("dojoAttachEvent")||c.getAttribute("data-dojo-attach-event");if(a)this._events=new d.EventNode(g.trim(a));if(a=c.getAttribute("dojoAttachPoint")||c.getAttribute("data-dojo-attach-point"))this._attach=
new d.AttachNode(g.trim(a).split(/\s*,\s*/));b?(c=k(c),a=d.widgetsInTemplate,d.widgetsInTemplate=!1,this._template=new h.DomTemplate(c),d.widgetsInTemplate=a):this._dijit=m.instantiate([k(c)])[0]},{render:function(c,b){if(this._parsed){var a=new h.DomBuffer;this._template.render(c,a);var a=k(a.getRootNode()),d=document.createElement("div");d.appendChild(a);var f=d.innerHTML;d.removeChild(a);if(f!=this._rendered)this._rendered=f,this._dijit&&this._dijit.destroyRecursive(),this._dijit=m.instantiate([a])[0]}a=
this._dijit.domNode;if(this._events)this._events._object=this._dijit,this._events.render(c,b);if(this._attach)this._attach._object=this._dijit,this._attach.render(c,b);return b.concat(a)},unrender:function(c,b){return b.remove(this._dijit.domNode)},clone:function(){return new this.constructor(this._node,this._parsed)}});g.mixin(d,{widgetsInTemplate:!0,dojoAttachPoint:function(c,b){return new d.AttachNode(b.contents.slice(b.contents.indexOf("data-")!==-1?23:16).split(/\s*,\s*/))},dojoAttachEvent:function(c,
b){return new d.EventNode(b.contents.slice(b.contents.indexOf("data-")!==-1?23:16))},dojoType:function(c,b){var a=!1;b.contents.slice(-7)==" parsed"&&(a=!0);var e=b.contents.indexOf("data-")!==-1?b.contents.slice(15):b.contents.slice(9),e=a?e.slice(0,-7):e.toString();if(d.widgetsInTemplate){var f=c.swallowNode();f.setAttribute("data-dojo-type",e);return new d.DojoTypeNode(f,a)}return new h.AttributeNode("data-dojo-type",e)},on:function(c,b){var a=b.contents.split();return new d.EventNode(a[0]+":"+
a.slice(1).join(" "))}});d["data-dojo-type"]=d.dojoType;d["data-dojo-attach-point"]=d.dojoAttachPoint;d["data-dojo-attach-event"]=d.dojoAttachEvent;h.register.tags("dojox.dtl.contrib",{dijit:["attr:dojoType","attr:data-dojo-type","attr:dojoAttachPoint","attr:data-dojo-attach-point",["attr:attach","dojoAttachPoint"],["attr:attach","data-dojo-attach-point"],"attr:dojoAttachEvent","attr:data-dojo-attach-event",[/(attr:)?on(click|key(up))/i,"on"]]});return dojox.dtl.contrib.dijit});