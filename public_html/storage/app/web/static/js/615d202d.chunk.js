"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-core-components-NoContentWithIcon"],{70700:function(e,t,n){n.r(t),n.d(t,{default:function(){return m}});var o=n(85597),i=n(77029),a=n(21822),l=n(22410),c=n(73327),r=n(67294);let s=(0,l.Z)(e=>(0,c.Z)({root:{display:"flex",flexDirection:"column",justifyContent:"center",padding:e.spacing(0,2),alignItems:"center",marginTop:e.spacing(11.25)},icon:{fontSize:e.mixins.pxToRem(72),color:e.palette.text.secondary,marginBottom:e.spacing(4)},title:{fontSize:e.mixins.pxToRem(24),fontWeight:e.typography.fontWeightBold,marginBottom:e.spacing(1.5),textAlign:"center",[e.breakpoints.down("xs")]:{fontSize:e.mixins.pxToRem(18)}},content:{fontSize:e.mixins.pxToRem(18),color:e.palette.text.secondary,textAlign:"center",[e.breakpoints.down("xs")]:{fontSize:e.mixins.pxToRem(15)}}}),{name:"NoContentWithIcon"});function m({image:e,title:t,description:n,labelButton:l,prev_identity:c,action:m}){var d,p,u;let f=s(),{i18n:x,usePageParams:g,dispatch:v}=(0,o.OgA)(),y=(0,o.THL)(),h=g(),b=(0,o.oRt)("core","primaryMenu"),z=(null==h?void 0:null===(d=h.heading)||void 0===d?void 0:null===(p=d.props)||void 0===p?void 0:p.identity)||`${c}${null==h?void 0:h.id}`,N=e||(null===(u=b.items.find(e=>{var t;return(null===(t=e.to)||void 0===t?void 0:t.split("/")[1])===y.pathname.split("/")[1]}))||void 0===u?void 0:u.icon)||"ico-user-circle-o",k=h.appName||h.resourceName,E=t||`no${(null==h?void 0:h.tab)?`_${null==h?void 0:h.tab}`:""}_${k}_found`,T=()=>{v({type:m,payload:{identity:z}})};return r.createElement("div",{className:f.root},r.createElement(i.zb,{className:f.icon,icon:N}),r.createElement("div",{className:f.title},r.createElement("span",null,x.formatMessage({id:E}))),n?r.createElement("div",{className:f.content},x.formatMessage({id:n})):null,l&&r.createElement(a.Z,{variant:"contained",color:"primary",startIcon:r.createElement(i.zb,{icon:"ico-plus"}),sx:{fontSize:18,mt:2.5},onClick:T},x.formatMessage({id:l})))}}}]);