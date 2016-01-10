//>>built
define("dojox/gfx/decompose",["./_base","dojo/_base/lang","./matrix"],function(o,k,h){function j(a,b){return Math.abs(a-b)<=1.0E-6*(Math.abs(a)+Math.abs(b))}function i(a,b,d,c){if(isFinite(a)){if(!isFinite(d))return a}else return d;b=Math.abs(b);c=Math.abs(c);return(b*a+c*d)/(b+c)}function l(a){var a=h.normalize(a),b=-a.xx-a.yy,d=a.xx*a.yy-a.xy*a.yx,c=Math.sqrt(b*b-4*d),b=-(b+(b<0?-c:c))/2;d/=b;var c=a.xy/(b-a.xx),f=1,e=a.xy/(d-a.xx),g=1;j(b,d)&&(c=1,e=f=0,g=1);isFinite(c)||(c=1,f=(b-a.xx)/a.xy,isFinite(f)||
(c=(b-a.yy)/a.yx,f=1,isFinite(c)||(c=1,f=a.yx/(b-a.yy))));isFinite(e)||(e=1,g=(d-a.xx)/a.xy,isFinite(g)||(e=(d-a.yy)/a.yx,g=1,isFinite(e)||(e=1,g=a.yx/(d-a.yy))));var a=Math.sqrt(c*c+f*f),i=Math.sqrt(e*e+g*g);if(!isFinite(c/=a))c=0;if(!isFinite(f/=a))f=0;if(!isFinite(e/=i))e=0;if(!isFinite(g/=i))g=0;return{value1:b,value2:d,vector1:{x:c,y:f},vector2:{x:e,y:g}}}function m(a,b){var d=a.xx*a.yy<0||a.xy*a.yx>0?-1:1,c=b.angle1=(Math.atan2(a.yx,a.yy)+Math.atan2(-d*a.xy,d*a.xx))/2,d=Math.cos(c),c=Math.sin(c);
b.sx=i(a.xx/d,d,-a.xy/c,c);b.sy=i(a.yy/d,d,a.yx/c,c);return b}function n(a,b){var d=a.xx*a.yy<0||a.xy*a.yx>0?-1:1,c=b.angle2=(Math.atan2(d*a.yx,d*a.xx)+Math.atan2(-a.xy,a.yy))/2,d=Math.cos(c),c=Math.sin(c);b.sx=i(a.xx/d,d,a.yx/c,c);b.sy=i(a.yy/d,d,-a.xy/c,c);return b}return o.decompose=function(a){var b=h.normalize(a),a={dx:b.dx,dy:b.dy,sx:1,sy:1,angle1:0,angle2:0};if(j(b.xy,0)&&j(b.yx,0))return k.mixin(a,{sx:b.xx,sy:b.yy});if(j(b.xx*b.yx,-b.xy*b.yy))return m(b,a);if(j(b.xx*b.xy,-b.yx*b.yy))return n(b,
a);var d,c=new h.Matrix2D(b);d=k.mixin(c,{dx:0,dy:0,xy:c.yx,yx:c.xy});c=l([b,d]);d=l([d,b]);c=new h.Matrix2D({xx:c.vector1.x,xy:c.vector2.x,yx:c.vector1.y,yy:c.vector2.y});d=new h.Matrix2D({xx:d.vector1.x,xy:d.vector1.y,yx:d.vector2.x,yy:d.vector2.y});b=new h.Matrix2D([h.invert(c),b,h.invert(d)]);m(d,a);b.xx*=a.sx;b.yy*=a.sy;n(c,a);b.xx*=a.sx;b.yy*=a.sy;return k.mixin(a,{sx:b.xx,sy:b.yy})}});