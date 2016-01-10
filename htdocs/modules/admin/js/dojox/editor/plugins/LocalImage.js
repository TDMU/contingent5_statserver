//>>built
define("dojox/editor/plugins/LocalImage",["dojo","dijit","dijit/registry","dijit/_base/popup","dijit/_editor/_Plugin","dijit/_editor/plugins/LinkDialog","dijit/TooltipDialog","dijit/form/_TextBoxMixin","dijit/form/Button","dijit/form/ValidationTextBox","dijit/form/DropDownButton","dojo/_base/connect","dojo/_base/declare","dojo/_base/sniff","dojox/form/FileUploader","dojo/i18n!dojox/editor/plugins/nls/LocalImage"],function(d,t,f,l,m,n,g,o,u,p,q,v,w,h,r,c){var k=d.declare("dojox.editor.plugins.LocalImage",
n.ImgLinkDialog,{uploadable:!1,uploadUrl:"",baseImageUrl:"",fileMask:"*.jpg;*.jpeg;*.gif;*.png;*.bmp",urlRegExp:"",htmlFieldName:"uploadedfile",_isLocalFile:!1,_messages:"",_cssPrefix:"dijitEditorEilDialog",_closable:!0,linkDialogTemplate:"<div style='border-bottom: 1px solid black; padding-bottom: 2pt; margin-bottom: 4pt;'></div><div class='dijitEditorEilDialogDescription'>${prePopuTextUrl}${prePopuTextBrowse}</div><table><tr><td colspan='2'><label for='${id}_urlInput' title='${prePopuTextUrl}${prePopuTextBrowse}'>${url}</label></td></tr><tr><td class='dijitEditorEilDialogField'><input dojoType='dijit.form.ValidationTextBox' class='dijitEditorEilDialogField'regExp='${urlRegExp}' title='${prePopuTextUrl}${prePopuTextBrowse}'  selectOnClick='true' required='true' id='${id}_urlInput' name='urlInput' intermediateChanges='true' invalidMessage='${invalidMessage}' prePopuText='&lt;${prePopuTextUrl}${prePopuTextBrowse}&gt'></td><td><div id='${id}_browse' style='display:${uploadable}'>${browse}</div></td></tr><tr><td colspan='2'><label for='${id}_textInput'>${text}</label></td></tr><tr><td><input dojoType='dijit.form.TextBox' required='false' id='${id}_textInput' name='textInput' intermediateChanges='true' selectOnClick='true' class='dijitEditorEilDialogField'></td><td></td></tr><tr><td></td><td></td></tr><tr><td colspan='2'><button dojoType='dijit.form.Button' id='${id}_setButton'>${set}</button></td></tr></table>",
_initButton:function(){var a=this;this._messages=c;this.tag="img";var b=this.dropDown=new g({title:c[this.command+"Title"],onOpen:function(){a._initialFileUploader();a._onOpenDialog();g.prototype.onOpen.apply(this,arguments);setTimeout(function(){o.selectInputText(a._urlInput.textbox);a._urlInput.isLoadComplete=!0},0)},onClose:function(){d.disconnect(a.blurHandler);a.blurHandler=null;this.onHide()},onCancel:function(){setTimeout(d.hitch(a,"_onCloseDialog"),0)}}),e=this.getLabel(this.command),s=this.iconClassPrefix+
" "+this.iconClassPrefix+this.command.charAt(0).toUpperCase()+this.command.substr(1),e=d.mixin({label:e,showLabel:!1,iconClass:s,dropDown:this.dropDown,tabIndex:"-1"},this.params||{});if(!h("ie"))e.closeDropDown=function(b){if(a._closable&&this._opened)l.close(this.dropDown),b&&this.focus(),this._opened=!1,this.state="";setTimeout(function(){a._closable=!0},10)};this.button=new q(e);var e=this.fileMask.split(";"),i="";d.forEach(e,function(a){a=a.replace(/\./,"\\.").replace(/\*/g,".*");i+="|"+a+"|"+
a.toUpperCase()});c.urlRegExp=this.urlRegExp=i.substring(1);if(!this.uploadable)c.prePopuTextBrowse=".";c.id=f.getUniqueId(this.editor.id);c.uploadable=this.uploadable?"inline":"none";this._uniqueId=c.id;this._setContent("<div class='"+this._cssPrefix+"Title'>"+b.title+"</div>"+d.string.substitute(this.linkDialogTemplate,c));b.startup();b=this._urlInput=f.byId(this._uniqueId+"_urlInput");this._textInput=f.byId(this._uniqueId+"_textInput");this._setButton=f.byId(this._uniqueId+"_setButton");if(b){var j=
p.prototype,b=d.mixin(b,{isLoadComplete:!1,isValid:function(){return this.isLoadComplete?j.isValid.apply(this,arguments):this.get("value").length>0},reset:function(){this.isLoadComplete=!1;j.reset.apply(this,arguments)}});this.connect(b,"onKeyDown","_cancelFileUpload");this.connect(b,"onChange","_checkAndFixInput")}this._setButton&&this.connect(this._setButton,"onClick","_checkAndSetValue");this._connectTagEvents()},_initialFileUploader:function(){var a=null,b=this,c=b._uniqueId+"_browse",d=b._urlInput;
if(b.uploadable&&!b._fileUploader)a=b._fileUploader=new r({force:"html",uploadUrl:b.uploadUrl,htmlFieldName:b.htmlFieldName,uploadOnChange:!1,selectMultipleFiles:!1,showProgress:!0},c),a.reset=function(){b._isLocalFile=!1;a._resetHTML()},b.connect(a,"onClick",function(){d.validate(!1);if(!h("ie"))b._closable=!1}),b.connect(a,"onChange",function(a){b._isLocalFile=!0;d.set("value",a[0].name);d.focus()}),b.connect(a,"onComplete",function(a){var c=b.baseImageUrl,c=c&&c.charAt(c.length-1)=="/"?c:c+"/";
d.set("value",c+a[0].file);b._isLocalFile=!1;b._setDialogStatus(!0);b.setValue(b.dropDown.get("value"))}),b.connect(a,"onError",function(){console.log("Error occurred when uploading image file!");b._setDialogStatus(!0)})},_checkAndFixInput:function(){this._setButton.set("disabled",!this._isValid())},_isValid:function(){return this._urlInput.isValid()},_cancelFileUpload:function(){this._fileUploader.reset();this._isLocalFile=!1},_checkAndSetValue:function(){this._fileUploader&&this._isLocalFile?(this._setDialogStatus(!1),
this._fileUploader.upload()):this.setValue(this.dropDown.get("value"))},_setDialogStatus:function(a){this._urlInput.set("disabled",!a);this._textInput.set("disabled",!a);this._setButton.set("disabled",!a)},destroy:function(){this.inherited(arguments);this._fileUploader&&(this._fileUploader.destroy(),delete this._fileUploader)}});m.registry.LocalImage=function(a){return new k({command:"insertImage",uploadable:"uploadable"in a?a.uploadable:!1,uploadUrl:"uploadable"in a&&"uploadUrl"in a?a.uploadUrl:
"",htmlFieldName:"uploadable"in a&&"htmlFieldName"in a?a.htmlFieldName:"uploadedfile",baseImageUrl:"uploadable"in a&&"baseImageUrl"in a?a.baseImageUrl:"",fileMask:"fileMask"in a?a.fileMask:"*.jpg;*.jpeg;*.gif;*.png;*.bmp"})};return k});