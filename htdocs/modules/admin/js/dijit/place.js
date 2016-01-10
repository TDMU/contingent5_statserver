//>>built
define("dijit/place",["dojo/_base/array","dojo/dom-geometry","dojo/dom-style","dojo/_base/kernel","dojo/_base/window","dojo/window","."],function(r,q,t,u,v,w,x){function s(f,a,j,k){var c=w.getBox();(!f.parentNode||String(f.parentNode.tagName).toLowerCase()!="body")&&v.body().appendChild(f);var d=null;r.some(a,function(b){var a=b.corner,g=b.pos,h=0,n={w:{L:c.l+c.w-g.x,R:g.x-c.l,M:c.w}[a.charAt(1)],h:{T:c.t+c.h-g.y,B:g.y-c.t,M:c.h}[a.charAt(0)]};j&&(h=j(f,b.aroundCorner,a,n,k),h=typeof h=="undefined"?
0:h);var e=f.style,i=e.display,l=e.visibility;if(e.display=="none")e.visibility="hidden",e.display="";var m=q.getMarginBox(f);e.display=i;e.visibility=l;i={L:g.x,R:g.x-m.w,M:Math.max(c.l,Math.min(c.l+c.w,g.x+(m.w>>1))-m.w)}[a.charAt(1)];l={T:g.y,B:g.y-m.h,M:Math.max(c.t,Math.min(c.t+c.h,g.y+(m.h>>1))-m.h)}[a.charAt(0)];g=Math.max(c.l,i);e=Math.max(c.t,l);i=Math.min(c.l+c.w,i+m.w)-g;l=Math.min(c.t+c.h,l+m.h)-e;h+=m.w-i+(m.h-l);if(d==null||h<d.overflow)d={corner:a,aroundCorner:b.aroundCorner,x:g,y:e,
w:i,h:l,overflow:h,spaceAvailable:n};return!h});d.overflow&&j&&j(f,d.aroundCorner,d.corner,d.spaceAvailable,k);var a=q.isBodyLtr(),b=f.style;b.top=d.y+"px";b[a?"left":"right"]=(a?d.x:c.w-d.x-d.w)+"px";b[a?"right":"left"]="auto";return d}return x.place={at:function(f,a,j,k){j=r.map(j,function(c){var d={corner:c,pos:{x:a.x,y:a.y}};k&&(d.pos.x+=c.charAt(1)=="L"?k.x:-k.x,d.pos.y+=c.charAt(0)=="T"?k.y:-k.y);return d});return s(f,j)},around:function(f,a,j,k,c){function d(b,a){l.push({aroundCorner:b,corner:a,
pos:{x:{L:h,R:h+e,M:h+(e>>1)}[b.charAt(1)],y:{T:n,B:n+i,M:n+(i>>1)}[b.charAt(0)]}})}var b=typeof a=="string"||"offsetWidth"in a?q.position(a,!0):a;if(a.parentNode)for(a=a.parentNode;a&&a.nodeType==1&&a.nodeName!="BODY";){var o=q.position(a,!0),p=t.getComputedStyle(a).overflow;if(p=="hidden"||p=="auto"||p=="scroll"){var p=Math.min(b.y+b.h,o.y+o.h),g=Math.min(b.x+b.w,o.x+o.w);b.x=Math.max(b.x,o.x);b.y=Math.max(b.y,o.y);b.h=p-b.y;b.w=g-b.x}a=a.parentNode}var h=b.x,n=b.y,e="w"in b?b.w:b.w=b.width,i="h"in
b?b.h:(u.deprecated("place.around: dijit.place.__Rectangle: { x:"+h+", y:"+n+", height:"+b.height+", width:"+e+" } has been deprecated.  Please use { x:"+h+", y:"+n+", h:"+b.height+", w:"+e+" }","","2.0"),b.h=b.height),l=[];r.forEach(j,function(b){var a=k;switch(b){case "above-centered":d("TM","BM");break;case "below-centered":d("BM","TM");break;case "after":a=!a;case "before":d(a?"ML":"MR",a?"MR":"ML");break;case "below-alt":a=!a;case "below":d(a?"BL":"BR",a?"TL":"TR");d(a?"BR":"BL",a?"TR":"TL");
break;case "above-alt":a=!a;case "above":d(a?"TL":"TR",a?"BL":"BR");d(a?"TR":"TL",a?"BR":"BL");break;default:d(b.aroundCorner,b.corner)}});f=s(f,l,c,{w:e,h:i});f.aroundNodePos=b;return f}}});