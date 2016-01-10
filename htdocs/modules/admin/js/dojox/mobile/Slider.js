//>>built
define("dojox/mobile/Slider",["dojo/_base/array","dojo/_base/connect","dojo/_base/declare","dojo/_base/lang","dojo/_base/window","dojo/dom-class","dojo/dom-construct","dojo/dom-geometry","dojo/dom-style","dijit/_WidgetBase","dijit/form/_FormValueMixin"],function(o,s,t,h,q,c,b,r,i,u,v){return t("dojox.mobile.Slider",[u,v],{value:0,min:0,max:100,step:1,baseClass:"mblSlider",flip:!1,orientation:"auto",halo:"8pt",buildRendering:function(){this.focusNode=this.domNode=b.create("div",{});this.valueNode=
b.create("input",this.srcNodeRef&&this.srcNodeRef.name?{type:"hidden",name:this.srcNodeRef.name}:{type:"hidden"},this.domNode,"last");var a=b.create("div",{style:{position:"relative",height:"100%",width:"100%"}},this.domNode,"last");this.progressBar=b.create("div",{style:{position:"absolute"},"class":"mblSliderProgressBar"},a,"last");this.touchBox=b.create("div",{style:{position:"absolute"},"class":"mblSliderTouchBox"},a,"last");this.handle=b.create("div",{style:{position:"absolute"},"class":"mblSliderHandle"},
a,"last");this.inherited(arguments)},_setValueAttr:function(a,b){this.valueNode.value=a;this.inherited(arguments);if(this._started){this.focusNode.setAttribute("aria-valuenow",a);var d=(a-this.min)*100/(this.max-this.min);b===!0?(c.add(this.handle,"mblSliderTransition"),c.add(this.progressBar,"mblSliderTransition")):(c.remove(this.handle,"mblSliderTransition"),c.remove(this.progressBar,"mblSliderTransition"));i.set(this.handle,this._attrs.handleLeft,(this._reversed?100-d:d)+"%");i.set(this.progressBar,
this._attrs.width,d+"%")}},postCreate:function(){function a(a){function c(a){b=e?a[this._attrs.pageX]:a.touches?a.touches[0][this._attrs.pageX]:a[this._attrs.clientX];d=b-n;d=Math.min(Math.max(d,0),j);a=this.step?(this.max-this.min)/this.step:j;if(a<=1||a==Infinity)a=j;f=(this.max-this.min)*Math.round(d*a/j)/a;f=this._reversed?this.max-f:this.min+f}a.preventDefault();var e=a.type=="mousedown",g=r.position(k,!1),l=i.get(q.body(),"zoom")||1;isNaN(l)&&(l=1);var m=i.get(k,"zoom")||1;isNaN(m)&&(m=1);var n=
g[this._attrs.x]*m*l+r.docScroll()[this._attrs.x],j=g[this._attrs.w]*m*l;h.hitch(this,c)(a);a.target==this.touchBox&&this.set("value",f,!0);o.forEach(p,s.disconnect);var a=q.doc.documentElement,p=[this.connect(a,e?"onmousemove":"ontouchmove",function(a){a.preventDefault();h.hitch(this,c)(a);this.set("value",f,!1)}),this.connect(a,e?"onmouseup":"ontouchend",function(a){a.preventDefault();o.forEach(p,h.hitch(this,"disconnect"));p=[];this.set("value",this.value,!0)})]}this.inherited(arguments);var b,
d,f,k=this.domNode;if(this.orientation=="auto")this.orientation=k.offsetHeight<=k.offsetWidth?"H":"V";c.add(this.domNode,o.map(this.baseClass.split(" "),h.hitch(this,function(a){return a+this.orientation})));var e=this.orientation!="V",n=e?this.isLeftToRight():!1,g=this.flip;this._reversed=!(e&&(n&&!g||!n&&g))||!e&&!g;this._attrs=e?{x:"x",w:"w",l:"l",r:"r",pageX:"pageX",clientX:"clientX",handleLeft:"left",left:this._reversed?"right":"left",width:"width"}:{x:"y",w:"h",l:"t",r:"b",pageX:"pageY",clientX:"clientY",
handleLeft:"top",left:this._reversed?"bottom":"top",width:"height"};this.progressBar.style[this._attrs.left]="0px";this.connect(this.touchBox,"touchstart",a);this.connect(this.touchBox,"onmousedown",a);this.connect(this.handle,"touchstart",a);this.connect(this.handle,"onmousedown",a);this.startup();this.set("value",this.value)}})});