//>>built
define("dijit/form/_ListMouseMixin",["dojo/_base/declare","dojo/_base/event","dojo/touch","./_ListBase"],function(f,d,e,g){return f("dijit.form._ListMouseMixin",g,{postCreate:function(){this.inherited(arguments);this.connect(this.domNode,e.press,"_onMouseDown");this.connect(this.domNode,e.release,"_onMouseUp");this.connect(this.domNode,"onmouseover","_onMouseOver");this.connect(this.domNode,"onmouseout","_onMouseOut")},_onMouseDown:function(a){d.stop(a);if(this._hoveredNode)this.onUnhover(this._hoveredNode),
this._hoveredNode=null;this._isDragging=!0;this._setSelectedAttr(this._getTarget(a))},_onMouseUp:function(a){d.stop(a);this._isDragging=!1;var c=this._getSelectedAttr(),a=this._getTarget(a),b=this._hoveredNode;if(c&&a==c)this.onClick(c);else b&&a==b&&(this._setSelectedAttr(b),this.onClick(b))},_onMouseOut:function(){if(this._hoveredNode){this.onUnhover(this._hoveredNode);if(this._getSelectedAttr()==this._hoveredNode)this.onSelect(this._hoveredNode);this._hoveredNode=null}if(this._isDragging)this._cancelDrag=
(new Date).getTime()+1E3},_onMouseOver:function(a){if(this._cancelDrag){if((new Date).getTime()>this._cancelDrag)this._isDragging=!1;this._cancelDrag=null}if((a=this._getTarget(a))&&this._hoveredNode!=a)if(this._hoveredNode&&this._onMouseOut({target:this._hoveredNode}),a&&a.parentNode==this.containerNode)this._isDragging?this._setSelectedAttr(a):(this._hoveredNode=a,this.onHover(a))}})});