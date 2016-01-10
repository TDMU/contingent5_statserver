//>>built
define("dojox/gfx/gradutils",["./_base","dojo/_base/lang","./matrix","dojo/_base/Color"],function(g,j,f,h){function k(a,d){if(a<=0)return d[0].color;var b=d.length;if(a>=1)return d[b-1].color;for(var c=0;c<b;++c){var e=d[c];if(e.offset>=a){if(c)return b=d[c-1],h.blendColors(new h(b.color),new h(e.color),(a-b.offset)/(e.offset-b.offset));return e.color}}return d[b-1].color}g=g.gradutils={};g.getColor=function(a,d){var b;if(a){switch(a.type){case "linear":b=f.rotate(-Math.atan2(a.y2-a.y1,a.x2-a.x1));
var c=f.project(a.x2-a.x1,a.y2-a.y1),e=f.multiplyPoint(c,d),i=f.multiplyPoint(c,a.x1,a.y1),c=f.multiplyPoint(c,a.x2,a.y2),c=f.multiplyPoint(b,c.x-i.x,c.y-i.y).x;b=f.multiplyPoint(b,e.x-i.x,e.y-i.y).x/c;break;case "radial":b=d.x-a.cx,e=d.y-a.cy,b=Math.sqrt(b*b+e*e)/a.r}return k(b,a.colors)}return new h(a||[0,0,0,0])};g.reverse=function(a){if(a)switch(a.type){case "linear":case "radial":if(a=j.delegate(a),a.colors){for(var d=a.colors,b=d.length,c=0,e,f=a.colors=Array(d.length);c<b;++c)e=d[c],f[c]={offset:1-
e.offset,color:e.color};f.sort(function(a,b){return a.offset-b.offset})}}return a};return g});