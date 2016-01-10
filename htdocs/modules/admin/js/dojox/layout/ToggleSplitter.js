//>>built
define("dojox/layout/ToggleSplitter",["dojo","dijit","dijit/layout/BorderContainer"],function(b,h){b.experimental("dojox.layout.ToggleSplitter");b.declare("dojox.layout.ToggleSplitter",h.layout._Splitter,{state:"full",_closedSize:"0",baseClass:"dojoxToggleSplitter",templateString:'<div class="dijitSplitter dojoxToggleSplitter" dojoAttachEvent="onkeypress:_onKeyPress,onmousedown:_startDrag,onmouseenter:_onMouse,onmouseleave:_onMouse"><div dojoAttachPoint="toggleNode" class="dijitSplitterThumb dojoxToggleSplitterIcon" tabIndex="0" role="separator" dojoAttachEvent="onmousedown:_onToggleNodeMouseDown,onclick:_toggle,onmouseenter:_onToggleNodeMouseMove,onmouseleave:_onToggleNodeMouseMove,onfocus:_onToggleNodeMouseMove,onblur:_onToggleNodeMouseMove"><span class="dojoxToggleSplitterA11y" dojoAttachPoint="a11yText"></span></div></div>',
postCreate:function(){this.inherited(arguments);var a=this.region;b.addClass(this.domNode,this.baseClass+a.charAt(0).toUpperCase()+a.substring(1))},startup:function(){this.inherited(arguments);var a=this.child.domNode,c=b.style(a,this.horizontal?"height":"width");this.domNode.setAttribute("aria-controls",a.id);b.forEach(["toggleSplitterState","toggleSplitterFullSize","toggleSplitterCollapsedSize"],function(a){var b=a.substring(14),b=b.charAt(0).toLowerCase()+b.substring(1);a in this.child&&(this[b]=
this.child[a])},this);if(!this.fullSize)this.fullSize=this.state=="full"?c+"px":"75px";this._openStyleProps=this._getStyleProps(a,"full");this._started=!0;this.set("state",this.state);return this},_onKeyPress:function(a){this.state=="full"&&this.inherited(arguments);(a.charCode==b.keys.SPACE||a.keyCode==b.keys.ENTER)&&this._toggle(a)},_onToggleNodeMouseDown:function(a){b.stopEvent(a);this.toggleNode.focus()},_startDrag:function(){this.state=="full"&&this.inherited(arguments)},_stopDrag:function(){this.inherited(arguments);
this.toggleNode.blur()},_toggle:function(){var a;switch(this.state){case "full":a=this.collapsedSize?"collapsed":"closed";break;case "collapsed":a="closed";break;default:a="full"}this.set("state",a)},_onToggleNodeMouseMove:function(a){var c=this.baseClass,d=this.toggleNode,e=this.state=="full"||this.state=="collapsed",a=a.type=="mouseout"||a.type=="blur";b.toggleClass(d,c+"IconOpen",a&&e);b.toggleClass(d,c+"IconOpenHover",!a&&e);b.toggleClass(d,c+"IconClosed",a&&!e);b.toggleClass(d,c+"IconClosedHover",
!a&&!e)},_handleOnChange:function(){var a=this.child.domNode,c,d,e=this.horizontal?"height":"width";if(this.state=="full")c=b.mixin({display:"block",overflow:"auto",visibility:"visible"},this._openStyleProps),c[e]=this._openStyleProps&&this._openStyleProps[e]?this._openStyleProps[e]:this.fullSize,b.style(this.domNode,"cursor",""),b.style(a,c);else if(this.state=="collapsed")d=b.getComputedStyle(a),this._openStyleProps=c=this._getStyleProps(a,"full",d),b.style(this.domNode,"cursor","auto"),b.style(a,
e,this.collapsedSize);else{if(!this.collapsedSize)d=b.getComputedStyle(a),this._openStyleProps=c=this._getStyleProps(a,"full",d);e=this._getStyleProps(a,"closed",d);b.style(this.domNode,"cursor","auto");b.style(a,e)}this._setStateClass();this.container._started&&this.container._layoutChildren(this.region)},_getStyleProps:function(a,c,d){d||(d=b.getComputedStyle(a));var e={},f=this.horizontal?"height":"width";e.overflow=c!="closed"?d.overflow:"hidden";e.visibility=c!="closed"?d.visibility:"hidden";
e[f]=c!="closed"?a.style[f]||d[f]:this._closedSize;var g=["Top","Right","Bottom","Left"];b.forEach(["padding","margin","border"],function(a){for(var b=0;b<g.length;b++){var f=a+g[b];a=="border"&&(f+="Width");void 0!==d[f]&&(e[f]=c!="closed"?d[f]:0)}});return e},_setStateClass:function(){var a="&#9652",c=this.region.toLowerCase(),d=this.baseClass,e=this.toggleNode,f=this.state=="full"||this.state=="collapsed",g=this.focused;b.toggleClass(e,d+"IconOpen",f&&!g);b.toggleClass(e,d+"IconClosed",!f&&!g);
b.toggleClass(e,d+"IconOpenHover",f&&g);b.toggleClass(e,d+"IconClosedHover",!f&&g);if(c=="top"&&f||c=="bottom"&&!f)a="&#9650";else if(c=="top"&&!f||c=="bottom"&&f)a="&#9660";else if(c=="right"&&f||c=="left"&&!f)a="&#9654";else if(c=="right"&&!f||c=="left"&&f)a="&#9664";this.a11yText.innerHTML=a},_setStateAttr:function(a){if(this._started){var b=this.state;this.state=a;this._handleOnChange(b);switch(a){case "full":this.domNode.setAttribute("aria-expanded",!0);a="onOpen";break;case "collapsed":this.domNode.setAttribute("aria-expanded",
!0);a="onCollapsed";break;default:this.domNode.setAttribute("aria-expanded",!1),a="onClosed"}this[a](this.child)}},onOpen:function(){},onCollapsed:function(){},onClosed:function(){}});b.extend(h._Widget,{toggleSplitterState:"full",toggleSplitterFullSize:"",toggleSplitterCollapsedSize:""})});