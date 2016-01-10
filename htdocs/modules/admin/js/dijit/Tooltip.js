//>>built
require({cache:{"url:dijit/templates/Tooltip.html":'<div class="dijitTooltip dijitTooltipLeft" id="dojoTooltip"\n\t><div class="dijitTooltipContainer dijitTooltipContents" data-dojo-attach-point="containerNode" role=\'alert\'></div\n\t><div class="dijitTooltipConnector" data-dojo-attach-point="connectorNode"></div\n></div>\n'}});
define("dijit/Tooltip",["dojo/_base/array","dojo/_base/declare","dojo/_base/fx","dojo/dom","dojo/dom-class","dojo/dom-geometry","dojo/dom-style","dojo/_base/lang","dojo/_base/sniff","dojo/_base/window","./_base/manager","./place","./_Widget","./_TemplatedMixin","./BackgroundIframe","dojo/text!./templates/Tooltip.html","."],function(b,k,l,m,s,n,j,d,o,t,u,v,p,w,x,y,g){var r=k("dijit._MasterTooltip",[p,w],{duration:u.defaultDuration,templateString:y,postCreate:function(){t.body().appendChild(this.domNode);
this.bgIframe=new x(this.domNode);this.fadeIn=l.fadeIn({node:this.domNode,duration:this.duration,onEnd:d.hitch(this,"_onShow")});this.fadeOut=l.fadeOut({node:this.domNode,duration:this.duration,onEnd:d.hitch(this,"_onHide")})},show:function(a,e,b,q,z){if(!this.aroundNode||!(this.aroundNode===e&&this.containerNode.innerHTML==a))if(this.domNode.width="auto",this.fadeOut.status()=="playing")this._onDeck=arguments;else{this.containerNode.innerHTML=a;this.set("textDir",z);this.containerNode.align=q?"right":
"left";var f=v.around(this.domNode,e,b&&b.length?b:c.defaultPosition,!q,d.hitch(this,"orient")),h=f.aroundNodePos;if(f.corner.charAt(0)=="M"&&f.aroundCorner.charAt(0)=="M")this.connectorNode.style.top=h.y+(h.h-this.connectorNode.offsetHeight>>1)-f.y+"px",this.connectorNode.style.left="";else if(f.corner.charAt(1)=="M"&&f.aroundCorner.charAt(1)=="M")this.connectorNode.style.left=h.x+(h.w-this.connectorNode.offsetWidth>>1)-f.x+"px";j.set(this.domNode,"opacity",0);this.fadeIn.play();this.isShowingNow=
!0;this.aroundNode=e}},orient:function(a,e,c,b,d){this.connectorNode.style.top="";var f=b.w-this.connectorNode.offsetWidth;a.className="dijitTooltip "+{"MR-ML":"dijitTooltipRight","ML-MR":"dijitTooltipLeft","TM-BM":"dijitTooltipAbove","BM-TM":"dijitTooltipBelow","BL-TL":"dijitTooltipBelow dijitTooltipABLeft","TL-BL":"dijitTooltipAbove dijitTooltipABLeft","BR-TR":"dijitTooltipBelow dijitTooltipABRight","TR-BR":"dijitTooltipAbove dijitTooltipABRight","BR-BL":"dijitTooltipRight","BL-BR":"dijitTooltipLeft"}[e+
"-"+c];this.domNode.style.width="auto";var h=n.getContentBox(this.domNode),g=Math.min(Math.max(f,1),h.w),i=g<h.w;this.domNode.style.width=g+"px";if(i&&(this.containerNode.style.overflow="auto",i=this.containerNode.scrollWidth,this.containerNode.style.overflow="visible",i>g))i=i+j.get(this.domNode,"paddingLeft")+j.get(this.domNode,"paddingRight"),this.domNode.style.width=i+"px";c.charAt(0)=="B"&&e.charAt(0)=="B"?(a=n.getMarginBox(a),e=this.connectorNode.offsetHeight,a.h>b.h?(this.connectorNode.style.top=
b.h-(d.h+e>>1)+"px",this.connectorNode.style.bottom=""):(this.connectorNode.style.bottom=Math.min(Math.max(d.h/2-e/2,0),a.h-e)+"px",this.connectorNode.style.top="")):(this.connectorNode.style.top="",this.connectorNode.style.bottom="");return Math.max(0,h.w-f)},_onShow:function(){if(o("ie"))this.domNode.style.filter=""},hide:function(a){if(this._onDeck&&this._onDeck[1]==a)this._onDeck=null;else if(this.aroundNode===a)this.fadeIn.stop(),this.isShowingNow=!1,this.aroundNode=null,this.fadeOut.play()},
_onHide:function(){this.domNode.style.cssText="";this.containerNode.innerHTML="";if(this._onDeck)this.show.apply(this,this._onDeck),this._onDeck=null},_setAutoTextDir:function(a){this.applyTextDir(a,o("ie")?a.outerText:a.textContent);b.forEach(a.children,function(a){this._setAutoTextDir(a)},this)},_setTextDirAttr:function(a){this._set("textDir",typeof a!="undefined"?a:"");a=="auto"?this._setAutoTextDir(this.containerNode):this.containerNode.dir=this.textDir}});g.showTooltip=function(a,e,b,d,j){if(!c._masterTT)g._masterTT=
c._masterTT=new r;return c._masterTT.show(a,e,b,d,j)};g.hideTooltip=function(a){return c._masterTT&&c._masterTT.hide(a)};var c=k("dijit.Tooltip",p,{label:"",showDelay:400,connectId:[],position:[],_setConnectIdAttr:function(a){b.forEach(this._connections||[],function(a){b.forEach(a,d.hitch(this,"disconnect"))},this);this._connectIds=b.filter(d.isArrayLike(a)?a:a?[a]:[],function(a){return m.byId(a)});this._connections=b.map(this._connectIds,function(a){a=m.byId(a);return[this.connect(a,"onmouseenter",
"_onHover"),this.connect(a,"onmouseleave","_onUnHover"),this.connect(a,"onfocus","_onHover"),this.connect(a,"onblur","_onUnHover")]},this);this._set("connectId",a)},addTarget:function(a){a=a.id||a;b.indexOf(this._connectIds,a)==-1&&this.set("connectId",this._connectIds.concat(a))},removeTarget:function(a){a=b.indexOf(this._connectIds,a.id||a);a>=0&&(this._connectIds.splice(a,1),this.set("connectId",this._connectIds))},buildRendering:function(){this.inherited(arguments);s.add(this.domNode,"dijitTooltipData")},
startup:function(){this.inherited(arguments);var a=this.connectId;b.forEach(d.isArrayLike(a)?a:[a],this.addTarget,this)},_onHover:function(a){if(!this._showTimer){var b=a.target;this._showTimer=setTimeout(d.hitch(this,function(){this.open(b)}),this.showDelay)}},_onUnHover:function(){this._focus||(this._showTimer&&(clearTimeout(this._showTimer),delete this._showTimer),this.close())},open:function(a){this._showTimer&&(clearTimeout(this._showTimer),delete this._showTimer);c.show(this.label||this.domNode.innerHTML,
a,this.position,!this.isLeftToRight(),this.textDir);this._connectNode=a;this.onShow(a,this.position)},close:function(){this._connectNode&&(c.hide(this._connectNode),delete this._connectNode,this.onHide());this._showTimer&&(clearTimeout(this._showTimer),delete this._showTimer)},onShow:function(){},onHide:function(){},uninitialize:function(){this.close();this.inherited(arguments)}});c._MasterTooltip=r;c.show=g.showTooltip;c.hide=g.hideTooltip;c.defaultPosition=["after","before"];return c});