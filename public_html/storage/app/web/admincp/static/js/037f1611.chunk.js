"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-group-blocks-MyContent-RemovedPost-Block"],{5616:function(e,t,o){o.r(t),o.d(t,{default:function(){return l}});var r=o(85597),n=o(21241),a=o(90717),i=o(67294);function c(){return(c=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var o=arguments[t];for(var r in o)Object.prototype.hasOwnProperty.call(o,r)&&(e[r]=o[r])}return e}).apply(this,arguments)}let s=({title:e,canLoadMore:t,gridVariant:o="listView",gridLayout:s,itemView:l,itemLayout:m,...u})=>{let{ListView:d,jsxBackend:p,usePageParams:g,compactData:f,i18n:P,useAppMenu:y}=(0,r.OgA)(),{id:v}=g(),k=(0,r.oHF)(a.eA,a.eA,"viewCreatorRemovedPost"),w=p.get("core.block.error404"),_=y(a.eA,a.pC).items;if(!k)return i.createElement(w);let b=_[_.findIndex(e=>e.name===a.U$)],{apiUrl:h,apiParams:C}=k;return i.createElement(n.gO,c({},u),i.createElement(n.ti,{title:e}),i.createElement(n.sU,null,i.createElement(d,{canLoadMore:!0,dataSource:{apiUrl:h,apiParams:f(C,{id:v})},gridVariant:o,gridLayout:s,itemLayout:m,clearDataOnUnMount:!0,itemView:l,emptyPage:"core.block.no_content_with_icon",emptyPageProps:{title:P.formatMessage({id:"no_removed_post"}),image:b.icon}})))};var l=(0,r.j4Z)({name:"MyGroupRemovedPosts",extendBlock:s,overrides:{title:"removed",itemView:"group.itemView.myRemovedPost",blockLayout:"App Lists Pending Posts",gridLayout:"Group - PendingPost - Main Card",itemLayout:"Group - PendingPost - Main Card",contentType:"group"}})}}]);