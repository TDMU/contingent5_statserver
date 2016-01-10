/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/dnd/TimedMoveable",["../main","./Moveable"],function(c){var d=c.dnd.Moveable.prototype.onMove;c.declare("dojo.dnd.TimedMoveable",c.dnd.Moveable,{timeout:40,constructor:function(a,b){b||(b={});if(b.timeout&&typeof b.timeout=="number"&&b.timeout>=0)this.timeout=b.timeout},onMoveStop:function(a){a._timer&&(clearTimeout(a._timer),d.call(this,a,a._leftTop));c.dnd.Moveable.prototype.onMoveStop.apply(this,arguments)},onMove:function(a,b){a._leftTop=b;if(!a._timer){var c=this;a._timer=setTimeout(function(){a._timer=
null;d.call(c,a,a._leftTop)},this.timeout)}}});return c.dnd.TimedMoveable});