"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-activity-point-blocks-TransactionPackage-Block"],{52961:function(e,t,a){a.r(t),a.d(t,{default:function(){return y}});var i=a(85597),n=a(10979),r=a(81719),c=a(30120),l=a(91647),o=a(30030),g=a(21241),s=a(67294),p=a(84635),m=a(27274),d=a(17673);function u(){return(u=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var i in a)Object.prototype.hasOwnProperty.call(a,i)&&(e[i]=a[i])}return e}).apply(this,arguments)}let f=[{label:"package_name",grid:3},{label:"point",grid:2},{label:"price",grid:2},{label:"status",grid:2},{label:"id",grid:1},{label:"date",grid:2}],k=(0,r.ZP)(o.ZP,{name:"TitleStyled"})(({theme:e})=>({display:"flex",paddingTop:e.spacing(3),paddingBottom:e.spacing(2),paddingLeft:e.spacing(2),paddingRight:e.spacing(2)}));function b({title:e,...t}){let{usePageParams:a,navigate:r,jsxBackend:b,i18n:y}=(0,i.OgA)(),h=a(),v=(0,i.oHF)(n.k,"package_transaction","viewAll"),_=(0,i.gM1)(n.k,"package_transaction","search"),E=b.get("core.block.mainListing"),P=(e,t)=>{let a=v.apiRules,i=(0,m.Su)(e,a);r(`?${d.stringify(i)}`,{replace:!0}),t.setSubmitting(!1)};return s.createElement(g.gO,u({testid:"activityPointBlock"},t),s.createElement(g.ti,{title:e}),s.createElement(g.sU,u({},t),s.createElement(c.Z,{sx:{p:2}},s.createElement(s.Fragment,null,s.createElement(p.qu,{navigationConfirmWhenDirty:!1,formSchema:_,onSubmit:P}),s.createElement(k,{container:!0},f.map((e,t)=>s.createElement(o.ZP,{item:!0,key:t,xs:e.grid},s.createElement(l.Z,{variant:"h5"},y.formatMessage({id:e.label}))))),s.createElement(E,{itemView:"activitypoint.itemView.package",dataSource:v,emptyPage:"core.itemView.no_content_history_point",blockLayout:"Large Main Lists",pageParams:h,gridContainerProps:{spacing:0},clearDataOnUnMount:!0})))))}b.displayName="package_transaction";var y=(0,i.j4Z)({extendBlock:b})}}]);