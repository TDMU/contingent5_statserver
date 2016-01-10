/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/store/JsonRest",["../_base/xhr","../json","../_base/declare","./util/QueryResults"],function(e,i,f,j){return f("dojo.store.JsonRest",null,{constructor:function(b){f.safeMixin(this,b)},target:"",idProperty:"id",get:function(b,a){var c=a||{};c.Accept=this.accepts;return e("GET",{url:this.target+b,handleAs:"json",headers:c})},accepts:"application/javascript, application/json",getIdentity:function(b){return b[this.idProperty]},put:function(b,a){var a=a||{},c="id"in a?a.id:this.getIdentity(b),
d=typeof c!="undefined";return e(d&&!a.incremental?"PUT":"POST",{url:d?this.target+c:this.target,postData:i.stringify(b),handleAs:"json",headers:{"Content-Type":"application/json",Accept:this.accepts,"If-Match":a.overwrite===!0?"*":null,"If-None-Match":a.overwrite===!1?"*":null}})},add:function(b,a){a=a||{};a.overwrite=!1;return this.put(b,a)},remove:function(b){return e("DELETE",{url:this.target+b})},query:function(b,a){var c={Accept:this.accepts},a=a||{};if(a.start>=0||a.count>=0)c.Range="items="+
(a.start||"0")+"-"+("count"in a&&a.count!=Infinity?a.count+(a.start||0)-1:"");b&&typeof b=="object"&&(b=(b=e.objectToQuery(b))?"?"+b:"");if(a&&a.sort){var d=this.sortParam;b+=(b?"&":"?")+(d?d+"=":"sort(");for(var g=0;g<a.sort.length;g++){var f=a.sort[g];b+=(g>0?",":"")+(f.descending?"-":"+")+encodeURIComponent(f.attribute)}d||(b+=")")}var h=e("GET",{url:this.target+(b||""),handleAs:"json",headers:c});h.total=h.then(function(){var a=h.ioArgs.xhr.getResponseHeader("Content-Range");return a&&(a=a.match(/\/(.*)/))&&
+a[1]});return j(h)}})});