/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/mouse",["./_base/kernel","./on","./has","./dom","./_base/window"],function(e,f,b,g,c){function d(a,b){var c=function(c,d){return f(c,a,function(a){if(!g.isDescendant(a.relatedTarget,b?a.target:c))return d.call(this,a)})};if(!b)c.bubble=d(a,!0);return c}b.add("dom-quirks",c.doc&&c.doc.compatMode=="BackCompat");b.add("events-mouseenter",c.doc&&"onmouseenter"in c.doc.createElement("div"));b=b("dom-quirks")||!b("dom-addeventlistener")?{LEFT:1,MIDDLE:4,RIGHT:2,isButton:function(a,b){return a.button&
b},isLeft:function(a){return a.button&1},isMiddle:function(a){return a.button&4},isRight:function(a){return a.button&2}}:{LEFT:0,MIDDLE:1,RIGHT:2,isButton:function(a,b){return a.button==b},isLeft:function(a){return a.button==0},isMiddle:function(a){return a.button==1},isRight:function(a){return a.button==2}};e.mouseButtons=b;return{enter:d("mouseover"),leave:d("mouseout"),isLeft:b.isLeft,isMiddle:b.isMiddle,isRight:b.isRight}});