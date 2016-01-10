//>>built
define("dojox/charting/action2d/MoveSlice",["dojo/_base/connect","dojo/_base/declare","./PlotAction","dojo/fx/easing","dojox/gfx/matrix","dojox/gfx/fx","dojox/lang/functional","dojox/lang/functional/scan","dojox/lang/functional/fold"],function(l,f,m,n,g,o,d){return f("dojox.charting.action2d.MoveSlice",m,{defaultParams:{duration:400,easing:n.backOut,scale:1.05,shift:7},optionalParams:{},constructor:function(a,d,b){b||(b={});this.scale=typeof b.scale=="number"?b.scale:1.05;this.shift=typeof b.shift==
"number"?b.shift:7;this.connect()},process:function(a){if(a.shape&&a.element=="slice"&&a.type in this.overOutEvents){if(!this.angles){var e=g._degToRad(a.plot.opt.startAngle);this.angles=typeof a.run.data[0]=="number"?d.map(d.scanl(a.run.data,"+",e),"* 2 * Math.PI / this",d.foldl(a.run.data,"+",0)):d.map(d.scanl(a.run.data,"a + b.y",e),"* 2 * Math.PI / this",d.foldl(a.run.data,"a + b.y",0))}var b=a.index,c,h,i,j,k;c=(this.angles[b]+this.angles[b+1])/2;var e=g.rotateAt(-c,a.cx,a.cy),f=g.rotateAt(c,
a.cx,a.cy);(c=this.anim[b])?c.action.stop(!0):this.anim[b]=c={};a.type=="onmouseover"?(j=0,k=this.shift,h=1,i=this.scale):(j=this.shift,k=0,h=this.scale,i=1);c.action=o.animateTransform({shape:a.shape,duration:this.duration,easing:this.easing,transform:[f,{name:"translate",start:[j,0],end:[k,0]},{name:"scaleAt",start:[h,a.cx,a.cy],end:[i,a.cx,a.cy]},e]});a.type=="onmouseout"&&l.connect(c.action,"onEnd",this,function(){delete this.anim[b]});c.action.play()}},reset:function(){delete this.angles}})});