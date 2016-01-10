//>>built
define(["dijit","dojo","dojox","dojo/require!dojox/drawing/stencil/Text"],function(n,b,l){b.provide("dojox.drawing.tools.TextBlock");b.require("dojox.drawing.stencil.Text");(function(){var f;b.addOnLoad(function(){(f=b.byId("conEdit"))?f.parentNode.removeChild(f):console.error("A contenteditable div is missing from the main document. See 'dojox.drawing.tools.TextBlock'")});l.drawing.tools.TextBlock=l.drawing.util.oo.declare(l.drawing.stencil.Text,function(a){if(a.data){var a=a.data,d=a.text?this.typesetter(a.text):
a.text,c=!a.width?this.style.text.minWidth:a.width=="auto"?"auto":Math.max(a.width,this.style.text.minWidth),e=this._lineHeight;d&&c=="auto"?(e=this.measureText(this.cleanText(d,!1),c),c=e.w,e=e.h):this._text="";this.points=[{x:a.x,y:a.y},{x:a.x+c,y:a.y},{x:a.x+c,y:a.y+e},{x:a.x,y:a.y+e}];a.showEmpty||d?(this.editMode=!0,b.disconnect(this._postRenderCon),this._postRenderCon=null,this.connect(this,"render",this,"onRender",!0),a.showEmpty?(this._text=d||"",this.edit()):d&&a.editMode?(this._text="",
this.edit()):d&&this.render(d),setTimeout(b.hitch(this,function(){this.editMode=!1}),100)):this.render()}else this.connectMouse(),this._postRenderCon=b.connect(this,"render",this,"_onPostRender")},{draws:!0,baseRender:!1,type:"dojox.drawing.tools.TextBlock",_caretStart:0,_caretEnd:0,_blockExec:!1,selectOnExec:!0,showEmpty:!1,onDrag:function(a){this.parentNode||this.showParent(a);var d=this._startdrag,a=a.page;this._box.left=d.x<a.x?d.x:a.x;this._box.top=d.y;this._box.width=(d.x<a.x?a.x-d.x:d.x-a.x)+
this.style.text.pad;b.style(this.parentNode,this._box.toPx())},onUp:function(a){if(this._downOnCanvas){this._downOnCanvas=!1;var d=b.connect(this,"render",this,function(){b.disconnect(d);this.onRender(this)});this.editMode=!0;this.showParent(a);this.created=!0;this.createTextField();this.connectTextField()}},showParent:function(a){if(!this.parentNode){var d=a.pageX||10,c=a.pageY||10;this.parentNode=b.doc.createElement("div");this.parentNode.id=this.id;var e=this.style.textMode.create;this._box={left:d,
top:c,width:a.width||1,height:a.height&&a.height>8?a.height:this._lineHeight,border:e.width+"px "+e.style+" "+e.color,position:"absolute",zIndex:500,toPx:function(){var a={},b;for(b in this)a[b]=typeof this[b]=="number"&&b!="zIndex"?this[b]+"px":this[b];return a}};b.style(this.parentNode,this._box);document.body.appendChild(this.parentNode)}},createTextField:function(a){var d=this.style.textMode.edit;this._box.border=d.width+"px "+d.style+" "+d.color;this._box.height="auto";this._box.width=Math.max(this._box.width,
this.style.text.minWidth*this.mouse.zoom);b.style(this.parentNode,this._box.toPx());this.parentNode.appendChild(f);b.style(f,{height:a?"auto":this._lineHeight+"px",fontSize:this.textSize/this.mouse.zoom+"px",fontFamily:this.style.text.family});f.innerHTML=a||"";return f},connectTextField:function(){if(!this._textConnected){var a=n.byId("greekPalette"),d=a==void 0?!1:!0;d&&b.mixin(a,{_pushChangeTo:f,_textBlock:this});this._textConnected=!0;this._dropMode=!1;this.mouse.setEventMode("TEXT");this.keys.editMode(!0);
var c,e,h,g,i=this,j=!1,k=function(){if(!i._dropMode)b.forEach([c,e,h,g],function(a){b.disconnect(a)}),i._textConnected=!1,i.keys.editMode(!1),i.mouse.setEventMode(),i.execText()};c=b.connect(f,"keyup",this,function(c){b.trim(f.innerHTML)&&!j?(b.style(f,"height","auto"),j=!0):b.trim(f.innerHTML).length<2&&j&&(b.style(f,"height",this._lineHeight+"px"),j=!1);if(this._blockExec)c.keyCode==b.keys.SPACE&&(b.stopEvent(c),d&&a.onCancel());else if(c.keyCode==13||c.keyCode==27)b.stopEvent(c),k()});e=b.connect(f,
"keydown",this,function(c){(c.keyCode==13||c.keyCode==27)&&b.stopEvent(c);if(c.keyCode==220){if(!d){console.info("For greek letter assistance instantiate: dojox.drawing.plugins.drawing.GreekPalette");return}b.stopEvent(c);this.getSelection(f);this.insertText(f,"\\");this._blockExec=this._dropMode=!0;a.show({around:this.parentNode,orient:{BL:"TL"}})}if(this._dropMode)switch(c.keyCode){case b.keys.UP_ARROW:case b.keys.DOWN_ARROW:case b.keys.LEFT_ARROW:case b.keys.RIGHT_ARROW:b.stopEvent(c);a._navigateByArrow(c);
break;case b.keys.ENTER:b.stopEvent(c);a._onCellClick(c);break;case b.keys.BACKSPACE:case b.keys.DELETE:b.stopEvent(c),a.onCancel()}else this._blockExec=!1});h=b.connect(document,"mouseup",this,function(a){!this._onAnchor&&a.target.id!="conEdit"?(b.stopEvent(a),k()):a.target.id=="conEdit"&&f.innerHTML==""&&(f.blur(),setTimeout(function(){f.focus()},200))});this.createAnchors();g=b.connect(this.mouse,"setZoom",this,function(){k()});f.focus();this.onDown=function(){};this.onDrag=function(){};setTimeout(b.hitch(this,
function(){f.focus();this.onUp=function(){if(!i._onAnchor&&this.parentNode)i.disconnectMouse(),k(),i.onUp=function(){}}}),500)}},execText:function(){var a=b.marginBox(this.parentNode),a=Math.max(a.w,this.style.text.minWidth),d=this.cleanText(f.innerHTML,!0);f.innerHTML="";f.blur();this.destroyAnchors();var d=this.typesetter(d),d=this.measureText(d,a),c=this.mouse.scrollOffset(),e=this.mouse.origin,h=this._box.left+c.left-e.x,c=this._box.top+c.top-e.y;h*=this.mouse.zoom;c*=this.mouse.zoom;a*=this.mouse.zoom;
d.h*=this.mouse.zoom;this.points=[{x:h,y:c},{x:h+a,y:c},{x:h+a,y:c+d.h},{x:h,y:c+d.h}];this.editMode=!1;console.log("EXEC TEXT::::",this._postRenderCon);if(!d.text)this._text="",this._textArray=[];this.render(d.text);this.onChangeText(this.getText())},edit:function(){this.editMode=!0;var a=this.getText()||"";console.log("EDIT TEXT:",a," ",a.replace("/n"," "));if(!this.parentNode&&this.points){var b=this.pointsToData(),c=this.mouse.scrollOffset(),e=this.mouse.origin,b={pageX:b.x/this.mouse.zoom-c.left+
e.x,pageY:b.y/this.mouse.zoom-c.top+e.y,width:b.width/this.mouse.zoom,height:b.height/this.mouse.zoom};this.remove(this.shape,this.hit);this.showParent(b);this.createTextField(a.replace("/n"," "));this.connectTextField();a&&this.setSelection(f,"end")}},cleanText:function(a,d){d&&b.forEach(["<br>","<br/>","<br />","\\n","\\r"],function(c){a=a.replace(RegExp(c,"gi")," ")});a=a.replace(/&nbsp;/g," ");a=function(a){var b={"&lt;":"<","&gt;":">","&amp;":"&"},d;for(d in b)a=a.replace(RegExp(d,"gi"),b[d]);
return a}(a);a=b.trim(a);return a=a.replace(/\s{2,}/g," ")},measureText:function(a,d){this.showParent({width:d||"auto",height:"auto"});this.createTextField(a);var c="",e=f;e.innerHTML="X";c=b.marginBox(e).h;e.innerHTML=a;if(!d||RegExp("(<br\\s*/*>)|(\\n)|(\\r)","gi").test(a))c=a.replace(RegExp("(<br\\s*/*>)|(\\n)|(\\r)","gi"),"\n"),e.innerHTML=a.replace(RegExp("(<br\\s*/*>)|(\\n)|(\\r)","gi"),"<br/>");else if(b.marginBox(e).h==c)c=a;else{var h=a.split(" "),g=[[]],i=0;for(e.innerHTML="";h.length;){var j=
h.shift();e.innerHTML+=j+" ";if(b.marginBox(e).h>c)i++,g[i]=[],e.innerHTML=j+" ";g[i].push(j)}b.forEach(g,function(a,b){g[b]=a.join(" ")});c=g.join("\n");e.innerHTML=c.replace("\n","<br/>")}e=b.marginBox(e);f.parentNode.removeChild(f);b.destroy(this.parentNode);this.parentNode=null;return{h:e.h,w:e.w,text:c}},_downOnCanvas:!1,onDown:function(a){this._startdrag={x:a.pageX,y:a.pageY};b.disconnect(this._postRenderCon);this._postRenderCon=null;this._downOnCanvas=!0},createAnchors:function(){this._anchors=
{};var a=this,d=this.style.anchors,c=d.width,e=d.size-c*2,h=d.size/2*-1+"px",d={position:"absolute",width:e+"px",height:d.size-c*2+"px",backgroundColor:d.fill,border:c+"px "+d.style+" "+d.color};if(b.isIE)d.paddingLeft=e+"px",d.fontSize=e+"px";e=[{top:h,left:h},{top:h,right:h},{bottom:h,right:h},{bottom:h,left:h}];for(h=0;h<4;h++){var g=h==0||h==3,c=this.util.uid(g?"left_anchor":"right_anchor"),i=b.create("div",{id:c},this.parentNode);b.style(i,b.mixin(b.clone(d),e[h]));var j,k,m;j=b.connect(i,"mousedown",
this,function(c){g=c.target.id.indexOf("left")>-1;a._onAnchor=!0;var d=c.pageX,e=this._box.width;b.stopEvent(c);k=b.connect(document,"mousemove",this,function(a){a=a.pageX;g?(this._box.left=a,this._box.width=e+d-a):this._box.width=a+e-d;b.style(this.parentNode,this._box.toPx())});m=b.connect(document,"mouseup",this,function(c){d=this._box.left;e=this._box.width;b.disconnect(k);b.disconnect(m);a._onAnchor=!1;f.focus();b.stopEvent(c)})});this._anchors[c]={a:i,cons:[j]}}},destroyAnchors:function(){for(var a in this._anchors)b.forEach(this._anchors[a].con,
b.disconnect,b),b.destroy(this._anchors[a].a)},setSavedCaret:function(a){this._caretStart=this._caretEnd=a},getSavedCaret:function(){return{start:this._caretStart,end:this._caretEnd}},insertText:function(a,b){var c;c=a.innerHTML;var e=this.getSavedCaret();c=c.replace(/&nbsp;/g," ");c=c.substr(0,e.start)+b+c.substr(e.end);c=this.cleanText(c,!0);this.setSavedCaret(Math.min(c.length,e.end+b.length));a.innerHTML=c;this.setSelection(a,"stored")},getSelection:function(a){var e;var d,c;b.doc.selection?(c=
b.doc.selection.createRange(),d=b.body().createTextRange(),d.moveToElementText(a),a=d.duplicate(),d.moveToBookmark(c.getBookmark()),a.setEndPoint("EndToStart",d),d=this._caretStart=a.text.length,e=this._caretEnd=a.text.length+c.text.length,c=e,console.warn("Caret start: ",d," end: ",c," length: ",a.text.length," text: ",a.text)):(this._caretStart=b.global.getSelection().getRangeAt(a).startOffset,this._caretEnd=b.global.getSelection().getRangeAt(a).endOffset,console.log("Caret start: ",this._caretStart,
" end: ",this._caretEnd))},setSelection:function(a,d){console.warn("setSelection:");if(b.doc.selection){var c=b.body().createTextRange();c.moveToElementText(a);switch(d){case "end":c.collapse(!1);break;case "beg":c.collapse();break;case "all":c.collapse();c.moveStart("character",0);c.moveEnd("character",a.text.length);break;case "stored":c.collapse();var e=this._caretStart-this._caretEnd;c.moveStart("character",this._caretStart);c.moveEnd("character",e)}c.select()}else{var f=function(a,b){for(var b=
b||[],c=0;c<a.childNodes.length;c++){var d=a.childNodes[c];d.nodeType==3?b.push(d):d.tagName&&d.tagName.toLowerCase()=="img"&&b.push(d);d.childNodes&&d.childNodes.length&&f(d,b)}return b};console.log("ff node:",a);a.focus();c=b.global.getSelection();c.removeAllRanges();var e=b.doc.createRange(),g=f(a);switch(d){case "end":console.log("len:",g[g.length-1].textContent.length);e.setStart(g[g.length-1],g[g.length-1].textContent.length);e.setEnd(g[g.length-1],g[g.length-1].textContent.length);break;case "beg":e.setStart(g[0],
0);e.setEnd(g[0],0);break;case "all":e.setStart(g[0],0);e.setEnd(g[g.length-1],g[g.length-1].textContent.length);break;case "stored":console.log("Caret start: ",this._caretStart," caret end: ",this._caretEnd),e.setStart(g[0],this._caretStart),e.setEnd(g[0],this._caretEnd)}c.addRange(e);console.log("sel ",d," on ",a)}}});l.drawing.tools.TextBlock.setup={name:"dojox.drawing.tools.TextBlock",tooltip:"Text Tool",iconClass:"iconText"};l.drawing.register(l.drawing.tools.TextBlock.setup,"tool")})()});