"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-group-blocks-ProfileAbout-Block"],{14561:function(e,t,l){l.r(t),l.d(t,{default:function(){return d}});var n=l(85597),o=l(90910),r=l(90717),a=l(84116),i=l(21241),c=l(41547),u=l(30120),m=l(71682),f=l(81719),p=l(67294);let s=(0,f.ZP)("div")(({theme:e})=>({marginBottom:e.spacing(1.5),fontSize:e.mixins.pxToRem(15),color:e.palette.text.primary}));var d=(0,n.j4Z)({name:"GroupProfileAbout",extendBlock:function({title:e}){let{useFetchDetail:t,usePageParams:l,jsxBackend:f,i18n:d}=(0,n.OgA)(),v=l(),E=(0,n.oHF)(r.eA,r.oQ,"groupInfo"),[g,k,h]=t({dataSource:E,pageParams:v,preventReload:!0}),Z=()=>p.createElement(u.Z,null,p.createElement(m.Z,{width:"100%"}),p.createElement(m.Z,{width:"100%"}),p.createElement(m.Z,{width:"100%"})),b=f.get("core.block.no_content"),{text:x,location:_,phone:w,total_member:y,external_link:A,category:C}=Object.assign({},g),P=p.createElement(n.rUS,{to:(null==C?void 0:C.link)||(null==C?void 0:C.url),color:"primary"},null==C?void 0:C.name),j=[{icon:"ico-checkin-o",info:_,value:!!_},{icon:"ico-layers-o",info:P,value:!!P},{icon:"ico-phone-o",info:w,value:!!w},{icon:"ico-user-two-men-o",info:d.formatMessage({id:"people_joined_group"},{value:y}),value:!!y},{icon:"ico-globe-alt-o",info:A,value:!!A}].filter(e=>e.value);return p.createElement(i.gO,null,p.createElement(i.ti,{title:e}),p.createElement(i.sU,null,p.createElement(o.Z,{loading:k,error:h,loadingComponent:Z,emptyComponent:g?null:b},p.createElement(u.Z,null,x&&p.createElement(s,null,p.createElement(c.Ys,{lines:3},p.createElement(a.ZP,{html:x}))),p.createElement("div",null,p.createElement(c.VJ,{values:j}))))))}})}}]);