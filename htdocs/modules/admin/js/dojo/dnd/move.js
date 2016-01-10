/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/dnd/move",["../main","./Mover","./Moveable"],function(b){b.declare("dojo.dnd.move.constrainedMoveable",b.dnd.Moveable,{constraints:function(){},within:!1,constructor:function(b,a){a||(a={});this.constraints=a.constraints;this.within=a.within},onFirstMove:function(f){var a=this.constraintBox=this.constraints.call(this,f);a.r=a.l+a.w;a.b=a.t+a.h;this.within&&(f=b._getMarginSize(f.node),a.r-=f.w,a.b-=f.h)},onMove:function(b,a){var d=this.constraintBox,g=b.node.style;this.onMoving(b,a);a.l=
a.l<d.l?d.l:d.r<a.l?d.r:a.l;a.t=a.t<d.t?d.t:d.b<a.t?d.b:a.t;g.left=a.l+"px";g.top=a.t+"px";this.onMoved(b,a)}});b.declare("dojo.dnd.move.boxConstrainedMoveable",b.dnd.move.constrainedMoveable,{box:{},constructor:function(b,a){var d=a&&a.box;this.constraints=function(){return d}}});b.declare("dojo.dnd.move.parentConstrainedMoveable",b.dnd.move.constrainedMoveable,{area:"content",constructor:function(f,a){var d=a&&a.area;this.constraints=function(){var a=this.node.parentNode,f=b.getComputedStyle(a),
c=b._getMarginBox(a,f);if(d=="margin")return c;var e=b._getMarginExtents(a,f);c.l+=e.l;c.t+=e.t;c.w-=e.w;c.h-=e.h;if(d=="border")return c;e=b._getBorderExtents(a,f);c.l+=e.l;c.t+=e.t;c.w-=e.w;c.h-=e.h;if(d=="padding")return c;e=b._getPadExtents(a,f);c.l+=e.l;c.t+=e.t;c.w-=e.w;c.h-=e.h;return c}}});b.dnd.constrainedMover=b.dnd.move.constrainedMover;b.dnd.boxConstrainedMover=b.dnd.move.boxConstrainedMover;b.dnd.parentConstrainedMover=b.dnd.move.parentConstrainedMover;return b.dnd.move});