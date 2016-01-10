/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/rpc/RpcService",["../main","../_base/url"],function(b){b.declare("dojo.rpc.RpcService",null,{constructor:function(a){if(a)if(b.isString(a)||a instanceof b._Url){var c=b.xhrGet({url:a instanceof b._Url?a+"":a,handleAs:"json-comment-optional",sync:!0});c.addCallback(this,"processSmd");c.addErrback(function(){throw Error("Unable to load SMD from "+a);})}else if(a.smdStr)this.processSmd(b.eval("("+a.smdStr+")"));else{if(a.serviceUrl)this.serviceUrl=a.serviceUrl;this.timeout=a.timeout||3E3;
if("strictArgChecks"in a)this.strictArgChecks=a.strictArgChecks;this.processSmd(a)}},strictArgChecks:!0,serviceUrl:"",parseResults:function(a){return a},errorCallback:function(a){return function(c){a.errback(c.message)}},resultCallback:function(a){return b.hitch(this,function(c){if(c.error!=null){var b;typeof c.error=="object"?(b=Error(c.error.message),b.code=c.error.code,b.error=c.error.error):b=Error(c.error);b.id=c.id;b.errorObject=c;a.errback(b)}else a.callback(this.parseResults(c))})},generateMethod:function(a,
c,e){return b.hitch(this,function(){var d=new b.Deferred;if(this.strictArgChecks&&c!=null&&arguments.length!=c.length)throw Error("Invalid number of parameters for remote method.");else this.bind(a,b._toArray(arguments),d,e);return d})},processSmd:function(a){a.methods&&b.forEach(a.methods,function(a){if(a&&a.name&&(this[a.name]=this.generateMethod(a.name,a.parameters,a.url||a.serviceUrl||a.serviceURL),!b.isFunction(this[a.name])))throw Error("RpcService: Failed to create"+a.name+"()");},this);this.serviceUrl=
a.serviceUrl||a.serviceURL;this.required=a.required;this.smd=a}});return b.rpc.RpcService});