"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-forum-blocks-ThreadDetail-Block"],{45964:function(e,t,a){a.r(t),a.d(t,{default:function(){return P}});var n=a(85597),l=a(18302),i=a(84116),r=a(21241),o=a(76224),m=a(30120),s=a(81719),d=a(91647),c=a(61225),p=a(62097),u=a(67294),g=a(86706),h=a(43847);let f="ThreadDetailView",v=(0,s.ZP)("div",{name:f,slot:"ContentWrapper"})(({theme:e})=>({backgroundColor:e.mixins.backgroundColor("paper"),borderRadius:e.spacing(1)})),x=(0,s.ZP)("div",{name:f,slot:"threadViewContainer"})(({theme:e})=>({width:"100%",marginLeft:"auto",marginRight:"auto",padding:`${e.spacing(2)} ${e.spacing(2)} 0 ${e.spacing(2)}`,position:"relative",borderBottomLeftRadius:e.shape.borderRadius,borderBottomRightRadius:e.shape.borderRadius})),E=(0,s.ZP)("div",{name:f,slot:"AvatarWrapper"})(({theme:e})=>({marginRight:e.spacing(1.5)})),y=(0,s.ZP)("div",{name:f,slot:"threadContent"})(({theme:e})=>({fontSize:e.mixins.pxToRem(15),lineHeight:1.33,marginTop:e.spacing(3),"& p + p":{marginBottom:e.spacing(2.5)}})),b=(0,s.ZP)("div",{name:f,slot:"tagItem"})(({theme:e})=>({fontSize:e.mixins.pxToRem(13),fontWeight:e.typography.fontWeightBold,borderRadius:4,background:"light"===e.palette.mode?e.palette.background.default:e.palette.action.hover,marginRight:e.spacing(1),marginBottom:e.spacing(1),padding:e.spacing(0,1.5),height:e.spacing(3),lineHeight:e.spacing(3),display:"block",color:"light"===e.palette.mode?"#121212":"#fff"})),_=(0,s.ZP)("div",{name:f,slot:"attachmentTitle"})(({theme:e})=>({fontSize:e.mixins.pxToRem(18),marginTop:e.spacing(4),color:e.palette.text.secondary,fontWeight:e.typography.fontWeightBold})),k=(0,s.ZP)("div",{name:f,slot:"attachment"})(({theme:e})=>({width:"100%",display:"flex",flexWrap:"wrap",marginTop:e.spacing(2),justifyContent:"space-between"})),w=(0,s.ZP)("div",{name:f,slot:"attachmentItemWrapper"})(({theme:e})=>({marginTop:e.spacing(2),flexGrow:0,flexShrink:0,flexBasis:"calc(50% - 8px)",minWidth:300})),Z=(0,s.ZP)(h.Z,{name:"OwnerStyled"})(({theme:e})=>({fontWeight:e.typography.fontWeightBold,color:e.palette.text.primary,fontSize:e.mixins.pxToRem(15),"&:hover":{textDecoration:"underline"}})),S=(0,s.ZP)("span",{name:"HeadlineSpan"})(({theme:e})=>({paddingRight:e.spacing(.5),color:e.palette.text.secondary}));function R({user:e,identity:t,item:a,state:l,actions:s,handleAction:h}){let{ItemActionMenu:f,ItemDetailInteraction:R,useGetItems:C,useGetItem:P,i18n:T,jsxBackend:W}=(0,n.OgA)(),[M,z]=u.useState(!0),B=C(null==a?void 0:a.attachments),D=P(null==a?void 0:a.item),A=(0,p.Z)(),F=(0,c.Z)(A.breakpoints.down("sm")),V=(0,n.oHF)("forum","forum_thread","viewPosters"),$=(0,g.v9)(e=>{return(0,n.AV9)(e,null==a?void 0:a.owner)}),H=W.get("core.itemView.pendingReviewCard"),I=W.get("poll.embedItem.insideFeedItem"),L=W.get("forum_post.block.detailListingBlock"),U=W.get("forum_post.block.addForm");if(!e||!a)return null;let{tags:N,description:O,item:j}=a,K=j&&j.startsWith("poll.")?j:null,Q=()=>{z(e=>!e)};return u.createElement(u.Fragment,null,u.createElement(r.gO,{testid:`detailview ${a.resource_name}`},u.createElement(r.sU,null,u.createElement(v,null,H&&(null==a?void 0:a.is_pending)?u.createElement(m.Z,{sx:{px:2,pt:2}},u.createElement(H,{sx:!0,item:{...a}})):null,u.createElement(x,null,u.createElement(o.tQ,{sx:{position:"absolute",top:8,right:8}},u.createElement(f,{identity:t,icon:"ico-dottedmore-vertical-o",state:l,menuName:"detailActionMenu",handleAction:h,size:"smaller"})),u.createElement(o.XQ,{variant:"h3",component:"div",my:0,showFull:!0},u.createElement(o.K6,{variant:"itemView",value:a.is_featured}),u.createElement(o.k5,{variant:"itemView",value:a.is_sponsor}),u.createElement(d.Z,{component:"h1",variant:"h3",sx:{pr:2.5,display:{sm:"inline",xs:"block"},verticalAlign:"middle"}},null==a?void 0:a.title)),u.createElement(m.Z,{mt:2,display:"flex"},u.createElement(E,null,u.createElement(o.Yt,{user:e,size:48})),u.createElement(m.Z,null,u.createElement(n.rUS,{variant:"body1",color:"text.primary",to:e.link,children:null==e?void 0:e.full_name,hoverCard:`/user/${e.id}`,sx:{fontWeight:"bold",mr:.5}}),(null==$?void 0:$.resource_name)!==(null==e?void 0:e.resource_name)&&u.createElement(S,null,T.formatMessage({id:"to_parent_user"},{icon:()=>u.createElement(o.zb,{icon:"ico-caret-right"}),parent_user:()=>u.createElement(Z,{user:$})})),u.createElement(o.Ee,{sx:{color:"text.secondary",mt:1}},u.createElement(o.r2,{"data-testid":"publishedDate",value:null==a?void 0:a.creation_date,format:"MMMM DD, yyyy"})))),u.createElement(y,null,u.createElement(o.jK,{maxHeight:"300px"},u.createElement(i.ZP,{html:O||""})),(null==a?void 0:a.modification_date)?u.createElement(o.Ee,{sx:{color:"text.secondary",mt:1,fontStyle:"italic"}},u.createElement(o.r2,{"data-testid":"modifyDate",value:null==a?void 0:a.modification_date,format:"MMMM DD, yyyy",phrase:"last_update_on_time"})):null),I&&K&&!(null==D?void 0:D.error)?u.createElement(m.Z,{mt:4},u.createElement(I,{identity:K})):null,(null==N?void 0:N.length)>0?u.createElement(m.Z,{mt:4,display:"flex",flexWrap:"wrap"},N.map(e=>u.createElement(b,{key:e},u.createElement(n.rUS,{to:`/forum/search?q=%23${e}`},e)))):null,(null==B?void 0:B.length)>0&&u.createElement(u.Fragment,null,u.createElement(_,null,T.formatMessage({id:"attachments"})),u.createElement(k,null,B.map(e=>{return u.createElement(w,{key:e.id.toString()},u.createElement(o.M$,{fileName:e.file_name,downloadUrl:e.download_url,isImage:e.is_image,fileSizeText:e.file_size_text,size:F?"mini":"large",image:null==e?void 0:e.image}))}))),u.createElement(R,{identity:t,state:l,handleAction:h,messageCommentStatistic:"total_reply",dataSourceCommentStatistic:V,forceHideCommentList:!0,handleActionCommentStatistic:Q}))))),u.createElement(m.Z,{sx:{borderRadius:1,overflow:"hidden"},className:!M&&"srOnly"},u.createElement(L,null),u.createElement(U,{blockLayout:"Forum Post Form Detail Thread"})))}R.LoadingSkeleton=function({wrapAs:e,wrapProps:t}){return u.createElement(o.Az,{testid:"skeleton",wrapAs:e,wrapProps:t})},R.displayName="ThreadItem_DetailView";let C=(0,n.Uh$)((0,l.Y)(R,l.c,{}));var P=(0,n.j4Z)({extendBlock:C,defaults:{blockProps:{titleComponent:"h2",titleVariant:"subtitle1",titleColor:"textPrimary",noFooter:!0,noHeader:!0,blockStyle:{sx:{mb:2,mt:2}},contentStyle:{sx:{borderRadius:1,bgcolor:"background.paper",pt:0,pb:0}}}}})}}]);