//>>built
define("dojox/widget/TitleGroup",["dojo","dijit/registry","dijit/_Widget","dijit/TitlePane"],function(c,e,d,g){var d=g.prototype,f=function(){var a=this._dxfindParent&&this._dxfindParent();a&&a.selectChild(this)};d._dxfindParent=function(){var a=this.domNode.parentNode;if(a)return(a=e.getEnclosingWidget(a))&&a instanceof dojox.widget.TitleGroup&&a;return a};c.connect(d,"_onTitleClick",f);c.connect(d,"_onTitleKey",function(a){(!a||!a.type||!(a.type=="keypress"&&a.charOrCode==c.keys.TAB))&&f.apply(this,
arguments)});return c.declare("dojox.widget.TitleGroup",dijit._Widget,{"class":"dojoxTitleGroup",addChild:function(a,b){return a.placeAt(this.domNode,b)},removeChild:function(a){this.domNode.removeChild(a.domNode);return a},selectChild:function(a){a&&c.query("> .dijitTitlePane",this.domNode).forEach(function(b){(b=e.byNode(b))&&b!==a&&b.open&&b.toggle()});return a}})});