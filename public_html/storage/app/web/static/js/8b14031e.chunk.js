"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-forum-components-QuoteItem"],{19679:function(e,t,a){a.r(t),a.d(t,{default:function(){return h}});var l=a(85597),n=a(67294),i=a(84116),o=a(77029),r=a(81719),m=a(30120),d=a(62097),s=a(47037),p=a.n(s);let c="QuotePostMain",u=(0,r.ZP)("div",{name:c,slot:"postContent"})(({theme:e})=>({fontSize:e.mixins.pxToRem(13),lineHeight:1.33,marginTop:e.spacing(1.5),color:"dark"===e.palette.mode?e.palette.text.primary:e.palette.text.secondary,"& p + p":{marginBottom:e.spacing(2.5)}})),g=(0,r.ZP)("div",{name:c,slot:"attachmentTitle"})(({theme:e})=>({fontSize:e.mixins.pxToRem(18),marginTop:e.spacing(2),color:e.palette.text.secondary,fontWeight:e.typography.fontWeightBold})),f=(0,r.ZP)("div",{name:c,slot:"attachment"})(({theme:e})=>({width:"100%",display:"flex",flexWrap:"wrap",justifyContent:"space-between"})),x=(0,r.ZP)("div",{name:c,slot:"attachmentItemWrapper"})(({theme:e})=>({marginTop:e.spacing(2),flexGrow:0,flexShrink:0,flexBasis:"calc(50% - 8px)",minWidth:300}));function h({item:e}){let{useGetItem:t,useGetItems:a,i18n:r}=(0,l.OgA)(),s=a(null==e?void 0:e.attachments),{content:c,user:h}=e,y=t(h);y||p()(h)||(y=h);let v=(0,d.Z)();return n.createElement(m.Z,{sx:{width:"100%",padding:"16px",borderRadius:"4px",border:"solid 1px rgba(85, 85, 85, 0.2)",backgroundColor:"dark"===v.palette.mode?v.palette.grey[700]:v.palette.grey[100]}},n.createElement(m.Z,null,y?n.createElement(o.Ys,{color:"dark"===v.palette.mode?"text.secondary":"text.hint",variant:"body2",lines:1},r.formatMessage({id:"originally_posted_by"})," ",n.createElement(l.rUS,{variant:"body2",color:"text.primary",to:null==y?void 0:y.link,children:null==y?void 0:y.full_name,hoverCard:`/user/${null==y?void 0:y.id}`,sx:{fontWeight:"bold",display:"inline"}})):null),n.createElement(u,null,n.createElement(i.ZP,{html:c||""})),(null==s?void 0:s.length)>0&&n.createElement(n.Fragment,null,n.createElement(g,null,r.formatMessage({id:"attachments"})),n.createElement(f,null,s.map(e=>{return n.createElement(x,{key:null==e?void 0:e.id.toString()},n.createElement(o.M$,{fileName:e.file_name,downloadUrl:e.download_url,isImage:e.is_image,fileSizeText:e.file_size_text,size:"mini",image:null==e?void 0:e.image}))}))))}h.displayName="ForumQuotePostMain"}}]);