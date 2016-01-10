//>>built
require({cache:{"url:dojox/layout/resources/GridContainer.html":'<div id="${id}" class="gridContainer" dojoAttachPoint="containerNode" tabIndex="0" dojoAttachEvent="onkeypress:_selectFocus">\n\t<div dojoAttachPoint="gridContainerDiv">\n\t\t<table class="gridContainerTable" dojoAttachPoint="gridContainerTable" cellspacing="0" cellpadding="0">\n\t\t\t<tbody>\n\t\t\t\t<tr dojoAttachPoint="gridNode" >\n\t\t\t\t\t\n\t\t\t\t</tr>\n\t\t\t</tbody>\n\t\t</table>\n\t</div>\n</div>'}});
define("dojox/layout/GridContainerLite",["dojo/_base/kernel","dojo/text!./resources/GridContainer.html","dojo/ready","dojo/_base/array","dojo/_base/lang","dojo/_base/declare","dojo/text","dojo/_base/sniff","dojo/_base/html","dojox/mdnd/AreaManager","dojox/mdnd/DropIndicator","dojox/mdnd/dropMode/OverDropMode","dojox/mdnd/AutoScroll","dijit/_Templated","dijit/layout/_LayoutWidget","dijit/focus","dijit/_base/focus"],function(c,l){var m=c.declare("dojox.layout.GridContainerLite",[dijit.layout._LayoutWidget,
dijit._TemplatedMixin],{autoRefresh:!0,templateString:l,dragHandleClass:"dojoxDragHandle",nbZones:1,doLayout:!0,isAutoOrganized:!0,acceptTypes:[],colWidths:"",constructor:function(a){this.acceptTypes=(a||{}).acceptTypes||["text"];this._disabled=!0},postCreate:function(){this.inherited(arguments);this._grid=[];this._createCells();this.subscribe("/dojox/mdnd/drop","resizeChildAfterDrop");this.subscribe("/dojox/mdnd/drag/start","resizeChildAfterDragStart");this._dragManager=dojox.mdnd.areaManager();
this._dragManager.autoRefresh=this.autoRefresh;this._dragManager.dragHandleClass=this.dragHandleClass;this.doLayout?this._border={h:c.isIE?c._getBorderExtents(this.gridContainerTable).h:0,w:c.isIE==6?1:0}:(c.style(this.domNode,"overflowY","hidden"),c.style(this.gridContainerTable,"height","auto"));this.inherited(arguments)},startup:function(){this._started||(this.isAutoOrganized?this._organizeChildren():this._organizeChildrenManually(),c.forEach(this.getChildren(),function(a){a.startup()}),this._isShown()&&
this.enableDnd(),this.inherited(arguments))},resizeChildAfterDrop:function(a,b){if(this._disabled)return!1;if(dijit.getEnclosingWidget(b.node)==this){var d=dijit.byNode(a);d.resize&&c.isFunction(d.resize)&&d.resize();d.set("column",a.parentNode.cellIndex);if(this.doLayout)d=this._contentBox.h,c.contentBox(this.gridContainerDiv).h>=d&&c.style(this.gridContainerTable,"height",d-this._border.h+"px");return!0}return!1},resizeChildAfterDragStart:function(a,b){if(this._disabled)return!1;if(dijit.getEnclosingWidget(b.node)==
this)return this._draggedNode=a,this.doLayout&&c.marginBox(this.gridContainerTable,{h:c.contentBox(this.gridContainerDiv).h-this._border.h}),!0;return!1},getChildren:function(){var a=[];c.forEach(this._grid,function(b){a=a.concat(c.query("> [widgetId]",b.node).map(dijit.byNode))});return a},_isShown:function(){if("open"in this)return this.open;else{var a=this.domNode;return a.style.display!="none"&&a.style.visibility!="hidden"&&!c.hasClass(a,"dijitHidden")}},layout:function(){if(this.doLayout){var a=
this._contentBox;c.marginBox(this.gridContainerTable,{h:a.h-this._border.h});c.contentBox(this.domNode,{w:a.w-this._border.w})}c.forEach(this.getChildren(),function(a){a.resize&&c.isFunction(a.resize)&&a.resize()})},onShow:function(){this._disabled&&this.enableDnd()},onHide:function(){this._disabled||this.disableDnd()},_createCells:function(){if(this.nbZones===0)this.nbZones=1;for(var a=this.acceptTypes.join(","),b=0,d=this.colWidths||[],e=[],f,i=0,b=0;b<this.nbZones;b++)e.length<d.length?(i+=d[b],
e.push(d[b])):(f||(f=(100-i)/(this.nbZones-b)),e.push(f));for(b=0;b<this.nbZones;)this._grid.push({node:c.create("td",{"class":"gridContainerZone",accept:a,id:this.id+"_dz"+b,style:{width:e[b]+"%"}},this.gridNode)}),b++},_getZonesAttr:function(){return c.query(".gridContainerZone",this.containerNode)},enableDnd:function(){var a=this._dragManager;c.forEach(this._grid,function(b){a.registerByNode(b.node)});a._dropMode.updateAreas(a._areaList);this._disabled=!1},disableDnd:function(){var a=this._dragManager;
c.forEach(this._grid,function(b){a.unregister(b.node)});a._dropMode.updateAreas(a._areaList);this._disabled=!0},_organizeChildren:function(){for(var a=dojox.layout.GridContainerLite.superclass.getChildren.call(this),b=this.nbZones,d=Math.floor(a.length/b),e=a.length%b,f=0,c=0;c<b;c++){for(var g=0;g<d;g++)this._insertChild(a[f],c),f++;if(e>0){try{this._insertChild(a[f],c),f++}catch(h){console.error("Unable to insert child in GridContainer",h)}e--}else if(d===0)break}},_organizeChildrenManually:function(){for(var a=
dojox.layout.GridContainerLite.superclass.getChildren.call(this),b=a.length,d,e=0;e<b;e++){d=a[e];try{this._insertChild(d,d.column-1)}catch(f){console.error("Unable to insert child in GridContainer",f)}}},_insertChild:function(a,b,d){var e=this._grid[b].node,f=e.childNodes.length;if(typeof d==void 0||d>f)d=f;this._disabled?(c.place(a.domNode,e,d),c.attr(a.domNode,"tabIndex","0")):a.dragRestriction?(c.place(a.domNode,e,d),c.attr(a.domNode,"tabIndex","0")):this._dragManager.addDragItem(e,a.domNode,
d,!0);a.set("column",b);return a},removeChild:function(a){this._disabled?this.inherited(arguments):this._dragManager.removeDragItem(a.domNode.parentNode,a.domNode)},addService:function(a,b,d){c.deprecated("addService is deprecated.","Please use  instead.","Future");this.addChild(a,b,d)},addChild:function(a,b,d){a.domNode.id=a.id;dojox.layout.GridContainerLite.superclass.addChild.call(this,a,0);if(b<0||b==void 0)b=0;d<=0&&(d=0);try{return this._insertChild(a,b,d)}catch(e){console.error("Unable to insert child in GridContainer",
e)}return null},_setColWidthsAttr:function(a){this.colWidths=c.isString(a)?a.split(","):c.isArray(a)?a:[a];this._started&&this._updateColumnsWidth()},_updateColumnsWidth:function(){var a=this._grid.length,b=this.colWidths||[],d=[],e,f=0,c;for(c=0;c<a;c++)d.length<b.length?(f+=b[c]*1,d.push(b[c])):(e||(e=(100-f)/(this.nbZones-c),e<0&&(e=100/this.nbZones)),d.push(e),f+=e*1);if(f>100){b=100/f;for(c=0;c<d.length;c++)d[c]*=b}for(c=0;c<a;c++)this._grid[c].node.style.width=d[c]+"%"},_selectFocus:function(a){if(!this._disabled){var b=
a.keyCode,d=c.keys,e=null,f=dijit.getFocus().node,i=this._dragManager,g,h;if(f==this.containerNode)switch(f=this.gridNode.childNodes,b){case d.DOWN_ARROW:case d.RIGHT_ARROW:d=!1;for(g=0;g<f.length;g++){b=f[g].childNodes;for(h=0;h<b.length;h++)if(e=b[h],e!=null&&e.style.display!="none"){dijit.focus(e);c.stopEvent(a);d=!0;break}if(d)break}break;case d.UP_ARROW:case d.LEFT_ARROW:f=this.gridNode.childNodes;d=!1;for(g=f.length-1;g>=0;g--){b=f[g].childNodes;for(h=b.length;h>=0;h--)if(e=b[h],e!=null&&e.style.display!=
"none"){dijit.focus(e);c.stopEvent(a);d=!0;break}if(d)break}}else if(f.parentNode.parentNode==this.gridNode){var j=b==d.UP_ARROW||b==d.LEFT_ARROW?"lastChild":"firstChild";h=b==d.UP_ARROW||b==d.LEFT_ARROW?"previousSibling":"nextSibling";switch(b){case d.UP_ARROW:case d.DOWN_ARROW:c.stopEvent(a);for(var d=!1,k=f;!d;){b=k.parentNode.childNodes;for(g=e=0;g<b.length;g++)if(b[g].style.display!="none"&&e++,e>1)break;if(e==1)return;e=k[h]==null?k.parentNode[j]:k[h];e.style.display==="none"?k=e:d=!0}if(a.shiftKey){d=
f.parentNode;for(g=0;g<this.gridNode.childNodes.length;g++)if(d==this.gridNode.childNodes[g])break;b=this.gridNode.childNodes[g].childNodes;for(h=0;h<b.length;h++)if(e==b[h])break;(c.isMoz||c.isWebKit)&&g--;e=dijit.byNode(f);e.dragRestriction?c.publish("/dojox/layout/gridContainer/moveRestriction",[this]):(i.removeDragItem(d,f),this.addChild(e,g,h),c.attr(f,"tabIndex","0"),dijit.focus(f))}else dijit.focus(e);break;case d.RIGHT_ARROW:case d.LEFT_ARROW:if(c.stopEvent(a),a.shiftKey){a=0;if(f.parentNode[h]==
null)c.isIE&&b==d.LEFT_ARROW&&(a=this.gridNode.childNodes.length-1);else if(f.parentNode[h].nodeType==3)a=this.gridNode.childNodes.length-2;else{for(g=0;g<this.gridNode.childNodes.length;g++){if(f.parentNode[h]==this.gridNode.childNodes[g])break;a++}(c.isMoz||c.isWebKit)&&a--}e=dijit.byNode(f);j=f.getAttribute("dndtype");j=j==null?e&&e.dndType?e.dndType.split(/\s*,\s*/):["text"]:j.split(/\s*,\s*/);k=!1;for(g=0;g<this.acceptTypes.length;g++)for(h=0;h<j.length;h++)if(j[h]==this.acceptTypes[g]){k=!0;
break}if(k&&!e.dragRestriction){g=f.parentNode;h=0;if(d.LEFT_ARROW==b){b=a;if(c.isMoz||c.isWebKit)b=a+1;h=this.gridNode.childNodes[b].childNodes.length}f=i.removeDragItem(g,f);this.addChild(e,a,h);c.attr(f,"tabIndex","0");dijit.focus(f)}else c.publish("/dojox/layout/gridContainer/moveRestriction",[this])}else{for(f=f.parentNode;e===null;)if(f=f[h]!==null&&f[h].nodeType!==3?f[h]:h==="previousSibling"?f.parentNode.childNodes[f.parentNode.childNodes.length-1]:c.isIE?f.parentNode.childNodes[0]:f.parentNode.childNodes[1],
(e=f[j])&&e.style.display=="none"){b=e.parentNode.childNodes;i=null;if(h=="previousSibling")for(g=b.length-1;g>=0;g--){if(b[g].style.display!="none"){i=b[g];break}}else for(g=0;g<b.length;g++)if(b[g].style.display!="none"){i=b[g];break}i?e=i:(f=e,f=f.parentNode,e=null)}dijit.focus(e)}}}}},destroy:function(){var a=this._dragManager;c.forEach(this._grid,function(b){a.unregister(b.node)});this.inherited(arguments)}});c.extend(dijit._Widget,{column:"1",dragRestriction:!1});return m});