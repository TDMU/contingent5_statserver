//>>built
define("dojox/rpc/JsonRPC",["dojo","dojox","dojox/rpc/Service"],function(d,c){function e(b){return{serialize:function(a,f,c){a={id:this._requestId++,method:f.name,params:c};if(b)a.jsonrpc=b;return{data:d.toJson(a),handleAs:"json",contentType:"application/json",transport:"POST"}},deserialize:function(a){"Error"==a.name&&(a=d.fromJson(a.responseText));if(a.error){var b=Error(a.error.message||a.error);b._rpcErrorObject=a.error;return b}return a.result}}}c.rpc.envelopeRegistry.register("JSON-RPC-1.0",
function(b){return b=="JSON-RPC-1.0"},d.mixin({namedParams:!1},e()));c.rpc.envelopeRegistry.register("JSON-RPC-2.0",function(b){return b=="JSON-RPC-2.0"},e("2.0"))});