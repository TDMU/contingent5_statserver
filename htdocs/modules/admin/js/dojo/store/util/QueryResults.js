/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/store/util/QueryResults",["../../_base/array","../../_base/lang","../../_base/Deferred"],function(e,f,g){var c=f.getObject("dojo.store.util",!0);c.QueryResults=function(a){function b(d){a[d]||(a[d]=function(){var b=arguments;return g.when(a,function(a){Array.prototype.unshift.call(b,a);return c.QueryResults(e[d].apply(e,b))})})}if(!a)return a;a.then&&(a=f.delegate(a));b("forEach");b("filter");b("map");if(!a.total)a.total=g.when(a,function(a){return a.length});return a};return c.QueryResults});