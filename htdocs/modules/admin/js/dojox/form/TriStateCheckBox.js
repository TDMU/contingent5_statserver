//>>built
require({cache:{"url:dojox/form/resources/TriStateCheckBox.html":'<div class="dijit dijitReset dijitInline" role="presentation"\n\t><div class="dojoxTriStateCheckBoxInner" dojoAttachPoint="stateLabelNode"></div\n\t><input ${!nameAttrSetting} type="${type}" dojoAttachPoint="focusNode"\n\tclass="dijitReset dojoxTriStateCheckBoxInput" dojoAttachEvent="onclick:_onClick"\n/></div>'}});
define("dojox/form/TriStateCheckBox",["dojo/_base/kernel","dojo/_base/declare","dojo/_base/array","dojo/_base/event","dojo/query","dojo/dom-attr","dojo/text!./resources/TriStateCheckBox.html","dijit/form/ToggleButton"],function(f,g,c,h,e,b,i,j){return g("dojox.form.TriStateCheckBox",j,{templateString:i,baseClass:"dojoxTriStateCheckBox",type:"checkbox",_currentState:0,_stateType:"False",readOnly:!1,constructor:function(){this.states=[!1,!0,"mixed"];this._stateLabels={False:"&#63219",True:"&#8730;",
Mixed:"&#8801"};this.stateValues={False:"off",True:"on",Mixed:"mixed"}},_setIconClassAttr:null,_setCheckedAttr:function(a,d){this._set("checked",a);this._currentState=c.indexOf(this.states,a);this._stateType=this._getStateType(a);b.set(this.focusNode||this.domNode,"checked",a);b.set(this.focusNode,"value",this.stateValues[this._stateType]);(this.focusNode||this.domNode).setAttribute("aria-checked",a);this._handleOnChange(a,d)},setChecked:function(a){f.deprecated("setChecked("+a+") is deprecated. Use set('checked',"+
a+") instead.","","2.0");this.set("checked",a)},_setReadOnlyAttr:function(a){this._set("readOnly",a);b.set(this.focusNode,"readOnly",a);this.focusNode.setAttribute("aria-readonly",a)},_setValueAttr:function(a,d){if(typeof a=="string"&&c.indexOf(this.states,a)<0)a==""&&(a="on"),this.stateValues.True=a,a=!0;if(this._created)this._currentState=c.indexOf(this.states,a),this.set("checked",a,d),b.set(this.focusNode,"value",this.stateValues[this._stateType])},_setValuesAttr:function(a){this.stateValues.True=
a[0]?a[0]:this.stateValues.True;this.stateValues.Mixed=a[1]?a[1]:this.stateValues.False},_getValueAttr:function(){return this.stateValues[this._stateType]},startup:function(){this.set("checked",this.params.checked||this.states[this._currentState]);b.set(this.stateLabelNode,"innerHTML",this._stateLabels[this._stateType]);this.inherited(arguments)},_fillContent:function(){},reset:function(){this._hasBeenBlurred=!1;this.stateValues={False:"off",True:"on",Mixed:"mixed"};this.set("checked",this.params.checked||
this.states[0])},_onFocus:function(){this.id&&e("label[for='"+this.id+"']").addClass("dijitFocusedLabel");this.inherited(arguments)},_onBlur:function(){this.id&&e("label[for='"+this.id+"']").removeClass("dijitFocusedLabel");this.inherited(arguments)},_onClick:function(a){if(this.readOnly||this.disabled)return h.stop(a),!1;this._currentState>=this.states.length-1?this._currentState=0:this._currentState++;this.set("checked",this.states[this._currentState]);b.set(this.stateLabelNode,"innerHTML",this._stateLabels[this._stateType]);
return this.onClick(a)},_getStateType:function(a){return a?a=="mixed"?"Mixed":"True":"False"}})});