"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-advertise-blocks-AllAds-Block"],{93166:function(e,t,a){a.r(t),a.d(t,{default:function(){return y}});var r=a(85597),i=a(81719),n=a(30120),l=a(91647),c=a(30030),o=a(21241),s=a(67294),d=a(84635),m=a(27274),g=a(17673),p=a(95130);function u(){return(u=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var r in a)Object.prototype.hasOwnProperty.call(a,r)&&(e[r]=a[r])}return e}).apply(this,arguments)}let b=[{label:"title",grid:3},{label:"placement",grid:2.5},{label:"start_date",grid:1.5},{label:"status",grid:1.5,center:!0},{label:"impressions",grid:1,center:!0},{label:"clicks",grid:1,center:!0},{label:"active",grid:1,center:!0},{label:null,grid:.5,center:!0}],f=(0,i.ZP)(c.ZP,{name:"TitleStyled"})(({theme:e})=>({display:"flex",padding:e.spacing(3,2,2)})),v=(0,i.ZP)(n.Z,{name:"ContentWrapper"})(({theme:e})=>({padding:e.spacing(3,2,2),[e.breakpoints.down("md")]:{padding:e.spacing(0)}}));function k({title:e,...t}){let{usePageParams:a,navigate:i,jsxBackend:n,i18n:k}=(0,r.OgA)(),y=a(),h=(0,r.oHF)(p.iC,p.Uf,"viewAll"),E=(0,r.gM1)(p.iC,p.Uf,"search_form"),w=n.get("core.block.mainListing"),x=(e,t)=>{let a=h.apiRules,r=(0,m.Su)(e,a);i(`?${g.stringify(r)}`,{replace:!0}),t.setSubmitting(!1)};return s.createElement(o.gO,u({testid:"advertiseBlock"},t),s.createElement(o.ti,{title:e}),s.createElement(o.sU,u({},t),s.createElement(v,null,s.createElement(d.qu,{navigationConfirmWhenDirty:!1,formSchema:E,onSubmit:x}),s.createElement(f,{container:!0},b.map((e,t)=>s.createElement(c.ZP,{item:!0,key:t,xs:e.grid},s.createElement(l.Z,{variant:"h5",sx:e.center&&{textAlign:"center"}},e.label&&k.formatMessage({id:e.label}))))),s.createElement(w,{itemView:"advertise.itemView.addAdsRecord",dataSource:h,emptyPage:"advertise.itemView.no_content_record",blockLayout:"App List - Record Table",itemLayout:"Record Item - Table",gridLayout:"Record Item - Table",pageParams:y}))))}k.displayName="Advertise";var y=(0,r.j4Z)({extendBlock:k})}}]);