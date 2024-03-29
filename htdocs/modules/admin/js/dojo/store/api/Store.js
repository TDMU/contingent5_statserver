/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/store/api/Store",["dojo/_base/declare"],function(d){var a=d("dojo.store.api.Store",null,{idProperty:"id",queryEngine:null,get:function(){},getIdentity:function(){},put:function(){},add:function(){},remove:function(e){delete this.index[e];for(var b=this.data,f=this.idProperty,c=0,a=b.length;c<a;c++)if(b[c][f]==e){b.splice(c,1);break}},query:function(){},transaction:function(){},getChildren:function(){},getMetadata:function(){}});a.PutDirectives=function(e,b,a,c){this.id=e;this.before=
b;this.parent=a;this.overwrite=c};a.SortInformation=function(a,b){this.attribute=a;this.descending=b};a.QueryOptions=function(a,b,d){this.sort=a;this.start=b;this.count=d};d("dojo.store.api.Store.QueryResults",null,{forEach:function(){},filter:function(){},map:function(){},then:function(){},observe:function(){},total:0});d("dojo.store.api.Store.Transaction",null,{commit:function(){},abort:function(){}});return a});