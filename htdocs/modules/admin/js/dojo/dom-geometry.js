/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/dom-geometry",["./_base/sniff","./_base/window","./dom","./dom-style"],function(i,j,l,k){function m(a,d,b,c,e,g){g=g||"px";a=a.style;if(!isNaN(d))a.left=d+g;if(!isNaN(b))a.top=b+g;if(c>=0)a.width=c+g;if(e>=0)a.height=e+g}function n(a){return a.tagName.toLowerCase()=="button"||a.tagName.toLowerCase()=="input"&&(a.getAttribute("type")||"").toLowerCase()=="button"}function o(a){return f.boxModel=="border-box"||a.tagName.toLowerCase()=="table"||n(a)}var f={boxModel:"content-box"};if(i("ie"))f.boxModel=
document.compatMode=="BackCompat"?"border-box":"content-box";f.getPadExtents=function(a,d){var a=l.byId(a),b=d||k.getComputedStyle(a),c=k.toPixelValue,e=c(a,b.paddingLeft),g=c(a,b.paddingTop),f=c(a,b.paddingRight),b=c(a,b.paddingBottom);return{l:e,t:g,r:f,b:b,w:e+f,h:g+b}};f.getBorderExtents=function(a,d){var a=l.byId(a),b=k.toPixelValue,c=d||k.getComputedStyle(a),e=c.borderLeftStyle!="none"?b(a,c.borderLeftWidth):0,f=c.borderTopStyle!="none"?b(a,c.borderTopWidth):0,h=c.borderRightStyle!="none"?b(a,
c.borderRightWidth):0,b=c.borderBottomStyle!="none"?b(a,c.borderBottomWidth):0;return{l:e,t:f,r:h,b:b,w:e+h,h:f+b}};f.getPadBorderExtents=function(a,d){var a=l.byId(a),b=d||k.getComputedStyle(a),c=f.getPadExtents(a,b),b=f.getBorderExtents(a,b);return{l:c.l+b.l,t:c.t+b.t,r:c.r+b.r,b:c.b+b.b,w:c.w+b.w,h:c.h+b.h}};f.getMarginExtents=function(a,d){var a=l.byId(a),b=d||k.getComputedStyle(a),c=k.toPixelValue,e=c(a,b.marginLeft),f=c(a,b.marginTop),h=c(a,b.marginRight),c=c(a,b.marginBottom);i("webkit")&&
b.position!="absolute"&&(h=e);return{l:e,t:f,r:h,b:c,w:e+h,h:f+c}};f.getMarginBox=function(a,d){var a=l.byId(a),b=d||k.getComputedStyle(a),c=f.getMarginExtents(a,b),e=a.offsetLeft-c.l,g=a.offsetTop-c.t,h=a.parentNode,j=k.toPixelValue;if(i("mozilla")){var m=parseFloat(b.left),b=parseFloat(b.top);!isNaN(m)&&!isNaN(b)?(e=m,g=b):h&&h.style&&(h=k.getComputedStyle(h),h.overflow!="visible"&&(e+=h.borderLeftStyle!="none"?j(a,h.borderLeftWidth):0,g+=h.borderTopStyle!="none"?j(a,h.borderTopWidth):0))}else if((i("opera")||
i("ie")==8&&!i("quirks"))&&h)h=k.getComputedStyle(h),e-=h.borderLeftStyle!="none"?j(a,h.borderLeftWidth):0,g-=h.borderTopStyle!="none"?j(a,h.borderTopWidth):0;return{l:e,t:g,w:a.offsetWidth+c.w,h:a.offsetHeight+c.h}};f.getContentBox=function(a,d){var a=l.byId(a),b=d||k.getComputedStyle(a),c=a.clientWidth,e=f.getPadExtents(a,b),g=f.getBorderExtents(a,b);c?(b=a.clientHeight,g.w=g.h=0):(c=a.offsetWidth,b=a.offsetHeight);i("opera")&&(e.l+=g.l,e.t+=g.t);return{l:e.l,t:e.t,w:c-e.w-g.w,h:b-e.h-g.h}};f.setContentSize=
function(a,d,b){var a=l.byId(a),c=d.w,d=d.h;o(a)&&(b=f.getPadBorderExtents(a,b),c>=0&&(c+=b.w),d>=0&&(d+=b.h));m(a,NaN,NaN,c,d)};var p={l:0,t:0,w:0,h:0};f.setMarginBox=function(a,d,b){var a=l.byId(a),c=b||k.getComputedStyle(a),b=d.w,e=d.h,g=o(a)?p:f.getPadBorderExtents(a,c),c=f.getMarginExtents(a,c);if(i("webkit")&&n(a)){var h=a.style;if(b>=0&&!h.width)h.width="4px";if(e>=0&&!h.height)h.height="4px"}b>=0&&(b=Math.max(b-g.w-c.w,0));e>=0&&(e=Math.max(e-g.h-c.h,0));m(a,d.l,d.t,b,e)};f.isBodyLtr=function(){return(j.body().dir||
j.doc.documentElement.dir||"ltr").toLowerCase()=="ltr"};f.docScroll=function(){var a=j.doc.parentWindow||j.doc.defaultView;return"pageXOffset"in a?{x:a.pageXOffset,y:a.pageYOffset}:(a=i("quirks")?j.body():j.doc.documentElement,{x:f.fixIeBiDiScrollLeft(a.scrollLeft||0),y:a.scrollTop||0})};f.getIeDocumentElementOffset=function(){var a=j.doc.documentElement;if(i("ie")<8){var d=a.getBoundingClientRect(),b=d.left,d=d.top;i("ie")<7&&(b+=a.clientLeft,d+=a.clientTop);return{x:b<0?0:b,y:d<0?0:d}}else return{x:0,
y:0}};f.fixIeBiDiScrollLeft=function(a){var d=i("ie");if(d&&!f.isBodyLtr()){var b=i("quirks"),c=b?j.body():j.doc.documentElement;d==6&&!b&&j.global.frameElement&&c.scrollHeight>c.clientHeight&&(a+=c.clientLeft);return d<8||b?a+c.clientWidth-c.scrollWidth:-a}return a};f.position=function(a,d){var a=l.byId(a),b=j.body(),c=b.parentNode,e=a.getBoundingClientRect(),e={x:e.left,y:e.top,w:e.right-e.left,h:e.bottom-e.top};if(i("ie"))c=f.getIeDocumentElementOffset(),e.x-=c.x+(i("quirks")?b.clientLeft+b.offsetLeft:
0),e.y-=c.y+(i("quirks")?b.clientTop+b.offsetTop:0);else if(i("ff")==3){var b=k.getComputedStyle(c),g=k.toPixelValue;e.x-=g(c,b.marginLeft)+g(c,b.borderLeftWidth);e.y-=g(c,b.marginTop)+g(c,b.borderTopWidth)}d&&(c=f.docScroll(),e.x+=c.x,e.y+=c.y);return e};f.getMarginSize=function(a,d){var a=l.byId(a),b=f.getMarginExtents(a,d||k.getComputedStyle(a)),c=a.getBoundingClientRect();return{w:c.right-c.left+b.w,h:c.bottom-c.top+b.h}};f.normalizeEvent=function(a){if(!("layerX"in a))a.layerX=a.offsetX,a.layerY=
a.offsetY;if(!i("dom-addeventlistener")){var d=a.target,d=d&&d.ownerDocument||document,d=i("quirks")?d.body:d.documentElement,b=f.getIeDocumentElementOffset();a.pageX=a.clientX+f.fixIeBiDiScrollLeft(d.scrollLeft||0)-b.x;a.pageY=a.clientY+(d.scrollTop||0)-b.y}};return f});