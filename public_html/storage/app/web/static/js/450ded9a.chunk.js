"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-chatplus-components-BuddyBlock-Block","metafox-chatplus-components-Avatar"],{6867:function(e,t,n){n.d(t,{Z:function(){return M}});var a=n(63366),l=n(87462),r=n(67294),i=n(86010),o=n(2097),s=n(94780),c=n(1588),d=n(34867);function u(e){return(0,d.Z)("MuiBadge",e)}(0,c.Z)("MuiBadge",["root","badge","invisible"]);var g=n(34261),m=n(85893);let p=["badgeContent","component","children","invisible","max","slotProps","slots","showZero"],h=e=>{let{invisible:t}=e;return(0,s.Z)({root:["root"],badge:["badge",t&&"invisible"]},u,void 0)},v=r.forwardRef(function(e,t){let{component:n,children:r,max:i=99,slotProps:s={},slots:c={},showZero:d=!1}=e,u=(0,a.Z)(e,p),{badgeContent:v,max:f,displayValue:b,invisible:y}=function(e){let{badgeContent:t,invisible:n=!1,max:a=99,showZero:l=!1}=e,r=(0,o.Z)({badgeContent:t,max:a}),i=n;!1!==n||0!==t||l||(i=!0);let{badgeContent:s,max:c=a}=i?r:e,d=s&&Number(s)>c?`${c}+`:s;return{badgeContent:s,invisible:i,max:c,displayValue:d}}((0,l.Z)({},e,{max:i})),E=(0,l.Z)({},e,{badgeContent:v,invisible:y,max:f,showZero:d}),Z=h(E),x=n||c.root||"span",C=(0,g.Z)({elementType:x,externalSlotProps:s.root,externalForwardedProps:u,additionalProps:{ref:t},ownerState:E,className:Z.root}),w=c.badge||"span",k=(0,g.Z)({elementType:w,externalSlotProps:s.badge,ownerState:E,className:Z.badge});return(0,m.jsxs)(x,(0,l.Z)({},C,{children:[r,(0,m.jsx)(w,(0,l.Z)({},k,{children:b}))]}))});var f=n(81719),b=n(78884),y=n(69633),E=n(36622);function Z(e){return(0,d.Z)("MuiBadge",e)}let x=(0,c.Z)("MuiBadge",["root","badge","dot","standard","anchorOriginTopRight","anchorOriginBottomRight","anchorOriginTopLeft","anchorOriginBottomLeft","invisible","colorError","colorInfo","colorPrimary","colorSecondary","colorSuccess","colorWarning","overlapRectangular","overlapCircular","anchorOriginTopLeftCircular","anchorOriginTopLeftRectangular","anchorOriginTopRightCircular","anchorOriginTopRightRectangular","anchorOriginBottomLeftCircular","anchorOriginBottomLeftRectangular","anchorOriginBottomRightCircular","anchorOriginBottomRightRectangular"]),C=["anchorOrigin","className","component","components","componentsProps","overlap","color","invisible","max","badgeContent","slots","slotProps","showZero","variant"],w=e=>{let{color:t,anchorOrigin:n,invisible:a,overlap:l,variant:r,classes:i={}}=e,o={root:["root"],badge:["badge",r,a&&"invisible",`anchorOrigin${(0,E.Z)(n.vertical)}${(0,E.Z)(n.horizontal)}`,`anchorOrigin${(0,E.Z)(n.vertical)}${(0,E.Z)(n.horizontal)}${(0,E.Z)(l)}`,`overlap${(0,E.Z)(l)}`,"default"!==t&&`color${(0,E.Z)(t)}`]};return(0,s.Z)(o,Z,i)},k=(0,f.ZP)("span",{name:"MuiBadge",slot:"Root",overridesResolver:(e,t)=>t.root})({position:"relative",display:"inline-flex",verticalAlign:"middle",flexShrink:0}),P=(0,f.ZP)("span",{name:"MuiBadge",slot:"Badge",overridesResolver:(e,t)=>{let{ownerState:n}=e;return[t.badge,t[n.variant],t[`anchorOrigin${(0,E.Z)(n.anchorOrigin.vertical)}${(0,E.Z)(n.anchorOrigin.horizontal)}${(0,E.Z)(n.overlap)}`],"default"!==n.color&&t[`color${(0,E.Z)(n.color)}`],n.invisible&&t.invisible]}})(({theme:e,ownerState:t})=>(0,l.Z)({display:"flex",flexDirection:"row",flexWrap:"wrap",justifyContent:"center",alignContent:"center",alignItems:"center",position:"absolute",boxSizing:"border-box",fontFamily:e.typography.fontFamily,fontWeight:e.typography.fontWeightMedium,fontSize:e.typography.pxToRem(12),minWidth:20,lineHeight:1,padding:"0 6px",height:20,borderRadius:10,zIndex:1,transition:e.transitions.create("transform",{easing:e.transitions.easing.easeInOut,duration:e.transitions.duration.enteringScreen})},"default"!==t.color&&{backgroundColor:(e.vars||e).palette[t.color].main,color:(e.vars||e).palette[t.color].contrastText},"dot"===t.variant&&{borderRadius:4,height:8,minWidth:8,padding:0},"top"===t.anchorOrigin.vertical&&"right"===t.anchorOrigin.horizontal&&"rectangular"===t.overlap&&{top:0,right:0,transform:"scale(1) translate(50%, -50%)",transformOrigin:"100% 0%",[`&.${x.invisible}`]:{transform:"scale(0) translate(50%, -50%)"}},"bottom"===t.anchorOrigin.vertical&&"right"===t.anchorOrigin.horizontal&&"rectangular"===t.overlap&&{bottom:0,right:0,transform:"scale(1) translate(50%, 50%)",transformOrigin:"100% 100%",[`&.${x.invisible}`]:{transform:"scale(0) translate(50%, 50%)"}},"top"===t.anchorOrigin.vertical&&"left"===t.anchorOrigin.horizontal&&"rectangular"===t.overlap&&{top:0,left:0,transform:"scale(1) translate(-50%, -50%)",transformOrigin:"0% 0%",[`&.${x.invisible}`]:{transform:"scale(0) translate(-50%, -50%)"}},"bottom"===t.anchorOrigin.vertical&&"left"===t.anchorOrigin.horizontal&&"rectangular"===t.overlap&&{bottom:0,left:0,transform:"scale(1) translate(-50%, 50%)",transformOrigin:"0% 100%",[`&.${x.invisible}`]:{transform:"scale(0) translate(-50%, 50%)"}},"top"===t.anchorOrigin.vertical&&"right"===t.anchorOrigin.horizontal&&"circular"===t.overlap&&{top:"14%",right:"14%",transform:"scale(1) translate(50%, -50%)",transformOrigin:"100% 0%",[`&.${x.invisible}`]:{transform:"scale(0) translate(50%, -50%)"}},"bottom"===t.anchorOrigin.vertical&&"right"===t.anchorOrigin.horizontal&&"circular"===t.overlap&&{bottom:"14%",right:"14%",transform:"scale(1) translate(50%, 50%)",transformOrigin:"100% 100%",[`&.${x.invisible}`]:{transform:"scale(0) translate(50%, 50%)"}},"top"===t.anchorOrigin.vertical&&"left"===t.anchorOrigin.horizontal&&"circular"===t.overlap&&{top:"14%",left:"14%",transform:"scale(1) translate(-50%, -50%)",transformOrigin:"0% 0%",[`&.${x.invisible}`]:{transform:"scale(0) translate(-50%, -50%)"}},"bottom"===t.anchorOrigin.vertical&&"left"===t.anchorOrigin.horizontal&&"circular"===t.overlap&&{bottom:"14%",left:"14%",transform:"scale(1) translate(-50%, 50%)",transformOrigin:"0% 100%",[`&.${x.invisible}`]:{transform:"scale(0) translate(-50%, 50%)"}},t.invisible&&{transition:e.transitions.create("transform",{easing:e.transitions.easing.easeInOut,duration:e.transitions.duration.leavingScreen})})),_=r.forwardRef(function(e,t){var n,r,s,c,d,u;let g;let p=(0,b.Z)({props:e,name:"MuiBadge"}),{anchorOrigin:h={vertical:"top",horizontal:"right"},className:f,component:E="span",components:Z={},componentsProps:x={},overlap:_="rectangular",color:M="default",invisible:O=!1,max:S,badgeContent:R,slots:z,slotProps:$,showZero:B=!1,variant:A="standard"}=p,I=(0,a.Z)(p,C),T=(0,o.Z)({anchorOrigin:h,color:M,overlap:_,variant:A}),W=O;!1!==O||(0!==R||B)&&(null!=R||"dot"===A)||(W=!0);let{color:j=M,overlap:H=_,anchorOrigin:F=h,variant:N=A}=W?T:p,D=(0,l.Z)({},p,{anchorOrigin:F,invisible:W,color:j,overlap:H,variant:N}),U=w(D);"dot"!==N&&(g=R&&Number(R)>S?`${S}+`:R);let L=null!=(n=null!=(r=null==z?void 0:z.root)?r:Z.Root)?n:k,V=null!=(s=null!=(c=null==z?void 0:z.badge)?c:Z.Badge)?s:P,K=null!=(d=null==$?void 0:$.root)?d:x.root,X=null!=(u=null==$?void 0:$.badge)?u:x.badge;return(0,m.jsx)(v,(0,l.Z)({invisible:O,badgeContent:g,showZero:B,max:S},I,{slots:{root:L,badge:V},className:(0,i.default)(null==K?void 0:K.className,U.root,f),slotProps:{root:(0,l.Z)({},K,(0,y.Z)(L)&&{as:E,ownerState:(0,l.Z)({},null==K?void 0:K.ownerState,{anchorOrigin:F,color:j,overlap:H,variant:N})}),badge:(0,l.Z)({},X,{className:(0,i.default)(U.badge,null==X?void 0:X.className)},(0,y.Z)(V)&&{ownerState:(0,l.Z)({},null==X?void 0:X.ownerState,{anchorOrigin:F,color:j,overlap:H,variant:N})})},ref:t}))});var M=_},69633:function(e,t,n){var a=n(28442);let l=e=>{return!e||!(0,a.Z)(e)};t.Z=l},69709:function(e,t,n){n.r(t);var a=n(85597),l=n(27274),r=n(7961),i=n(6867),o=n(81719),s=n(62097),c=n(67294),d=n(93836),u=n(42977),g=n(17563);let m=(0,o.ZP)(r.Z,{name:"AvatarWrapper"})(({theme:e})=>({borderWidth:"thin",borderStyle:"solid",borderColor:e.palette.border.secondary})),p=(0,o.ZP)(a.rUS,{name:"Link"})(({theme:e})=>({"&:hover":{textDecoration:"none"}})),h=(0,o.ZP)(i.Z,{shouldForwardProp:e=>"status"!==e})(({theme:e,status:t})=>({"& .MuiBadge-badge":{...0===t&&{display:"none"},...1===t&&{color:e.palette.success.main,backgroundColor:e.palette.success.main},...2===t&&{color:e.palette.warning.main,backgroundColor:e.palette.warning.main},...3===t&&{color:e.palette.error.main,backgroundColor:e.palette.error.main},boxShadow:"dark"===e.palette.mode?"none":`0 0 0 2px ${e.palette.background.paper}`,"&::after":{position:"absolute",top:0,left:0,width:"100%",height:"100%",borderRadius:"50%",animation:"ripple 1.2s infinite ease-in-out",border:"1px solid currentColor",content:'""'}}})),v=(0,o.ZP)("div",{shouldForwardProp:e=>"sizeProps"!==e})(({theme:e,sizeProps:t})=>({position:"relative",width:"32px",height:"32px",...t&&{width:t,height:t}})),f=(0,o.ZP)(r.Z,{shouldForwardProp:e=>"sizeProps"!==e})(({theme:e,sizeProps:t})=>({position:"absolute",bottom:0,left:0,width:"26px",height:"26px",backgroundColor:"dark"===e.palette.mode?e.palette.grey["600"]:e.palette.grey["300"],borderWidth:1,borderStyle:"solid",borderColor:"light"===e.palette.mode?e.palette.grey["50"]:e.palette.grey["800"],zIndex:2,...t&&{width:t-6,height:t-6}})),b=(0,o.ZP)(r.Z,{shouldForwardProp:e=>"sizeProps"!==e})(({theme:e,sizeProps:t})=>({position:"relative",top:-2,left:6,width:"26px",height:"26px",...t&&{width:`${t-6}px !important`,height:`${t-6}px !important`}}));t.default=(0,c.memo)(function({username:e="Name",name:t="Name",src:n,size:i,to:o,onClick:y,status:E=0,room:Z,uploadLocal:x=!1,roomType:C=null,avatarETag:w,hoverCard:k=!1,component:P="span"}){var _,M;let{getSetting:O}=(0,a.OgA)(),S=(0,s.Z)(),R=C||(null==Z?void 0:Z.t),z=(0,l.vK)(t),$={width:i,height:i,color:S.palette.grey["50"],fontSize:S.mixins.pxToRem(14.4)},B=O("chatplus"),A=null==B?void 0:null===(_=B.server)||void 0===_?void 0:_.replace(/\/$/,""),I=n;if(I||(I=w?`${A}/avatar/${e}?${g.stringify({etag:w})}`:""),z&&($.backgroundColor=l.kU.hex(z)),o)return c.createElement(p,{to:o||`/${e}`},c.createElement(m,{src:I,alt:t,style:$,component:P,children:z}));if([d.n.Private,d.n.Public].includes(R)){if(x)return c.createElement(m,{src:I,alt:t,style:$,component:P,children:z});if(null==Z?void 0:Z.avatarETag)return I=`${A}/avatar/room/${null==Z?void 0:Z.id}?${g.stringify({etag:null==Z?void 0:Z.avatarETag})}`,c.createElement(m,{src:I,alt:t,style:$,component:P,children:z});{let T=`${A}/avatar/${null==Z?void 0:null===(M=Z.u)||void 0===M?void 0:M.username}`;return c.createElement(v,{sizeProps:i},c.createElement(f,{sizeProps:i,src:T,alt:t,component:P,children:z}),c.createElement(b,{sizeProps:i,src:I,alt:t,style:$,component:P,children:z}))}}if(R===d.n.Direct&&E){let W=(0,u.xW)(E);return c.createElement(h,{overlap:"circular",anchorOrigin:{vertical:"bottom",horizontal:"right"},variant:"dot",status:W},c.createElement(r.Z,{src:I,alt:t,style:$,component:P,children:z}))}return c.createElement(m,{src:I,alt:t,style:$,component:P,children:z,onClick:y,to:e,hoverCard:k})})},48724:function(e,t,n){n.r(t),n.d(t,{default:function(){return eC}});var a=n(85597),l=n(39312),r=n(21241),i=n(76224),o=n(30120),s=n(21822),c=n(81719),d=n(38790),u=n(91647),g=n(67294),m=n(80036),p=n(86706),h=n(38781),v=n(93836),f=n(27274),b=n(50130),y=n(1469),E=n.n(y),Z=n(41609),x=n.n(Z),C=n(96974),w=n(69709),k=n(42977);function P({className:e,value:t,component:n="span"}){let{moment:l}=(0,a.OgA)();if(!t)return null;let r=l(t),i=r.format("llll"),o=(0,k.jF)(r);return g.createElement(d.Z,{title:i},g.createElement(n,{role:"link",className:e,"aria-label":i},o))}let _=(0,c.ZP)("div",{shouldForwardProp:e=>"unread"!==e&&"isFocus"!==e&&"isMobile"!==e})(({theme:e,unread:t,isFocus:n,isMobile:a})=>({marginRight:e.spacing(1),padding:e.spacing(.5,1.5,.5,2),cursor:"pointer",transition:"background-color 300ms ease",display:"flex",alignItems:"center",marginBottom:e.spacing(.5),borderRadius:e.spacing(1),...n&&{background:e.palette.action.focus},"&:hover":{background:e.palette.action.selected},"&:hover .itemSubtitle,&:hover .uiItemUnReadDot":{visibility:"hidden"},"&:hover .uiChatItemBtn":{visibility:"visible"},...a&&{".uiChatItemBtn, &:hover .itemSubtitle":{visibility:"visible"}}})),M=(0,c.ZP)("div")(({theme:e})=>({flex:1,display:"flex",alignItems:"center",padding:e.spacing(1.5,0),color:e.palette.grey["700"],fontSize:e.spacing(1.75),cursor:"pointer",overflow:"hidden"})),O=(0,c.ZP)("div")(({theme:e})=>({marginRight:e.spacing(1)})),S=(0,c.ZP)("div")(({theme:e})=>({flex:1,minWidth:"0"})),R=(0,c.ZP)("div")(({theme:e})=>({display:"flex",justifyContent:"space-between",alignItems:"center"})),z=(0,c.ZP)("div")(({theme:e})=>({fontWeight:"bold",fontSize:e.spacing(1.875),lineHeight:e.spacing(2.25),color:e.palette.text.primary,flex:1,minWidth:0,maxWidth:"100%",whiteSpace:"nowrap",textOverflow:"ellipsis",overflow:"hidden"})),$=(0,c.ZP)("div")(({theme:e})=>({color:e.palette.text.secondary,display:"inline-flex",alignItems:"center",fontSize:e.spacing(1.625),lineHeight:e.spacing(2.35)})),B=(0,c.ZP)(i.zb,{shouldForwardProp:e=>"favorite"!==e})(({theme:e,favorite:t})=>({marginRight:e.spacing(.5),...t&&{color:e.palette.primary.main}})),A=(0,c.ZP)("div",{shouldForwardProp:e=>"unread"!==e})(({theme:e,unread:t})=>({display:"block",color:e.palette.text.secondary,padding:0,minWidth:0,maxWidth:"calc(100% - 12px)",overflow:"hidden",textOverflow:"ellipsis",fontSize:e.spacing(1.625),lineHeight:e.spacing(2.35),whiteSpace:"nowrap",...t&&{fontWeight:e.typography.fontWeightBold},a:{color:e.palette.text.secondary,pointerEvents:"none"},br:{display:"none"}})),I=(0,c.ZP)(b.Z,{slot:"UIChatItemBtn"})(({theme:e})=>({position:"relative",padding:e.spacing(1,.5),cursor:"pointer",minWidth:e.spacing(3),lineHeight:e.spacing(2.5)})),T=(0,c.ZP)("div",{slot:"root",shouldForwardProp:e=>"isSelfChat"!==e})(({theme:e,isSelfChat:t})=>({paddingLeft:e.spacing(1),visibility:"hidden","& .MuiActionMenu-menu":{overflow:"auto",maxHeight:"200px"},...t&&{minWidth:"24px",minHeight:"40px"}})),W=(0,c.ZP)("span",{slot:"UnReadDot"})(({theme:e})=>({display:"inline-block",width:12,height:12,backgroundColor:e.palette.primary.main,borderRadius:20,marginTop:e.spacing(.25)}));function j({item:e,handleResetSearch:t}){var n,r,o,s,c,u,b,y;let{usePageParams:Z,dispatch:k,i18n:j,useActionControl:H,ItemActionMenu:F,useIsMobile:N,navigate:D,useScrollRef:U}=(0,a.OgA)(),L=Z(),V=(0,C.TH)(),K=U(),{rid:X}=L,Q=N(),q=(0,p.v9)(t=>{return(0,m.TC)(t,null==e?void 0:e.id)}),G=(0,l.jS)(null==e?void 0:e.id),J=(0,l.ID)(null==e?void 0:e.userId),[Y]=H(null==e?void 0:e.id,{}),ee=(0,l.ip)(),et=(0,l.pB)(null==e?void 0:e.id),en=(0,l.F5)(null==e?void 0:e.id),ea=(0,l.Fy)(),el=(0,l.XP)(null==e?void 0:e.id),er=(0,l.Pu)(null==e?void 0:e.id),ei=(0,l.Oy)(),eo=g.useCallback(()=>{if(t&&t(),(null==e?void 0:e.no_join)||!G){if(null==e?void 0:e.spotlight_user){k({type:"chatplus/chatRoom/newConversation",payload:{users:[{value:null==e?void 0:e.username,label:null==e?void 0:e.name}]}});return}k({type:"chatplus/room/openChatRoomFromBuddy",payload:{rid:e.id||e._id}});return}V.pathname.includes("/messages")||X?D({pathname:`/messages/${e.id}`}):k({type:"chatplus/room/openBuddy",payload:{rid:e.id}})},[e]),es=er?1:(null==J?void 0:J.status)||0,ec=!!((null==en?void 0:en.t)===v.n.Direct&&((null==G?void 0:G.blocked)||(null==G?void 0:G.blocker))),ed=!!((null==en?void 0:en.t)===v.n.Direct&&((null==G?void 0:G.metafoxBlocked)||(null==G?void 0:G.metafoxBlocker))),eu=!!(ec||ed),eg=!((null==G?void 0:G.allowMessageFrom)!=="noone"),em=ei&&en&&en.muted&&E()(en.muted)&&!!en.muted.find(e=>e===ei.username),ep=g.useMemo(()=>{return(0,f.W$)(ee,{room:e,subscription:G,groups:null==et?void 0:et.groups,favorite:null==G?void 0:G.f,settings:ea,perms:el,isBlocked:eu,isMetaFoxBlocked:ed,isMuted:em,allowMsgNoOne:eg})},[et,G,en,ea,el,ee,eu,ed,em,eg]),eh=!(null==e?void 0:e.no_join)&&!x()(null==e?void 0:e.lastMessage)&&((null==G?void 0:G.alert)||!!(null==G?void 0:G.unread)),ev=(0,h.kK)(null==e?void 0:e.lastMessage);if(e&&(null==e?void 0:null===(n=e.lastMessage)||void 0===n?void 0:null===(r=n.attachments)||void 0===r?void 0:r.length)>0&&(null==e?void 0:e.lastMessage.attachments[0].type)==="file"){let ef=null==e?void 0:e.lastMessage.attachments.filter(e=>e.origin).length;ev=(null==e?void 0:e.lastMessage.attachments[0].image_url)?g.createElement(g.Fragment,null,g.createElement(B,{icon:"ico-photo"}),ef>1?j.formatMessage({id:"sent_multiple_photos"}):j.formatMessage({id:"sent_a_photo"})):g.createElement(g.Fragment,null,g.createElement(B,{icon:"ico-paperclip-alt"}),j.formatMessage({id:"sent_a_file"}))}return g.createElement(_,{unread:eh,isFocus:!(null==e?void 0:e.no_join)&&X===e.id,isMobile:Q},g.createElement(M,{onClick:eo},g.createElement(O,null,g.createElement(w.default,{name:(null==q?void 0:q.name)||e.name,username:(null==q?void 0:q.username)||e.fname||e.username,avatarETag:(null==q?void 0:q.avatarETag)||(null==e?void 0:e.avatarETag),size:40,src:null==q?void 0:q.avatar,status:es,room:e})),g.createElement(S,null,g.createElement(R,null,(null==G?void 0:G.f)?g.createElement(B,{favorite:!0,icon:"ico-heart"}):null,g.createElement(z,null,(null==q?void 0:q.name)||e.fname||e.name),(null==e?void 0:e.no_join)?null:g.createElement($,{className:"itemSubtitle"},g.createElement(P,{value:null==e?void 0:null===(o=e.lastMessage)||void 0===o?void 0:null===(s=o._updatedAt)||void 0===s?void 0:s.$date}))),(null==e?void 0:e.no_join)||x()(null==e?void 0:e.lastMessage)?null:g.createElement(R,null,g.createElement(A,{unread:eh},null==e?void 0:null===(c=e.lastMessage)||void 0===c?void 0:null===(u=c.u)||void 0===u?void 0:u.name,":"," ",E()(ev)?j.formatMessage(ev[0]):(null==e?void 0:null===(b=e.lastMessage)||void 0===b?void 0:null===(y=b.attachments)||void 0===y?void 0:y.length)?ev:g.createElement("span",{dangerouslySetInnerHTML:{__html:ev}})),(null==G?void 0:G.alert)||(null==G?void 0:G.unread)?g.createElement(W,{className:"uiItemUnReadDot"}):null))),(null==e?void 0:e.no_join)||!G?null:g.createElement(T,{isSelfChat:er,className:"uiChatItemBtn"},er?null:g.createElement(F,{items:ep,placement:"bottom-end",scrollRef:K,handleAction:Y,scrollClose:!0,control:g.createElement(d.Z,{title:j.formatMessage({id:"more"}),placement:"top"},g.createElement(I,{disableFocusRipple:!0,disableRipple:!0,disableTouchRipple:!0},g.createElement(i.zb,{icon:"ico-dottedmore-vertical-o"})))})))}let H="ArchivedList-ChatRoom",F=(0,c.ZP)("div",{name:H,slot:"NoResult"})(({theme:e})=>({padding:e.spacing(1.5,0),fontSize:e.spacing(1.875),color:e.palette.grey["600"],textAlign:"center"})),N=(0,c.ZP)("div",{name:H,slot:"NoMessages"})(({theme:e})=>({width:"100%",height:"100%",display:"flex",justifyContent:"center",alignItems:"center",padding:e.spacing(1.5,0),fontSize:e.spacing(1.875),color:e.palette.grey["600"],textAlign:"center"})),D=(0,c.ZP)("div",{name:H,slot:"MoreConversations"})(({theme:e})=>({padding:e.spacing(1.5,2),cursor:"pointer","& .ico":{display:"inline-block",width:e.spacing(4),height:e.spacing(4),lineHeight:"32px",backgroundColor:"dark"===e.palette.mode?e.palette.grey["400"]:e.palette.grey["300"],borderRadius:"50%",color:e.palette.grey["600"],textAlign:"center",marginRight:e.spacing(1)},"& span":{...e.typography.h5}})),U=({archivedMessages:e})=>{let{i18n:t}=(0,a.OgA)(),[n,l]=g.useState(20),r=g.useCallback(()=>{l(e=>e+10)},[]);return g.createElement("div",null,null==e?void 0:e.slice(0,n).map(e=>g.createElement(j,{item:e,key:e.id})),e.length>20?g.createElement(D,{onClick:r},g.createElement(i.zb,{icon:"ico-angle-down"}),g.createElement("span",null,t.formatMessage({id:"more_conversations"}))):null)};var L=function({searchValue:e}){let{i18n:t}=(0,a.OgA)(),n=(0,p.v9)(t=>(0,m.ev)(t,e));return n.length?e&&!n.length?g.createElement(F,null,t.formatMessage({id:"no_results_found"})):g.createElement(U,{archivedMessages:n}):g.createElement(N,null,t.formatMessage({id:"no_messages"}))},V=n(71682);let K="loading-skeleton",X=(0,c.ZP)(o.Z)(({theme:e})=>({display:"flex",alignItems:"center",justifyContent:"space-between",height:72,padding:e.spacing(.5,1)})),Q=(0,c.ZP)(o.Z,{name:K,slot:"BlockCollapse",shouldForwardProp:e=>"title"!==e})(({theme:e,title:t})=>({...t&&{borderTop:e.mixins.border("secondary")}})),q=(0,c.ZP)("div",{name:K,slot:"BlockCollapseHeader"})(({theme:e})=>({padding:e.spacing(2.5,2,2.5,3),display:"flex",flexDirection:"row",justifyContent:"space-between",alignItems:"center",cursor:"pointer"})),G=(0,c.ZP)("div",{name:K,slot:"BlockTitle"})(({theme:e})=>({...e.typography.body1,color:e.palette.text.primary,fontWeight:"600"}));function J({title:e}){return g.createElement(Q,{title:e},e?g.createElement(q,null,g.createElement(G,null,e),g.createElement(b.Z,{size:"small",color:"default"},g.createElement(i.zb,{icon:"ico-angle-up"}))):null,[,,,,].fill(0).map((e,t)=>g.createElement(X,{key:t},g.createElement(i.vk,null,g.createElement(V.Z,{variant:"avatar",width:40,height:40,sx:{mr:1}})),g.createElement(i.eT,null,g.createElement(i.XQ,null,g.createElement(V.Z,{variant:"text",width:"100%"})),g.createElement(i.XQ,null,g.createElement(V.Z,{width:120}))))))}let Y="BuddyList-ChatRoom",ee=(0,c.ZP)("div",{name:Y,slot:"NoResult"})(({theme:e})=>({padding:e.spacing(1.5,0),fontSize:e.spacing(1.875),color:e.palette.grey["600"],textAlign:"center"})),et=(0,c.ZP)("div",{name:Y,slot:"NoMessages"})(({theme:e})=>({width:"100%",height:"100%",display:"flex",justifyContent:"center",alignItems:"center",padding:e.spacing(1.5,0),fontSize:e.spacing(1.875),color:e.palette.grey["600"],textAlign:"center"})),en=(0,c.ZP)("div",{name:Y,slot:"MoreConversations"})(({theme:e})=>({padding:e.spacing(1.5,2),cursor:"pointer","& .ico":{display:"inline-block",width:e.spacing(4),height:e.spacing(4),lineHeight:"32px",backgroundColor:"dark"===e.palette.mode?e.palette.grey["400"]:e.palette.grey["300"],borderRadius:"50%",color:e.palette.grey["600"],textAlign:"center",marginRight:e.spacing(1)},"& span":{...e.typography.h5}})),ea=(0,c.ZP)("div",{name:Y,slot:"BlockCollapse"})(({theme:e})=>({borderTop:e.mixins.border("secondary")})),el=(0,c.ZP)("div",{name:Y,slot:"BlockCollapseHeader"})(({theme:e})=>({padding:e.spacing(2.5,2,2.5,3),display:"flex",flexDirection:"row",justifyContent:"space-between",alignItems:"center",cursor:"pointer"})),er=(0,c.ZP)("div",{name:Y,slot:"BlockTitle"})(({theme:e})=>({...e.typography.body1,color:e.palette.text.primary,fontWeight:"600"})),ei=({data:e,loading:t,handleResetSearch:n})=>{let{i18n:l}=(0,a.OgA)(),[r,o]=g.useState(5),s=g.useCallback(()=>{o(e=>e+5)},[]),c=null==e?void 0:e.slice(0,r);return t?g.createElement(J,null):g.createElement("div",null,c.map(e=>g.createElement(j,{item:e,key:e.id,handleResetSearch:n})),e.length>5?c.length===e.length?g.createElement(en,{sx:{textAlign:"center"}},g.createElement("span",null,l.formatMessage({id:"no_more_conversations"}))):g.createElement(en,{onClick:s},g.createElement(i.zb,{icon:"ico-angle-down"}),g.createElement("span",null,l.formatMessage({id:"more_conversations"}))):null)},eo=({title:e,data:t,searchValue:n,roomIdActive:l,loading:r,handleResetSearch:o})=>{let{i18n:s}=(0,a.OgA)(),[c,d]=g.useState(!1),u=g.useCallback(()=>d(e=>!e),[]);g.useEffect(()=>{let e=t.filter(e=>e.id===l);(n||(null==e?void 0:e.length))&&d(!0)},[n,t]);let[m,p]=g.useState(5),h=g.useCallback(()=>{p(e=>e+5)},[]);return r?g.createElement(J,{title:e}):t.length?g.createElement(ea,null,g.createElement(el,{onClick:u},g.createElement(er,null,e),g.createElement(b.Z,{size:"small",color:"default"},g.createElement(i.zb,{icon:c?"ico-angle-up":"ico-angle-down"}))),c?g.createElement("div",null,null==t?void 0:t.slice(0,m).map(e=>{return g.createElement(j,{item:e,key:null==e?void 0:e.id,handleResetSearch:o})}),t.length>5?(null==t?void 0:t.slice(0,m).length)===t.length?g.createElement(en,{sx:{textAlign:"center"}},g.createElement("span",null,s.formatMessage({id:"no_more_conversations"}))):g.createElement(en,{onClick:h},g.createElement(i.zb,{icon:"ico-angle-down"}),g.createElement("span",null,s.formatMessage({id:"more_conversations"}))):null):null):null};var es=function({rid:e,searchValue:t,handleResetSearch:n}){let{i18n:l,dispatch:r}=(0,a.OgA)(),{directChats:i,publicGroups:o,isFetchDone:s}=(0,p.v9)(e=>(0,m.j6)(e,t,!0));return(g.useEffect(()=>{return t&&r({type:"chatplus/spotlight",payload:{query:t,users:!0,rooms:!0}}),()=>{r({type:"chatplus/spotlight/reset"})}},[t]),!s||i.length||o.length)?!t||i.length||o.length?g.createElement("div",null,g.createElement(ei,{data:i,searchValue:t,handleResetSearch:n,loading:!s}),g.createElement(eo,{title:l.formatMessage({id:"public_group_chats"}).toUpperCase(),data:o,searchValue:t,roomIdActive:e,handleResetSearch:n,loading:!s})):g.createElement(ee,null,l.formatMessage({id:"no_results_found"})):g.createElement(et,null,l.formatMessage({id:"no_messages"}))},ec=n(96035);let ed="BuddyBlock",eu=(0,c.ZP)(o.Z,{name:ed,slot:"root"})(({theme:e})=>({backgroundColor:e.palette.background.paper,width:"100%",height:"100%",display:"flex",flexDirection:"column"})),eg=(0,c.ZP)("div",{name:ed,slot:"WrapperHeader"})(({theme:e})=>({})),em=(0,c.ZP)("div",{name:ed,slot:"BackAllMessages"})(({theme:e})=>({cursor:"pointer",color:e.palette.primary.main})),ep=(0,c.ZP)("div")(({theme:e})=>({alignItems:"center",boxSizing:"border-box",display:"flex",height:e.spacing(9),padding:e.spacing(1,1,1,2),justifyContent:"space-between"})),eh=(0,c.ZP)("div")(({theme:e})=>({fontSize:e.mixins.pxToRem(24),lineHeight:e.mixins.pxToRem(36),fontWeight:e.typography.fontWeightMedium})),ev=(0,c.ZP)("div")(({theme:e})=>({display:"flex",alignItems:"flex-start",justifyContent:"center",flexDirection:"column",boxSizing:"border-box",height:e.spacing(9),padding:e.spacing(1,1,1,2)})),ef=(0,c.ZP)("div")(({theme:e})=>({})),eb=(0,c.ZP)("div")(({theme:e})=>({padding:e.spacing(1,1,1,2)})),ey=(0,c.ZP)(s.Z)(({theme:e})=>({color:"light"===e.palette.mode?e.palette.grey["600"]:e.palette.text.primary,fontSize:e.spacing(2.75),minWidth:"auto","& .ico-circle ":{fontSize:e.spacing(1.75)},"& .ico-gear-o":{fontSize:e.spacing(1.75)},"& .ico-inbox-o":{fontSize:e.spacing(1.75)}})),eE=(0,c.ZP)("span")(({theme:e})=>({display:"inline-flex",textAlign:"center",alignItems:"center",justifyContent:"center"})),eZ=(0,c.ZP)("div")(({theme:e})=>({backgroundColor:e.palette.background.paper,padding:e.spacing(1,0,1,1),flex:1,minHeight:0})),ex=(0,a.j4Z)({name:"ChatplusBuddyBlock",extendBlock:function(e){var t,n;let{i18n:o,dispatch:s,useActionControl:c,usePageParams:m}=(0,a.OgA)(),p=g.useRef(),{searchText:h}=(0,ec.Z)(),[v,f]=g.useState(h||""),b=m(),{rid:y}=b,[E,Z]=g.useState(!1);g.useEffect(()=>{return()=>{s({type:"chatplus/spotlight/clearSearching"})}},[]);let[x]=c(null,{}),C=(e,t,n)=>{x(e,t,n),e.includes("archivedChatMode")&&Z(!0)},w=(0,l.Oy)(),k=[{label:"online",icon:"ico-circle",color:"success",value:"closeMenu, chatplus/setUserStatus:online",testid:"status_online",item_name:"online"},{label:"away",icon:"ico-circle",color:"warning",value:"closeMenu, chatplus/setUserStatus:away",testid:"status_away",item_name:"away"},{label:"busy",icon:"ico-circle",color:"danger",value:"closeMenu, chatplus/setUserStatus:busy",testid:"status_busy",item_name:"busy"},{label:"invisible",icon:"ico-circle",color:"gray",value:"closeMenu, chatplus/setUserStatus:offline",testid:"status_offline",item_name:"offline"},{as:"divider",testid:"status_divider"},{label:"settings",icon:"ico-gear-o",value:"closeMenu, chatplus/editUserPreferences",testid:"editUserPreferences"},{label:"archived_chats",icon:"ico-inbox-o",value:"closeMenu, chatplus/archivedChatMode",testid:"archivedChatMode"}],P=g.useMemo(()=>{return k.map(e=>{return(null==w?void 0:w.status)===e.item_name?{...e,active:!0}:e})},[k,w]),_=()=>{s({type:"chatplus/room/newGroup",payload:{}})},M=()=>{s({type:"chatplus/room/newConversationPage",payload:{}})},O=e=>{e&&e.target&&f(e.target.value)},S=()=>{f(""),s({type:"chatplus/spotlight/clearSearching"})};return E?g.createElement(eu,null,g.createElement(eg,null,g.createElement(ev,null,g.createElement(em,{onClick:()=>Z(!1)},g.createElement(u.Z,{variant:"body2"},o.formatMessage({id:"all_messages"}))),g.createElement(u.Z,{component:"h1",variant:"h3"},o.formatMessage({id:"archived_groups"}))),g.createElement(eb,null,g.createElement(i.Rj,{placeholder:o.formatMessage({id:"search_archived_group"}),value:v,onChange:O}))),g.createElement(eZ,null,g.createElement(r.l$,{autoHide:!0,autoHeight:!0,autoHeightMax:"100%",ref:p},g.createElement(L,{searchValue:v})))):g.createElement(eu,null,g.createElement(eg,null,g.createElement(ep,null,g.createElement(eh,null,o.formatMessage({id:"messages"})),g.createElement(ef,null,g.createElement(d.Z,{title:null!==(t=o.formatMessage({id:"create_new_group"}))&&void 0!==t?t:"",placement:"top"},g.createElement(ey,{onClick:_},g.createElement(i.zb,{icon:"ico-user2-three-o"}))),g.createElement(d.Z,{title:null!==(n=o.formatMessage({id:"new_conversation"}))&&void 0!==n?n:"",placement:"top"},g.createElement(ey,{onClick:M},g.createElement(i.zb,{icon:"ico-compose"}))),g.createElement(ey,null,g.createElement(i.VK,{items:P,disablePortal:!0,handleAction:C,control:g.createElement(d.Z,{title:o.formatMessage({id:"more"}),placement:"top"},g.createElement(eE,null,g.createElement(i.zb,{icon:"ico-dottedmore-o"})))})))),g.createElement(eb,null,g.createElement(i.Rj,{placeholder:o.formatMessage({id:"search_people_group"}),value:v,onChange:O}))),g.createElement(eZ,null,g.createElement(r.l$,{autoHide:!0,autoHeight:!0,autoHeightMax:"100%",ref:p},g.createElement(es,{searchValue:v,rid:y,handleResetSearch:S}))))}});var eC=ex},96035:function(e,t,n){n.d(t,{Z:function(){return r}});var a=n(86706),l=n(80036);function r(){return(0,a.v9)(e=>(0,l.HT)(e))}},38781:function(e,t,n){n.d(t,{Hj:function(){return d},ZP:function(){return u},kK:function(){return m}});var a=n(7187),l=n.n(a),r=n(42977);function i(e){return e.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,"$1<br/>$2")}function o(e,t={}){let{mentions:n=[]}=t,a=e.replace(/\[(\s)*\]\(([^\)]+)\)/gm,"");return n&&n.length>0&&(a=a.replace(/@([^\s\\#@:]+)/giu,(e,t)=>{let a=n.find(e=>e.username===t);return a&&a.name?`@${a.name}`:e})),a}function s(e,t={}){let{mentions:n=[]}=t,a=e.replace(/@\[([^\]]+)\]\(([^#@:]+)\)/giu,"<a onclick=\"triggerClick('$2')\">@$1</a>");return n&&n.length>0&&(a=a.replace(/@([^\s\\#@:]+)/giu,(e,t)=>{let a=n.find(e=>e.username===t);return a&&a.name?`<a onclick="triggerClick('/${t}')">@${a.name}</a>`:e})),a}window.triggerClick=r.Sv;let c=e=>e.replace(/:\w+:/gi,e=>e),d=(e,t={})=>{return e?o(e,t):null};function u(e,t={}){var n;return e?i(c(s((n=o(e),l()(n)).replace(/(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim,'<a onclick="triggerClick(\'$1\',true)" target="_blank">$1</a>'),t))):null}let g=(e={})=>{return/^(\w+)_(audio|video)_call_([cpd]$)/.test((null==e?void 0:e.t)||(null==e?void 0:e.type))};function m(e){var t;let n=null==e?void 0:e.t;return["rm"].includes(n)?[{id:"message_was_deleted"}]:g(e)?/(invite)_/.test(n)||/(start)_/.test(n)?[{id:"started_a_call"}]:/(miss)_/.test(n)?[{id:"missed_a_call"}]:[{id:"call_ended"}]:(null==e?void 0:e.msg)?i(c(s((t=o(null==e?void 0:e.msg),l()(t)),{mentions:(null==e?void 0:e.mentions)||[]}))):null}}}]);