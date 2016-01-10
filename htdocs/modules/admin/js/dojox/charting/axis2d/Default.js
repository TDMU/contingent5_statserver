//>>built
define("dojox/charting/axis2d/Default",["dojo/_base/lang","dojo/_base/array","dojo/_base/sniff","dojo/_base/declare","dojo/_base/connect","dojo/_base/html","dojo/dom-geometry","./Invisible","../scaler/common","../scaler/linear","./common","dojox/gfx","dojox/lang/utils"],function(v,E,J,K,y,B,R,O,P,Q,F,p,C){return K("dojox.charting.axis2d.Default",O,{defaultParams:{vertical:!1,fixUpper:"none",fixLower:"none",natural:!1,leftBottom:!0,includeZero:!1,fixed:!0,majorLabels:!0,minorTicks:!0,minorLabels:!0,
microTicks:!1,rotation:0,htmlLabels:!0,enableCache:!1},optionalParams:{min:0,max:1,from:0,to:1,majorTickStep:4,minorTickStep:2,microTickStep:1,labels:[],labelFunc:null,maxLabelSize:0,maxLabelCharCount:0,trailingSymbol:null,stroke:{},majorTick:{},minorTick:{},microTick:{},tick:{},font:"",fontColor:"",title:"",titleGap:0,titleFont:"",titleFontColor:"",titleOrientation:""},constructor:function(f,a){this.opt=v.clone(this.defaultParams);C.updateWithObject(this.opt,a);C.updateWithPattern(this.opt,a,this.optionalParams);
if(this.opt.enableCache)this._textFreePool=[],this._lineFreePool=[],this._textUsePool=[],this._lineUsePool=[]},getOffsets:function(){var f=this.scaler,a={l:0,r:0,t:0,b:0};if(!f)return a;var c=this.opt,b=0,d=P.getNumericLabel,i=f.major,f=f.minor,l=this.chart.theme.axis,k=c.font||l.majorTick&&l.majorTick.font||l.tick&&l.tick.font,m=c.titleFont||l.tick&&l.tick.titleFont,l=c.titleGap==0?0:c.titleGap||l.tick&&l.tick.titleGap||15,q=this.chart.theme.getTick("major",c),r=this.chart.theme.getTick("minor",
c),e=k?p.normalizedLength(p.splitFontString(k).size):0,m=m?p.normalizedLength(p.splitFontString(m).size):0,j=c.rotation%360,h=c.leftBottom,g=Math.abs(Math.cos(j*Math.PI/180)),n=Math.abs(Math.sin(j*Math.PI/180));this.trailingSymbol=c.trailingSymbol===void 0||c.trailingSymbol===null?this.trailingSymbol:c.trailingSymbol;j<0&&(j+=360);if(e){b=this.labels?this._groupLabelWidth(this.labels,k,c.maxLabelCharCount):this._groupLabelWidth([d(i.start,i.prec,c),d(i.start+i.count*i.tick,i.prec,c),d(f.start,f.prec,
c),d(f.start+f.count*f.tick,f.prec,c)],k,c.maxLabelCharCount);b=c.maxLabelSize?Math.min(c.maxLabelSize,b):b;if(this.vertical)switch(d=h?"l":"r",j){case 0:case 180:a[d]=b;a.t=a.b=e/2;break;case 90:case 270:a[d]=e;a.t=a.b=b/2;break;default:j<=45||180<j&&j<=225?(a[d]=e*n/2+b*g,a[h?"t":"b"]=e*g/2+b*n,a[h?"b":"t"]=e*g/2):j>315||180>j&&j>135?(a[d]=e*n/2+b*g,a[h?"b":"t"]=e*g/2+b*n,a[h?"t":"b"]=e*g/2):j<90||180<j&&j<270?(a[d]=e*n+b*g,a[h?"t":"b"]=e*g+b*n):(a[d]=e*n+b*g,a[h?"b":"t"]=e*g+b*n)}else switch(d=
h?"b":"t",j){case 0:case 180:a[d]=e;a.l=a.r=b/2;break;case 90:case 270:a[d]=b;a.l=a.r=e/2;break;default:45<=j&&j<=90||225<=j&&j<=270?(a[d]=e*n/2+b*g,a[h?"r":"l"]=e*g/2+b*n,a[h?"l":"r"]=e*g/2):90<=j&&j<=135||270<=j&&j<=315?(a[d]=e*n/2+b*g,a[h?"l":"r"]=e*g/2+b*n,a[h?"r":"l"]=e*g/2):j<45||180<j&&j<135?(a[d]=e*n+b*g,a[h?"r":"l"]=e*g+b*n):(a[d]=e*n+b*g,a[h?"l":"r"]=e*g+b*n)}a[d]+=4+Math.max(q.length,r.length)+(c.title?m+l:0)}if(b)this._cachedLabelWidth=b;return a},cleanGroup:function(){if(this.opt.enableCache&&
this.group)this._lineFreePool=this._lineFreePool.concat(this._lineUsePool),this._lineUsePool=[],this._textFreePool=this._textFreePool.concat(this._textUsePool),this._textUsePool=[];this.inherited(arguments)},createText:function(f,a,c,b,d,i,l,k,m){if(!this.opt.enableCache||f=="html")return F.createText[f](this.chart,a,c,b,d,i,l,k,m);this._textFreePool.length>0?(f=this._textFreePool.pop(),f.setShape({x:c,y:b,text:i,align:d}),a.add(f)):f=F.createText[f](this.chart,a,c,b,d,i,l,k,m);this._textUsePool.push(f);
return f},createLine:function(f,a){var c;this.opt.enableCache&&this._lineFreePool.length>0?(c=this._lineFreePool.pop(),c.setShape(a),f.add(c)):c=f.createLine(a);this.opt.enableCache&&this._lineUsePool.push(c);return c},render:function(f,a){if(!this.dirty)return this;var c=this.opt,b=this.chart.theme.axis,d=c.leftBottom,i=c.rotation%360,l,k,m=0,q,r,e,j,h,g,n=c.font||b.majorTick&&b.majorTick.font||b.tick&&b.tick.font,v=c.titleFont||b.tick&&b.tick.titleFont,y=c.fontColor||b.majorTick&&b.majorTick.fontColor||
b.tick&&b.tick.fontColor||"black",B=c.titleFontColor||b.tick&&b.tick.titleFontColor||"black";q=c.titleGap==0?0:c.titleGap||b.tick&&b.tick.titleGap||15;var m=c.titleOrientation||b.tick&&b.tick.titleOrientation||"axis",z=this.chart.theme.getTick("major",c),A=this.chart.theme.getTick("minor",c),G=this.chart.theme.getTick("micro",c),C="stroke"in c?c.stroke:b.stroke,o=n?p.normalizedLength(p.splitFontString(n).size):0;e=Math.abs(Math.cos(i*Math.PI/180));var L=Math.abs(Math.sin(i*Math.PI/180)),x=v?p.normalizedLength(p.splitFontString(v).size):
0;i<0&&(i+=360);if(this.vertical){l={y:f.height-a.b};b={y:a.t};k={y:(f.height-a.b+a.t)/2};q=o*L+(this._cachedLabelWidth||0)*e+4+Math.max(z.length,A.length)+x+q;r={x:0,y:-1};h={x:0,y:0};e={x:1,y:0};j={x:4,y:0};switch(i){case 0:g="end";h.y=o*0.4;break;case 90:g="middle";h.x=-o;break;case 180:g="start";h.y=-o*0.4;break;case 270:g="middle";break;default:i<45?(g="end",h.y=o*0.4):i<90?(g="end",h.y=o*0.4):i<135?g="start":i<225?(g="start",h.y=-o*0.4):i<270?(g="start",h.x=d?0:o*0.4):i<315?(g="end",h.x=d?0:
o*0.4):(g="end",h.y=o*0.4)}if(d)l.x=b.x=a.l,m=m&&m=="away"?90:270,k.x=a.l-q+(m==270?x:0),e.x=-1,j.x=-j.x;else switch(l.x=b.x=f.width-a.r,m=m&&m=="axis"?90:270,k.x=f.width-a.r+q-(m==270?0:x),g){case "start":g="end";break;case "end":g="start";break;case "middle":h.x+=o}}else{l={x:a.l};b={x:f.width-a.r};k={x:(f.width-a.r+a.l)/2};q=o*e+(this._cachedLabelWidth||0)*L+4+Math.max(z.length,A.length)+x+q;r={x:1,y:0};h={x:0,y:0};e={x:0,y:1};j={x:0,y:4};switch(i){case 0:g="middle";h.y=o;break;case 90:g="start";
h.x=-o*0.4;break;case 180:g="middle";break;case 270:g="end";h.x=o*0.4;break;default:i<45?(g="start",h.y=d?o:0):i<135?(g="start",h.x=-o*0.4):i<180?(g="start",h.y=d?0:-o):i<225?(g="end",h.y=d?0:-o):i<315?(g="end",h.y=d?o*0.4:0):(g="end",h.y=d?o:0)}if(d)l.y=b.y=f.height-a.b,m=m&&m=="axis"?180:0,k.y=f.height-a.b+q-(m?x:0);else switch(l.y=b.y=a.t,m=m&&m=="away"?180:0,k.y=a.t-q+(m?0:x),e.y=-1,j.y=-j.y,g){case "start":g="end";break;case "end":g="start";break;case "middle":h.y-=o}}this.cleanGroup();try{var w=
this.group,H=this.scaler,D=this.ticks,M,I=Q.getTransformerFromModel(this.scaler),s=(!c.title||!m)&&!i&&this.opt.htmlLabels&&!J("ie")&&!J("opera")?"html":"gfx",t=e.x*z.length,u=e.y*z.length;w.createLine({x1:l.x,y1:l.y,x2:b.x,y2:b.y}).setStroke(C);if(c.title){var N=F.createText[s](this.chart,w,k.x,k.y,"middle",c.title,v,B);s=="html"?this.htmlElements.push(N):N.setTransform(p.matrix.rotategAt(m,k.x,k.y))}if(D==null)return this.dirty=!1,this;E.forEach(D.major,function(a){var b=I(a.value),f=l.x+r.x*b,
d=l.y+r.y*b;this.createLine(w,{x1:f,y1:d,x2:f+t,y2:d+u}).setStroke(z);if(a.label){var e=c.maxLabelCharCount?this.getTextWithLimitCharCount(a.label,n,c.maxLabelCharCount):{text:a.label,truncated:!1},e=c.maxLabelSize?this.getTextWithLimitLength(e.text,n,c.maxLabelSize,e.truncated):e,b=this.createText(s,w,f+t+j.x+(i?0:h.x),d+u+j.y+(i?0:h.y),g,e.text,n,y);this.chart.truncateBidi&&e.truncated&&this.chart.truncateBidi(b,a.label,s);e.truncated&&this.labelTooltip(b,this.chart,a.label,e.text,n,s);s=="html"?
this.htmlElements.push(b):i&&b.setTransform([{dx:h.x,dy:h.y},p.matrix.rotategAt(i,f+t+j.x,d+u+j.y)])}},this);t=e.x*A.length;u=e.y*A.length;M=H.minMinorStep<=H.minor.tick*H.bounds.scale;E.forEach(D.minor,function(a){var b=I(a.value),e=l.x+r.x*b,f=l.y+r.y*b;this.createLine(w,{x1:e,y1:f,x2:e+t,y2:f+u}).setStroke(A);if(M&&a.label){var d=c.maxLabelCharCount?this.getTextWithLimitCharCount(a.label,n,c.maxLabelCharCount):{text:a.label,truncated:!1},d=c.maxLabelSize?this.getTextWithLimitLength(d.text,n,c.maxLabelSize,
d.truncated):d,b=this.createText(s,w,e+t+j.x+(i?0:h.x),f+u+j.y+(i?0:h.y),g,d.text,n,y);this.chart.getTextDir&&d.truncated&&this.chart.truncateBidi(b,a.label,s);d.truncated&&this.labelTooltip(b,this.chart,a.label,d.text,n,s);s=="html"?this.htmlElements.push(b):i&&b.setTransform([{dx:h.x,dy:h.y},p.matrix.rotategAt(i,e+t+j.x,f+u+j.y)])}},this);t=e.x*G.length;u=e.y*G.length;E.forEach(D.micro,function(a){var b=I(a.value),a=l.x+r.x*b,b=l.y+r.y*b;this.createLine(w,{x1:a,y1:b,x2:a+t,y2:b+u}).setStroke(G)},
this)}catch(K){}this.dirty=!1;return this},labelTooltip:function(f,a,c,b,d,i){var l=["dijit/Tooltip"],k={type:"rect"},m=["above","below"],b=p._base._getTextBox(b,{font:d}).w||0,d=d?p.normalizedLength(p.splitFontString(d).size):0;i=="html"?(v.mixin(k,B.coords(f.firstChild,!0)),k.width=Math.ceil(b),k.height=Math.ceil(d),this._events.push({shape:dojo,handle:y.connect(f.firstChild,"onmouseover",this,function(){require(l,function(a){a.show(c,k,m)})})}),this._events.push({shape:dojo,handle:y.connect(f.firstChild,
"onmouseout",this,function(){require(l,function(a){a.hide(k)})})})):(i=f.getShape(),a=B.coords(a.node,!0),k=v.mixin(k,{x:i.x-b/2,y:i.y}),k.x+=a.x,k.y+=a.y,k.x=Math.round(k.x),k.y=Math.round(k.y),k.width=Math.ceil(b),k.height=Math.ceil(d),this._events.push({shape:f,handle:f.connect("onmouseenter",this,function(){require(l,function(a){a.show(c,k,m)})})}),this._events.push({shape:f,handle:f.connect("onmouseleave",this,function(){require(l,function(a){a.hide(k)})})}))}})});