/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/rpc/JsonService",["../main","./RpcService"],function(b){b.declare("dojo.rpc.JsonService",b.rpc.RpcService,{bustCache:!1,contentType:"application/json-rpc",lastSubmissionId:0,callRemote:function(a,d){var c=new b.Deferred;this.bind(a,d,c);return c},bind:function(a,d,c,e){b.rawXhrPost({url:e||this.serviceUrl,postData:this.createRequest(a,d),contentType:this.contentType,timeout:this.timeout,handleAs:"json-comment-optional"}).addCallbacks(this.resultCallback(c),this.errorCallback(c))},createRequest:function(a,
d){var c={params:d,method:a,id:++this.lastSubmissionId};return b.toJson(c)},parseResults:function(a){if(b.isObject(a)){if("result"in a)return a.result;if("Result"in a)return a.Result;if("ResultSet"in a)return a.ResultSet}return a}});return b.rpc.JsonService});