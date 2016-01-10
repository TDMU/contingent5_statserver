/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/data/util/sorter",["dojo/_base/lang"],function(l){var f=l.getObject("dojo.data.util.sorter",!0);f.basicComparator=function(a,c){var d=-1;a===null&&(a=void 0);c===null&&(c=void 0);if(a==c)d=0;else if(a>c||a==null)d=1;return d};f.createSortFunction=function(a,c){function d(a,c,b,d){return function(e,f){var g=d.getValue(e,a),h=d.getValue(f,a);return c*b(g,h)}}for(var g=[],e,i=c.comparatorMap,j=f.basicComparator,h=0;h<a.length;h++){e=a[h];var b=e.attribute;if(b){e=e.descending?-1:1;var k=
j;i&&(typeof b!=="string"&&"toString"in b&&(b=b.toString()),k=i[b]||j);g.push(d(b,e,k,c))}}return function(a,c){for(var b=0;b<g.length;){var d=g[b++](a,c);if(d!==0)return d}return 0}};return f});