//>>built
define("dojox/mdnd/AutoScroll",["dojo/_base/kernel","dojo/_base/declare","dojo/_base/lang","dojo/_base/connect","dojo/_base/window"],function(a){var e=a.declare("dojox.mdnd.AutoScroll",null,{interval:3,recursiveTimer:10,marginMouse:50,constructor:function(){this.resizeHandler=a.connect(a.global,"onresize",this,function(){this.getViewport()});a.ready(a.hitch(this,"init"))},init:function(){this._html=a.isWebKit?a.body():a.body().parentNode;this.getViewport()},getViewport:function(){var b=a.doc.documentElement,
c=window,d=a.body();if(a.isMozilla)this._v={w:b.clientWidth,h:c.innerHeight};else if(!a.isOpera&&c.innerWidth)this._v={w:c.innerWidth,h:c.innerHeight};else if(!a.isOpera&&b&&b.clientWidth)this._v={w:b.clientWidth,h:b.clientHeight};else if(d.clientWidth)this._v={w:d.clientWidth,h:d.clientHeight}},setAutoScrollNode:function(a){this._node=a},setAutoScrollMaxPage:function(){this._yMax=this._html.scrollHeight;this._xMax=this._html.scrollWidth},checkAutoScroll:function(a){this._autoScrollActive&&this.stopAutoScroll();
this._y=a.pageY;this._x=a.pageX;if(a.clientX<this.marginMouse)this._autoScrollActive=!0,this._autoScrollLeft(a);else if(a.clientX>this._v.w-this.marginMouse)this._autoScrollActive=!0,this._autoScrollRight(a);if(a.clientY<this.marginMouse)this._autoScrollActive=!0,this._autoScrollUp(a);else if(a.clientY>this._v.h-this.marginMouse)this._autoScrollActive=!0,this._autoScrollDown()},_autoScrollDown:function(){this._timer&&clearTimeout(this._timer);if(this._autoScrollActive&&this._y+this.marginMouse<this._yMax)this._html.scrollTop+=
this.interval,this._node.style.top=parseInt(this._node.style.top)+this.interval+"px",this._y+=this.interval,this._timer=setTimeout(a.hitch(this,"_autoScrollDown"),this.recursiveTimer)},_autoScrollUp:function(){this._timer&&clearTimeout(this._timer);if(this._autoScrollActive&&this._y-this.marginMouse>0)this._html.scrollTop-=this.interval,this._node.style.top=parseInt(this._node.style.top)-this.interval+"px",this._y-=this.interval,this._timer=setTimeout(a.hitch(this,"_autoScrollUp"),this.recursiveTimer)},
_autoScrollRight:function(){this._timer&&clearTimeout(this._timer);if(this._autoScrollActive&&this._x+this.marginMouse<this._xMax)this._html.scrollLeft+=this.interval,this._node.style.left=parseInt(this._node.style.left)+this.interval+"px",this._x+=this.interval,this._timer=setTimeout(a.hitch(this,"_autoScrollRight"),this.recursiveTimer)},_autoScrollLeft:function(){this._timer&&clearTimeout(this._timer);if(this._autoScrollActive&&this._x-this.marginMouse>0)this._html.scrollLeft-=this.interval,this._node.style.left=
parseInt(this._node.style.left)-this.interval+"px",this._x-=this.interval,this._timer=setTimeout(a.hitch(this,"_autoScrollLeft"),this.recursiveTimer)},stopAutoScroll:function(){this._timer&&clearTimeout(this._timer);this._autoScrollActive=!1},destroy:function(){a.disconnect(this.resizeHandler)}});dojox.mdnd.autoScroll=null;dojox.mdnd.autoScroll=new dojox.mdnd.AutoScroll;return e});