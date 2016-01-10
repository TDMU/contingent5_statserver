//>>built
define("dojox/gfx3d/scheduler",["dojo/_base/lang","dojo/_base/array","dojo/_base/declare","./_base","./vector"],function(g,f,j,d,e){d.scheduler={zOrder:function(a,b){b=b?b:d.scheduler.order;a.sort(function(a,h){return b(h)-b(a)});return a},bsp:function(a,b){var b=b?b:d.scheduler.outline,c=new d.scheduler.BinarySearchTree(a[0],b);f.forEach(a.slice(1),function(a){c.add(a,b)});return c.iterate(b)},order:function(a){return a.getZOrder()},outline:function(a){return a.getOutline()}};g=j("dojox.gfx3d.scheduler.BinarySearchTree",
null,{constructor:function(a,b){this.minus=this.plus=null;this.object=a;var c=b(a);this.orient=c[0];this.normal=e.normalize(c)},add:function(a,b){var c=b(a),h=this.normal,g=this.orient,i=d.scheduler.BinarySearchTree;if(f.every(c,function(a){return Math.floor(0.5+e.dotProduct(h,e.substract(a,g)))<=0}))this.minus?this.minus.add(a,b):this.minus=new i(a,b);else if(f.every(c,function(a){return Math.floor(0.5+e.dotProduct(h,e.substract(a,g)))>=0}))this.plus?this.plus.add(a,b):this.plus=new i(a,b);else throw"The case: polygon cross siblings' plate is not implemented yet";
},iterate:function(){var a=[],b=null,b=Math.floor(0.5+e.dotProduct(this.normal,e.substract({x:0,y:0,z:-1E4},this.orient)))<=0?[this.plus,this.minus]:[this.minus,this.plus];b[0]&&(a=a.concat(b[0].iterate()));a.push(this.object);b[1]&&(a=a.concat(b[1].iterate()));return a}});d.drawer={conservative:function(a,b,c){f.forEach(this.objects,function(a){a.destroy()});f.forEach(b,function(a){a.draw(c.lighting)})},chart:function(a,b,c){f.forEach(this.todos,function(a){a.draw(c.lighting)})}};return{scheduler:d.scheduler,
drawer:d.drawer,BinarySearchTree:g}});