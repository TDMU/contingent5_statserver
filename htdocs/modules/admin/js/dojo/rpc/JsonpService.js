/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/rpc/JsonpService",["../main","./RpcService","../io/script"],function(a){a.declare("dojo.rpc.JsonpService",a.rpc.RpcService,{constructor:function(b,c){this.required&&(c&&a.mixin(this.required,c),a.forEach(this.required,function(a){if(a==""||a==void 0)throw Error("Required Service Argument not found: "+a);}))},strictArgChecks:!1,bind:function(b,c,d,e){a.io.script.get({url:e||this.serviceUrl,callbackParamName:this.callbackParamName||"callback",content:this.createRequest(c),timeout:this.timeout,
handleAs:"json",preventCache:!0}).addCallbacks(this.resultCallback(d),this.errorCallback(d))},createRequest:function(b){b=a.isArrayLike(b)&&b.length==1?b[0]:{};a.mixin(b,this.required);return b}});return a.rpc.JsonpService});