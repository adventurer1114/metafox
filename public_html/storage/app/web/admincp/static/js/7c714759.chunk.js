"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-forum-components-PostItem-EmbedCard-index"],{29691:function(e,t,n){n.r(t),n.d(t,{default:function(){return f}});var r=n(68006),l=n(85597),a=n(67294),o=n(84116),i=n(41547),d=n(81719),s=n(30120);let m="FeedPostMain",c=(0,d.ZP)(i.Ys,{name:m,slot:"postContent"})(({theme:e})=>({marginTop:e.spacing(1.5),color:e.palette.text.secondary,"& p + p":{marginBottom:e.spacing(2.5)}})),p=(0,d.ZP)("div",{name:m,slot:"wrapperItem"})(({theme:e})=>({width:"100%",padding:"16px 24px",borderRadius:"4px",border:`1px solid ${e.palette.divider}`}));function u({item:e}){let{useGetItem:t,i18n:n}=(0,l.OgA)(),{short_content:r,user:d,thread:m}=e,u=t(d),f=t(m),x=(null==f?void 0:f.id)?`/forum/thread/${null==f?void 0:f.id}`:"";return a.createElement(p,null,a.createElement(s.Z,null,a.createElement(i.Ys,{color:"text.hint",variant:"body2",lines:1},a.createElement(l.rUS,{variant:"body2",color:"text.primary",to:u.link,children:null==u?void 0:u.full_name,hoverCard:`/user/${u.id}`,sx:{fontWeight:"bold",display:"inline"}})," ",n.formatMessage({id:"posted_a_reply_on"})," ",x?a.createElement(l.rUS,{sx:{display:"inline"},to:x,color:"primary"},null==f?void 0:f.title):null)),a.createElement(c,null,a.createElement(i.Ys,{variant:"body1",lines:3},a.createElement(o.ZP,{html:r||""}))))}u.displayName="ForumFeedPostMain";var f=(0,r.Y)(u,r.c)}}]);