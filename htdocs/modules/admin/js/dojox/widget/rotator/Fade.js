//>>built
define(["dijit","dojo","dojox","dojo/require!dojo/fx"],function(f,b,d){b.provide("dojox.widget.rotator.Fade");b.require("dojo.fx");(function(c){function b(a,d){var e=a.next.node;c.style(e,{display:"",opacity:0});a.node=a.current.node;return c.fx[d]([c.fadeOut(a),c.fadeIn(c.mixin(a,{node:e}))])}c.mixin(d.widget.rotator,{fade:function(a){return b(a,"chain")},crossFade:function(a){return b(a,"combine")}})})(b)});