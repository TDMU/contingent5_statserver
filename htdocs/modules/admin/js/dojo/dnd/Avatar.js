/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/dnd/Avatar",["../main","./common"],function(a){a.declare("dojo.dnd.Avatar",null,{constructor:function(a){this.manager=a;this.construct()},construct:function(){this.isA11y=a.hasClass(a.body(),"dijit_a11y");var f=a.create("table",{"class":"dojoDndAvatar",style:{position:"absolute",zIndex:"1999",margin:"0px"}}),b=this.manager.source,d,h=a.create("tbody",null,f),c=a.create("tr",null,h),g=a.create("td",null,c);this.isA11y&&a.create("span",{id:"a11yIcon",innerHTML:this.manager.copy?"+":"<"},
g);a.create("span",{innerHTML:b.generateText?this._generateText():""},g);var i=Math.min(5,this.manager.nodes.length),e=0;for(a.attr(c,{"class":"dojoDndAvatarHeader",style:{opacity:0.9}});e<i;++e)b.creator?d=b._normalizedCreator(b.getItem(this.manager.nodes[e].id).data,"avatar").node:(d=this.manager.nodes[e].cloneNode(!0),d.tagName.toLowerCase()=="tr"&&(c=a.create("table"),a.create("tbody",null,c).appendChild(d),d=c)),d.id="",c=a.create("tr",null,h),g=a.create("td",null,c),g.appendChild(d),a.attr(c,
{"class":"dojoDndAvatarItem",style:{opacity:(9-e)/10}});this.node=f},destroy:function(){a.destroy(this.node);this.node=!1},update:function(){a[(this.manager.canDropFlag?"add":"remove")+"Class"](this.node,"dojoDndAvatarCanDrop");if(this.isA11y){var f=a.byId("a11yIcon"),b="+";this.manager.canDropFlag&&!this.manager.copy?b="< ":!this.manager.canDropFlag&&!this.manager.copy?b="o":this.manager.canDropFlag||(b="x");f.innerHTML=b}a.query("tr.dojoDndAvatarHeader td span"+(this.isA11y?" span":""),this.node).forEach(function(a){a.innerHTML=
this._generateText()},this)},_generateText:function(){return this.manager.nodes.length.toString()}});return a.dnd.Avatar});