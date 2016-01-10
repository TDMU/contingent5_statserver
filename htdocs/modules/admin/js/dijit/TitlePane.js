//>>built
require({cache:{"url:dijit/templates/TitlePane.html":'<div>\n\t<div data-dojo-attach-event="onclick:_onTitleClick, onkeypress:_onTitleKey"\n\t\t\tclass="dijitTitlePaneTitle" data-dojo-attach-point="titleBarNode">\n\t\t<div class="dijitTitlePaneTitleFocus" data-dojo-attach-point="focusNode">\n\t\t\t<img src="${_blankGif}" alt="" data-dojo-attach-point="arrowNode" class="dijitArrowNode" role="presentation"\n\t\t\t/><span data-dojo-attach-point="arrowNodeInner" class="dijitArrowNodeInner"></span\n\t\t\t><span data-dojo-attach-point="titleNode" class="dijitTitlePaneTextNode"></span>\n\t\t</div>\n\t</div>\n\t<div class="dijitTitlePaneContentOuter" data-dojo-attach-point="hideNode" role="presentation">\n\t\t<div class="dijitReset" data-dojo-attach-point="wipeNode" role="presentation">\n\t\t\t<div class="dijitTitlePaneContentInner" data-dojo-attach-point="containerNode" role="region" id="${id}_pane">\n\t\t\t\t<\!-- nested divs because wipeIn()/wipeOut() doesn\'t work right on node w/padding etc.  Put padding on inner div. --\>\n\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n'}});
define("dijit/TitlePane",["dojo/_base/array","dojo/_base/declare","dojo/dom","dojo/dom-attr","dojo/dom-class","dojo/dom-geometry","dojo/_base/event","dojo/fx","dojo/_base/kernel","dojo/keys","./_CssStateMixin","./_TemplatedMixin","./layout/ContentPane","dojo/text!./templates/TitlePane.html","./_base/manager"],function(g,h,i,c,j,d,k,e,l,f,m,n,o,p,q){return h("dijit.TitlePane",[o,n,m],{title:"",_setTitleAttr:{node:"titleNode",type:"innerHTML"},open:!0,toggleable:!0,tabIndex:"0",duration:q.defaultDuration,
baseClass:"dijitTitlePane",templateString:p,doLayout:!1,_setTooltipAttr:{node:"focusNode",type:"attribute",attribute:"title"},buildRendering:function(){this.inherited(arguments);i.setSelectable(this.titleNode,!1)},postCreate:function(){this.inherited(arguments);this.toggleable&&this._trackMouseState(this.titleBarNode,"dijitTitlePaneTitle");var a=this.hideNode,b=this.wipeNode;this._wipeIn=e.wipeIn({node:b,duration:this.duration,beforeBegin:function(){a.style.display=""}});this._wipeOut=e.wipeOut({node:b,
duration:this.duration,onEnd:function(){a.style.display="none"}})},_setOpenAttr:function(a,b){g.forEach([this._wipeIn,this._wipeOut],function(a){a&&a.status()=="playing"&&a.stop()});b?this[a?"_wipeIn":"_wipeOut"].play():this.hideNode.style.display=this.wipeNode.style.display=a?"":"none";if(this._started)if(a)this._onShow();else this.onHide();this.arrowNodeInner.innerHTML=a?"-":"+";this.containerNode.setAttribute("aria-hidden",a?"false":"true");this.focusNode.setAttribute("aria-pressed",a?"true":"false");
this._set("open",a);this._setCss()},_setToggleableAttr:function(a){this.focusNode.setAttribute("role",a?"button":"heading");a?(this.focusNode.setAttribute("aria-controls",this.id+"_pane"),c.set(this.focusNode,"tabIndex",this.tabIndex)):c.remove(this.focusNode,"tabIndex");this._set("toggleable",a);this._setCss()},_setContentAttr:function(){!this.open||!this._wipeOut||this._wipeOut.status()=="playing"?this.inherited(arguments):(this._wipeIn&&this._wipeIn.status()=="playing"&&this._wipeIn.stop(),d.setMarginBox(this.wipeNode,
{h:d.getMarginBox(this.wipeNode).h}),this.inherited(arguments),this._wipeIn?this._wipeIn.play():this.hideNode.style.display="")},toggle:function(){this._setOpenAttr(!this.open,!0)},_setCss:function(){var a=this.titleBarNode||this.focusNode,b=this._titleBarClass;this._titleBarClass="dijit"+(this.toggleable?"":"Fixed")+(this.open?"Open":"Closed");j.replace(a,this._titleBarClass,b||"");this.arrowNodeInner.innerHTML=this.open?"-":"+"},_onTitleKey:function(a){a.charOrCode==f.ENTER||a.charOrCode==" "?(this.toggleable&&
this.toggle(),k.stop(a)):a.charOrCode==f.DOWN_ARROW&&this.open&&(this.containerNode.focus(),a.preventDefault())},_onTitleClick:function(){this.toggleable&&this.toggle()},setTitle:function(a){l.deprecated("dijit.TitlePane.setTitle() is deprecated.  Use set('title', ...) instead.","","2.0");this.set("title",a)}})});