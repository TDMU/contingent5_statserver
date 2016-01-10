//>>built
define(["dijit","dojo","dojox","dojo/require!dojo/AdapterRegistry"],function(l,c,h){c.provide("dojox.cometd._base");c.require("dojo.AdapterRegistry");h.cometd={Connection:function(k){c.mixin(this,{prefix:k,_status:"unconnected",_handshook:!1,_initialized:!1,_polling:!1,expectedNetworkDelay:1E4,connectTimeout:0,version:"1.0",minimumVersion:"0.9",clientId:null,messageId:0,batch:0,_isXD:!1,handshakeReturn:null,currentTransport:null,url:null,lastMessage:null,_messageQ:[],handleAs:"json",_advice:{},_backoffInterval:0,
_backoffIncrement:1E3,_backoffMax:6E4,_deferredSubscribes:{},_deferredUnsubscribes:{},_subscriptions:[],_extendInList:[],_extendOutList:[]});this.state=function(){return this._status};this.init=function(a,b,e){b=b||{};b.version=this.version;b.minimumVersion=this.minimumVersion;b.channel="/meta/handshake";b.id=""+this.messageId++;this.url=a||c.config.cometdRoot;if(!this.url)throw"no cometd root";var d=(""+window.location).match(/^(([^:/?#]+):)?(\/\/([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?$/);if(d[4]){var d=
d[4].split(":"),a=d[0],i=d[1]||"80",d=this.url.match(/^(([^:/?#]+):)?(\/\/([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?$/);if(d[4]){var d=d[4].split(":"),f=d[1]||"80";this._isXD=d[0]!=a||f!=i}}if(!this._isXD)b.supportedConnectionTypes=c.map(h.cometd.connectionTypes.pairs,"return item[0]");b=this._extendOut(b);a={url:this.url,handleAs:this.handleAs,content:{message:c.toJson([b])},load:c.hitch(this,function(a){this._backon();this._finishInit(a)}),error:c.hitch(this,function(a){this._backoff();this._finishInit(a)}),
timeout:this.expectedNetworkDelay};e&&c.mixin(a,e);this._props=b;for(var g in this._subscriptions)for(var j in this._subscriptions[g])this._subscriptions[g][j].topic&&c.unsubscribe(this._subscriptions[g][j].topic);this._messageQ=[];this._subscriptions=[];this._initialized=!0;this._status="handshaking";this.batch=0;this.startBatch();this._isXD?(a.callbackParamName="jsonp",b=c.io.script.get(a)):b=c.xhrPost(a);return b};this.publish=function(a,b,e){a={data:b,channel:a};e&&c.mixin(a,e);this._sendMessage(a)};
this.subscribe=function(a,b,e,d){d=d||{};if(b){var i=k+a,f=this._subscriptions[i];if(!f||f.length==0){f=[];d.channel="/meta/subscribe";d.subscription=a;this._sendMessage(d);var g=this._deferredSubscribes;g[a]&&(g[a].cancel(),delete g[a]);g[a]=new c.Deferred}for(var j in f)if(f[j].objOrFunc===b&&(!f[j].funcName&&!e||f[j].funcName==e))return null;g=c.subscribe(i,b,e);f.push({topic:g,objOrFunc:b,funcName:e});this._subscriptions[i]=f}i=this._deferredSubscribes[a]||{};i.args=c._toArray(arguments);return i};
this.unsubscribe=function(a,b,e,d){if(arguments.length==1&&!c.isString(a)&&a.args)return this.unsubscribe.apply(this,a.args);var i=k+a,f=this._subscriptions[i];if(!f||f.length==0)return null;var g=0,j;for(j in f){var h=f[j];!b||h.objOrFunc===b&&(!h.funcName&&!e||h.funcName==e)?(c.unsubscribe(f[j].topic),delete f[j]):g++}if(g==0)d=d||{},d.channel="/meta/unsubscribe",d.subscription=a,delete this._subscriptions[i],this._sendMessage(d),this._deferredUnsubscribes[a]=new c.Deferred,this._deferredSubscribes[a]&&
(this._deferredSubscribes[a].cancel(),delete this._deferredSubscribes[a]);return this._deferredUnsubscribes[a]};this.disconnect=function(){for(var a in this._subscriptions)for(var b in this._subscriptions[a])this._subscriptions[a][b].topic&&c.unsubscribe(this._subscriptions[a][b].topic);this._subscriptions=[];this._messageQ=[];if(this._initialized&&this.currentTransport)this._initialized=!1,this.currentTransport.disconnect();this._polling||this._publishMeta("connect",!1);this._handshook=this._initialized=
!1;this._status="disconnected";this._publishMeta("disconnect",!0)};this.subscribed=function(){};this.unsubscribed=function(){};this.tunnelInit=function(){};this.tunnelCollapse=function(){};this._backoff=function(){if(this._advice){if(!this._advice.interval)this._advice.interval=0}else this._advice={reconnect:"retry",interval:0};this._backoffInterval<this._backoffMax&&(this._backoffInterval+=this._backoffIncrement)};this._backon=function(){this._backoffInterval=0};this._interval=function(){var a=this._backoffInterval+
(this._advice?this._advice.interval?this._advice.interval:0:0);a>0&&console.log("Retry in interval+backoff="+this._advice.interval+"+"+this._backoffInterval+"="+a+"ms");return a};this._publishMeta=function(a,b,e){try{var d={cometd:this,action:a,successful:b,state:this.state()};e&&c.mixin(d,e);c.publish(this.prefix+"/meta",[d])}catch(i){console.log(i)}};this._finishInit=function(a){if(this._status=="handshaking"){var b=this._handshook,e=!1,d={};if(a instanceof Error)c.mixin(d,{reestablish:!1,failure:!0,
error:a,advice:this._advice});else{a=a[0];this.handshakeReturn=a=this._extendIn(a);if(a.advice)this._advice=a.advice;e=a.successful?a.successful:!1;if(a.version<this.minimumVersion)console.log&&console.log("cometd protocol version mismatch. We wanted",this.minimumVersion,"but got",a.version),e=!1,this._advice.reconnect="none";c.mixin(d,{reestablish:e&&b,response:a})}this._publishMeta("handshake",e,d);if(this._status=="handshaking")e?(this._status="connecting",this._handshook=!0,b=this.currentTransport=
h.cometd.connectionTypes.match(a.supportedConnectionTypes,a.version,this._isXD),b._cometd=this,b.version=a.version,this.clientId=a.clientId,this.tunnelInit=b.tunnelInit&&c.hitch(b,"tunnelInit"),this.tunnelCollapse=b.tunnelCollapse&&c.hitch(b,"tunnelCollapse"),b.startup(a)):(!this._advice||this._advice.reconnect!="none")&&setTimeout(c.hitch(this,"init",this.url,this._props),this._interval())}};this._extendIn=function(a){c.forEach(h.cometd._extendInList,function(b){a=b(a)||a});return a};this._extendOut=
function(a){c.forEach(h.cometd._extendOutList,function(b){a=b(a)||a});return a};this.deliver=function(a){c.forEach(a,this._deliver,this);return a};this._deliver=function(a){a=this._extendIn(a);if(a.channel||a.success===!0){this.lastMessage=a;if(a.advice)this._advice=a.advice;var b=null;if(a.channel&&a.channel.length>5&&a.channel.substr(0,5)=="/meta")switch(a.channel){case "/meta/connect":b={response:a};if(a.successful&&this._status!="connected")this._status="connected",this.endBatch();this._initialized&&
this._publishMeta("connect",a.successful,b);break;case "/meta/subscribe":b=this._deferredSubscribes[a.subscription];try{if(!a.successful){b&&b.errback(Error(a.error));this.currentTransport.cancelConnect();return}b&&b.callback(!0);this.subscribed(a.subscription,a)}catch(e){log.warn(e)}break;case "/meta/unsubscribe":b=this._deferredUnsubscribes[a.subscription];try{if(!a.successful){b&&b.errback(Error(a.error));this.currentTransport.cancelConnect();return}b&&b.callback(!0);this.unsubscribed(a.subscription,
a)}catch(d){log.warn(d)}break;default:if(a.successful&&!a.successful){this.currentTransport.cancelConnect();return}}this.currentTransport.deliver(a);if(a.data)try{for(var b=[a],i=k+a.channel,f=a.channel.split("/"),a=k,g=1;g<f.length-1;g++)c.publish(a+"/**",b),a+="/"+f[g];c.publish(a+"/**",b);c.publish(a+"/*",b);c.publish(i,b)}catch(h){console.log(h)}}};this._sendMessage=function(a){return this.currentTransport&&!this.batch?this.currentTransport.sendMessages([a]):(this._messageQ.push(a),null)};this.startBatch=
function(){this.batch++};this.endBatch=function(){if(--this.batch<=0&&this.currentTransport&&this._status=="connected"){this.batch=0;var a=this._messageQ;this._messageQ=[];a.length>0&&this.currentTransport.sendMessages(a)}};this._onUnload=function(){c.addOnUnload(h.cometd,"disconnect")};this._connectTimeout=function(){var a=0;this._advice&&this._advice.timeout&&this.expectedNetworkDelay>0&&(a=this._advice.timeout+this.expectedNetworkDelay);if(this.connectTimeout>0&&this.connectTimeout<a)return this.connectTimeout;
return a}},connectionTypes:new c.AdapterRegistry(!0)};h.cometd.Connection.call(h.cometd,"/cometd");c.addOnUnload(h.cometd,"_onUnload")});