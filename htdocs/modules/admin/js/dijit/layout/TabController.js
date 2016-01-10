//>>built
require({cache:{"url:dijit/layout/templates/_TabButton.html":"<div role=\"presentation\" data-dojo-attach-point=\"titleNode\" data-dojo-attach-event='onclick:onClick'>\n    <div role=\"presentation\" class='dijitTabInnerDiv' data-dojo-attach-point='innerDiv'>\n        <div role=\"presentation\" class='dijitTabContent' data-dojo-attach-point='tabContent'>\n        \t<div role=\"presentation\" data-dojo-attach-point='focusNode'>\n\t\t        <img src=\"${_blankGif}\" alt=\"\" class=\"dijitIcon dijitTabButtonIcon\" data-dojo-attach-point='iconNode' />\n\t\t        <span data-dojo-attach-point='containerNode' class='tabLabel'></span>\n\t\t        <span class=\"dijitInline dijitTabCloseButton dijitTabCloseIcon\" data-dojo-attach-point='closeNode'\n\t\t        \t\tdata-dojo-attach-event='onclick: onClickCloseButton' role=\"presentation\">\n\t\t            <span data-dojo-attach-point='closeText' class='dijitTabCloseText'>[x]</span\n\t\t        ></span>\n\t\t\t</div>\n        </div>\n    </div>\n</div>\n"}});
define("dijit/layout/TabController",["dojo/_base/declare","dojo/dom","dojo/dom-attr","dojo/dom-class","dojo/i18n","dojo/_base/lang","./StackController","../Menu","../MenuItem","dojo/text!./templates/_TabButton.html","dojo/i18n!../nls/common"],function(b,f,g,h,i,d,e,j,k,c){c=b("dijit.layout._TabButton",e.StackButton,{baseClass:"dijitTab",cssStateNodes:{closeNode:"dijitTabCloseButton"},templateString:c,scrollOnFocus:!1,buildRendering:function(){this.inherited(arguments);f.setSelectable(this.containerNode,
!1)},startup:function(){this.inherited(arguments);var a=this.domNode;setTimeout(function(){a.className=a.className},1)},_setCloseButtonAttr:function(a){this._set("closeButton",a);h.toggle(this.innerDiv,"dijitClosable",a);this.closeNode.style.display=a?"":"none";a?(a=i.getLocalization("dijit","common"),this.closeNode&&g.set(this.closeNode,"title",a.itemClose),this._closeMenu=new j({id:this.id+"_Menu",dir:this.dir,lang:this.lang,textDir:this.textDir,targetNodeIds:[this.domNode]}),this._closeMenu.addChild(new k({label:a.itemClose,
dir:this.dir,lang:this.lang,textDir:this.textDir,onClick:d.hitch(this,"onClickCloseButton")}))):this._closeMenu&&(this._closeMenu.destroyRecursive(),delete this._closeMenu)},_setLabelAttr:function(){this.inherited(arguments);if(!this.showLabel&&!this.params.title)this.iconNode.alt=d.trim(this.containerNode.innerText||this.containerNode.textContent||"")},destroy:function(){this._closeMenu&&(this._closeMenu.destroyRecursive(),delete this._closeMenu);this.inherited(arguments)}});b=b("dijit.layout.TabController",
e,{baseClass:"dijitTabController",templateString:"<div role='tablist' data-dojo-attach-event='onkeypress:onkeypress'></div>",tabPosition:"top",buttonWidget:c,_rectifyRtlTabList:function(){if(!(0>=this.tabPosition.indexOf("-h"))&&this.pane2button){var a=0,b;for(b in this.pane2button)a=Math.max(a,this.pane2button[b].innerDiv.scrollWidth);for(b in this.pane2button)this.pane2button[b].innerDiv.style.width=a+"px"}}});b.TabButton=c;return b});