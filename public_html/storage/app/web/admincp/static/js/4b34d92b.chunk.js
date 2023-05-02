"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-blog-blocks-ViewBlog-Block"],{21145:function(e,t,a){a.r(t),a.d(t,{DetailView:function(){return S},LoadingSkeleton:function(){return C}});var n=a(83595),i=a(85597),l=a(84116),o=a(21241),r=a(41547),m=a(27274),c=a(30120),s=a(81719),d=a(91647),p=a(43847),g=a(86706),u=a(67294);let h="BlogDetailView",f=(0,s.ZP)("div",{name:h,slot:"ContentWrapper"})(({theme:e})=>({backgroundColor:e.mixins.backgroundColor("paper")})),v=(0,s.ZP)("div",{name:h,slot:"bgCover",shouldForwardProp:e=>"isModalView"!==e})(({theme:e,isModalView:t})=>({backgroundRepeat:"no-repeat",backgroundPosition:"center",backgroundSize:"cover",height:320,...t&&{marginLeft:e.spacing(-2),marginRight:e.spacing(-2)},[e.breakpoints.down("sm")]:{height:179}})),x=(0,s.ZP)("div",{name:h,slot:"blogViewContainer"})(({theme:e})=>({width:"100%",marginLeft:"auto",marginRight:"auto",backgroundColor:e.mixins.backgroundColor("paper"),padding:e.spacing(2),position:"relative",borderBottomLeftRadius:e.shape.borderRadius,borderBottomRightRadius:e.shape.borderRadius})),E=(0,s.ZP)("div",{name:h,slot:"AvatarWrapper"})(({theme:e})=>({marginRight:e.spacing(1.5)})),y=(0,s.ZP)("div",{name:h,slot:"blogContent"})(({theme:e})=>({fontSize:e.mixins.pxToRem(15),lineHeight:1.33,marginTop:e.spacing(3),"& p + p":{marginBottom:e.spacing(2.5)}})),b=(0,s.ZP)("div",{name:h,slot:"tagItem"})(({theme:e})=>({fontSize:e.mixins.pxToRem(13),fontWeight:e.typography.fontWeightBold,borderRadius:4,background:"light"===e.palette.mode?e.palette.background.default:e.palette.action.hover,marginRight:e.spacing(1),marginBottom:e.spacing(1),padding:e.spacing(0,1.5),height:e.spacing(3),lineHeight:e.spacing(3),display:"block",color:"light"===e.palette.mode?"#121212":"#fff"})),k=(0,s.ZP)("div",{name:h,slot:"attachmentTitle"})(({theme:e})=>({fontSize:e.mixins.pxToRem(18),marginTop:e.spacing(4),color:e.palette.text.secondary,fontWeight:e.typography.fontWeightBold})),w=(0,s.ZP)("div",{name:h,slot:"attachment"})(({theme:e})=>({width:"100%",display:"flex",flexWrap:"wrap",marginTop:e.spacing(2),justifyContent:"space-between"})),_=(0,s.ZP)("div",{name:h,slot:"attachmentItemWrapper"})(({theme:e})=>({marginTop:e.spacing(2),flexGrow:0,flexShrink:0,flexBasis:"calc(50% - 8px)",minWidth:300})),Z=(0,s.ZP)("span",{name:"HeadlineSpan"})(({theme:e})=>({paddingRight:e.spacing(.5),color:e.palette.text.secondary})),R=(0,s.ZP)(i.rUS,{name:h,slot:"profileLink"})(({theme:e})=>({fontSize:e.mixins.pxToRem(15),fontWeight:e.typography.fontWeightBold,paddingRight:e.spacing(.5),color:e.palette.text.primary})),P=(0,s.ZP)(p.Z,{name:"OwnerStyled"})(({theme:e})=>({fontWeight:e.typography.fontWeightBold,color:e.palette.text.primary,fontSize:e.mixins.pxToRem(15),"&:hover":{textDecoration:"underline"}}));function C({wrapAs:e,wrapProps:t}){return u.createElement(r.Az,{testid:"skeleton",wrapAs:e,wrapProps:t})}function S({user:e,identity:t,item:a,state:n,actions:s,handleAction:p,isModalView:h}){let{ItemActionMenu:C,ItemDetailInteraction:S,useGetItems:W,i18n:B,jsxBackend:z,assetUrl:M}=(0,i.OgA)(),T=W(null==a?void 0:a.categories),V=(0,g.v9)(e=>{return(0,i.AV9)(e,null==a?void 0:a.owner)}),A=W(null==a?void 0:a.attachments),D=z.get("core.itemView.pendingReviewCard");if(!e||!a)return null;let U=(0,m.Q4)(null==a?void 0:a.image,"500",M("blog.no_image")),{tags:$}=a;return u.createElement(o.gO,{testid:`detailview ${a.resource_name}`},u.createElement(o.sU,null,u.createElement(f,null,U?u.createElement(v,{isModalView:h,style:{backgroundImage:`url(${U})`}}):null,D&&u.createElement(c.Z,{sx:{margin:2}},u.createElement(D,{sx:!0,item:a})),u.createElement(x,null,u.createElement(r.tQ,{sx:{position:"absolute",top:8,right:8}},u.createElement(C,{identity:t,icon:"ico-dottedmore-vertical-o",state:n,menuName:"detailActionMenu",handleAction:p,size:"smaller"})),u.createElement(r.ot,{to:"/blog/category",data:T,sx:{mb:1,mr:2}}),u.createElement(r.XQ,{variant:"h3",component:"div",pr:2,showFull:!0},u.createElement(r.K6,{variant:"itemView",value:a.is_featured}),u.createElement(r.k5,{variant:"itemView",value:a.is_sponsor}),u.createElement(r.Kc,{value:a.is_draft,variant:"h3",component:"span",sx:{verticalAlign:"middle",fontWeight:"normal"}}),u.createElement(d.Z,{component:"h1",variant:"h3",sx:{pr:2.5,display:{sm:"inline",xs:"block"},mt:{sm:0,xs:1},verticalAlign:"middle"}},null==a?void 0:a.title)),u.createElement(c.Z,{mt:2,display:"flex"},u.createElement(E,null,u.createElement(r.Yt,{user:e,size:48})),u.createElement(c.Z,null,u.createElement(R,{to:e.link,children:e.full_name,hoverCard:`/user/${e.id}`,"data-testid":"headline"}),(null==V?void 0:V.resource_name)!==(null==e?void 0:e.resource_name)&&u.createElement(Z,null,B.formatMessage({id:"to_parent_user"},{icon:()=>u.createElement(r.zb,{icon:"ico-caret-right"}),parent_user:()=>u.createElement(P,{user:V})})),u.createElement(r.Ee,{sx:{color:"text.secondary",mt:1}},u.createElement(r.r2,{"data-testid":"publishedDate",value:null==a?void 0:a.creation_date,format:"MMMM DD, yyyy"}),u.createElement(r.$k,{values:a.statistic,display:"total_view",component:"span",skipZero:!1}),u.createElement(r.Cd,{value:null==a?void 0:a.privacy,item:null==a?void 0:a.privacy_detail})))),u.createElement(y,null,u.createElement(l.ZP,{html:(null==a?void 0:a.text)||""})),(null==$?void 0:$.length)>0?u.createElement(c.Z,{mt:4,display:"flex",flexWrap:"wrap"},$.map(e=>u.createElement(b,{key:e},u.createElement(i.rUS,{to:`/blog/search?q=%23${encodeURIComponent(e)}`},e)))):null,(null==A?void 0:A.length)>0&&u.createElement(u.Fragment,null,u.createElement(k,null,B.formatMessage({id:"attachments"})),u.createElement(w,null,A.map(e=>{return u.createElement(_,{key:e.id.toString()},u.createElement(r.M$,{fileName:e.file_name,downloadUrl:e.download_url,isImage:e.is_image,fileSizeText:e.file_size_text,size:"large",image:null==e?void 0:e.image}))}))),u.createElement(S,{identity:t,state:n,handleAction:p})))))}S.LoadingSkeleton=C,S.displayName="BlogItem_DetailView";let W=(0,i.Uh$)((0,i.YUM)(S,n.Z,{categories:!0,attachments:!0}));t.default=(0,i.j4Z)({extendBlock:W})}}]);