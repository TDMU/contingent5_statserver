/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/behavior",["./_base/kernel","./_base/lang","./_base/array","./_base/connect","./query","./ready"],function(e,g,k,h,l,j){e.behavior=new function(){function e(a,b){a[b]||(a[b]=[]);return a[b]}function i(a,b,f){var d={},c;for(c in a)typeof d[c]=="undefined"&&(f?f.call(b,a[c],c):b(a[c],c))}var j=0;this._behaviors={};this.add=function(a){i(a,this,function(b,a){var d=e(this._behaviors,a);if(typeof d.id!="number")d.id=j++;var c=[];d.push(c);if(g.isString(b)||g.isFunction(b))b={found:b};i(b,
function(a,b){e(c,b).push(a)})})};var m=function(a,b,f){g.isString(b)?f=="found"?h.publish(b,[a]):h.connect(a,f,function(){h.publish(b,arguments)}):g.isFunction(b)&&(f=="found"?b(a):h.connect(a,f,b))};this.apply=function(){i(this._behaviors,function(a,b){l(b).forEach(function(b){var d=0,c="_dj_behavior_"+a.id;if(typeof b[c]=="number"&&(d=b[c],d==a.length))return;for(var e;e=a[d];d++)i(e,function(a,c){g.isArray(a)&&k.forEach(a,function(a){m(b,a,c)})});b[c]=a.length})})}};j(e.behavior,"apply");return e.behavior});