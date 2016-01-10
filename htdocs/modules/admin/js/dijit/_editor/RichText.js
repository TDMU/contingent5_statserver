//>>built
define("dijit/_editor/RichText",["dojo/_base/array","dojo/_base/config","dojo/_base/declare","dojo/_base/Deferred","dojo/dom","dojo/dom-attr","dojo/dom-class","dojo/dom-construct","dojo/dom-geometry","dojo/dom-style","dojo/_base/event","dojo/_base/kernel","dojo/keys","dojo/_base/lang","dojo/on","dojo/query","dojo/ready","dojo/_base/sniff","dojo/topic","dojo/_base/unload","dojo/_base/url","dojo/_base/window","../_Widget","../_CssStateMixin","./selection","./range","./html","../focus",".."],function(w,
F,N,G,H,C,I,i,O,A,J,D,v,g,P,K,Q,d,L,R,E,s,S,T,x,y,B,M,z){var k=N("dijit._editor.RichText",[S,T],{constructor:function(a){this.contentPreFilters=[];this.contentPostFilters=[];this.contentDomPreFilters=[];this.contentDomPostFilters=[];this.editingAreaStyleSheets=[];this.events=[].concat(this.events);this._keyHandlers={};if(a&&g.isString(a.value))this.value=a.value;this.onLoadDeferred=new G},baseClass:"dijitEditor",inheritWidth:!1,focusOnLoad:!1,name:"",styleSheets:"",height:"300px",minHeight:"1em",
isClosed:!0,isLoaded:!1,_SEPARATOR:"@@**%%__RICHTEXTBOUNDRY__%%**@@",_NAME_CONTENT_SEP:"@@**%%:%%**@@",onLoadDeferred:null,isTabIndent:!1,disableSpellCheck:!1,postCreate:function(){"textarea"===this.domNode.tagName.toLowerCase()&&console.warn("RichText should not be used with the TEXTAREA tag.  See dijit._editor.RichText docs.");this.contentPreFilters=[g.hitch(this,"_preFixUrlAttributes")].concat(this.contentPreFilters);if(d("mozilla"))this.contentPreFilters=[this._normalizeFontStyle].concat(this.contentPreFilters),
this.contentPostFilters=[this._removeMozBogus].concat(this.contentPostFilters);if(d("webkit"))this.contentPreFilters=[this._removeWebkitBogus].concat(this.contentPreFilters),this.contentPostFilters=[this._removeWebkitBogus].concat(this.contentPostFilters);if(d("ie"))this.contentPostFilters=[this._normalizeFontStyle].concat(this.contentPostFilters),this.contentDomPostFilters=[g.hitch(this,this._stripBreakerNodes)].concat(this.contentDomPostFilters);this.inherited(arguments);L.publish(z._scopeName+
"._editor.RichText::init",this);this.open();this.setupDefaultShortcuts()},setupDefaultShortcuts:function(){var a=g.hitch(this,function(a,b){return function(){return!this.execCommand(a,b)}}),b={b:a("bold"),i:a("italic"),u:a("underline"),a:a("selectall"),s:function(){this.save(!0)},m:function(){this.isTabIndent=!this.isTabIndent},1:a("formatblock","h1"),2:a("formatblock","h2"),3:a("formatblock","h3"),4:a("formatblock","h4"),"\\":a("insertunorderedlist")};if(!d("ie"))b.Z=a("redo");for(var c in b)this.addKeyHandler(c,
!0,!1,b[c])},events:["onKeyPress","onKeyDown","onKeyUp"],captureEvents:[],_editorCommandsLocalized:!1,_localizeEditorCommands:function(){if(k._editorCommandsLocalized)this._local2NativeFormatNames=k._local2NativeFormatNames,this._native2LocalFormatNames=k._native2LocalFormatNames;else{k._editorCommandsLocalized=!0;k._local2NativeFormatNames={};k._native2LocalFormatNames={};this._local2NativeFormatNames=k._local2NativeFormatNames;this._native2LocalFormatNames=k._native2LocalFormatNames;for(var a=["div",
"p","pre","h1","h2","h3","h4","h5","h6","ol","ul","address"],b="",c,e=0;c=a[e++];)b+=c.charAt(1)!=="l"?"<"+c+"><span>content</span></"+c+"><br/>":"<"+c+"><li>content</li></"+c+"><br/>";var d=i.create("div",{style:{position:"absolute",top:"0px",zIndex:10,opacity:0.01},innerHTML:b});s.body().appendChild(d);a=g.hitch(this,function(){for(var a=d.firstChild;a;)try{x.selectElement(a.firstChild);var b=a.tagName.toLowerCase();this._local2NativeFormatNames[b]=document.queryCommandValue("formatblock");this._native2LocalFormatNames[this._local2NativeFormatNames[b]]=
b;a=a.nextSibling.nextSibling}catch(c){}d.parentNode.removeChild(d);d.innerHTML=""});setTimeout(a,0)}},open:function(a){if(!this.onLoadDeferred||this.onLoadDeferred.fired>=0)this.onLoadDeferred=new G;this.isClosed||this.close();L.publish(z._scopeName+"._editor.RichText::open",this);if(arguments.length===1&&a.nodeName)this.domNode=a;var b=this.domNode,c;if(g.isString(this.value))c=this.value,delete this.value,b.innerHTML="";else if(b.nodeName&&b.nodeName.toLowerCase()=="textarea"){var e=this.textarea=
b;this.name=e.name;c=e.value;b=this.domNode=s.doc.createElement("div");b.setAttribute("widgetId",this.id);e.removeAttribute("widgetId");b.cssText=e.cssText;b.className+=" "+e.className;i.place(b,e,"before");var m=g.hitch(this,function(){A.set(e,{display:"block",position:"absolute",top:"-1000px"});if(d("ie")){var a=e.style;this.__overflow=a.overflow;a.overflow="hidden"}});d("ie")?setTimeout(m,10):m();if(e.form){var u=e.value;this.reset=function(){this.getValue()!==u&&this.replaceValue(u)};P(e.form,
"submit",g.hitch(this,function(){C.set(e,"disabled",this.disabled);e.value=this.getValue()}))}}else c=B.getChildrenHtml(b),b.innerHTML="";this.value=c;if(b.nodeName&&b.nodeName==="LI")b.innerHTML=" <br>";this.header=b.ownerDocument.createElement("div");b.appendChild(this.header);this.editingArea=b.ownerDocument.createElement("div");b.appendChild(this.editingArea);this.footer=b.ownerDocument.createElement("div");b.appendChild(this.footer);if(!this.name)this.name=this.id+"_AUTOGEN";if(this.name!==""&&
(!F.useXDomain||F.allowXdRichTextSave)){if((m=H.byId(z._scopeName+"._editor.RichText.value"))&&m.value!=="")for(var q=m.value.split(this._SEPARATOR),r=0,n;n=q[r++];)if(n=n.split(this._NAME_CONTENT_SEP),n[0]===this.name){c=n[1];q=q.splice(r,1);m.value=q.join(this._SEPARATOR);break}if(!k._globalSaveHandler)k._globalSaveHandler={},R.addOnUnload(function(){for(var a in k._globalSaveHandler){var b=k._globalSaveHandler[a];g.isFunction(b)&&b()}});k._globalSaveHandler[this.id]=g.hitch(this,"_saveContent")}this.isClosed=
!1;var j=this.editorObject=this.iframe=s.doc.createElement("iframe");j.id=this.id+"_iframe";this._iframeSrc=this._getIframeDocTxt();j.style.border="none";j.style.width="100%";if(this._layoutMode)j.style.height="100%";else if(d("ie")>=7){if(this.height)j.style.height=this.height;if(this.minHeight)j.style.minHeight=this.minHeight}else j.style.height=this.height?this.height:this.minHeight;j.frameBorder=0;j._loadFunc=g.hitch(this,function(a){this.window=a;this.document=this.window.document;d("ie")&&this._localizeEditorCommands();
this.onLoad(c)});var m="parent."+z._scopeName+'.byId("'+this.id+'")._iframeSrc',l="javascript:(function(){try{return "+m+'}catch(e){document.open();document.domain="'+document.domain+'";document.write('+m+");document.close();}})()";j.setAttribute("src",l);this.editingArea.appendChild(j);d("safari")<=4&&(m=j.getAttribute("src"),(!m||m.indexOf("javascript")===-1)&&setTimeout(function(){j.setAttribute("src",l)},0));if(b.nodeName==="LI")b.lastChild.style.marginTop="-1.2em";I.add(this.domNode,this.baseClass)},
_local2NativeFormatNames:{},_native2LocalFormatNames:{},_getIframeDocTxt:function(){var a=A.getComputedStyle(this.domNode),b="",c=!0;if(d("ie")||d("webkit")||!this.height&&!d("mozilla"))b="<div id='dijitEditorBody'></div>",c=!1;else if(d("mozilla"))this._cursorToStart=!0,b="&#160;";var e=[a.fontWeight,a.fontSize,a.fontFamily].join(" "),m=a.lineHeight,m=m.indexOf("px")>=0?parseFloat(m)/parseFloat(a.fontSize):m.indexOf("em")>=0?parseFloat(m):"normal",i="",q=this;this.style.replace(/(^|;)\s*(line-|font-?)[^;]+/ig,
function(a){var a=a.replace(/^;/ig,"")+";",b=a.split(":")[0];if(b){var b=g.trim(b),b=b.toLowerCase(),c,e="";for(c=0;c<b.length;c++){var d=b.charAt(c);switch(d){case "-":c++,d=b.charAt(c).toUpperCase();default:e+=d}}A.set(q.domNode,e,"")}i+=a+";"});a=K('label[for="'+this.id+'"]');return[this.isLeftToRight()?"<html>\n<head>\n":"<html dir='rtl'>\n<head>\n",d("mozilla")&&a.length?"<title>"+a[0].innerHTML+"</title>\n":"","<meta http-equiv='Content-Type' content='text/html'>\n<style>\n\tbody,html {\n\t\tbackground:transparent;\n\t\tpadding: 1px 0 0 0;\n\t\tmargin: -1px 0 0 0;\n",
d("webkit")?"\t\twidth: 100%;\n":"",d("webkit")?"\t\theight: 100%;\n":"","\t}\n\tbody{\n\t\ttop:0px;\n\t\tleft:0px;\n\t\tright:0px;\n\t\tfont:",e,";\n",this.height||d("opera")?"":"\t\tposition: fixed;\n","\t\tmin-height:",this.minHeight,";\n\t\tline-height:",m,";\n\t}\n\tp{ margin: 1em 0; }\n",!c&&!this.height?"\tbody,html {overflow-y: hidden;}\n":"","\t#dijitEditorBody{overflow-x: auto; overflow-y:"+(this.height?"auto;":"hidden;")+" outline: 0px;}\n","\tli > ul:-moz-first-node, li > ol:-moz-first-node{ padding-top: 1.2em; }\n",
!d("ie")?"\tli{ min-height:1.2em; }\n":"","</style>\n",this._applyEditingAreaStyleSheets(),"\n</head>\n<body ",c?"id='dijitEditorBody' ":"","onload='frameElement._loadFunc(window,document)' style='"+i+"'>",b,"</body>\n</html>"].join("")},_applyEditingAreaStyleSheets:function(){var a=[];if(this.styleSheets)a=this.styleSheets.split(";"),this.styleSheets="";a=a.concat(this.editingAreaStyleSheets);this.editingAreaStyleSheets=[];for(var b="",c=0,e;e=a[c++];)e=(new E(s.global.location,e)).toString(),this.editingAreaStyleSheets.push(e),
b+='<link rel="stylesheet" type="text/css" href="'+e+'"/>';return b},addStyleSheet:function(a){var b=a.toString();if(b.charAt(0)==="."||b.charAt(0)!=="/"&&!a.host)b=(new E(s.global.location,b)).toString();w.indexOf(this.editingAreaStyleSheets,b)>-1||(this.editingAreaStyleSheets.push(b),this.onLoadDeferred.addCallback(g.hitch(this,function(){if(this.document.createStyleSheet)this.document.createStyleSheet(b);else{var a=this.document.getElementsByTagName("head")[0],e=this.document.createElement("link");
e.rel="stylesheet";e.type="text/css";e.href=b;a.appendChild(e)}})))},removeStyleSheet:function(a){var b=a.toString();if(b.charAt(0)==="."||b.charAt(0)!=="/"&&!a.host)b=(new E(s.global.location,b)).toString();a=w.indexOf(this.editingAreaStyleSheets,b);a!==-1&&(delete this.editingAreaStyleSheets[a],s.withGlobal(this.window,"query",dojo,['link:[href="'+b+'"]']).orphan())},disabled:!1,_mozSettingProps:{styleWithCSS:!1},_setDisabledAttr:function(a){a=!!a;this._set("disabled",a);if(this.isLoaded){if(d("ie")||
d("webkit")||d("opera")){var b=d("ie")&&(this.isLoaded||!this.focusOnLoad);if(b)this.editNode.unselectable="on";this.editNode.contentEditable=!a;if(b){var c=this;setTimeout(function(){if(c.editNode)c.editNode.unselectable="off"},0)}}else{try{this.document.designMode=a?"off":"on"}catch(e){return}if(!a&&this._mozSettingProps)for(b in a=this._mozSettingProps,a)if(a.hasOwnProperty(b))try{this.document.execCommand(b,!1,a[b])}catch(m){}}this._disabledOK=!0}},onLoad:function(a){if(!this.window.__registeredWindow)this.window.__registeredWindow=
!0,this._iframeRegHandle=M.registerIframe(this.iframe);if(!d("ie")&&!d("webkit")&&(this.height||d("mozilla")))this.editNode=this.document.body;else{this.editNode=this.document.body.firstChild;var b=this;if(d("ie"))this.tabStop=i.create("div",{tabIndex:-1},this.editingArea),this.iframe.onfocus=function(){b.editNode.setActive()}}this.focusNode=this.editNode;var c=this.events.concat(this.captureEvents),e=this.iframe?this.document:this.editNode;w.forEach(c,function(a){this.connect(e,a.toLowerCase(),a)},
this);this.connect(e,"onmouseup","onClick");d("ie")?(this.connect(this.document,"onmousedown","_onIEMouseDown"),this.editNode.style.zoom=1):this.connect(this.document,"onmousedown",function(){delete this._cursorToStart});if(d("webkit"))this._webkitListener=this.connect(this.document,"onmouseup","onDisplayChanged"),this.connect(this.document,"onmousedown",function(a){(a=a.target)&&(a===this.document.body||a===this.document)&&setTimeout(g.hitch(this,"placeCursorAtEnd"),0)});if(d("ie"))try{this.document.execCommand("RespectVisibilityInDesign",
!0,null)}catch(m){}this.isLoaded=!0;this.set("disabled",this.disabled);c=g.hitch(this,function(){this.setValue(a);this.onLoadDeferred&&this.onLoadDeferred.callback(!0);this.onDisplayChanged();this.focusOnLoad&&Q(g.hitch(this,function(){setTimeout(g.hitch(this,"focus"),this.updateInterval)}));this.value=this.getValue(!0)});this.setValueDeferred?this.setValueDeferred.addCallback(c):c()},onKeyDown:function(a){if(a.keyCode===v.TAB&&this.isTabIndent&&(J.stop(a),this.queryCommandEnabled(a.shiftKey?"outdent":
"indent")))this.execCommand(a.shiftKey?"outdent":"indent");if(d("ie"))if(a.keyCode==v.TAB&&!this.isTabIndent)a.shiftKey&&!a.ctrlKey&&!a.altKey?this.iframe.focus():!a.shiftKey&&!a.ctrlKey&&!a.altKey&&this.tabStop.focus();else if(a.keyCode===v.BACKSPACE&&this.document.selection.type==="Control")J.stop(a),this.execCommand("delete");else if(65<=a.keyCode&&a.keyCode<=90||a.keyCode>=37&&a.keyCode<=40)a.charCode=a.keyCode,this.onKeyPress(a);d("ff")&&(a.keyCode===v.PAGE_UP||a.keyCode===v.PAGE_DOWN)&&this.editNode.clientHeight>=
this.editNode.scrollHeight&&a.preventDefault();return!0},onKeyUp:function(){},setDisabled:function(a){D.deprecated("dijit.Editor::setDisabled is deprecated",'use dijit.Editor::attr("disabled",boolean) instead',2);this.set("disabled",a)},_setValueAttr:function(a){this.setValue(a)},_setDisableSpellCheckAttr:function(a){this.document?C.set(this.document.body,"spellcheck",!a):this.onLoadDeferred.addCallback(g.hitch(this,function(){C.set(this.document.body,"spellcheck",!a)}));this._set("disableSpellCheck",
a)},onKeyPress:function(a){var b=this._keyHandlers[a.keyChar&&a.keyChar.toLowerCase()||a.keyCode],c=arguments;b&&!a.altKey&&w.some(b,function(b){if(!(b.shift^a.shiftKey)&&!(b.ctrl^(a.ctrlKey||a.metaKey)))return b.handler.apply(this,c)||a.preventDefault(),!0},this);if(!this._onKeyHitch)this._onKeyHitch=g.hitch(this,"onKeyPressed");setTimeout(this._onKeyHitch,1);return!0},addKeyHandler:function(a,b,c,e){g.isArray(this._keyHandlers[a])||(this._keyHandlers[a]=[]);this._keyHandlers[a].push({shift:c||!1,
ctrl:b||!1,handler:e})},onKeyPressed:function(){this.onDisplayChanged()},onClick:function(a){this.onDisplayChanged(a)},_onIEMouseDown:function(){!this.focused&&!this.disabled&&this.focus()},_onBlur:function(){this.inherited(arguments);var a=this.getValue(!0);if(a!==this.value)this.onChange(a);this._set("value",a)},_onFocus:function(){this.disabled||(this._disabledOK||this.set("disabled",!1),this.inherited(arguments))},blur:function(){!d("ie")&&this.window.document.documentElement&&this.window.document.documentElement.focus?
this.window.document.documentElement.focus():s.doc.body.focus&&s.doc.body.focus()},focus:function(){if(this.isLoaded){if(this._cursorToStart&&(delete this._cursorToStart,this.editNode.childNodes)){this.placeCursorAtStart();return}d("ie")?this.editNode&&this.editNode.focus&&this.iframe.fireEvent("onfocus",document.createEventObject()):M.focus(this.iframe)}else this.focusOnLoad=!0},updateInterval:200,_updateTimer:null,onDisplayChanged:function(){this._updateTimer&&clearTimeout(this._updateTimer);if(!this._updateHandler)this._updateHandler=
g.hitch(this,"onNormalizedDisplayChanged");this._updateTimer=setTimeout(this._updateHandler,this.updateInterval)},onNormalizedDisplayChanged:function(){delete this._updateTimer},onChange:function(){},_normalizeCommand:function(a,b){var c=a.toLowerCase();c==="formatblock"?d("safari")&&b===void 0&&(c="heading"):c==="hilitecolor"&&!d("mozilla")&&(c="backcolor");return c},_qcaCache:{},queryCommandAvailable:function(a){var b=this._qcaCache[a];if(b!==void 0)return b;return this._qcaCache[a]=this._queryCommandAvailable(a)},
_queryCommandAvailable:function(a){function b(a){return{ie:Boolean(a&c),mozilla:Boolean(a&e),webkit:Boolean(a&m),opera:Boolean(a&i)}}var c=1,e=2,m=4,i=8,g=null;switch(a.toLowerCase()){case "bold":case "italic":case "underline":case "subscript":case "superscript":case "fontname":case "fontsize":case "forecolor":case "hilitecolor":case "justifycenter":case "justifyfull":case "justifyleft":case "justifyright":case "delete":case "selectall":case "toggledir":g=b(e|c|m|i);break;case "createlink":case "unlink":case "removeformat":case "inserthorizontalrule":case "insertimage":case "insertorderedlist":case "insertunorderedlist":case "indent":case "outdent":case "formatblock":case "inserthtml":case "undo":case "redo":case "strikethrough":case "tabindent":g=
b(e|c|i|m);break;case "blockdirltr":case "blockdirrtl":case "dirltr":case "dirrtl":case "inlinedirltr":case "inlinedirrtl":g=b(c);break;case "cut":case "copy":case "paste":g=b(c|e|m);break;case "inserttable":g=b(e|c);break;case "insertcell":case "insertcol":case "insertrow":case "deletecells":case "deletecols":case "deleterows":case "mergecells":case "splitcell":g=b(c|e);break;default:return!1}return d("ie")&&g.ie||d("mozilla")&&g.mozilla||d("webkit")&&g.webkit||d("opera")&&g.opera},execCommand:function(a,
b){var c;this.focus();a=this._normalizeCommand(a,b);if(b!==void 0)if(a==="heading")throw Error("unimplemented");else a==="formatblock"&&d("ie")&&(b="<"+b+">");var e="_"+a+"Impl";if(this[e])c=this[e](b);else if((b=arguments.length>1?b:null)||a!=="createlink")c=this.document.execCommand(a,!1,b);this.onDisplayChanged();return c},queryCommandEnabled:function(a){if(this.disabled||!this._disabledOK)return!1;var a=this._normalizeCommand(a),b="_"+a+"EnabledImpl";return this[b]?this[b](a):this._browserQueryCommandEnabled(a)},
queryCommandState:function(a){if(this.disabled||!this._disabledOK)return!1;a=this._normalizeCommand(a);try{return this.document.queryCommandState(a)}catch(b){return!1}},queryCommandValue:function(a){if(this.disabled||!this._disabledOK)return!1;a=this._normalizeCommand(a);if(d("ie")&&a==="formatblock")a=this._native2LocalFormatNames[this.document.queryCommandValue(a)];else if(d("mozilla")&&a==="hilitecolor"){var b;try{b=this.document.queryCommandValue("styleWithCSS")}catch(c){b=!1}this.document.execCommand("styleWithCSS",
!1,!0);a=this.document.queryCommandValue(a);this.document.execCommand("styleWithCSS",!1,b)}else a=this.document.queryCommandValue(a);return a},_sCall:function(a,b){return s.withGlobal(this.window,a,x,b)},placeCursorAtStart:function(){this.focus();var a=!1;if(d("mozilla"))for(var b=this.editNode.firstChild;b;){if(b.nodeType===3){if(b.nodeValue.replace(/^\s+|\s+$/g,"").length>0){a=!0;this._sCall("selectElement",[b]);break}}else if(b.nodeType===1){a=!0;/br|input|img|base|meta|area|basefont|hr|link/.test(b.tagName?
b.tagName.toLowerCase():"")?this._sCall("selectElement",[b]):this._sCall("selectElementChildren",[b]);break}b=b.nextSibling}else a=!0,this._sCall("selectElementChildren",[this.editNode]);a&&this._sCall("collapse",[!0])},placeCursorAtEnd:function(){this.focus();var a=!1;if(d("mozilla"))for(var b=this.editNode.lastChild;b;){if(b.nodeType===3){if(b.nodeValue.replace(/^\s+|\s+$/g,"").length>0){a=!0;this._sCall("selectElement",[b]);break}}else if(b.nodeType===1){a=!0;b.lastChild?this._sCall("selectElement",
[b.lastChild]):this._sCall("selectElement",[b]);break}b=b.previousSibling}else a=!0,this._sCall("selectElementChildren",[this.editNode]);a&&this._sCall("collapse",[!1])},getValue:function(a){if(this.textarea&&(this.isClosed||!this.isLoaded))return this.textarea.value;return this._postFilterContent(null,a)},_getValueAttr:function(){return this.getValue(!0)},setValue:function(a){if(this.isLoaded){this._cursorToStart=!0;if(this.textarea&&(this.isClosed||!this.isLoaded))this.textarea.value=a;else{var a=
this._preFilterContent(a),b=this.isClosed?this.domNode:this.editNode;a&&d("mozilla")&&a.toLowerCase()==="<p></p>"&&(a="<p>&#160;</p>");!a&&d("webkit")&&(a="&#160;");b.innerHTML=a;this._preDomFilterContent(b)}this.onDisplayChanged();this._set("value",this.getValue(!0))}else this.onLoadDeferred.addCallback(g.hitch(this,function(){this.setValue(a)}))},replaceValue:function(a){if(this.isClosed)this.setValue(a);else if(this.window&&this.window.getSelection&&!d("mozilla"))this.setValue(a);else if(this.window&&
this.window.getSelection){a=this._preFilterContent(a);this.execCommand("selectall");if(!a)this._cursorToStart=!0,a="&#160;";this.execCommand("inserthtml",a);this._preDomFilterContent(this.editNode)}else this.document&&this.document.selection&&this.setValue(a);this._set("value",this.getValue(!0))},_preFilterContent:function(a){var b=a;w.forEach(this.contentPreFilters,function(a){a&&(b=a(b))});return b},_preDomFilterContent:function(a){a=a||this.editNode;w.forEach(this.contentDomPreFilters,function(b){b&&
g.isFunction(b)&&b(a)},this)},_postFilterContent:function(a,b){var c;g.isString(a)?c=a:(a=a||this.editNode,this.contentDomPostFilters.length&&(b&&(a=g.clone(a)),w.forEach(this.contentDomPostFilters,function(b){a=b(a)})),c=B.getChildrenHtml(a));g.trim(c.replace(/^\xA0\xA0*/,"").replace(/\xA0\xA0*$/,"")).length||(c="");w.forEach(this.contentPostFilters,function(a){c=a(c)});return c},_saveContent:function(){var a=H.byId(z._scopeName+"._editor.RichText.value");a&&(a.value&&(a.value+=this._SEPARATOR),
a.value+=this.name+this._NAME_CONTENT_SEP+this.getValue(!0))},escapeXml:function(a,b){a=a.replace(/&/gm,"&amp;").replace(/</gm,"&lt;").replace(/>/gm,"&gt;").replace(/"/gm,"&quot;");b||(a=a.replace(/'/gm,"&#39;"));return a},getNodeHtml:function(a){D.deprecated("dijit.Editor::getNodeHtml is deprecated","use dijit/_editor/html::getNodeHtml instead",2);return B.getNodeHtml(a)},getNodeChildrenHtml:function(a){D.deprecated("dijit.Editor::getNodeChildrenHtml is deprecated","use dijit/_editor/html::getChildrenHtml instead",
2);return B.getChildrenHtml(a)},close:function(a){if(!this.isClosed){arguments.length||(a=!0);a&&this._set("value",this.getValue(!0));this.interval&&clearInterval(this.interval);this._webkitListener&&(this.disconnect(this._webkitListener),delete this._webkitListener);if(d("ie"))this.iframe.onfocus=null;this.iframe._loadFunc=null;this._iframeRegHandle&&(this._iframeRegHandle.remove(),delete this._iframeRegHandle);if(this.textarea){var b=this.textarea.style;b.position="";b.left=b.top="";if(d("ie"))b.overflow=
this.__overflow,this.__overflow=null;this.textarea.value=this.value;i.destroy(this.domNode);this.domNode=this.textarea}else this.domNode.innerHTML=this.value;delete this.iframe;I.remove(this.domNode,this.baseClass);this.isClosed=!0;this.isLoaded=!1;delete this.editNode;delete this.focusNode;if(this.window&&this.window._frameElement)this.window._frameElement=null;this.editorObject=this.editingArea=this.document=this.window=null}},destroy:function(){this.isClosed||this.close(!1);this._updateTimer&&
clearTimeout(this._updateTimer);this.inherited(arguments);k._globalSaveHandler&&delete k._globalSaveHandler[this.id]},_removeMozBogus:function(a){return a.replace(/\stype="_moz"/gi,"").replace(/\s_moz_dirty=""/gi,"").replace(/_moz_resizing="(true|false)"/gi,"")},_removeWebkitBogus:function(a){a=a.replace(/\sclass="webkit-block-placeholder"/gi,"");a=a.replace(/\sclass="apple-style-span"/gi,"");return a=a.replace(/<meta charset=\"utf-8\" \/>/gi,"")},_normalizeFontStyle:function(a){return a.replace(/<(\/)?strong([ \>])/gi,
"<$1b$2").replace(/<(\/)?em([ \>])/gi,"<$1i$2")},_preFixUrlAttributes:function(a){return a.replace(/(?:(<a(?=\s).*?\shref=)("|')(.*?)\2)|(?:(<a\s.*?href=)([^"'][^ >]+))/gi,"$1$4$2$3$5$2 _djrealurl=$2$3$5$2").replace(/(?:(<img(?=\s).*?\ssrc=)("|')(.*?)\2)|(?:(<img\s.*?src=)([^"'][^ >]+))/gi,"$1$4$2$3$5$2 _djrealurl=$2$3$5$2")},_browserQueryCommandEnabled:function(a){if(!a)return!1;var b=d("ie")?this.document.selection.createRange():this.document;try{return b.queryCommandEnabled(a)}catch(c){return!1}},
_createlinkEnabledImpl:function(){var a=!0;return a=d("opera")?this.window.getSelection().isCollapsed?!0:this.document.queryCommandEnabled("createlink"):this._browserQueryCommandEnabled("createlink")},_unlinkEnabledImpl:function(){var a=!0;return a=d("mozilla")||d("webkit")?this._sCall("hasAncestorElement",["a"]):this._browserQueryCommandEnabled("unlink")},_inserttableEnabledImpl:function(){var a=!0;return a=d("mozilla")||d("webkit")?!0:this._browserQueryCommandEnabled("inserttable")},_cutEnabledImpl:function(){var a=
!0;d("webkit")?((a=this.window.getSelection())&&(a=a.toString()),a=!!a):a=this._browserQueryCommandEnabled("cut");return a},_copyEnabledImpl:function(){var a=!0;d("webkit")?((a=this.window.getSelection())&&(a=a.toString()),a=!!a):a=this._browserQueryCommandEnabled("copy");return a},_pasteEnabledImpl:function(){var a=!0;if(d("webkit"))return!0;else a=this._browserQueryCommandEnabled("paste");return a},_inserthorizontalruleImpl:function(a){if(d("ie"))return this._inserthtmlImpl("<hr>");return this.document.execCommand("inserthorizontalrule",
!1,a)},_unlinkImpl:function(a){if(this.queryCommandEnabled("unlink")&&(d("mozilla")||d("webkit")))return this._sCall("selectElement",[this._sCall("getAncestorElement",["a"])]),this.document.execCommand("unlink",!1,null);return this.document.execCommand("unlink",!1,a)},_hilitecolorImpl:function(a){var b;this._handleTextColorOrProperties("hilitecolor",a)||(d("mozilla")?(this.document.execCommand("styleWithCSS",!1,!0),console.log("Executing color command."),b=this.document.execCommand("hilitecolor",
!1,a),this.document.execCommand("styleWithCSS",!1,!1)):b=this.document.execCommand("hilitecolor",!1,a));return b},_backcolorImpl:function(a){d("ie")&&(a=a?a:null);var b=this._handleTextColorOrProperties("backcolor",a);b||(b=this.document.execCommand("backcolor",!1,a));return b},_forecolorImpl:function(a){d("ie")&&(a=a?a:null);var b=!1;(b=this._handleTextColorOrProperties("forecolor",a))||(b=this.document.execCommand("forecolor",!1,a));return b},_inserthtmlImpl:function(a){var a=this._preFilterContent(a),
b=!0;if(d("ie")){var c=this.document.selection.createRange();if(this.document.selection.type.toUpperCase()==="CONTROL"){for(var e=c.item(0);c.length;)c.remove(c.item(0));e.outerHTML=a}else c.pasteHTML(a);c.select()}else d("mozilla")&&!a.length?this._sCall("remove"):b=this.document.execCommand("inserthtml",!1,a);return b},_boldImpl:function(a){var b=!1;d("ie")&&(this._adaptIESelection(),b=this._adaptIEFormatAreaAndExec("bold"));b||(b=this.document.execCommand("bold",!1,a));return b},_italicImpl:function(a){var b=
!1;d("ie")&&(this._adaptIESelection(),b=this._adaptIEFormatAreaAndExec("italic"));b||(b=this.document.execCommand("italic",!1,a));return b},_underlineImpl:function(a){var b=!1;d("ie")&&(this._adaptIESelection(),b=this._adaptIEFormatAreaAndExec("underline"));b||(b=this.document.execCommand("underline",!1,a));return b},_strikethroughImpl:function(a){var b=!1;d("ie")&&(this._adaptIESelection(),b=this._adaptIEFormatAreaAndExec("strikethrough"));b||(b=this.document.execCommand("strikethrough",!1,a));return b},
_superscriptImpl:function(a){var b=!1;d("ie")&&(this._adaptIESelection(),b=this._adaptIEFormatAreaAndExec("superscript"));b||(b=this.document.execCommand("superscript",!1,a));return b},_subscriptImpl:function(a){var b=!1;d("ie")&&(this._adaptIESelection(),b=this._adaptIEFormatAreaAndExec("subscript"));b||(b=this.document.execCommand("subscript",!1,a));return b},_fontnameImpl:function(a){var b;d("ie")&&(b=this._handleTextColorOrProperties("fontname",a));b||(b=this.document.execCommand("fontname",!1,
a));return b},_fontsizeImpl:function(a){var b;d("ie")&&(b=this._handleTextColorOrProperties("fontsize",a));b||(b=this.document.execCommand("fontsize",!1,a));return b},_insertorderedlistImpl:function(a){var b=!1;d("ie")&&(b=this._adaptIEList("insertorderedlist",a));b||(b=this.document.execCommand("insertorderedlist",!1,a));return b},_insertunorderedlistImpl:function(a){var b=!1;d("ie")&&(b=this._adaptIEList("insertunorderedlist",a));b||(b=this.document.execCommand("insertunorderedlist",!1,a));return b},
getHeaderHeight:function(){return this._getNodeChildrenHeight(this.header)},getFooterHeight:function(){return this._getNodeChildrenHeight(this.footer)},_getNodeChildrenHeight:function(a){var b=0;if(a&&a.childNodes){var c;for(c=0;c<a.childNodes.length;c++){var e=O.position(a.childNodes[c]);b+=e.h}}return b},_isNodeEmpty:function(a,b){if(a.nodeType===1){if(a.childNodes.length>0)return this._isNodeEmpty(a.childNodes[0],b);return!0}else if(a.nodeType===3)return a.nodeValue.substring(b)==="";return!1},
_removeStartingRangeFromRange:function(a,b){if(a.nextSibling)b.setStart(a.nextSibling,0);else{for(var c=a.parentNode;c&&c.nextSibling==null;)c=c.parentNode;c&&b.setStart(c.nextSibling,0)}return b},_adaptIESelection:function(){var a=y.getSelection(this.window);if(a&&a.rangeCount&&!a.isCollapsed){for(var b=a.getRangeAt(0),c=b.startContainer,e=b.startOffset;c.nodeType===3&&e>=c.length&&c.nextSibling;)e-=c.length,c=c.nextSibling;for(var d=null;this._isNodeEmpty(c,e)&&c!==d;)d=c,b=this._removeStartingRangeFromRange(c,
b),c=b.startContainer,e=0;a.removeAllRanges();a.addRange(b)}},_adaptIEFormatAreaAndExec:function(a){var b=y.getSelection(this.window),c=this.document,e,d,u,q,r,n,j,l;if(a&&b&&b.isCollapsed)if(this.queryCommandValue(a)){var p=this._tagNamesForCommand(a);u=b.getRangeAt(0);var o=u.startContainer;if(o.nodeType===3){var f=u.endOffset;if(o.length<f)d=this._adjustNodeAndOffset(e,f),o=d.node,f=d.offset}for(var k;o&&o!==this.editNode;){f=o.tagName?o.tagName.toLowerCase():"";if(w.indexOf(p,f)>-1){k=o;break}o=
o.parentNode}if(k&&(e=u.startContainer,p=c.createElement(k.tagName),i.place(p,k,"after"),e&&e.nodeType===3)){var h,v,o=u.endOffset;if(e.length<o)d=this._adjustNodeAndOffset(e,o),e=d.node,o=d.offset;q=e.nodeValue;r=c.createTextNode(q.substring(0,o));(o=q.substring(o,q.length))&&(n=c.createTextNode(o));i.place(r,e,"before");if(n)j=c.createElement("span"),j.className="ieFormatBreakerSpan",i.place(j,e,"after"),i.place(n,j,"after"),n=j;i.destroy(e);f=r.parentNode;for(o=[];f!==k;){var t=f.tagName;h={tagName:t};
o.push(h);t=c.createElement(t);if(f.style&&t.style&&f.style.cssText)t.style.cssText=f.style.cssText,h.cssText=f.style.cssText;if(f.tagName==="FONT"){if(f.color)t.color=f.color,h.color=f.color;if(f.face)t.face=f.face,h.face=f.face;if(f.size)t.size=f.size,h.size=f.size}if(f.className)t.className=f.className,h.className=f.className;if(n)for(h=n;h;)v=h.nextSibling,t.appendChild(h),h=v;t.tagName==f.tagName?(j=c.createElement("span"),j.className="ieFormatBreakerSpan",i.place(j,f,"after"),i.place(t,j,"after")):
i.place(t,f,"after");r=f;n=t;f=f.parentNode}if(n){h=n;if(h.nodeType===1||h.nodeType===3&&h.nodeValue)p.innerHTML="";for(;h;)v=h.nextSibling,p.appendChild(h),h=v}if(o.length){h=o.pop();k=c.createElement(h.tagName);if(h.cssText&&k.style)k.style.cssText=h.cssText;if(h.className)k.className=h.className;if(h.tagName==="FONT"){if(h.color)k.color=h.color;if(h.face)k.face=h.face;if(h.size)k.size=h.size}for(i.place(k,p,"before");o.length;){h=o.pop();f=c.createElement(h.tagName);if(h.cssText&&f.style)f.style.cssText=
h.cssText;if(h.className)f.className=h.className;if(h.tagName==="FONT"){if(h.color)f.color=h.color;if(h.face)f.face=h.face;if(h.size)f.size=h.size}k.appendChild(f);k=f}l=c.createTextNode(".");j.appendChild(l);k.appendChild(l);s.withGlobal(this.window,g.hitch(this,function(){var a=y.create();a.setStart(l,0);a.setEnd(l,l.length);b.removeAllRanges();b.addRange(a);x.collapse(!1);l.parentNode.innerHTML=""}))}else j=c.createElement("span"),j.className="ieFormatBreakerSpan",l=c.createTextNode("."),j.appendChild(l),
i.place(j,p,"before"),s.withGlobal(this.window,g.hitch(this,function(){var a=y.create();a.setStart(l,0);a.setEnd(l,l.length);b.removeAllRanges();b.addRange(a);x.collapse(!1);l.parentNode.innerHTML=""}));p.firstChild||i.destroy(p);return!0}return!1}else{if(u=b.getRangeAt(0),(e=u.startContainer)&&e.nodeType===3)return s.withGlobal(this.window,g.hitch(this,function(){var f=u.startOffset;if(e.length<f)d=this._adjustNodeAndOffset(e,f),e=d.node,f=d.offset;q=e.nodeValue;r=c.createTextNode(q.substring(0,
f));q.substring(f)!==""&&(n=c.createTextNode(q.substring(f)));j=c.createElement("span");l=c.createTextNode(".");j.appendChild(l);r.length?i.place(r,e,"after"):r=e;i.place(j,r,"after");n&&i.place(n,j,"after");i.destroy(e);f=y.create();f.setStart(l,0);f.setEnd(l,l.length);b.removeAllRanges();b.addRange(f);c.execCommand(a);i.place(j.firstChild,j,"before");i.destroy(j);f.setStart(l,0);f.setEnd(l,l.length);b.removeAllRanges();b.addRange(f);x.collapse(!1);l.parentNode.innerHTML=""})),!0}else return!1},
_adaptIEList:function(a){var b=y.getSelection(this.window);if(b.isCollapsed&&b.rangeCount&&!this.queryCommandValue(a)){var c=b.getRangeAt(0),d=c.startContainer;if(d&&d.nodeType==3&&!c.startOffset)return s.withGlobal(this.window,g.hitch(this,function(){var c="ul";a==="insertorderedlist"&&(c="ol");var c=i.create(c),g=i.create("li",null,c);i.place(c,d,"before");g.appendChild(d);i.create("br",null,c,"after");c=y.create();c.setStart(d,0);c.setEnd(d,d.length);b.removeAllRanges();b.addRange(c);x.collapse(!0)})),
!0}return!1},_handleTextColorOrProperties:function(a,b){var c=y.getSelection(this.window),e=this.document,m,k,q,r,n,j,l,p,b=b||null;if(a&&c&&c.isCollapsed&&c.rangeCount&&(q=c.getRangeAt(0),(m=q.startContainer)&&m.nodeType===3))return s.withGlobal(this.window,g.hitch(this,function(){var g=q.startOffset;if(m.length<g)k=this._adjustNodeAndOffset(m,g),m=k.node,g=k.offset;r=m.nodeValue;n=e.createTextNode(r.substring(0,g));r.substring(g)!==""&&(j=e.createTextNode(r.substring(g)));l=i.create("span");p=e.createTextNode(".");
l.appendChild(p);g=i.create("span");l.appendChild(g);n.length?i.place(n,m,"after"):n=m;i.place(l,n,"after");j&&i.place(j,l,"after");i.destroy(m);var f=y.create();f.setStart(p,0);f.setEnd(p,p.length);c.removeAllRanges();c.addRange(f);if(d("webkit")){f="color";if(a==="hilitecolor"||a==="backcolor")f="backgroundColor";A.set(l,f,b);x.remove();i.destroy(g);l.innerHTML="&#160;";x.selectElement(l);this.focus()}else this.execCommand(a,b),i.place(l.firstChild,l,"before"),i.destroy(l),f.setStart(p,0),f.setEnd(p,
p.length),c.removeAllRanges(),c.addRange(f),x.collapse(!1),p.parentNode.removeChild(p)})),!0;return!1},_adjustNodeAndOffset:function(a,b){for(;a.length<b&&a.nextSibling&&a.nextSibling.nodeType===3;)b-=a.length,a=a.nextSibling;return{node:a,offset:b}},_tagNamesForCommand:function(a){if(a==="bold")return["b","strong"];else if(a==="italic")return["i","em"];else if(a==="strikethrough")return["s","strike"];else if(a==="superscript")return["sup"];else if(a==="subscript")return["sub"];else if(a==="underline")return["u"];
return[]},_stripBreakerNodes:function(a){s.withGlobal(this.window,g.hitch(this,function(){var b=K(".ieFormatBreakerSpan",a),c;for(c=0;c<b.length;c++){for(var d=b[c];d.firstChild;)i.place(d.firstChild,d,"before");i.destroy(d)}}));return a}});return k});