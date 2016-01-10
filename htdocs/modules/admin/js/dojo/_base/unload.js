/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/_base/unload",["./kernel","./connect"],function(a,b){var c=window;a.addOnWindowUnload=function(e,d){if(!a.windowUnloaded)b.connect(c,"unload",a.windowUnloaded=function(){});b.connect(c,"unload",e,d)};a.addOnUnload=function(a,d){b.connect(c,"beforeunload",a,d)};return{addOnWindowUnload:a.addOnWindowUnload,addOnUnload:a.addOnUnload}});