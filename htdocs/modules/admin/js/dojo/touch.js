/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/touch",["./_base/kernel","./on","./has","./mouse"],function(c,d,a,e){function b(a){return function(b,c){return d(b,a,c)}}a=a("touch");c.touch={press:b(a?"touchstart":"mousedown"),move:b(a?"touchmove":"mousemove"),release:b(a?"touchend":"mouseup"),cancel:a?b("touchcancel"):e.leave};return c.touch});