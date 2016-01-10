/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/Evented",["./aspect","./on"],function(d,b){function c(){}var e=d.after;c.prototype={on:function(a,c){return b.parse(this,a,c,function(a,b){return e(a,"on"+b,c,!0)})},emit:function(){var a=[this];a.push.apply(a,arguments);return b.emit.apply(b,a)}};return c});