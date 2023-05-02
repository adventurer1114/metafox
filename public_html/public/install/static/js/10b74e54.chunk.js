"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-ui-SmartDataGrid-CellCheckbox"],{49960:function(e,t,o){o.d(t,{Z:function(){return R}});var n=o(63366),r=o(87462),a=o(67294),c=o(86010),i=o(94780),l=o(41796),d=o(69317),s=o(54235),u=o(85893),p=(0,s.Z)((0,u.jsx)("path",{d:"M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"}),"CheckBoxOutlineBlank"),h=(0,s.Z)((0,u.jsx)("path",{d:"M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"}),"CheckBox"),m=(0,s.Z)((0,u.jsx)("path",{d:"M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2z"}),"IndeterminateCheckBox"),f=o(36622),v=o(78884),Z=o(81719),k=o(1588),b=o(34867);function x(e){return(0,b.Z)("MuiCheckbox",e)}let C=(0,k.Z)("MuiCheckbox",["root","checked","disabled","indeterminate","colorPrimary","colorSecondary"]),g=["checkedIcon","color","icon","indeterminate","indeterminateIcon","inputProps","size","className"],y=e=>{let{classes:t,indeterminate:o,color:n}=e,a={root:["root",o&&"indeterminate",`color${(0,f.Z)(n)}`]},c=(0,i.Z)(a,x,t);return(0,r.Z)({},t,c)},z=(0,Z.ZP)(d.Z,{shouldForwardProp:e=>(0,Z.FO)(e)||"classes"===e,name:"MuiCheckbox",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:o}=e;return[t.root,o.indeterminate&&t.indeterminate,"default"!==o.color&&t[`color${(0,f.Z)(o.color)}`]]}})(({theme:e,ownerState:t})=>(0,r.Z)({color:(e.vars||e).palette.text.secondary},!t.disableRipple&&{"&:hover":{backgroundColor:e.vars?`rgba(${"default"===t.color?e.vars.palette.action.activeChannel:e.vars.palette.primary.mainChannel} / ${e.vars.palette.action.hoverOpacity})`:(0,l.Fq)("default"===t.color?e.palette.action.active:e.palette[t.color].main,e.palette.action.hoverOpacity),"@media (hover: none)":{backgroundColor:"transparent"}}},"default"!==t.color&&{[`&.${C.checked}, &.${C.indeterminate}`]:{color:(e.vars||e).palette[t.color].main},[`&.${C.disabled}`]:{color:(e.vars||e).palette.action.disabled}})),S=(0,u.jsx)(h,{}),w=(0,u.jsx)(p,{}),B=(0,u.jsx)(m,{}),P=a.forwardRef(function(e,t){var o,i;let l=(0,v.Z)({props:e,name:"MuiCheckbox"}),{checkedIcon:d=S,color:s="primary",icon:p=w,indeterminate:h=!1,indeterminateIcon:m=B,inputProps:f,size:Z="medium",className:k}=l,b=(0,n.Z)(l,g),x=h?m:p,C=h?m:d,P=(0,r.Z)({},l,{color:s,indeterminate:h,size:Z}),R=y(P);return(0,u.jsx)(z,(0,r.Z)({type:"checkbox",inputProps:(0,r.Z)({"data-indeterminate":h},f),icon:a.cloneElement(x,{fontSize:null!=(o=x.props.fontSize)?o:Z}),checkedIcon:a.cloneElement(C,{fontSize:null!=(i=C.props.fontSize)?i:Z}),ownerState:P,ref:t,className:(0,c.default)(R.root,k)},b,{classes:R}))});var R=P},69317:function(e,t,o){o.d(t,{Z:function(){return g}});var n=o(63366),r=o(87462),a=o(67294),c=o(86010),i=o(94780),l=o(36622),d=o(81719),s=o(42293),u=o(59711),p=o(45355),h=o(1588),m=o(34867);function f(e){return(0,m.Z)("PrivateSwitchBase",e)}(0,h.Z)("PrivateSwitchBase",["root","checked","disabled","input","edgeStart","edgeEnd"]);var v=o(85893);let Z=["autoFocus","checked","checkedIcon","className","defaultChecked","disabled","disableFocusRipple","edge","icon","id","inputProps","inputRef","name","onBlur","onChange","onFocus","readOnly","required","tabIndex","type","value"],k=e=>{let{classes:t,checked:o,disabled:n,edge:r}=e,a={root:["root",o&&"checked",n&&"disabled",r&&`edge${(0,l.Z)(r)}`],input:["input"]};return(0,i.Z)(a,f,t)},b=(0,d.ZP)(p.Z)(({ownerState:e})=>(0,r.Z)({padding:9,borderRadius:"50%"},"start"===e.edge&&{marginLeft:"small"===e.size?-3:-12},"end"===e.edge&&{marginRight:"small"===e.size?-3:-12})),x=(0,d.ZP)("input")({cursor:"inherit",position:"absolute",opacity:0,width:"100%",height:"100%",top:0,left:0,margin:0,padding:0,zIndex:1}),C=a.forwardRef(function(e,t){let{autoFocus:o,checked:a,checkedIcon:i,className:l,defaultChecked:d,disabled:p,disableFocusRipple:h=!1,edge:m=!1,icon:f,id:C,inputProps:g,inputRef:y,name:z,onBlur:S,onChange:w,onFocus:B,readOnly:P,required:R,tabIndex:F,type:I,value:j}=e,$=(0,n.Z)(e,Z),[M,O]=(0,s.Z)({controlled:a,default:Boolean(d),name:"SwitchBase",state:"checked"}),E=(0,u.Z)(),H=e=>{B&&B(e),E&&E.onFocus&&E.onFocus(e)},N=e=>{S&&S(e),E&&E.onBlur&&E.onBlur(e)},V=e=>{if(e.nativeEvent.defaultPrevented)return;let t=e.target.checked;O(t),w&&w(e,t)},_=p;E&&void 0===_&&(_=E.disabled);let q=(0,r.Z)({},e,{checked:M,disabled:_,disableFocusRipple:h,edge:m}),L=k(q);return(0,v.jsxs)(b,(0,r.Z)({component:"span",className:(0,c.default)(L.root,l),centerRipple:!0,focusRipple:!h,disabled:_,tabIndex:null,role:void 0,onFocus:H,onBlur:N,ownerState:q,ref:t},$,{children:[(0,v.jsx)(x,(0,r.Z)({autoFocus:o,checked:a,defaultChecked:d,className:L.input,disabled:_,id:("checkbox"===I||"radio"===I)&&C,name:z,onChange:V,readOnly:P,ref:y,required:R,ownerState:q,tabIndex:F,type:I},"checkbox"===I&&void 0===j?{}:{value:j},g)),M?i:f]}))});var g=C},98629:function(e,t,o){o.r(t);var n=o(49960),r=o(67294),a=o(10999);t.default=function({selected:e,id:t}){let{apiRef:o}=(0,a.Z)(),c=()=>o.current.toggleSelect(t);return r.createElement(n.Z,{size:"medium",checked:e,color:"primary",onClick:c})}},10999:function(e,t,o){o.d(t,{Z:function(){return a}});var n=o(67294);let r=(0,n.createContext)(void 0);function a(){return(0,n.useContext)(r)}}}]);