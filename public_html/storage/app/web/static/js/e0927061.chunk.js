"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-activity-point-blocks-History-Block"],{58886:function(t,e,i){i.r(e),i.d(e,{default:function(){return v}});var a=i(85597),n=i(10979),r=i(81719),l=i(30120),c=i(91647),o=i(30030),s=i(21241),p=i(67294),g=i(84635),m=i(13478),d=i(17673);function u(){return(u=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var a in i)Object.prototype.hasOwnProperty.call(i,a)&&(t[a]=i[a])}return t}).apply(this,arguments)}let y=[{label:"app",grid:2},{label:"point",grid:1},{label:"action",grid:5},{label:"type",grid:1},{label:"id",grid:1},{label:"date",grid:2}],f=(0,r.ZP)(o.ZP,{name:"TitleStyled"})(({theme:t})=>({display:"flex",paddingTop:t.spacing(3),paddingBottom:t.spacing(2),paddingLeft:t.spacing(2),paddingRight:t.spacing(2)}));function b({title:t,...e}){let{usePageParams:i,navigate:r,jsxBackend:b,i18n:v}=(0,a.OgA)(),k=i(),h=(0,a.oHF)(n.k,"activitypoint_transaction","viewAll"),E=(0,a.gM1)(n.k,"activitypoint_transaction","search"),_=b.get("core.block.mainListing"),P=(t,e)=>{let i=h.apiRules,a=(0,m.Su)(t,i);r(`?${d.stringify(a)}`,{replace:!0}),e.setSubmitting(!1)};return p.createElement(s.gO,u({testid:"activityPointBlock"},e),p.createElement(s.ti,{title:t}),p.createElement(s.sU,u({},e),p.createElement(l.Z,{sx:{p:2}},p.createElement(p.Fragment,null,p.createElement(g.qu,{navigationConfirmWhenDirty:!1,formSchema:E,onSubmit:P}),p.createElement(f,{container:!0},y.map((t,e)=>p.createElement(o.ZP,{item:!0,key:e,xs:t.grid},p.createElement(c.Z,{variant:"h5"},v.formatMessage({id:t.label}))))),p.createElement(_,{itemView:"activitypoint.itemView.transaction",dataSource:h,emptyPage:"core.itemView.no_content_history_point",blockLayout:"Large Main Lists",pageParams:k,gridContainerProps:{spacing:0}})))))}b.displayName="ActivityPoint_History";var v=(0,a.j4Z)({extendBlock:b})}}]);