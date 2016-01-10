//>>built
define("dojox/grid/enhanced/plugins/filter/FilterStatusTip",["dojo/_base/declare","dojo/_base/array","dojo/_base/lang","dojo/query","dojo/cache","dojo/string","dojo/date/locale","dijit/_Widget","dijit/_TemplatedMixin","dijit/_WidgetsInTemplateMixin","dijit/TooltipDialog","dijit/form/Button","dijit/_base/popup","dojo/i18n!../../nls/Filter"],function(h,i,f,j,k,l,r,m,n,s,o,p,g){var q=h("dojox.grid.enhanced.plugins.filter.FilterStatusPane",[m,n],{templateString:k("dojox.grid","enhanced/templates/FilterStatusPane.html")});
return h("dojox.grid.enhanced.plugins.filter.FilterStatusTip",null,{constructor:function(a){var b;b=this.plugin=a.plugin,a=b;this._statusHeader=["<table border='0' cellspacing='0' class=''><thead><tr class=''><th class=''><div>",a.nls.statusTipHeaderColumn,"</div></th><th class=' lastColumn'><div>",a.nls.statusTipHeaderCondition,"</div></th></tr></thead><tbody>"].join("");this._removedCriterias=[];this._rules=[];this.statusPane=new q;this._dlg=new o({"class":"dojoxGridFStatusTipDialog",content:this.statusPane,
autofocus:!1});this._dlg.connect(this._dlg.domNode,"onmouseleave",f.hitch(this,this.closeDialog));this._dlg.connect(this._dlg.domNode,"click",f.hitch(this,this._modifyFilter))},destroy:function(){this._dlg.destroyRecursive()},showDialog:function(a,b,d){this._pos={x:a,y:b};g.close(this._dlg);this._removedCriterias=[];this._rules=[];this._updateStatus(d);g.open({popup:this._dlg,parent:this.plugin.filterBar,onCancel:function(){},x:a-12,y:b-3})},closeDialog:function(){g.close(this._dlg);if(this._removedCriterias.length)this.plugin.filterDefDialog.removeCriteriaBoxes(this._removedCriterias),
this._removedCriterias=[],this.plugin.filterDefDialog.onFilter()},_updateStatus:function(a){var b,d=this.plugin,c=d.nls,e=this.statusPane;b=d.filterDefDialog;if(b.getCriteria()===0)e.statusTitle.innerHTML=c.statusTipTitleNoFilter,e.statusRel.innerHTML="",b=d.grid.layout.cells[a],b=l.substitute(c.statusTipMsg,[b?"'"+(b.name||b.field)+"'":c.anycolumn]);else{e.statusTitle.innerHTML=c.statusTipTitleHasFilter;e.statusRel.innerHTML=b._relOpCls=="logicall"?c.statusTipRelAll:c.statusTipRelAny;this._rules=
[];c=0;for(a=b.getCriteria(c++);a;)a.index=c-1,this._rules.push(a),a=b.getCriteria(c++);b=this._createStatusDetail()}e.statusDetailNode.innerHTML=b;this._addButtonForRules()},_createStatusDetail:function(){return this._statusHeader+i.map(this._rules,function(a,b){return this._getCriteriaStr(a,b)},this).join("")+"</tbody></table>"},_addButtonForRules:function(){this._rules.length>1&&j(".dojoxGridFStatusTipHandle",this.statusPane.statusDetailNode).forEach(f.hitch(this,function(a,b){(new p({label:this.plugin.nls.removeRuleButton,
showLabel:!1,iconClass:"dojoxGridFStatusTipDelRuleBtnIcon",onClick:f.hitch(this,function(a){a.stopPropagation();this._removedCriterias.push(this._rules[b].index);this._rules.splice(b,1);this.statusPane.statusDetailNode.innerHTML=this._createStatusDetail();this._addButtonForRules()})})).placeAt(a,"last")}))},_getCriteriaStr:function(a,b){return["<tr class=' ",b%2?"dojoxGridFStatusTipOddRow":"","'><td class=''>",a.colTxt,"</td><td class=''><div class='dojoxGridFStatusTipHandle'><span class='dojoxGridFStatusTipCondition'>",
a.condTxt,"&nbsp;</span>",a.formattedVal,"</div></td></tr>"].join("")},_modifyFilter:function(){this.closeDialog();var a=this.plugin;a.filterDefDialog.showDialog(a.filterBar.getColumnIdx(this._pos.x))}})});