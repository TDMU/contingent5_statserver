//>>built
define(["dijit","dojo","dojox","dojo/require!dojox/lang/observable"],function(v,g,i){g.provide("dojox.secure.DOM");g.require("dojox.lang.observable");i.secure.DOM=function(e){function f(a){if(a){if(a.nodeType){var b=p(a);if(a.nodeType==1&&typeof b.style=="function")b.style=t(a.style),b.ownerDocument=k,b.childNodes={__get__:function(b){return f(a.childNodes[b])},length:0};return b}if(a&&typeof a=="object"){if(a.__observable)return a.__observable;b=a instanceof Array?[]:{};a.__observable=b;for(var d in a)d!=
"__observable"&&(b[d]=f(a[d]));b.data__=a;return b}if(typeof a=="function"){var c=function(a){if(typeof a=="function")return function(){for(var b=0;b<arguments.length;b++)arguments[b]=f(arguments[b]);return c(a.apply(f(this),arguments))};return i.secure.unwrap(a)};return function(){a.safetyCheck&&a.safetyCheck.apply(c(this),arguments);for(var b=0;b<arguments.length;b++)arguments[b]=c(arguments[b]);return f(a.apply(c(this),arguments))}}}return a}function j(a){a+="";if(a.match(/behavior:|content:|javascript:|binding|expression|\@import/))throw Error("Illegal CSS");
var b=e.id||(e.id="safe"+(""+Math.random()).substring(2));return a.replace(/(\}|^)\s*([^\{]*\{)/g,function(a,c,f){return c+" #"+b+" "+f})}function q(a){if(a.match(/:/)&&!a.match(/^(http|ftp|mailto)/))throw Error("Unsafe URL "+a);}function m(a){if(a&&a.nodeType==1){if(a.tagName.match(/script/i)){var b=a.src;b&&b!=""?(a.parentNode.removeChild(a),g.xhrGet({url:b,secure:!0}).addCallback(function(a){k.evaluate(a)})):(b=a.innerHTML,a.parentNode.removeChild(a),f.evaluate(b))}if(a.tagName.match(/link/i))throw Error("illegal tag");
if(a.tagName.match(/style/i)){var d=function(b){a.styleSheet?a.styleSheet.cssText=b:(b=l.createTextNode(b),a.childNodes[0]?a.replaceChild(b,a.childNodes[0]):a.appendChild(b))};if((b=a.src)&&b!="")alert("src"+b),a.src=null,g.xhrGet({url:b,secure:!0}).addCallback(function(a){d(j(a))});d(j(a.innerHTML))}a.style&&j(a.style.cssText);a.href&&q(a.href);a.src&&q(a.src);for(var c,b=0;c=a.attributes[b++];)if(c.name.substring(0,2)=="on"&&c.value!="null"&&c.value!="")throw Error("event handlers not allowed in the HTML, they must be set with element.addEventListener");
c=a.childNodes;for(var b=0,e=c.length;b<e;b++)m(c[b])}}function n(a){var b=document.createElement("div");if(a.match(/<object/i))throw Error("The object tag is not allowed");b.innerHTML=a;m(b);return b}function o(a,b){return function(d,c){m(c[b]);return d[a](c[0])}}function r(a){return i.lang.makeObservable(function(a,d){return a[d]},a,function(a,d,c,e){for(var g=0;g<e.length;g++)e[g]=unwrap(e[g]);if(h[c])return f(h[c].call(a,d,e));return f(d[c].apply(d,e))},h)}unwrap=i.secure.unwrap;var l=e.ownerDocument,
k={getElementById:function(a){a:if(a=l.getElementById(a)){var b=a;do if(b==e){a=f(a);break a}while(b=b.parentNode);a=null}return a},createElement:function(a){return f(l.createElement(a))},createTextNode:function(a){return f(l.createTextNode(a))},write:function(a){for(a=n(a);a.childNodes.length;)e.appendChild(a.childNodes[0])}};k.open=k.close=function(){};var s={innerHTML:function(a,b){console.log("setting innerHTML");a.innerHTML=n(b).innerHTML},outerHTML:function(){throw Error("Can not set this property");
}},h={appendChild:o("appendChild",0),insertBefore:o("insertBefore",0),replaceChild:o("replaceChild",1),cloneNode:function(a,b){return a.cloneNode(b[0])},addEventListener:function(a,b){g.connect(a,"on"+b[0],this,function(a){a=p(a||window.event);b[1].call(this,a)})}};h.childNodes=h.style=h.ownerDocument=function(){};var p=r(function(a,b,d){if(s[b])s[b](a,d);a[b]=d}),u={behavior:1,MozBinding:1},t=r(function(a,b,d){u[b]||(a[b]=j(d))});f.safeHTML=n;f.safeCSS=j;return f};i.secure.unwrap=function(e){return e&&
e.data__||e}});