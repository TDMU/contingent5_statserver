//>>built
define("dojox/fx/ext-dojo/NodeList-style",["dojo/_base/lang","dojo/_base/NodeList","dojo/NodeList-fx","dojo/fx","../style"],function(g,f,h,a,e){g.extend(f,{addClassFx:function(b,c){return a.combine(this.map(function(d){return e.addClass(d,b,c)}))},removeClassFx:function(b,c){return a.combine(this.map(function(d){return e.removeClass(d,b,c)}))},toggleClassFx:function(b,c,d){return a.combine(this.map(function(a){return e.toggleClass(a,b,c,d)}))}});return f});