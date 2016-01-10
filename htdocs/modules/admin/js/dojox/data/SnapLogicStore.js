//>>built
define("dojox/data/SnapLogicStore",["dojo","dojox","dojo/io/script","dojo/data/util/sorter"],function(d,g){d.declare("dojox.data.SnapLogicStore",null,{Parts:{DATA:"data",COUNT:"count"},url:"",constructor:function(a){if(a.url)this.url=a.url;this._parameters=a.parameters},_assertIsItem:function(a){if(!this.isItem(a))throw Error("dojox.data.SnapLogicStore: a function was passed an item argument that was not an item");},_assertIsAttribute:function(a){if(typeof a!=="string")throw Error("dojox.data.SnapLogicStore: a function was passed an attribute argument that was not an attribute name string");
},getFeatures:function(){return{"dojo.data.api.Read":!0}},getValue:function(a,b,c){this._assertIsItem(a);this._assertIsAttribute(b);b=d.indexOf(a.attributes,b);if(b!==-1)return a.values[b];return c},getAttributes:function(a){this._assertIsItem(a);return a.attributes},hasAttribute:function(a,b){this._assertIsItem(a);this._assertIsAttribute(b);for(var c=0;c<a.attributes.length;++c)if(b==a.attributes[c])return!0;return!1},isItemLoaded:function(a){return this.isItem(a)},loadItem:function(){},getLabel:function(){},
getLabelAttributes:function(){return null},containsValue:function(a,b,c){return this.getValue(a,b)===c},getValues:function(a,b){this._assertIsItem(a);this._assertIsAttribute(b);var c=d.indexOf(a.attributes,b);if(c!==-1)return[a.values[c]];return[]},isItem:function(a){if(a&&a._store===this)return!0;return!1},close:function(){},_fetchHandler:function(a){var b=a.scope||d.global;a.onBegin&&a.onBegin.call(b,a._countResponse[0],a);if(a.onItem||a.onComplete){var c=a._dataResponse;if(c.length){if(a.query!=
"record count"){for(var g=c.shift(),e=[],f=0;f<c.length;++f){if(a._aborted)break;e.push({attributes:g,values:c[f],_store:this})}a.sort&&!a._aborted&&e.sort(d.data.util.sorter.createSortFunction(a.sort,self))}else e=[{attributes:["count"],values:c,_store:this}];if(a.onItem){for(f=0;f<e.length;++f){if(a._aborted)break;a.onItem.call(b,e[f],a)}e=null}a.onComplete&&!a._aborted&&a.onComplete.call(b,e,a)}else a.onError.call(b,Error("dojox.data.SnapLogicStore: invalid response of length 0"),a)}},_partHandler:function(a,
b,c){if(c instanceof Error)b==this.Parts.DATA?a._dataHandle=null:a._countHandle=null,a._aborted=!0,a.onError&&a.onError.call(a.scope,c,a);else if(!a._aborted)b==this.Parts.DATA?a._dataResponse=c:a._countResponse=c,(!a._dataHandle||a._dataResponse!==null)&&(!a._countHandle||a._countResponse!==null)&&this._fetchHandler(a)},fetch:function(a){a._countResponse=null;a._dataResponse=null;a._aborted=!1;a.abort=function(){if(!a._aborted)a._aborted=!0,a._dataHandle&&a._dataHandle.cancel&&a._dataHandle.cancel(),
a._countHandle&&a._countHandle.cancel&&a._countHandle.cancel()};if(a.onItem||a.onComplete){var b=this._parameters||{};if(a.start){if(a.start<0)throw Error("dojox.data.SnapLogicStore: request start value must be 0 or greater");b["sn.start"]=a.start+1}if(a.count){if(a.count<0)throw Error("dojox.data.SnapLogicStore: request count value 0 or greater");b["sn.limit"]=a.count}b["sn.content_type"]="application/javascript";b={url:this.url,content:b,timeout:6E4,callbackParamName:"sn.stream_header",handle:d.hitch(this,
"_partHandler",a,this.Parts.DATA)};a._dataHandle=d.io.script.get(b)}if(a.onBegin)b={"sn.count":"records","sn.content_type":"application/javascript"},b={url:this.url,content:b,timeout:6E4,callbackParamName:"sn.stream_header",handle:d.hitch(this,"_partHandler",a,this.Parts.COUNT)},a._countHandle=d.io.script.get(b);return a}});return g.data.SnapLogicStore});