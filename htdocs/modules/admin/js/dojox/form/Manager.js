//>>built
define("dojox/form/Manager",["dijit/_Widget","dijit/_TemplatedMixin","./manager/_Mixin","./manager/_NodeMixin","./manager/_FormMixin","./manager/_ValueMixin","./manager/_EnableMixin","./manager/_DisplayMixin","./manager/_ClassMixin","dojo/_base/declare"],function(c,a,d,e,f,g,h,i,j,k){return k("dojox.form.Manager",[c,d,e,f,g,h,i,j],{buildRendering:function(){var b=this.domNode=this.srcNodeRef;if(!this.containerNode)this.containerNode=b;this.inherited(arguments);this._attachPoints=[];this._attachEvents=
[];a.prototype._attachTemplateNodes.call(this,b,function(a,b){return a.getAttribute(b)})},destroyRendering:function(){if(!this.__ctm)this.__ctm=!0,a.prototype.destroyRendering.apply(this,arguments),delete this.__ctm,this.inherited(arguments)}})});