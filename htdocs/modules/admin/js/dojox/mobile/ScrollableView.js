//>>built
define("dojox/mobile/ScrollableView",["dojo/_base/array","dojo/_base/declare","dojo/dom-class","dojo/dom-construct","dijit/registry","./View","./_ScrollableMixin"],function(f,g,h,i,e,j,k){return g("dojox.mobile.ScrollableView",[j,k],{scrollableParams:null,keepScrollPos:!1,constructor:function(){this.scrollableParams={noResize:!0}},buildRendering:function(){this.inherited(arguments);h.add(this.domNode,"mblScrollableView");this.domNode.style.overflow="hidden";this.domNode.style.top="0px";this.containerNode=
i.create("DIV",{className:"mblScrollableViewContainer"},this.domNode);this.containerNode.style.position="absolute";this.containerNode.style.top="0px";if(this.scrollDir==="v")this.containerNode.style.width="100%";this.reparent();this.findAppBars()},resize:function(){this.inherited(arguments);f.forEach(this.getChildren(),function(a){a.resize&&a.resize()})},isTopLevel:function(){var a=this.getParent&&this.getParent();return!a||!a.resize},addChild:function(a){var b=a.domNode,c=this.checkFixedBar(b,!0);
if(c){this.domNode.appendChild(b);if(c==="top")this.fixedHeaderHeight=b.offsetHeight,this.isLocalHeader=!0;else if(c==="bottom")this.fixedFooterHeight=b.offsetHeight,this.isLocalFooter=!0,b.style.bottom="0px";this.resize();this._started&&!a._started&&a.startup()}else this.inherited(arguments)},reparent:function(){var a,b,c,d;b=a=0;for(c=this.domNode.childNodes.length;a<c;a++)d=this.domNode.childNodes[b],d===this.containerNode||this.checkFixedBar(d,!0)?b++:this.containerNode.appendChild(this.domNode.removeChild(d))},
onAfterTransitionIn:function(){this.flashScrollBar()},getChildren:function(){var a=this.inherited(arguments);this.fixedHeader&&this.fixedHeader.parentNode===this.domNode&&a.push(e.byNode(this.fixedHeader));this.fixedFooter&&this.fixedFooter.parentNode===this.domNode&&a.push(e.byNode(this.fixedFooter));return a}})});