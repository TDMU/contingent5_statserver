//>>built
define(["dijit","dojo","dojox","dojo/require!dojox/math/round"],function(k,g,i){g.provide("dojox.drawing.util.common");g.require("dojox.math.round");(function(){var h={},j=0;i.drawing.util.common={radToDeg:function(a){return a*180/Math.PI},degToRad:function(a){return a*Math.PI/180},angle:function(a,c){if(c){c/=180;var d=this.radians(a),b=Math.PI*c,d=i.math.round(d/b)*b;return i.math.round(this.radToDeg(d))}else return this.radToDeg(this.radians(a))},oppAngle:function(a){(a+=180)>360&&(a-=360);return a},
radians:function(a){return Math.atan2(a.start.y-a.y,a.x-a.start.x)},length:function(a){return Math.sqrt(Math.pow(a.start.x-a.x,2)+Math.pow(a.start.y-a.y,2))},lineSub:function(a,c,d,b,e){var f=this.distance(this.argsToObj.apply(this,arguments)),f=f<e?e:f,f=(f-e)/f;return{x:a-(a-d)*f,y:c-(c-b)*f}},argsToObj:function(){var a=arguments;if(a.length<4)return a[0];return{start:{x:a[0],y:a[1]},x:a[2],y:a[3]}},distance:function(){var a=this.argsToObj.apply(this,arguments);return Math.abs(Math.sqrt(Math.pow(a.start.x-
a.x,2)+Math.pow(a.start.y-a.y,2)))},slope:function(a,c){if(!(a.x-c.x))return 0;return(a.y-c.y)/(a.x-c.x)},pointOnCircle:function(a,c,d,b){b=b*Math.PI/180;return{x:a+d*Math.cos(b),y:c-d*Math.sin(b)}},constrainAngle:function(a,c,d){var b=this.angle(a);if(b>=c&&b<=d)return a;var e=this.length(a);return this.pointOnCircle(a.start.x,a.start.y,e,b>d?d:c-b<100?c:d)},snapAngle:function(a,c){var d=this.radians(a),b=this.length(a),e=Math.PI*c,d=this.radToDeg(Math.round(d/e)*e);return this.pointOnCircle(a.start.x,
a.start.y,b,d)},idSetStart:function(a){j=a},uid:function(a){a=a||"shape";h[a]=h[a]===void 0?j:h[a]+1;return a+h[a]},abbr:function(a){return a.substring(a.lastIndexOf(".")+1).charAt(0).toLowerCase()+a.substring(a.lastIndexOf(".")+2)},mixin:function(){},objects:{},register:function(a){this.objects[a.id]=a},byId:function(a){return this.objects[a]},attr:function(a,c,d){if(!a)return!1;try{if(a.shape&&a.util)a=a.shape;if(!d&&c=="id"&&a.target){for(var b=a.target;!g.attr(b,"id");)b=b.parentNode;return g.attr(b,
"id")}if(a.rawNode||a.target){var e=Array.prototype.slice.call(arguments);e[0]=a.rawNode||a.target;return g.attr.apply(g,e)}return g.attr(a,"id")}catch(f){return!1}}}})()});