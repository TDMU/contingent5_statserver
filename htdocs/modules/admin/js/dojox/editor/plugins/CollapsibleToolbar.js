//>>built
define("dojox/editor/plugins/CollapsibleToolbar",["dojo","dijit","dojox","dijit/_Widget","dijit/_TemplatedMixin","dijit/_editor/_Plugin","dijit/form/Button","dijit/focus","dojo/_base/connect","dojo/_base/declare","dojo/i18n","dojo/i18n!dojox/editor/plugins/nls/CollapsibleToolbar"],function(a,c,e){a.declare("dojox.editor.plugins._CollapsibleToolbarButton",[c._Widget,c._TemplatedMixin],{templateString:"<div tabindex='0' role='button' title='${title}' class='${buttonClass}' dojoAttachEvent='ondijitclick: onClick'><span class='${textClass}'>${text}</span></div>",
title:"",buttonClass:"",text:"",textClass:"",onClick:function(){}});a.declare("dojox.editor.plugins.CollapsibleToolbar",c._editor._Plugin,{_myWidgets:null,setEditor:function(a){this.editor=a;this._constructContainer()},_constructContainer:function(){var b=a.i18n.getLocalization("dojox.editor.plugins","CollapsibleToolbar");this._myWidgets=[];var c=a.create("table",{style:{width:"100%"},tabindex:-1,"class":"dojoxCollapsibleToolbarContainer"}),f=a.create("tbody",{tabindex:-1},c),d=a.create("tr",{tabindex:-1},
f),f=a.create("td",{"class":"dojoxCollapsibleToolbarControl",tabindex:-1},d),g=a.create("td",{"class":"dojoxCollapsibleToolbarControl",tabindex:-1},d),d=a.create("td",{style:{width:"100%"},tabindex:-1},d),d=a.create("span",{style:{width:"100%"},tabindex:-1},d),h=new e.editor.plugins._CollapsibleToolbarButton({buttonClass:"dojoxCollapsibleToolbarCollapse",title:b.collapse,text:"-",textClass:"dojoxCollapsibleToolbarCollapseText"});a.place(h.domNode,f);b=new e.editor.plugins._CollapsibleToolbarButton({buttonClass:"dojoxCollapsibleToolbarExpand",
title:b.expand,text:"+",textClass:"dojoxCollapsibleToolbarExpandText"});a.place(b.domNode,g);this._myWidgets.push(h);this._myWidgets.push(b);a.style(g,"display","none");a.place(c,this.editor.toolbar.domNode,"after");a.place(this.editor.toolbar.domNode,d);this.openTd=f;this.closeTd=g;this.menu=d;this.connect(h,"onClick","_onClose");this.connect(b,"onClick","_onOpen")},_onClose:function(b){b&&a.stopEvent(b);b=a.marginBox(this.editor.domNode);a.style(this.openTd,"display","none");a.style(this.closeTd,
"display","");a.style(this.menu,"display","none");this.editor.resize({h:b.h});if(a.isIE)this.editor.header.className=this.editor.header.className,this.editor.footer.className=this.editor.footer.className;c.focus(this.closeTd.firstChild)},_onOpen:function(b){b&&a.stopEvent(b);b=a.marginBox(this.editor.domNode);a.style(this.closeTd,"display","none");a.style(this.openTd,"display","");a.style(this.menu,"display","");this.editor.resize({h:b.h});if(a.isIE)this.editor.header.className=this.editor.header.className,
this.editor.footer.className=this.editor.footer.className;c.focus(this.openTd.firstChild)},destroy:function(){this.inherited(arguments);if(this._myWidgets){for(;this._myWidgets.length;)this._myWidgets.pop().destroy();delete this._myWidgets}}});a.subscribe(c._scopeName+".Editor.getPlugin",null,function(a){if(!a.plugin&&a.args.name.toLowerCase()==="collapsibletoolbar")a.plugin=new e.editor.plugins.CollapsibleToolbar({})});return e.editor.plugins.CollapsibleToolbar});