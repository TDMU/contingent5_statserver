//>>built
require({cache:{"url:dijit/templates/InlineEditBox.html":'<span data-dojo-attach-point="editNode" role="presentation" style="position: absolute; visibility:hidden" class="dijitReset dijitInline"\n\tdata-dojo-attach-event="onkeypress: _onKeyPress"\n\t><span data-dojo-attach-point="editorPlaceholder"></span\n\t><span data-dojo-attach-point="buttonContainer"\n\t\t><button data-dojo-type="dijit.form.Button" data-dojo-props="label: \'${buttonSave}\', \'class\': \'saveButton\'"\n\t\t\tdata-dojo-attach-point="saveButton" data-dojo-attach-event="onClick:save"></button\n\t\t><button data-dojo-type="dijit.form.Button"  data-dojo-props="label: \'${buttonCancel}\', \'class\': \'cancelButton\'"\n\t\t\tdata-dojo-attach-point="cancelButton" data-dojo-attach-event="onClick:cancel"></button\n\t></span\n></span>\n'}});
define("dijit/InlineEditBox",["dojo/_base/array","dojo/_base/declare","dojo/dom-attr","dojo/dom-class","dojo/dom-construct","dojo/dom-style","dojo/_base/event","dojo/i18n","dojo/_base/kernel","dojo/keys","dojo/_base/lang","dojo/_base/sniff","./focus","./_Widget","./_TemplatedMixin","./_WidgetsInTemplateMixin","./_Container","./form/Button","./form/_TextBoxMixin","./form/TextBox","dojo/text!./templates/InlineEditBox.html","dojo/i18n!./nls/common"],function(i,b,j,f,m,d,k,q,l,n,c,r,o,p,g,s,w,x,t,u,v){g=
b("dijit._InlineEditor",[p,g,s],{templateString:v,postMixInProperties:function(){this.inherited(arguments);this.messages=q.getLocalization("dijit","common",this.lang);i.forEach(["buttonSave","buttonCancel"],function(a){this[a]||(this[a]=this.messages[a])},this)},buildRendering:function(){this.inherited(arguments);var a=typeof this.editor=="string"?c.getObject(this.editor):this.editor,h=this.sourceStyle,b="line-height:"+h.lineHeight+";",f=d.getComputedStyle(this.domNode);i.forEach(["Weight","Family",
"Size","Style"],function(a){f["font"+a]!=h["font"+a]&&(b+="font-"+a+":"+h["font"+a]+";")},this);i.forEach(["marginTop","marginBottom","marginLeft","marginRight"],function(a){this.domNode.style[a]=h[a]},this);var e=this.inlineEditBox.width;e=="100%"?(b+="width:100%;",this.domNode.style.display="block"):b+="width:"+(e+(Number(e)==e?"px":""))+";";e=c.delegate(this.inlineEditBox.editorParams,{style:b,dir:this.dir,lang:this.lang,textDir:this.textDir});e["displayedValue"in a.prototype?"displayedValue":
"value"]=this.value;this.editWidget=new a(e,this.editorPlaceholder);this.inlineEditBox.autoSave&&m.destroy(this.buttonContainer)},postCreate:function(){this.inherited(arguments);var a=this.editWidget;this.inlineEditBox.autoSave?(this.connect(a,"onChange","_onChange"),this.connect(a,"onKeyPress","_onKeyPress")):"intermediateChanges"in a&&(a.set("intermediateChanges",!0),this.connect(a,"onChange","_onIntermediateChange"),this.saveButton.set("disabled",!0))},_onIntermediateChange:function(){this.saveButton.set("disabled",
this.getValue()==this._resetValue||!this.enableSave())},destroy:function(){this.editWidget.destroy(!0);this.inherited(arguments)},getValue:function(){var a=this.editWidget;return String(a.get("displayedValue"in a?"displayedValue":"value"))},_onKeyPress:function(a){this.inlineEditBox.autoSave&&this.inlineEditBox.editing&&!a.altKey&&!a.ctrlKey&&(a.charOrCode==n.ESCAPE?(k.stop(a),this.cancel(!0)):a.charOrCode==n.ENTER&&a.target.tagName=="INPUT"&&(k.stop(a),this._onChange()))},_onBlur:function(){this.inherited(arguments);
this.inlineEditBox.autoSave&&this.inlineEditBox.editing&&(this.getValue()==this._resetValue?this.cancel(!1):this.enableSave()&&this.save(!1))},_onChange:function(){this.inlineEditBox.autoSave&&this.inlineEditBox.editing&&this.enableSave()&&o.focus(this.inlineEditBox.displayNode)},enableSave:function(){return this.editWidget.isValid?this.editWidget.isValid():!0},focus:function(){this.editWidget.focus();setTimeout(c.hitch(this,function(){this.editWidget.focusNode&&this.editWidget.focusNode.tagName==
"INPUT"&&t.selectInputText(this.editWidget.focusNode)}),0)}});b=b("dijit.InlineEditBox",p,{editing:!1,autoSave:!0,buttonSave:"",buttonCancel:"",renderAsHtml:!1,editor:u,editorWrapper:g,editorParams:{},disabled:!1,onChange:function(){},onCancel:function(){},width:"100%",value:"",noValueIndicator:r("ie")<=6?"<span style='font-family: wingdings; text-decoration: underline;'>&#160;&#160;&#160;&#160;&#x270d;&#160;&#160;&#160;&#160;</span>":"<span style='text-decoration: underline;'>&#160;&#160;&#160;&#160;&#x270d;&#160;&#160;&#160;&#160;</span>",
constructor:function(){this.editorParams={}},postMixInProperties:function(){this.inherited(arguments);this.displayNode=this.srcNodeRef;var a={ondijitclick:"_onClick",onmouseover:"_onMouseOver",onmouseout:"_onMouseOut",onfocus:"_onMouseOver",onblur:"_onMouseOut"},b;for(b in a)this.connect(this.displayNode,b,a[b]);this.displayNode.setAttribute("role","button");this.displayNode.getAttribute("tabIndex")||this.displayNode.setAttribute("tabIndex",0);if(!this.value&&!("value"in this.params))this.value=c.trim(this.renderAsHtml?
this.displayNode.innerHTML:this.displayNode.innerText||this.displayNode.textContent||"");if(!this.value)this.displayNode.innerHTML=this.noValueIndicator;f.add(this.displayNode,"dijitInlineEditBoxDisplayMode")},setDisabled:function(a){l.deprecated("dijit.InlineEditBox.setDisabled() is deprecated.  Use set('disabled', bool) instead.","","2.0");this.set("disabled",a)},_setDisabledAttr:function(a){this.domNode.setAttribute("aria-disabled",a);a?this.displayNode.removeAttribute("tabIndex"):this.displayNode.setAttribute("tabIndex",
0);f.toggle(this.displayNode,"dijitInlineEditBoxDisplayModeDisabled",a);this._set("disabled",a)},_onMouseOver:function(){this.disabled||f.add(this.displayNode,"dijitInlineEditBoxDisplayModeHover")},_onMouseOut:function(){f.remove(this.displayNode,"dijitInlineEditBoxDisplayModeHover")},_onClick:function(a){this.disabled||(a&&k.stop(a),this._onMouseOut(),setTimeout(c.hitch(this,"edit"),0))},edit:function(){if(!this.disabled&&!this.editing){this._set("editing",!0);this._savedPosition=d.get(this.displayNode,
"position")||"static";this._savedOpacity=d.get(this.displayNode,"opacity")||"1";this._savedTabIndex=j.get(this.displayNode,"tabIndex")||"0";if(this.wrapperWidget){var a=this.wrapperWidget.editWidget;a.set("displayedValue"in a?"displayedValue":"value",this.value)}else a=m.create("span",null,this.domNode,"before"),this.wrapperWidget=new (typeof this.editorWrapper=="string"?c.getObject(this.editorWrapper):this.editorWrapper)({value:this.value,buttonSave:this.buttonSave,buttonCancel:this.buttonCancel,
dir:this.dir,lang:this.lang,tabIndex:this._savedTabIndex,editor:this.editor,inlineEditBox:this,sourceStyle:d.getComputedStyle(this.displayNode),save:c.hitch(this,"save"),cancel:c.hitch(this,"cancel"),textDir:this.textDir},a),this._started||this.startup();a=this.wrapperWidget;d.set(this.displayNode,{position:"absolute",opacity:"0"});d.set(a.domNode,{position:this._savedPosition,visibility:"visible",opacity:"1"});j.set(this.displayNode,"tabIndex","-1");setTimeout(c.hitch(a,function(){this.focus();this._resetValue=
this.getValue()}),0)}},_onBlur:function(){this.inherited(arguments)},destroy:function(){this.wrapperWidget&&!this.wrapperWidget._destroyed&&(this.wrapperWidget.destroy(),delete this.wrapperWidget);this.inherited(arguments)},_showText:function(a){d.set(this.wrapperWidget.domNode,{position:"absolute",visibility:"hidden",opacity:"0"});d.set(this.displayNode,{position:this._savedPosition,opacity:this._savedOpacity});j.set(this.displayNode,"tabIndex",this._savedTabIndex);a&&o.focus(this.displayNode)},
save:function(a){!this.disabled&&this.editing&&(this._set("editing",!1),this.set("value",this.wrapperWidget.getValue()),this._showText(a))},setValue:function(a){l.deprecated("dijit.InlineEditBox.setValue() is deprecated.  Use set('value', ...) instead.","","2.0");return this.set("value",a)},_setValueAttr:function(a){a=c.trim(a);this.displayNode.innerHTML=(this.renderAsHtml?a:a.replace(/&/gm,"&amp;").replace(/</gm,"&lt;").replace(/>/gm,"&gt;").replace(/"/gm,"&quot;").replace(/\n/g,"<br>"))||this.noValueIndicator;
this._set("value",a);this._started&&setTimeout(c.hitch(this,"onChange",a),0);this.textDir=="auto"&&this.applyTextDir(this.displayNode,this.displayNode.innerText)},getValue:function(){l.deprecated("dijit.InlineEditBox.getValue() is deprecated.  Use get('value') instead.","","2.0");return this.get("value")},cancel:function(a){!this.disabled&&this.editing&&(this._set("editing",!1),setTimeout(c.hitch(this,"onCancel"),0),this._showText(a))},_setTextDirAttr:function(a){if(!this._created||this.textDir!=
a)this._set("textDir",a),this.applyTextDir(this.displayNode,this.displayNode.innerText),this.displayNode.align=this.dir=="rtl"?"right":"left"}});b._InlineEditor=g;return b});