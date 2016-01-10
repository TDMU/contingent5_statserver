//>>built
define("dojox/charting/plot2d/StackedColumns",["dojo/_base/lang","dojo/_base/array","dojo/_base/declare","./Columns","./common","dojox/lang/functional","dojox/lang/functional/reversed","dojox/lang/functional/sequence"],function(z,w,j,k,p,q,x){var y=x.lambda("item.purgeGroup()");return j("dojox.charting.plot2d.StackedColumns",k,{getSeriesStats:function(){var f=p.collectStackedStats(this.series);this._maxRunLength=f.hmax;f.hmin-=0.5;f.hmax+=0.5;return f},render:function(f,g){if(this._maxRunLength<=
0)return this;for(var h=q.repeat(this._maxRunLength,"-> 0",0),e=0;e<this.series.length;++e)for(var c=this.series[e],b=0;b<c.data.length;++b){var a=c.data[b];if(a!==null){var d=typeof a=="number"?a:a.y;isNaN(d)&&(d=0);h[b]+=d}}if(this.zoom&&!this.isDataDirty())return this.performZoom(f,g);this.resetEvents();if(this.dirty=this.isDirty()){w.forEach(this.series,y);this._eventSeries={};this.cleanGroup();var l=this.group;q.forEachRev(this.series,function(a){a.cleanGroup(l)})}var i=this.chart.theme,r,m,
j=this._hScaler.scaler.getTransformerFromModel(this._hScaler),s=this._vScaler.scaler.getTransformerFromModel(this._vScaler),k=this.events(),e=p.calculateBarSize(this._hScaler.bounds.scale,this.opt);r=e.gap;m=e.size;for(e=this.series.length-1;e>=0;--e)if(c=this.series[e],!this.dirty&&!c.dirty)i.skip(),this._reconnectEvents(c.name);else{c.cleanGroup();for(var t=i.next("column",[this.opt,c]),l=c.group,u=Array(h.length),b=0;b<h.length;++b)if(a=c.data[b],a!==null){var d=h[b],n=s(d),a=typeof a!="number"?
i.addMixin(t,"column",a,!0):i.post(t,"column");if(m>=1&&n>=0){var v={x:g.l+j(b+0.5)+r,y:f.height-g.b-s(d),width:m,height:n},o=this._plotFill(a.series.fill,f,g),o=this._shapeFill(o,v),a=l.createRect(v).setFill(o).setStroke(a.series.stroke);c.dyn.fill=a.getFill();c.dyn.stroke=a.getStroke();k&&(d={element:"column",index:b,run:c,shape:a,x:b+0.5,y:d},this._connectEvents(d),u[b]=d);this.animate&&this._animateColumn(a,f.height-g.b,n)}}this._eventSeries[c.name]=u;c.dirty=!1;for(b=0;b<c.data.length;++b)a=
c.data[b],a!==null&&(d=typeof a=="number"?a:a.y,isNaN(d)&&(d=0),h[b]-=d)}this.dirty=!1;return this}})});