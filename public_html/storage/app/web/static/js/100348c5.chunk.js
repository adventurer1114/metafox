"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-feed-components-FeedItemViewModal"],{15393:function(e,t,a){a.r(t);var n=a(85597),l=a(84116),r=a(21241),i=a(76224),o=a(71682),c=a(22410),d=a(73327),m=a(67294),s=a(25237);let p=(0,c.Z)(e=>(0,d.Z)({root:{borderRadius:e.shape.borderRadius,backgroundColor:e.palette.background.paper,padding:e.spacing(2),paddingBottom:0,display:"flex",flexFlow:"column",height:"100%",maxHeight:"600px",justifyContent:"space-between",[e.breakpoints.down("xs")]:{width:"100%",maxHeight:"400px"}},rootSkeleton:{padding:e.spacing(2)},header:{display:"flex",flexDirection:"row",marginBottom:e.spacing(2)},headerInfo:{padding:"4px 0",flex:1},headerAvatarHolder:{paddingRight:e.spacing(1.5)},profileLink:{fontWeight:e.typography.fontWeightBold,paddingRight:e.spacing(.5),color:e.palette.text.primary},privacyBlock:{display:"flex",flexDirection:"row",alignItems:"center",color:e.palette.text.secondary},separateSpans:{display:"flex",alignItems:"center","& span + span:before":{content:'"\xb7"',display:"inline-block",padding:`${e.spacing(0,.5)}`}},body:{flexGrow:1},content:{flexGrow:1,display:"flex",flexFlow:"column wrap"},commentListing:{flexGrow:1,overflow:"hidden"},commentReaction:{flexGrow:"initial"},info:{marginBottom:e.spacing(2),fontSize:e.mixins.pxToRem(15),color:e.palette.text.primary}}),{name:"MuiFeedItemViewModal"}),h=({item:e,user:t,identity:a,itemProps:o})=>{var c,d;let{info:h,statistic:f,most_reactions:u,item_type:v,item_id:g}=e,{useActionControl:x}=(0,n.OgA)(),[E,y]=(0,s.X)(),w=m.useRef(),{CommentList:b,ReactionActButton:k,CommentReaction:N,CommentActButton:_,ShareActButton:A,ItemActionMenu:S,jsxBackend:Z}=(0,n.OgA)(),B=p(),[C,I]=x(a,{menuOpened:!1,commentOpened:!0,commentInputRef:w});if(!e)return null;let R=Z.get("CommentComposer");return m.createElement("div",{className:B.root},m.createElement("div",{className:B.content},m.createElement("div",{className:B.header},m.createElement("div",{className:B.headerAvatarHolder},m.createElement(i.Yt,{user:t,size:48})),m.createElement("div",{className:B.headerInfo},m.createElement("div",null,m.createElement(n.rUS,{to:`/${t.user_name}`,children:t.full_name,hoverCard:`/user/${t.id}`,className:B.profileLink})),m.createElement("div",{className:B.privacyBlock},m.createElement("span",{className:B.separateSpans},m.createElement(i.zb,{icon:"ico-globe-o",style:{fontSize:12},"aria-label":"share with public",role:"img"}),m.createElement(i.Lt,{value:e.creation_date})))),m.createElement(S,{identity:a,state:I,handleAction:C})),m.createElement("div",{className:B.body},m.createElement(r.l$,{autoHide:!0},h?m.createElement("div",{className:B.info},m.createElement(l.ZP,{html:h})):null,m.createElement(i.Dq,{handleAction:C,identity:a,reactions:u,statistic:f}),m.createElement(N,null,(null===(c=e.extra)||void 0===c?void 0:c.can_like)?m.createElement(k,{reacted:e.user_reacted,identity:a,handleAction:C}):null,(null===(d=e.extra)||void 0===d?void 0:d.can_comment)?m.createElement(_,{identity:a,handleAction:C}):null,e.extra.can_share?m.createElement(A,{handleAction:C,identity:a}):null),m.createElement(b,{open:!0,handleAction:C,item_type:v,item_id:g,className:B.commentListing,setSortType:y,sortType:E})))),m.createElement(R,{open:I.commentOpened}))},f=({itemProps:e})=>{let t=p();return m.createElement("div",{className:`${t.root} ${t.rootSkeleton}`},m.createElement("div",{className:t.header},m.createElement("div",{className:t.headerAvatarHolder},m.createElement(o.Z,{variant:"circular",width:40,height:40})),m.createElement("div",{className:t.headerInfo},m.createElement("div",null,m.createElement(o.Z,{variant:"text",component:"div"})),m.createElement("div",{className:t.privacyBlock},m.createElement(o.Z,{variant:"text",width:120})))),m.createElement("div",null,m.createElement(o.Z,{variant:"text"}),m.createElement(o.Z,{variant:"text"}),m.createElement(o.Z,{variant:"text"})))};h.LoadingSkeleton=f,t.default=h}}]);