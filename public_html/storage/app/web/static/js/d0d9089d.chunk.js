"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-video-components-VideoModalView-VideoItemModalView"],{68160:function(e,t,l){l.r(t),l.d(t,{default:function(){return h}});var n=l(85597),i=l(76224),o=l(69452),a=l(50130),r=l(81719),c=l(38790),s=l(30120),u=l(67294);let m="VideoItemModalView",d=(0,r.ZP)("div",{name:m,slot:"actionBar"})(({theme:e})=>({position:"absolute",right:0,top:0,width:"100%",padding:e.spacing(1),display:"flex",justifyContent:"space-between",zIndex:1,alignItems:"center"})),f=(0,r.ZP)(a.Z,{name:m,slot:"tagFriend"})(({theme:e})=>({color:"#fff !important",width:32,height:32,fontSize:e.mixins.pxToRem(15)}));function h({item:e,onMinimizePhoto:t}){let{i18n:l,useDialog:a,useIsMobile:r}=(0,n.OgA)(),{closeDialog:m}=a(),h=(0,n.Pk8)(),[p,w]=u.useState(!0),g=r();if(!e)return null;let x=()=>{w(!p),t&&t(p)},E=()=>{m(),t&&t(!1)};return u.createElement(s.Z,{sx:{height:"100%"}},u.createElement(o.Z,{src:e.video_url||e.destination||null,thumb_url:e.image,autoPlay:!0}),u.createElement(d,null,g?null:u.createElement(s.Z,null,u.createElement(c.Z,{title:l.formatMessage({id:"close"})},u.createElement(f,{onClick:E},u.createElement(i.zb,{icon:"ico-close",color:"white"})))),h?u.createElement(s.Z,{sx:{marginLeft:"auto"}},u.createElement(c.Z,{title:l.formatMessage({id:p?"switch_to_full_screen":"exit_full_screen"})},u.createElement(f,{onClick:x},u.createElement(i.zb,{icon:p?"ico-arrow-expand":"ico-arrow-collapse",color:"white"})))):null))}}}]);