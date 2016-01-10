/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/dnd/Mover",["../main","../Evented","../touch","./common","./autoscroll"],function(b,h,e){b.declare("dojo.dnd.Mover",[h],{constructor:function(a,c,d){this.node=b.byId(a);this.marginBox={l:c.pageX,t:c.pageY};this.mouseButton=c.button;c=this.host=d;a=a.ownerDocument;this.events=[b.connect(a,e.move,this,"onFirstMove"),b.connect(a,e.move,this,"onMouseMove"),b.connect(a,e.release,this,"onMouseUp"),b.connect(a,"ondragstart",b.stopEvent),b.connect(a.body,"onselectstart",b.stopEvent)];if(c&&c.onMoveStart)c.onMoveStart(this)},
onMouseMove:function(a){b.dnd.autoScroll(a);var c=this.marginBox;this.host.onMove(this,{l:c.l+a.pageX,t:c.t+a.pageY},a);b.stopEvent(a)},onMouseUp:function(a){(b.isWebKit&&b.isMac&&this.mouseButton==2?a.button==0:this.mouseButton==a.button)&&this.destroy();b.stopEvent(a)},onFirstMove:function(a){var c=this.node.style,d,e=this.host;switch(c.position){case "relative":case "absolute":d=Math.round(parseFloat(c.left))||0;c=Math.round(parseFloat(c.top))||0;break;default:c.position="absolute";c=b.marginBox(this.node);
d=b.doc.body;var f=b.getComputedStyle(d),g=b._getMarginBox(d,f),f=b._getContentBox(d,f);d=c.l-(f.l-g.l);c=c.t-(f.t-g.t)}this.marginBox.l=d-this.marginBox.l;this.marginBox.t=c-this.marginBox.t;if(e&&e.onFirstMove)e.onFirstMove(this,a);b.disconnect(this.events.shift())},destroy:function(){b.forEach(this.events,b.disconnect);var a=this.host;if(a&&a.onMoveStop)a.onMoveStop(this);this.events=this.node=this.host=null}});return b.dnd.Mover});