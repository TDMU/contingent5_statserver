//>>built
define("dojox/mdnd/DropIndicator",["dojo/_base/kernel","dojo/_base/declare","dojo/_base/html","./AreaManager"],function(a){var d=a.declare("dojox.mdnd.DropIndicator",null,{node:null,constructor:function(){var b=document.createElement("div"),c=document.createElement("div");b.appendChild(c);a.addClass(b,"dropIndicator");this.node=b},place:function(b,c,a){if(a)this.node.style.height=a.h+"px";try{return c?b.insertBefore(this.node,c):b.appendChild(this.node),this.node}catch(d){return null}},remove:function(){if(this.node)this.node.style.height=
"",this.node.parentNode&&this.node.parentNode.removeChild(this.node)},destroy:function(){this.node&&(this.node.parentNode&&this.node.parentNode.removeChild(this.node),a._destroyElement(this.node),delete this.node)}});dojox.mdnd.areaManager()._dropIndicator=new dojox.mdnd.DropIndicator;return d});