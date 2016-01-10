//>>built
define(["dijit","dojo","dojox"],function(i,a,g){a.provide("dojox.drawing.ui.dom.Toolbar");a.deprecated("dojox.drawing.ui.dom.Toolbar","It may not even make it to the 1.4 release.",1.4);(function(){a.declare("dojox.drawing.ui.dom.Toolbar",[],{baseClass:"drawingToolbar",buttonClass:"drawingButton",iconClass:"icon",constructor:function(c,d){a.addOnLoad(this,function(){this.domNode=a.byId(d);a.addClass(this.domNode,this.baseClass);this.parse()})},createIcon:function(c,d){var b=d&&d.setup?d.setup:{};if(b.iconClass){var h=
b.iconClass?b.iconClass:"iconNone",b=a.create("div",{title:b.tooltip?b.tooltip:"Tool"},c);a.addClass(b,this.iconClass);a.addClass(b,h);a.connect(c,"mouseup",function(b){a.stopEvent(b);a.removeClass(c,"active")});a.connect(c,"mouseover",function(b){a.stopEvent(b);a.addClass(c,"hover")});a.connect(c,"mousedown",this,function(b){a.stopEvent(b);a.addClass(c,"active")});a.connect(c,"mouseout",this,function(b){a.stopEvent(b);a.removeClass(c,"hover")})}},createTool:function(c){c.innerHTML="";var d=a.attr(c,
"tool");this.toolNodes[d]=c;a.attr(c,"tabIndex",1);var b=a.getObject(d);this.createIcon(c,b);this.drawing.registerTool(d,b);a.connect(c,"mouseup",this,function(b){a.stopEvent(b);a.removeClass(c,"active");this.onClick(d)});a.connect(c,"mouseover",function(b){a.stopEvent(b);a.addClass(c,"hover")});a.connect(c,"mousedown",this,function(b){a.stopEvent(b);a.addClass(c,"active")});a.connect(c,"mouseout",this,function(b){a.stopEvent(b);a.removeClass(c,"hover")})},parse:function(){var c=a.attr(this.domNode,
"drawingId");this.drawing=g.drawing.util.common.byId(c);!this.drawing&&console.error("Drawing not found based on 'drawingId' in Toolbar. ");this.toolNodes={};var d;a.query(">",this.domNode).forEach(function(b,c){b.className=this.buttonClass;var e=a.attr(b,"tool");a.attr(b,"action");var f=a.attr(b,"plugin");if(e){if(c==0||a.attr(b,"selected")=="true")d=e;this.createTool(b)}else if(f){e={name:f,options:{}};if(f=a.attr(b,"options"))e.options=eval("("+f+")");e.options.node=b;b.innerHTML="";this.drawing.addPlugin(e);
this.createIcon(b,a.getObject(a.attr(b,"plugin")))}},this);this.drawing.initPlugins();a.connect(this.drawing,"setTool",this,"onSetTool");this.drawing.setTool(d)},onClick:function(a){this.drawing.setTool(a)},onSetTool:function(c){for(var d in this.toolNodes)d==c?(a.addClass(this.toolNodes[c],"selected"),this.toolNodes[c].blur()):a.removeClass(this.toolNodes[d],"selected")}})})()});