"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-core-components-NoContentWithIcon"],{70700:function(e,t,n){n.r(t),n.d(t,{default:function(){return d}});var o=n(85597),i=n(76224),a=n(21822),l=n(22410),c=n(73327),r=n(67294),s=n(86706);let m=(0,l.Z)(e=>(0,c.Z)({root:{display:"flex",flexDirection:"column",justifyContent:"center",padding:e.spacing(0,2),alignItems:"center",marginTop:e.spacing(11.25)},icon:{fontSize:e.mixins.pxToRem(72),color:e.palette.text.secondary,marginBottom:e.spacing(4)},title:{fontSize:e.mixins.pxToRem(24),fontWeight:e.typography.fontWeightBold,marginBottom:e.spacing(1.5),textAlign:"center",[e.breakpoints.down("xs")]:{fontSize:e.mixins.pxToRem(18)}},content:{fontSize:e.mixins.pxToRem(18),color:e.palette.text.secondary,textAlign:"center",[e.breakpoints.down("xs")]:{fontSize:e.mixins.pxToRem(15)}}}),{name:"NoContentWithIcon"});function d({image:e,title:t,description:n,labelButton:l,prev_identity:c,action:d}){var p,u,f;let x=m(),{i18n:g,usePageParams:v,dispatch:b}=(0,o.OgA)(),y=v(),h=(0,s.v9)(e=>(0,o.kjY)(e,y.appName,"sidebarMenu")),N=(null==y?void 0:null===(p=y.heading)||void 0===p?void 0:null===(u=p.props)||void 0===u?void 0:u.identity)||`${c}${null==y?void 0:y.id}`,k=e||(null===(f=h.items.find(e=>{return e.tab===(null==y?void 0:y.tab)}))||void 0===f?void 0:f.icon)||"ico-user-circle-o",z=y.appName||y.resourceName,E=t||`no${(null==y?void 0:y.tab)?`_${null==y?void 0:y.tab}`:""}_${z}_found`,_=()=>{b({type:d,payload:{identity:N}})};return r.createElement("div",{className:x.root},r.createElement(i.zb,{className:x.icon,icon:k}),r.createElement("div",{className:x.title},r.createElement("span",null,g.formatMessage({id:E}))),n?r.createElement("div",{className:x.content},g.formatMessage({id:n})):null,l&&r.createElement(a.Z,{variant:"contained",color:"primary",startIcon:r.createElement(i.zb,{icon:"ico-plus"}),sx:{fontSize:18,mt:2.5},onClick:_},g.formatMessage({id:l})))}}}]);