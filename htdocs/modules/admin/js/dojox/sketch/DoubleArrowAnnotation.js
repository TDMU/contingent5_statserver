//>>built
define("dojox/sketch/DoubleArrowAnnotation",["dojo/_base/kernel","dojo/_base/lang","./Annotation","./Anchor"],function(c){c.getObject("sketch",!0,dojox);var d=dojox.sketch;console.log(d);d.DoubleArrowAnnotation=function(a,b){d.Annotation.call(this,a,b);this.transform={dx:0,dy:0};this.start={x:0,y:0};this.control={x:100,y:-50};this.end={x:200,y:0};this.textPosition={x:0,y:0};this.textOffset=6;this.textYOffset=10;this.textAlign="middle";this.endRotation=this.startRotation=0;this.endArrowGroup=this.endArrow=
this.startArrowGroup=this.startArrow=this.pathShape=this.labelShape=null;this.anchors.start=new d.Anchor(this,"start");this.anchors.control=new d.Anchor(this,"control");this.anchors.end=new d.Anchor(this,"end")};d.DoubleArrowAnnotation.prototype=new d.Annotation;c=d.DoubleArrowAnnotation.prototype;c.constructor=d.DoubleArrowAnnotation;c.type=function(){return"DoubleArrow"};c.getType=function(){return d.DoubleArrowAnnotation};c._rot=function(){var a=this.control.y-this.start.y,b=this.control.x-this.start.x;
this.startRotation=Math.atan2(a,b);a=this.end.y-this.control.y;b=this.end.x-this.control.x;this.endRotation=Math.atan2(a,b)};c._pos=function(){var a=this.textOffset;this.control.y<this.end.y?a*=-1:a+=this.textYOffset;var b={x:(this.control.x-this.start.x)*0.5+this.start.x,y:(this.control.y-this.start.y)*0.5+this.start.y},e={x:(this.end.x-this.control.x)*0.5+this.control.x,y:(this.end.y-this.control.y)*0.5+this.control.y};this.textPosition={x:(e.x-b.x)*0.5+b.x,y:(e.y-b.y)*0.5+b.y+a}};c.apply=function(a){if(a){if(a.documentElement)a=
a.documentElement;this.readCommonAttrs(a);for(var b=0;b<a.childNodes.length;b++){var e=a.childNodes[b];if(e.localName=="text")this.property("label",e.childNodes.length?e.childNodes[0].nodeValue:"");else if(e.localName=="path"){var c=e.getAttribute("d").split(" "),d=c[0].split(",");this.start.x=parseFloat(d[0].substr(1),10);this.start.y=parseFloat(d[1],10);d=c[1].split(",");this.control.x=parseFloat(d[0].substr(1),10);this.control.y=parseFloat(d[1],10);d=c[2].split(",");this.end.x=parseFloat(d[0],
10);this.end.y=parseFloat(d[1],10);c=this.property("stroke");e=e.getAttribute("style");if(d=e.match(/stroke:([^;]+);/))c.color=d[1],this.property("fill",d[1]);if(d=e.match(/stroke-width:([^;]+);/))c.width=d[1];this.property("stroke",c)}}}};c.initialize=function(a){this.apply(a);this._rot();this._pos();var b=this.startRotation,a=dojox.gfx.matrix.rotate(b),b=this.endRotation,b=dojox.gfx.matrix.rotateAt(b,this.end.x,this.end.y);this.shape=this.figure.group.createGroup();this.shape.getEventSource().setAttribute("id",
this.id);this.pathShape=this.shape.createPath("M"+this.start.x+" "+this.start.y+"Q"+this.control.x+" "+this.control.y+" "+this.end.x+" "+this.end.y+" l0,0");this.startArrowGroup=this.shape.createGroup().setTransform({dx:this.start.x,dy:this.start.y});this.startArrowGroup.applyTransform(a);this.startArrow=this.startArrowGroup.createPath();this.endArrowGroup=this.shape.createGroup().setTransform(b);this.endArrow=this.endArrowGroup.createPath();this.labelShape=this.shape.createText({x:this.textPosition.x,
y:this.textPosition.y,text:this.property("label"),align:this.textAlign}).setFill(this.property("fill"));this.labelShape.getEventSource().setAttribute("id",this.id+"-labelShape");this.draw()};c.destroy=function(){if(this.shape)this.startArrowGroup.remove(this.startArrow),this.endArrowGroup.remove(this.endArrow),this.shape.remove(this.startArrowGroup),this.shape.remove(this.endArrowGroup),this.shape.remove(this.pathShape),this.shape.remove(this.labelShape),this.figure.group.remove(this.shape),this.shape=
this.pathShape=this.labelShape=this.startArrowGroup=this.startArrow=this.endArrowGroup=this.endArrow=null};c.draw=function(a){this.apply(a);this._rot();this._pos();var b=this.startRotation,a=dojox.gfx.matrix.rotate(b),b=this.endRotation,b=dojox.gfx.matrix.rotateAt(b,this.end.x,this.end.y);this.shape.setTransform(this.transform);this.pathShape.setShape("M"+this.start.x+" "+this.start.y+" Q"+this.control.x+" "+this.control.y+" "+this.end.x+" "+this.end.y+" l0,0");this.startArrowGroup.setTransform({dx:this.start.x,
dy:this.start.y}).applyTransform(a);this.startArrow.setFill(this.property("fill"));this.endArrowGroup.setTransform(b);this.endArrow.setFill(this.property("fill"));this.labelShape.setShape({x:this.textPosition.x,y:this.textPosition.y,text:this.property("label")}).setFill(this.property("fill"));this.zoom()};c.zoom=function(a){if(this.startArrow){a=a||this.figure.zoomFactor;d.Annotation.prototype.zoom.call(this,a);var b=a>1?20:Math.floor(20/a),c=a>1?5:Math.floor(5/a),a=a>1?3:Math.floor(3/a);this.startArrow.setShape("M0,0 l"+
b+",-"+c+" -"+a+","+c+" "+a+","+c+" Z");this.endArrow.setShape("M"+this.end.x+","+this.end.y+" l-"+b+",-"+c+" "+a+","+c+" -"+a+","+c+" Z")}};c.getBBox=function(){var a=Math.min(this.start.x,this.control.x,this.end.x),b=Math.min(this.start.y,this.control.y,this.end.y);return{x:a,y:b,width:Math.max(this.start.x,this.control.x,this.end.x)-a,height:Math.max(this.start.y,this.control.y,this.end.y)-b}};c.serialize=function(){var a=this.property("stroke");return"<g "+this.writeCommonAttrs()+'><path style="stroke:'+
a.color+";stroke-width:"+a.width+';fill:none;" d="M'+this.start.x+","+this.start.y+" Q"+this.control.x+","+this.control.y+" "+this.end.x+","+this.end.y+'" /><g transform="translate('+this.start.x+","+this.start.y+") rotate("+Math.round(this.startRotation*(180/Math.PI)*Math.pow(10,4))/Math.pow(10,4)+')"><path style="fill:'+a.color+';" d="M0,0 l20,-5, -3,5, 3,5 Z" /></g><g transform="rotate('+Math.round(this.endRotation*(180/Math.PI)*Math.pow(10,4))/Math.pow(10,4)+", "+this.end.x+", "+this.end.y+')"><path style="fill:'+
a.color+';" d="M'+this.end.x+","+this.end.y+' l-20,-5, 3,5, -3,5 Z" /></g><text style="fill:'+a.color+";text-anchor:"+this.textAlign+'" font-weight="bold" x="'+this.textPosition.x+'" y="'+this.textPosition.y+'">'+this.property("label")+"</text></g>"};d.Annotation.register("DoubleArrow");return dojox.sketch.DoubleArrowAnnotation});