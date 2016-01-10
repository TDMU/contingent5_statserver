//>>built
define("dojox/app/scene",["dojo/_base/kernel","dojo/_base/declare","dojo/_base/connect","dojo/_base/array","dojo/_base/Deferred","dojo/_base/lang","dojo/_base/sniff","dojo/dom-style","dojo/dom-geometry","dojo/dom-class","dojo/dom-construct","dojo/dom-attr","dojo/query","dijit","dojox","dijit/_WidgetBase","dijit/_TemplatedMixin","dijit/_WidgetsInTemplateMixin","dojox/css3/transit","./animation","./model","./view","./bind"],function(i,x,t,j,k,q,y,e,n,o,z,u,A,p,D,E,F,G,B,r,v,C,w){var s=function(a,b){var c=
a.resize?a.resize(b):n.setMarginBox(a.domNode,b);c?i.mixin(a,c):(i.mixin(a,n.getMarginBox(a.domNode)),i.mixin(a,b))};return x("dojox.app.scene",[p._WidgetBase,p._TemplatedMixin,p._WidgetsInTemplateMixin],{isContainer:!0,widgetsInTemplate:!0,defaultView:"default",selectedChild:null,baseClass:"scene mblView",isFullScreen:!1,defaultViewType:C,getParent:function(){return null},constructor:function(a){this.children={};if(a.parent)this.parent=a.parent;if(a.app)this.app=a.app},buildRendering:function(){this.inherited(arguments);
e.set(this.domNode,{width:"100%",height:"100%"});o.add(this.domNode,"dijitContainer")},splitChildRef:function(a){var b=a.split(",");if(b.length>0)var c=b.shift();else console.warn("invalid child id passed to splitChildRef(): ",a);return{id:c||this.defaultView,next:b.join(",")}},loadChild:function(a,b){if(!a)var c=this.defaultView?this.defaultView.split(","):"default",a=c.shift(),b=c.join(",");c=this.id+"_"+a;if(this.children[c])return this.children[c];if(this.views&&this.views[a]){var d=this.views[a];
if(!d.dependencies)d.dependencies=[];var c=d.template?d.dependencies.concat(["dojo/text!app/"+d.template]):d.dependencies.concat([]),f=new k;c.length>0?require(c,function(){f.resolve.call(f,arguments)}):f.resolve(!0);var m=new k,h=this;k.when(f,function(c){var g;if(d.type)g=i.getObject(d.type);else if(h.defaultViewType)g=h.defaultViewType;else throw Error("Unable to find appropriate ctor for the base child class");c=i.mixin({},d,{id:h.id+"_"+a,templateString:d.template?c[c.length-1]:"<div></div>",
parent:h,app:h.app});if(b)c.defaultView=b;g=new g(c);if(!g.loadedModels)g.loadedModels=v(d.models,h.loadedModels),w([g],g.loadedModels);var f=h.addChild(g);t.publish("/app/loadchild",[g]);var e;b=b.split(",");b[0].length>0&&b.length>1?e=g.loadChild(b[0],b[1]):b[0].length>0&&(e=g.loadChild(b[0],""));i.when(e,function(){m.resolve(f)})});return m}throw Error("Child '"+a+"' not found.");},resize:function(a,b){var c=this.domNode;if(a){n.setMarginBox(c,a);if(a.t)c.style.top=a.t+"px";if(a.l)c.style.left=
a.l+"px"}var d=b||{};i.mixin(d,a||{});if(!("h"in d)||!("w"in d))d=i.mixin(n.getMarginBox(c),d);var f=e.getComputedStyle(c),m=n.getMarginExtents(c,f),h=n.getBorderExtents(c,f),d=this._borderBox={w:d.w-(m.w+h.w),h:d.h-(m.h+h.h)},m=n.getPadExtents(c,f);this._contentBox={l:e.toPixelValue(c,f.paddingLeft),t:e.toPixelValue(c,f.paddingTop),w:d.w-m.w,h:d.h-m.h};this.layout()},layout:function(){var a;this.selectedChild&&this.selectedChild.isFullScreen?console.warn("fullscreen sceen layout"):(a=A("> [region]",
this.domNode).map(function(b){var a=p.getEnclosingWidget(b);if(a)return a;return{domNode:b,region:u.get(b,"region")}}),this.selectedChild?a=j.filter(a,function(b){if(b.region=="center"&&this.selectedChild&&this.selectedChild.domNode!==b.domNode)return e.set(b.domNode,"zIndex",25),e.set(b.domNode,"display","none"),!1;else b.region!="center"&&(e.set(b.domNode,"display",""),e.set(b.domNode,"zIndex",100));return b.domNode&&b.region},this):j.forEach(a,function(b){b&&b.domNode&&b.region=="center"&&(e.set(b.domNode,
"zIndex",25),e.set(b.domNode,"display","none"))}));this._contentBox&&this.layoutChildren(this.domNode,this._contentBox,a);j.forEach(this.getChildren(),function(b){!b._started&&b.startup&&b.startup()})},layoutChildren:function(a,b,c,d,f){b=i.mixin({},b);o.add(a,"dijitLayoutContainer");c=j.filter(c,function(b){return b.region!="center"&&b.layoutAlign!="client"}).concat(j.filter(c,function(b){return b.region=="center"||b.layoutAlign=="client"}));j.forEach(c,function(a){var c=a.domNode,l=a.region||a.layoutAlign,
g=c.style;g.left=b.l+"px";g.top=b.t+"px";g.position="absolute";o.add(c,"dijitAlign"+(l.substring(0,1).toUpperCase()+l.substring(1)));c={};d&&d==a.id&&(c[a.region=="top"||a.region=="bottom"?"h":"w"]=f);l=="top"||l=="bottom"?(c.w=b.w,s(a,c),b.h-=a.h,l=="top"?b.t+=a.h:g.top=b.t+b.h+"px"):l=="left"||l=="right"?(c.h=b.h,s(a,c),b.w-=a.w,l=="left"?b.l+=a.w:g.left=b.l+b.w+"px"):(l=="client"||l=="center")&&s(a,b)})},getChildren:function(){return this._supportingWidgets},startup:function(){if(!this._started){this._started=
!0;var a=this.defaultView?this.defaultView.split(","):"default",b;b=a.shift();a.join(",");if(this.models&&!this.loadedModels)this.loadedModels=v(this.models),w(this.getChildren(),this.loadedModels);a=this.id+"_"+b;if(this.children[a]){this.set("selectedChild",this.children[a]);a=this.getParent&&this.getParent();if(!a||!a.isLayoutContainer)this.resize(),this.connect(y("ie")?this.domNode:i.global,"onresize",function(){this.resize()});j.forEach(this.getChildren(),function(b){b.startup()});this._startView&&
this._startView!=this.defaultView&&this.transition(this._startView,{})}}},addChild:function(a){o.add(a.domNode,this.baseClass+"_child");a.region="center";u.set(a.domNode,"region","center");this._supportingWidgets.push(a);z.place(a.domNode,this.domNode);return this.children[a.id]=a},removeChild:function(a){if(a){var b=a.domNode;b&&b.parentNode&&b.parentNode.removeChild(b);return a}},_setSelectedChildAttr:function(a){if(a!==this.selectedChild)return k.when(a,q.hitch(this,function(b){this.selectedChild&&
(this.selectedChild.deactivate&&this.selectedChild.deactivate(),e.set(this.selectedChild.domNode,"zIndex",25));this.selectedChild=b;e.set(b.domNode,"display","");e.set(b.domNode,"zIndex",50);this.selectedChild=b;this._started&&(b.startup&&!b._started?b.startup():b.activate&&b.activate());this.layout()}))},transition:function(a,b){var c,d,f,e=this.selectedChild;console.log("scene",this.id,a);a?(f=a.split(","),c=f.shift(),d=f.join(",")):(c=this.defaultView,this.views[this.defaultView]&&this.views[this.defaultView].defaultView&&
(d=this.views[this.defaultView].defaultView));f=this.loadChild(c,d);if(!e)return this.set("selectedChild",f);var h=new k;k.when(f,q.hitch(this,function(a){var g;if(a!==e){var f=r.getWaitingList([a.domNode,e.domNode]),j={};j[e.domNode.id]=r.playing[e.domNode.id]=new k;j[a.domNode.id]=r.playing[e.domNode.id]=new k;k.when(f,i.hitch(this,function(){this.set("selectedChild",a);t.publish("/app/transition",[a,c]);B(e.domNode,a.domNode,i.mixin({},b,{transition:this.defaultTransition||"none",transitionDefs:j})).then(q.hitch(this,
function(){d&&a.transition&&(g=a.transition(d,b));k.when(g,function(){h.resolve()})}))}))}else d&&a.transition&&(g=a.transition(d,b)),k.when(g,function(){h.resolve()})}));return h},toString:function(){return this.id},activate:function(){},deactive:function(){}})});