//>>built
define("dojox/embed/Object",["dojo/_base/kernel","dojo/_base/declare","dojo/dom-geometry","dijit/_Widget","./Flash","./Quicktime"],function(c,i,f,e,g,h){c.experimental("dojox.embed.Object");return c.declare("dojox.embed.Object",e,{width:0,height:0,src:"",movie:null,params:null,reFlash:/\.swf|\.flv/gi,reQtMovie:/\.3gp|\.avi|\.m4v|\.mov|\.mp4|\.mpg|\.mpeg|\.qt/gi,reQtAudio:/\.aiff|\.aif|\.m4a|\.m4b|\.m4p|\.midi|\.mid|\.mp3|\.mpa|\.wav/gi,postCreate:function(){if(!this.width||!this.height){var a=f.getMarginBox(this.domNode);
this.width=a.w;this.height=a.h}a=g;if(this.src.match(this.reQtMovie)||this.src.match(this.reQtAudio))a=h;if(!this.params&&(this.params={},this.domNode.hasAttributes()))for(var c={dojoType:"",width:"",height:"","class":"",style:"",id:"",src:""},d=this.domNode.attributes,b=0,e=d.length;b<e;b++)if(!c[d[b].name])this.params[d[b].name]=d[b].value;this.movie=new a({path:this.src,width:this.width,height:this.height,params:this.params},this.domNode)}})});