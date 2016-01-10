//>>built
require({cache:{"url:dojox/grid/resources/Expando.html":'<div class="dojoxGridExpando"\n\t><div class="dojoxGridExpandoNode" dojoAttachEvent="onclick:onToggle"\n\t\t><div class="dojoxGridExpandoNodeInner" dojoAttachPoint="expandoInner"></div\n\t></div\n></div>\n'}});
define("dojox/grid/LazyTreeGrid",["dojo/_base/kernel","dojo/_base/declare","dojo/_base/lang","dojo/_base/event","dojo/_base/array","dojo/query","dojo/parser","dojo/dom-construct","dojo/dom-class","dojo/dom-style","dojo/dom-geometry","dojo/dom","dojo/keys","dojo/text!./resources/Expando.html","dijit/_Widget","dijit/_TemplatedMixin","./TreeGrid","./_Builder","./_View","./_Layout","./cells/tree","./_RowManager","./_FocusManager","./_EditManager","./DataSelection","./util"],function(z,i,h,A,r,v,B,w,n,
C,p,D,E,q,F,G,x,H,u,I,J,K,L,M,N,O){var P=i("dojox.grid._LazyExpando",[F,G],{grid:null,view:null,rowIdx:-1,cellIdx:-1,level:0,itemId:"",templateString:q,onToggle:function(a){if(this.grid._treeCache.items[this.rowIdx]){this.grid.focus.setFocusIndex(this.rowIdx,this.cellIdx);this.setOpen(!this.grid._treeCache.items[this.rowIdx].opened);try{A.stop(a)}catch(b){}}},setOpen:function(a){var b=this.grid,c=b._by_idx[this.rowIdx].item;if(c&&b.treeModel.mayHaveChildren(c)&&!b._loading&&b._treeCache.items[this.rowIdx].opened!==
a)b._treeCache.items[this.rowIdx].opened=a,b.expandoFetch(this.rowIdx,a),this._updateOpenState(c)},_updateOpenState:function(a){var b=this.grid;a&&b.treeModel.mayHaveChildren(a)?(a=b._treeCache.items[this.rowIdx].opened,this.expandoInner.innerHTML=a?"-":"+",n.toggle(this.domNode,"dojoxGridExpandoOpened",a),this.domNode.parentNode.setAttribute("aria-expanded",a)):n.remove(this.domNode,"dojoxGridExpandoOpened")},setRowNode:function(a,b,c){if(this.cellIdx<0||!this.itemId)return!1;this.view=c;this.grid=
c.grid;this.rowIdx=a;a=this.grid.isLeftToRight()?"marginLeft":"marginRight";C.set(this.domNode.parentNode,a,this.level*1.125+"em");this._updateOpenState(this.grid._by_idx[this.rowIdx].item);return!0}}),q=i("dojox.grid._TreeGridContentBuilder",H._ContentBuilder,{generateHtml:function(a,b){var c=this.getTableArray(),d=this.grid,g=this.view.structure.cells,e=d.getItem(b),f=0,y="",k=d._treeCache.items[b]?d._treeCache.items[b].treePath:null;O.fire(this.view,"onBeforeRow",[b,g]);if(e&&h.isArray(k))f=k.length,
y=d.treeModel.mayHaveChildren(e)?"":"dojoxGridNoChildren";for(var j=0,d=0,i,l,n=0,q=[];i=g[d];d++)if(!i.hidden&&!i.header){c.push('<tr class="'+y+'">');(k=this._getColSpans(f))&&r.forEach(k,function(a){for(j=0;l=i[j];j++)j>=a.start&&j<=a.end&&(n+=this._getCellWidth(i,j));q.push(n);n=0},this);for(var m,s,t,o=0,j=0;l=i[j];j++)m=l.markup,s=l.customClasses=[],t=l.customStyles=[],k&&k[o]&&j>=k[o].start&&j<=k[o].end?j==(k[o].primary||k[o].start)?(m[5]=l.formatAtLevel(e,f,b),m[1]=s.join(" "),s=p.getMarginBox(l.getHeaderNode()).w-
p.getContentBox(l.getHeaderNode()).w,t=l.customStyles=["width:"+(q[o]-s)+"px"],m[3]=t.join(";"),c.push.apply(c,m)):j==k[o].end&&o++:(m[5]=l.formatAtLevel(e,f,b),m[1]=s.join(" "),m[3]=t.join(";"),c.push.apply(c,m));c.push("</tr>")}c.push("</table>");return c.join("")},_getColSpans:function(a){var b=this.grid.colSpans;return b&&b[a]?b[a]:null},_getCellWidth:function(a,b){var c=a[b],d=c.getHeaderNode();if(c.hidden)return 0;if(b==a.length-1||r.every(a.slice(b+1),function(a){return a.hidden}))return c=
p.position(a[b].view.headerContentNode.firstChild),c.x+c.w-p.position(d).x;else{do c=a[++b];while(c.hidden);return p.position(c.getHeaderNode()).x-p.position(d).x}}});i("dojox.grid._TreeGridView",u,{_contentBuilderClass:q,postCreate:function(){this.inherited(arguments);this._expandos={};this.connect(this.grid,"_onCleanupExpandoCache","_cleanupExpandoCache")},destroy:function(){this._cleanupExpandoCache();this.inherited(arguments)},_cleanupExpandoCache:function(a){if(a&&this._expandos[a])this._expandos[a].destroy(),
delete this._expandos[a];else{for(var b in this._expandos)this._expandos[b].destroy();this._expandos={}}},onAfterRow:function(a,b,c){v("span.dojoxGridExpando",c).forEach(function(b){if(b&&b.parentNode){var g,e,f=this.grid._by_idx;if(f&&f[a]&&f[a].idty)g=f[a].idty,e=this._expandos[g];if(e){if(w.place(e.domNode,b,"replace"),e.itemId=b.getAttribute("itemId"),e.cellIdx=parseInt(b.getAttribute("cellIdx"),10),isNaN(e.cellIdx))e.cellIdx=-1}else e=B.parse(b.parentNode)[0],g&&(this._expandos[g]=e);e.setRowNode(a,
c,this)||e.domNode.parentNode.removeChild(e.domNode);w.destroy(b)}},this);this.inherited(arguments)},updateRow:function(a){var b=this.grid,c;b.keepSelection&&(c=b.getItem(a))&&b.selection.preserver._reSelectById(c,a);this.inherited(arguments)}});var Q=h.mixin(h.clone(J),{formatAtLevel:function(a,b,c){if(!a)return this.formatIndexes(c,a,b);var d="",d="";this.isCollapsable&&this.grid.store.isItem(a)&&(d="<span "+z._scopeName+'Type="dojox.grid._LazyExpando" level="'+b+'" class="dojoxGridExpando" itemId="'+
this.grid.store.getIdentity(a)+'" cellIdx="'+this.index+'"></span>');a=this.formatIndexes(c,a,b);return d!==""?"<div>"+d+a+"</div>":a},formatIndexes:function(a,b,c){var d=this.grid.edit.info,b=this.get?this.get(a,b):this.value||this.defaultValue;return this.editable&&(this.alwaysEditing||d.rowIndex===a&&d.cell===this)?this.formatEditing(b,a):this._defaultFormat(b,[b,a,c,this])}}),u=i("dojox.grid._LazyTreeLayout",I,{setStructure:function(a){var b=a;this.grid&&!r.every(b,function(a){return!!a.cells})&&
(b=arguments[0]=[{cells:[b]}]);if(b.length===1&&b[0].cells.length===1)b[0].type="dojox.grid._TreeGridView",this._isCollapsable=!0,b[0].cells[0][this.grid.expandoCell].isCollapsable=!0;this.inherited(arguments)},addCellDef:function(){var a=this.inherited(arguments);return h.mixin(a,Q)}}),R=i("dojox.grid._LazyTreeGridCache",null,{constructor:function(){this.items=[]},getSiblingIndex:function(a,b){for(var c=a-1,d=0,g;c>=0;c--)if(g=this.items[c]?this.items[c].treePath:[],g.join("/")===b.join("/"))d++;
else if(g.length<b.length)break;return d},removeChildren:function(a){for(var b=a+1,c,d=this.items[a]?this.items[a].treePath:[];b<this.items.length;b++)if(c=this.items[b]?this.items[b].treePath:[],c.join("/")===d.join("/")||c.length<=d.length)break;b-=a+1;this.items.splice(a+1,b);return b}}),i=i("dojox.grid.LazyTreeGrid",x,{_layoutClass:u,_size:0,treeModel:null,defaultState:null,colSpans:null,postCreate:function(){this._setState();this.inherited(arguments);if(!this._treeCache)this._treeCache=new R;
if(!this.treeModel||!(this.treeModel instanceof dijit.tree.ForestStoreModel))throw Error("dojox.grid.LazyTreeGrid: must be used with a treeModel which is an instance of dijit.tree.ForestStoreModel");n.add(this.domNode,"dojoxGridTreeModel");D.setSelectable(this.domNode,this.selectable)},createManagers:function(){this.rows=new K(this);this.focus=new L(this);this.edit=new M(this)},createSelection:function(){this.selection=new N(this)},setModel:function(a){a&&(this._setModel(a),this._cleanup(),this._refresh(!0))},
setStore:function(a,b,c){if(a)this._setQuery(b,c),this.treeModel.query=b,this.treeModel.store=a,this.treeModel.root.children=[],this.setModel(this.treeModel)},onSetState:function(){},_setState:function(){if(this.defaultState)this._treeCache=this.defaultState.cache,this.sortInfo=this.defaultState.sortInfo||0,this.query=this.defaultState.query||this.query,this._lastScrollTop=this.defaultState.scrollTop,this.keepSelection?this.selection.preserver._selectedById=this.defaultState.selection:this.selection.selected=
this.defaultState.selection||[],this.onSetState()},getState:function(){var a=this.keepSelection?this.selection.preserver._selectedById:this.selection.selected;return{cache:h.clone(this._treeCache),query:h.clone(this.query),sortInfo:h.clone(this.sortInfo),scrollTop:h.clone(this.scrollTop),selection:h.clone(a)}},_setQuery:function(a){this.inherited(arguments);this.treeModel.query=a},filter:function(){this._cleanup();this.inherited(arguments)},destroy:function(){this._cleanup();this.inherited(arguments)},
expand:function(a){this._fold(a,!0)},collapse:function(a){this._fold(a,!1)},refresh:function(a){a||this._cleanup();this._refresh(!0)},_cleanup:function(){this._treeCache.items=[];this._onCleanupExpandoCache()},setSortIndex:function(a){this.canSort(a+1)&&this._cleanup();this.inherited(arguments)},_refresh:function(){this._clearData();this.updateRowCount(this._size);this._fetch(0,!0)},render:function(){this.inherited(arguments);this.setScrollTop(this.scrollTop)},_onNew:function(a,b){var c=this._treeCache.items,
d=this._by_idx;if(b&&this.store.isItem(b.item)&&r.some(this.treeModel.childrenAttrs,function(a){return a===b.attribute})){for(var g=this.store.getIdentity(b.item),e=-1,f=0;f<d.length;f++)if(g===d[f].idty){e=f;break}if(e>=0)if(c[e]&&c[e].opened){f=c[e].treePath;for(e+=1;e<c.length;e++)if(c[e].treePath.length<=f.length)break;c=f.slice();c.push(g);this._treeCache.items.splice(e,0,{opened:!1,treePath:c});g=this.store.getIdentity(a);this._by_idty[g]={idty:g,item:a};d.splice(e,0,this._by_idty[g]);this._size+=
1;this.updateRowCount(this._size);this._updateRenderedRows(e)}else this.updateRow(e)}else c.push({opened:!1,treePath:[]}),this._size+=1,this.inherited(arguments)},_onDelete:function(a){for(var b=0,c=-1,a=this.store.getIdentity(a);b<this._by_idx.length;b++)if(a===this._by_idx[b].idty){c=b;break}if(c>=0){for(var d=this._treeCache.items,g=d[c]?d[c].treePath:[],e=1,b=c+1;b<this._size;b++,e++)if(d[b].treePath.length<=g.length)break;d.splice(c,e);this._onCleanupExpandoCache(a);this._by_idx.splice(c,e);
this._size-=e;this.updateRowCount(this._size);this._updateRenderedRows(c)}},_onCleanupExpandoCache:function(){},_fetch:function(a){if(!this._loading)this._loading=!0;var a=a||0,b=this._size-a>0?Math.min(this.rowsPerPage,this._size-a):this.rowsPerPage,c=0,d=[];for(this._reqQueueLen=0;c<b;c++)if(this._by_idx[a+c])d.push(this._by_idx[a+c].item);else break;if(d.length===b)this._reqQueueLen=1,this._onFetchBegin(this._size,{startRowIdx:a,count:b}),this._onFetchComplete(d,{startRowIdx:a,count:b});else{for(var g,
e=1,f=this._treeCache.items,h=f[a]?f[a].treePath:[],c=1;c<b;c++)d=f[a+e-1]?f[a+e-1].treePath.length:0,g=f[a+e]?f[a+e].treePath.length:0,d!==g?(this._reqQueueLen++,this._fetchItems({startRowIdx:a,count:e,treePath:h}),a+=e,e=1,h=f[a]?f[a].treePath:0):e++;this._reqQueueLen++;this._fetchItems({startRowIdx:a,count:e,treePath:h})}},_fetchItems:function(a){if(!this._pending_requests[a.startRowIdx]){this.showMessage(this.loadingMessage);this._pending_requests[a.startRowIdx]=!0;var b=h.hitch(this,"_onFetchError"),
c=this._treeCache.getSiblingIndex(a.startRowIdx,a.treePath);if(a.treePath.length===0)this.store.fetch({start:c,startRowIdx:a.startRowIdx,treePath:a.treePath,count:a.count,query:this.query,sort:this.getSortProps(),queryOptions:this.queryOptions,onBegin:h.hitch(this,"_onFetchBegin"),onComplete:h.hitch(this,"_onFetchComplete"),onError:h.hitch(this,"_onFetchError")});else{var d=a.treePath[a.treePath.length-1],g={start:c,startRowIdx:a.startRowIdx,treePath:a.treePath,count:a.count,parentId:d,sort:this.getSortProps()},
e=this,f=function(){var a=h.hitch(e,"_onFetchComplete");arguments.length==1?a.apply(e,[arguments[0],g]):a.apply(e,arguments)};this._by_idty[d]?(a=this._by_idty[d].item,this.treeModel.getChildren(a,f,b,g)):this.store.fetchItemByIdentity({identity:d,onItem:function(a){e.treeModel.getChildren(a,f,b,g)},onError:b})}}},_onFetchBegin:function(a){if(this._treeCache.items.length===0)this._size=parseInt(a,10);a=this._size;this.inherited(arguments)},_onFetchComplete:function(a,b){var c=b.startRowIdx,d=b.count,
g=a.length<=d?0:b.start,e=b.treePath||[];if(h.isArray(a)&&a.length>0){for(var f=0,d=Math.min(d,a.length);f<d;f++)this._treeCache.items[c+f]||(this._treeCache.items[c+f]={opened:!1,treePath:e}),this._by_idx[c+f]||this._addItem(a[g+f],c+f,!0);this.updateRows(c,d)}this._size==0?this.showMessage(this.noDataMessage):this.showMessage();this._pending_requests[c]=!1;this._reqQueueLen--;if(this._loading&&this._reqQueueLen===0)this._loading=!1,this._lastScrollTop&&this.setScrollTop(this._lastScrollTop)},expandoFetch:function(a,
b){if(!this._loading&&this._by_idx[a]){this._loading=!0;this._toggleLoadingClass(a,!0);this.expandoRowIndex=a;var c=this._by_idx[a].item;if(b){var d={start:0,count:this.rowsPerPage,parentId:this.store.getIdentity(this._by_idx[a].item),sort:this.getSortProps()};this.treeModel.getChildren(c,h.hitch(this,"_onExpandoComplete"),h.hitch(this,"_onFetchError"),d)}else{c=this._treeCache.removeChildren(a);this._by_idx.splice(a+1,c);this._bop=this._eop=-1;this._size-=c;this.updateRowCount(this._size);this._updateRenderedRows(a+
1);this._toggleLoadingClass(a,!1);if(this._loading)this._loading=!1;this.focus._delayedCellFocus()}}},_onExpandoComplete:function(a,b,c){var c=isNaN(c)?a.length:parseInt(c,10),d=this._treeCache.items[this.expandoRowIndex].treePath.slice(0);d.push(this.store.getIdentity(this._by_idx[this.expandoRowIndex].item));for(b=1;b<=c;b++)this._treeCache.items.splice(this.expandoRowIndex+b,0,{treePath:d,opened:!1});this._size+=c;this.updateRowCount(this._size);for(b=0;b<c;b++)a[b]?(d=this.store.getIdentity(a[b]),
this._by_idty[d]={idty:d,item:a[b]},this._by_idx.splice(this.expandoRowIndex+1+b,0,this._by_idty[d])):this._by_idx.splice(this.expandoRowIndex+1+b,0,null);this._updateRenderedRows(this.expandoRowIndex+1);this._toggleLoadingClass(this.expandoRowIndex,!1);this.stateChangeNode=null;if(this._loading)this._loading=!1;this.autoHeight===!0&&this._resize();this.focus._delayedCellFocus()},styleRowNode:function(a,b){b&&this.rows.styleRowNode(a,b)},onStyleRow:function(a){this.layout._isCollapsable?(a.customClasses=
(a.odd?" dojoxGridRowOdd":"")+(a.selected?" dojoxGridRowSelected":"")+(a.over?" dojoxGridRowOver":""),this.focus.styleRow(a),this.edit.styleRow(a)):this.inherited(arguments)},onKeyDown:function(a){if(!a.altKey&&!a.metaKey){var b=dijit.findWidgets(a.target)[0];if(a.keyCode===E.ENTER&&b instanceof P)b.onToggle();this.inherited(arguments)}},_toggleLoadingClass:function(a,b){var c=this.views.views;if(c=c[c.length-1].getRowNode(a))(c=v(".dojoxGridExpando",c)[0])&&n.toggle(c,"dojoxGridExpandoLoading",b)},
_updateRenderedRows:function(a){r.forEach(this.scroller.stack,function(b){b*this.rowsPerPage>=a?this.updateRows(b*this.rowsPerPage,this.rowsPerPage):(b+1)*this.rowsPerPage>=a&&this.updateRows(a,(b+1)*this.rowsPerPage-a+1)},this)},_fold:function(a,b){var c=-1,d=0,g=this._by_idx,e=this._by_idty[a];if(e&&e.item&&this.treeModel.mayHaveChildren(e.item)){for(;d<g.length;d++)if(g[d]&&g[d].idty===a){c=d;break}if(c>=0&&(c=this.views.views[this.views.views.length-1].getRowNode(c)))(c=dijit.findWidgets(c)[0])&&
c.setOpen(b)}}});i.markupFactory=function(a,b,c,d){return x.markupFactory(a,b,c,d)};return i});