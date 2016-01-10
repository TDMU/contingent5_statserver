//>>built
define("dojox/charting/plot2d/Stacked",["dojo/_base/lang","dojo/_base/declare","dojo/_base/array","./Default","./common","dojox/lang/functional","dojox/lang/functional/reversed","dojox/lang/functional/sequence"],function(x,o,l,p,m,u,q){var y=q.lambda("item.purgeGroup()");return o("dojox.charting.plot2d.Stacked",p,{getSeriesStats:function(){var h=m.collectStackedStats(this.series);this._maxRunLength=h.hmax;return h},render:function(h,i){if(this._maxRunLength<=0)return this;for(var r=u.repeat(this._maxRunLength,
"-> 0",0),j=0;j<this.series.length;++j)for(var c=this.series[j],f=0;f<c.data.length;++f){var a=c.data[f];a!==null&&(isNaN(a)&&(a=0),r[f]+=a)}if(this.zoom&&!this.isDataDirty())return this.performZoom(h,i);this.resetEvents();if(this.dirty=this.isDirty()){l.forEach(this.series,y);this._eventSeries={};this.cleanGroup();var e=this.group;u.forEachRev(this.series,function(a){a.cleanGroup(e)})}for(var v=this.chart.theme,o=this.events(),p=this._hScaler.scaler.getTransformerFromModel(this._hScaler),q=this._vScaler.scaler.getTransformerFromModel(this._vScaler),
j=this.series.length-1;j>=0;--j)if(c=this.series[j],!this.dirty&&!c.dirty)v.skip(),this._reconnectEvents(c.name);else{c.cleanGroup();var b=v.next(this.opt.areas?"area":"line",[this.opt,c],!0),e=c.group,g,d=l.map(r,function(a,c){return{x:p(c+1)+i.l,y:h.height-i.b-q(a)}},this),f=this.opt.tension?m.curve(d,this.opt.tension):"";if(this.opt.areas)a=x.clone(d),this.opt.tension?(a=m.curve(a,this.opt.tension),a+=" L"+d[d.length-1].x+","+(h.height-i.b)+" L"+d[0].x+","+(h.height-i.b)+" L"+d[0].x+","+d[0].y,
c.dyn.fill=e.createPath(a).setFill(b.series.fill).getFill()):(a.push({x:d[d.length-1].x,y:h.height-i.b}),a.push({x:d[0].x,y:h.height-i.b}),a.push(d[0]),c.dyn.fill=e.createPolyline(a).setFill(b.series.fill).getFill());if((this.opt.lines||this.opt.markers)&&b.series.outline)g=m.makeStroke(b.series.outline),g.width=2*g.width+b.series.stroke.width;if(this.opt.markers)c.dyn.marker=b.symbol;var n,s,t;if(b.series.shadow&&b.series.stroke){var k=b.series.shadow,a=l.map(d,function(a){return{x:a.x+k.dx,y:a.y+
k.dy}});if(this.opt.lines)c.dyn.shadow=this.opt.tension?e.createPath(m.curve(a,this.opt.tension)).setStroke(k).getStroke():e.createPolyline(a).setStroke(k).getStroke();if(this.opt.markers)k=b.marker.shadow,t=l.map(a,function(a){return e.createPath("M"+a.x+" "+a.y+" "+b.symbol).setStroke(k).setFill(k.color)},this)}if(this.opt.lines){if(g)c.dyn.outline=this.opt.tension?e.createPath(f).setStroke(g).getStroke():e.createPolyline(d).setStroke(g).getStroke();c.dyn.stroke=this.opt.tension?e.createPath(f).setStroke(b.series.stroke).getStroke():
e.createPolyline(d).setStroke(b.series.stroke).getStroke()}if(this.opt.markers){n=Array(d.length);s=Array(d.length);g=null;if(b.marker.outline)g=m.makeStroke(b.marker.outline),g.width=2*g.width+(b.marker.stroke?b.marker.stroke.width:0);l.forEach(d,function(a,c){var d="M"+a.x+" "+a.y+" "+b.symbol;g&&(s[c]=e.createPath(d).setStroke(g));n[c]=e.createPath(d).setStroke(b.marker.stroke).setFill(b.marker.fill)},this);if(o){var w=Array(n.length);l.forEach(n,function(a,b){var e={element:"marker",index:b,run:c,
shape:a,outline:s[b]||null,shadow:t&&t[b]||null,cx:d[b].x,cy:d[b].y,x:b+1,y:c.data[b]};this._connectEvents(e);w[b]=e},this);this._eventSeries[c.name]=w}else delete this._eventSeries[c.name]}c.dirty=!1;for(f=0;f<c.data.length;++f)a=c.data[f],a!==null&&(isNaN(a)&&(a=0),r[f]-=a)}this.dirty=!1;return this}})});