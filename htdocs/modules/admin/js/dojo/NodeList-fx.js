/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/NodeList-fx",["dojo/_base/NodeList","./_base/lang","./_base/connect","./_base/fx","./fx"],function(h,e,i,c,f){e.extend(h,{_anim:function(a,c,d){var d=d||{},g=f.combine(this.map(function(b){b={node:b};e.mixin(b,d);return a[c](b)}));return d.auto?g.play()&&this:g},wipeIn:function(a){return this._anim(f,"wipeIn",a)},wipeOut:function(a){return this._anim(f,"wipeOut",a)},slideTo:function(a){return this._anim(f,"slideTo",a)},fadeIn:function(a){return this._anim(c,"fadeIn",a)},fadeOut:function(a){return this._anim(c,
"fadeOut",a)},animateProperty:function(a){return this._anim(c,"animateProperty",a)},anim:function(a,h,d,g,b){var e=f.combine(this.map(function(b){return c.animateProperty({node:b,properties:a,duration:h||350,easing:d})}));g&&i.connect(e,"onEnd",g);return e.play(b||0)}});return h});