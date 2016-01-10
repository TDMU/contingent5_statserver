//>>built
define("dojox/editor/plugins/EntityPalette",["dojo","dijit","dojox","dijit/_Widget","dijit/_TemplatedMixin","dijit/_PaletteMixin","dojo/_base/connect","dojo/_base/declare","dojo/i18n","dojo/i18n!dojox/editor/plugins/nls/latinEntities"],function(b,d,h){b.experimental("dojox.editor.plugins.EntityPalette");b.declare("dojox.editor.plugins.EntityPalette",[d._Widget,d._TemplatedMixin,d._PaletteMixin],{templateString:'<div class="dojoxEntityPalette">\n\t<table>\n\t\t<tbody>\n\t\t\t<tr>\n\t\t\t\t<td>\n\t\t\t\t\t<table class="dijitPaletteTable">\n\t\t\t\t\t\t<tbody dojoAttachPoint="gridNode"></tbody>\n\t\t\t\t   </table>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t<tr>\n\t\t\t\t<td>\n\t\t\t\t\t<table dojoAttachPoint="previewPane" class="dojoxEntityPalettePreviewTable">\n\t\t\t\t\t\t<tbody>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<th class="dojoxEntityPalettePreviewHeader">Preview</th>\n\t\t\t\t\t\t\t\t<th class="dojoxEntityPalettePreviewHeader" dojoAttachPoint="codeHeader">Code</th>\n\t\t\t\t\t\t\t\t<th class="dojoxEntityPalettePreviewHeader" dojoAttachPoint="entityHeader">Name</th>\n\t\t\t\t\t\t\t\t<th class="dojoxEntityPalettePreviewHeader">Description</th>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td class="dojoxEntityPalettePreviewDetailEntity" dojoAttachPoint="previewNode"></td>\n\t\t\t\t\t\t\t\t<td class="dojoxEntityPalettePreviewDetail" dojoAttachPoint="codeNode"></td>\n\t\t\t\t\t\t\t\t<td class="dojoxEntityPalettePreviewDetail" dojoAttachPoint="entityNode"></td>\n\t\t\t\t\t\t\t\t<td class="dojoxEntityPalettePreviewDetail" dojoAttachPoint="descNode"></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</tbody>\n\t\t\t\t\t</table>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t</tbody>\n\t</table>\n</div>',
baseClass:"dojoxEntityPalette",showPreview:!0,showCode:!1,showEntityName:!1,palette:"latin",dyeClass:"dojox.editor.plugins.LatinEntity",paletteClass:"editorLatinEntityPalette",cellClass:"dojoxEntityPaletteCell",postMixInProperties:function(){var a=b.i18n.getLocalization("dojox.editor.plugins","latinEntities"),c=0,d;for(d in a)c++;var c=Math.floor(Math.sqrt(c)),g=0,f=[],e=[];for(d in a)g++,e.push(d),g%c===0&&(f.push(e),e=[]);e.length>0&&f.push(e);this._palette=f},buildRendering:function(){this.inherited(arguments);
this._preparePalette(this._palette,b.i18n.getLocalization("dojox.editor.plugins","latinEntities"));var a=b.query(".dojoxEntityPaletteCell",this.gridNode);b.forEach(a,function(a){this.connect(a,"onmouseenter","_onCellMouseEnter")},this)},_onCellMouseEnter:function(a){this._displayDetails(a.target)},postCreate:function(){this.inherited(arguments);b.style(this.codeHeader,"display",this.showCode?"":"none");b.style(this.codeNode,"display",this.showCode?"":"none");b.style(this.entityHeader,"display",this.showEntityName?
"":"none");b.style(this.entityNode,"display",this.showEntityName?"":"none");this.showPreview||b.style(this.previewNode,"display","none")},_setCurrent:function(a){this.inherited(arguments);this.showPreview&&this._displayDetails(a)},_displayDetails:function(a){var c=this._getDye(a);c?(a=c.getValue(),c=c._alias,this.previewNode.innerHTML=a,this.codeNode.innerHTML="&amp;#"+parseInt(a.charCodeAt(0),10)+";",this.entityNode.innerHTML="&amp;"+c+";",this.descNode.innerHTML=b.i18n.getLocalization("dojox.editor.plugins",
"latinEntities")[c].replace("\n","<br>")):(this.previewNode.innerHTML="",this.codeNode.innerHTML="",this.entityNode.innerHTML="",this.descNode.innerHTML="")}});b.declare("dojox.editor.plugins.LatinEntity",null,{constructor:function(a){this._alias=a},getValue:function(){return"&"+this._alias+";"},fillCell:function(a){a.innerHTML=this.getValue()}});return h.editor.plugins.EntityPalette});