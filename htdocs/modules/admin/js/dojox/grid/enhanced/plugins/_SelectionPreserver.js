//>>built
define("dojox/grid/enhanced/plugins/_SelectionPreserver",["dojo/_base/declare","dojo/_base/lang","dojo/_base/connect","../../_SelectionPreserver"],function(l,g,h,m){return l("dojox.grid.enhanced.plugins._SelectionPreserver",m,{constructor:function(d){var a=this.grid;a.onSelectedById=this.onSelectedById;this._oldClearData=a._clearData;var b=this;a._clearData=function(){b._updateMapping(!a._noInternalMapping);b._trustSelection=[];b._oldClearData.apply(a,arguments)};this._connects.push(h.connect(d,"selectRange",
g.hitch(this,"_updateMapping",!0,!0,!1)),h.connect(d,"deselectRange",g.hitch(this,"_updateMapping",!0,!1,!1)),h.connect(d,"deselectAll",g.hitch(this,"_updateMapping",!0,!1,!0)))},destroy:function(){this.inherited(arguments);this.grid._clearData=this._oldClearData},reset:function(){this.inherited(arguments);this._idMap=[];this._trustSelection=[];this._defaultSelected=!1},_reSelectById:function(d,a){var b=this.selection,i=this.grid;if(d&&i._hasIdentity){var e=i.store.getIdentity(d);if(this._selectedById[e]===
void 0){if(!this._trustSelection[a])b.selected[a]=this._defaultSelected}else b.selected[a]=this._selectedById[e];this._idMap.push(e);i.onSelectedById(e,a,b.selected[a])}},_selectById:function(d,a){this.inherited(arguments)||(this._trustSelection[a]=!0)},onSelectedById:function(){},_updateMapping:function(d,a,b,i,e){var g=this.selection,f=this.grid,h=0,k=0,c,j;for(c=f.rowCount-1;c>=0;--c)if(f._by_idx[c]){if((j=f._by_idx[c].idty)&&(d||this._selectedById[j]===void 0))this._selectedById[j]=!!g.selected[c]}else++k,
h+=g.selected[c]?1:-1;if(k)this._defaultSelected=h>0;!b&&i!==void 0&&e!==void 0&&(b=!f.usingPagination&&Math.abs(e-i+1)===f.rowCount);if(b&&(!f.usingPagination||f.selectionMode==="single"))for(c=this._idMap.length-1;c>=0;--c)this._selectedById[this._idMap[c]]=a}})});