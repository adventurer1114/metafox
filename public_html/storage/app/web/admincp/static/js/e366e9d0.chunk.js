"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-chatplus-components-MsgContent-MesagePinned","metafox-chatplus-components-Avatar"],{6867:function(e,t,r){r.d(t,{Z:function(){return C}});var n=r(63366),i=r(87462),a=r(67294),o=r(86010),l=r(2097),s=r(94780),d=r(1588),c=r(34867);function p(e){return(0,c.Z)("MuiBadge",e)}(0,d.Z)("MuiBadge",["root","badge","invisible"]);var u=r(34261),g=r(85893);let m=["badgeContent","component","children","invisible","max","slotProps","slots","showZero"],h=e=>{let{invisible:t}=e;return(0,s.Z)({root:["root"],badge:["badge",t&&"invisible"]},p,void 0)},f=a.forwardRef(function(e,t){let{component:r,children:a,max:o=99,slotProps:s={},slots:d={},showZero:c=!1}=e,p=(0,n.Z)(e,m),{badgeContent:f,max:v,displayValue:x,invisible:b}=function(e){let{badgeContent:t,invisible:r=!1,max:n=99,showZero:i=!1}=e,a=(0,l.Z)({badgeContent:t,max:n}),o=r;!1!==r||0!==t||i||(o=!0);let{badgeContent:s,max:d=n}=o?a:e,c=s&&Number(s)>d?`${d}+`:s;return{badgeContent:s,invisible:o,max:d,displayValue:c}}((0,i.Z)({},e,{max:o})),y=(0,i.Z)({},e,{badgeContent:f,invisible:b,max:v,showZero:c}),w=h(y),P=r||d.root||"span",O=(0,u.Z)({elementType:P,externalSlotProps:s.root,externalForwardedProps:p,additionalProps:{ref:t},ownerState:y,className:w.root}),Z=d.badge||"span",_=(0,u.Z)({elementType:Z,externalSlotProps:s.badge,ownerState:y,className:w.badge});return(0,g.jsxs)(P,(0,i.Z)({},O,{children:[a,(0,g.jsx)(Z,(0,i.Z)({},_,{children:x}))]}))});var v=r(81719),x=r(78884),b=r(69633),y=r(36622);function w(e){return(0,c.Z)("MuiBadge",e)}let P=(0,d.Z)("MuiBadge",["root","badge","dot","standard","anchorOriginTopRight","anchorOriginBottomRight","anchorOriginTopLeft","anchorOriginBottomLeft","invisible","colorError","colorInfo","colorPrimary","colorSecondary","colorSuccess","colorWarning","overlapRectangular","overlapCircular","anchorOriginTopLeftCircular","anchorOriginTopLeftRectangular","anchorOriginTopRightCircular","anchorOriginTopRightRectangular","anchorOriginBottomLeftCircular","anchorOriginBottomLeftRectangular","anchorOriginBottomRightCircular","anchorOriginBottomRightRectangular"]),O=["anchorOrigin","className","component","components","componentsProps","overlap","color","invisible","max","badgeContent","slots","slotProps","showZero","variant"],Z=e=>{let{color:t,anchorOrigin:r,invisible:n,overlap:i,variant:a,classes:o={}}=e,l={root:["root"],badge:["badge",a,n&&"invisible",`anchorOrigin${(0,y.Z)(r.vertical)}${(0,y.Z)(r.horizontal)}`,`anchorOrigin${(0,y.Z)(r.vertical)}${(0,y.Z)(r.horizontal)}${(0,y.Z)(i)}`,`overlap${(0,y.Z)(i)}`,"default"!==t&&`color${(0,y.Z)(t)}`]};return(0,s.Z)(l,w,o)},_=(0,v.ZP)("span",{name:"MuiBadge",slot:"Root",overridesResolver:(e,t)=>t.root})({position:"relative",display:"inline-flex",verticalAlign:"middle",flexShrink:0}),k=(0,v.ZP)("span",{name:"MuiBadge",slot:"Badge",overridesResolver:(e,t)=>{let{ownerState:r}=e;return[t.badge,t[r.variant],t[`anchorOrigin${(0,y.Z)(r.anchorOrigin.vertical)}${(0,y.Z)(r.anchorOrigin.horizontal)}${(0,y.Z)(r.overlap)}`],"default"!==r.color&&t[`color${(0,y.Z)(r.color)}`],r.invisible&&t.invisible]}})(({theme:e,ownerState:t})=>(0,i.Z)({display:"flex",flexDirection:"row",flexWrap:"wrap",justifyContent:"center",alignContent:"center",alignItems:"center",position:"absolute",boxSizing:"border-box",fontFamily:e.typography.fontFamily,fontWeight:e.typography.fontWeightMedium,fontSize:e.typography.pxToRem(12),minWidth:20,lineHeight:1,padding:"0 6px",height:20,borderRadius:10,zIndex:1,transition:e.transitions.create("transform",{easing:e.transitions.easing.easeInOut,duration:e.transitions.duration.enteringScreen})},"default"!==t.color&&{backgroundColor:(e.vars||e).palette[t.color].main,color:(e.vars||e).palette[t.color].contrastText},"dot"===t.variant&&{borderRadius:4,height:8,minWidth:8,padding:0},"top"===t.anchorOrigin.vertical&&"right"===t.anchorOrigin.horizontal&&"rectangular"===t.overlap&&{top:0,right:0,transform:"scale(1) translate(50%, -50%)",transformOrigin:"100% 0%",[`&.${P.invisible}`]:{transform:"scale(0) translate(50%, -50%)"}},"bottom"===t.anchorOrigin.vertical&&"right"===t.anchorOrigin.horizontal&&"rectangular"===t.overlap&&{bottom:0,right:0,transform:"scale(1) translate(50%, 50%)",transformOrigin:"100% 100%",[`&.${P.invisible}`]:{transform:"scale(0) translate(50%, 50%)"}},"top"===t.anchorOrigin.vertical&&"left"===t.anchorOrigin.horizontal&&"rectangular"===t.overlap&&{top:0,left:0,transform:"scale(1) translate(-50%, -50%)",transformOrigin:"0% 0%",[`&.${P.invisible}`]:{transform:"scale(0) translate(-50%, -50%)"}},"bottom"===t.anchorOrigin.vertical&&"left"===t.anchorOrigin.horizontal&&"rectangular"===t.overlap&&{bottom:0,left:0,transform:"scale(1) translate(-50%, 50%)",transformOrigin:"0% 100%",[`&.${P.invisible}`]:{transform:"scale(0) translate(-50%, 50%)"}},"top"===t.anchorOrigin.vertical&&"right"===t.anchorOrigin.horizontal&&"circular"===t.overlap&&{top:"14%",right:"14%",transform:"scale(1) translate(50%, -50%)",transformOrigin:"100% 0%",[`&.${P.invisible}`]:{transform:"scale(0) translate(50%, -50%)"}},"bottom"===t.anchorOrigin.vertical&&"right"===t.anchorOrigin.horizontal&&"circular"===t.overlap&&{bottom:"14%",right:"14%",transform:"scale(1) translate(50%, 50%)",transformOrigin:"100% 100%",[`&.${P.invisible}`]:{transform:"scale(0) translate(50%, 50%)"}},"top"===t.anchorOrigin.vertical&&"left"===t.anchorOrigin.horizontal&&"circular"===t.overlap&&{top:"14%",left:"14%",transform:"scale(1) translate(-50%, -50%)",transformOrigin:"0% 0%",[`&.${P.invisible}`]:{transform:"scale(0) translate(-50%, -50%)"}},"bottom"===t.anchorOrigin.vertical&&"left"===t.anchorOrigin.horizontal&&"circular"===t.overlap&&{bottom:"14%",left:"14%",transform:"scale(1) translate(-50%, 50%)",transformOrigin:"0% 100%",[`&.${P.invisible}`]:{transform:"scale(0) translate(-50%, 50%)"}},t.invisible&&{transition:e.transitions.create("transform",{easing:e.transitions.easing.easeInOut,duration:e.transitions.duration.leavingScreen})})),E=a.forwardRef(function(e,t){var r,a,s,d,c,p;let u;let m=(0,x.Z)({props:e,name:"MuiBadge"}),{anchorOrigin:h={vertical:"top",horizontal:"right"},className:v,component:y="span",components:w={},componentsProps:P={},overlap:E="rectangular",color:C="default",invisible:I=!1,max:A,badgeContent:z,slots:R,slotProps:$,showZero:S=!1,variant:M="standard"}=m,T=(0,n.Z)(m,O),F=(0,l.Z)({anchorOrigin:h,color:C,overlap:E,variant:M}),B=I;!1!==I||(0!==z||S)&&(null!=z||"dot"===M)||(B=!0);let{color:W=C,overlap:L=E,anchorOrigin:j=h,variant:U=M}=B?F:m,N=(0,i.Z)({},m,{anchorOrigin:j,invisible:B,color:W,overlap:L,variant:U}),D=Z(N);"dot"!==U&&(u=z&&Number(z)>A?`${A}+`:z);let V=null!=(r=null!=(a=null==R?void 0:R.root)?a:w.Root)?r:_,G=null!=(s=null!=(d=null==R?void 0:R.badge)?d:w.Badge)?s:k,H=null!=(c=null==$?void 0:$.root)?c:P.root,Y=null!=(p=null==$?void 0:$.badge)?p:P.badge;return(0,g.jsx)(f,(0,i.Z)({invisible:I,badgeContent:u,showZero:S,max:A},T,{slots:{root:V,badge:G},className:(0,o.default)(null==H?void 0:H.className,D.root,v),slotProps:{root:(0,i.Z)({},H,(0,b.Z)(V)&&{as:y,ownerState:(0,i.Z)({},null==H?void 0:H.ownerState,{anchorOrigin:j,color:W,overlap:L,variant:U})}),badge:(0,i.Z)({},Y,{className:(0,o.default)(D.badge,null==Y?void 0:Y.className)},(0,b.Z)(G)&&{ownerState:(0,i.Z)({},null==Y?void 0:Y.ownerState,{anchorOrigin:j,color:W,overlap:L,variant:U})})},ref:t}))});var C=E},69633:function(e,t,r){var n=r(28442);let i=e=>{return!e||!(0,n.Z)(e)};t.Z=i},69709:function(e,t,r){r.r(t);var n=r(85597),i=r(27274),a=r(7961),o=r(6867),l=r(81719),s=r(62097),d=r(67294),c=r(93836),p=r(42977),u=r(17563);let g=(0,l.ZP)(a.Z,{name:"AvatarWrapper"})(({theme:e})=>({borderWidth:"thin",borderStyle:"solid",borderColor:e.palette.border.secondary})),m=(0,l.ZP)(n.rUS,{name:"Link"})(({theme:e})=>({"&:hover":{textDecoration:"none"}})),h=(0,l.ZP)(o.Z,{shouldForwardProp:e=>"status"!==e})(({theme:e,status:t})=>({"& .MuiBadge-badge":{...0===t&&{display:"none"},...1===t&&{color:e.palette.success.main,backgroundColor:e.palette.success.main},...2===t&&{color:e.palette.warning.main,backgroundColor:e.palette.warning.main},...3===t&&{color:e.palette.error.main,backgroundColor:e.palette.error.main},boxShadow:"dark"===e.palette.mode?"none":`0 0 0 2px ${e.palette.background.paper}`,"&::after":{position:"absolute",top:0,left:0,width:"100%",height:"100%",borderRadius:"50%",animation:"ripple 1.2s infinite ease-in-out",border:"1px solid currentColor",content:'""'}}})),f=(0,l.ZP)("div",{shouldForwardProp:e=>"sizeProps"!==e})(({theme:e,sizeProps:t})=>({position:"relative",width:"32px",height:"32px",...t&&{width:t,height:t}})),v=(0,l.ZP)(a.Z,{shouldForwardProp:e=>"sizeProps"!==e})(({theme:e,sizeProps:t})=>({position:"absolute",bottom:0,left:0,width:"26px",height:"26px",backgroundColor:"dark"===e.palette.mode?e.palette.grey["600"]:e.palette.grey["300"],borderWidth:1,borderStyle:"solid",borderColor:"light"===e.palette.mode?e.palette.grey["50"]:e.palette.grey["800"],zIndex:2,...t&&{width:t-6,height:t-6}})),x=(0,l.ZP)(a.Z,{shouldForwardProp:e=>"sizeProps"!==e})(({theme:e,sizeProps:t})=>({position:"relative",top:-2,left:6,width:"26px",height:"26px",...t&&{width:`${t-6}px !important`,height:`${t-6}px !important`}}));t.default=(0,d.memo)(function({username:e="Name",name:t="Name",src:r,size:o,to:l,onClick:b,status:y=0,room:w,uploadLocal:P=!1,roomType:O=null,avatarETag:Z,hoverCard:_=!1,component:k="span"}){var E,C;let{getSetting:I}=(0,n.OgA)(),A=(0,s.Z)(),z=O||(null==w?void 0:w.t),R=(0,i.vK)(t),$={width:o,height:o,color:A.palette.grey["50"],fontSize:A.mixins.pxToRem(14.4)},S=I("chatplus"),M=null==S?void 0:null===(E=S.server)||void 0===E?void 0:E.replace(/\/$/,""),T=r;if(T||(T=Z?`${M}/avatar/${e}?${u.stringify({etag:Z})}`:""),R&&($.backgroundColor=i.kU.hex(R)),l)return d.createElement(m,{to:l||`/${e}`},d.createElement(g,{src:T,alt:t,style:$,component:k,children:R}));if([c.n.Private,c.n.Public].includes(z)){if(P)return d.createElement(g,{src:T,alt:t,style:$,component:k,children:R});if(null==w?void 0:w.avatarETag)return T=`${M}/avatar/room/${null==w?void 0:w.id}?${u.stringify({etag:null==w?void 0:w.avatarETag})}`,d.createElement(g,{src:T,alt:t,style:$,component:k,children:R});{let F=`${M}/avatar/${null==w?void 0:null===(C=w.u)||void 0===C?void 0:C.username}`;return d.createElement(f,{sizeProps:o},d.createElement(v,{sizeProps:o,src:F,alt:t,component:k,children:R}),d.createElement(x,{sizeProps:o,src:T,alt:t,style:$,component:k,children:R}))}}if(z===c.n.Direct&&y){let B=(0,p.xW)(y);return d.createElement(h,{overlap:"circular",anchorOrigin:{vertical:"bottom",horizontal:"right"},variant:"dot",status:B},d.createElement(a.Z,{src:T,alt:t,style:$,component:k,children:R}))}return d.createElement(g,{src:T,alt:t,style:$,component:k,children:R,onClick:b,to:e,hoverCard:_})})},83940:function(e,t,r){var n=r(93836),i=r(67294),a=r(69709);t.Z=(0,i.memo)(function({username:e,name:t,size:r="sm",status:o=null,room:l=null,avatarETag:s,hoverCard:d}){return i.createElement(a.default,{username:e,size:r,name:t,roomType:n.n.Direct,status:o,room:l,avatarETag:s,hoverCard:d})})},96211:function(e,t,r){var n=r(40684),i=r(85597),a=r(81719),o=r(67294);let l=(0,a.ZP)("audio",{slot:"AudioPre"})(({theme:e})=>({visibility:"hidden",height:0})),s=(0,a.ZP)("div",{slot:"uiChatAudioCustom",shouldForwardProp:e=>"isPageAllMessages"!==e&&"msgType"!==e&&"isOwner"!==e&&"isOther"!==e})(({theme:e,isPageAllMessages:t,msgType:r,isOwner:n,isOther:i})=>({width:"300px",maxWidth:"100%",...("message_pinned"===r||"message_unpinned"===r||i)&&{width:n?"177px":"137px"},...("message_pinned"===r||"message_unpinned"===r||i)&&t&&{width:"200px"},"& .rhap_container":{borderRadius:"4px",boxSizing:"border-box",display:"flex",flexDirection:"column",lineHeight:1,fontFamily:"inherit",width:"100%",padding:"10px 15px",backgroundColor:"#fff",boxShadow:"0 0 3px 0 rgba(0, 0, 0, 0.2)"},"& .rhap_main":{display:"flex",flexDirection:"column",flex:"1 1 auto","& .rhap_button-clear":{backgroundColor:"transparent",border:"none",padding:0,overflow:"hidden",cursor:"pointer"}},"& .rhap_progress-section":{display:"flex",flex:"3 1 auto",alignItems:"center","& .rhap_time":{color:"#555555",userSelect:"none",fontSize:t?"16px":"12px"},"& .rhap_progress-container":{display:"flex",alignItems:"center",height:"20px",flex:"1 0 auto",alignSelf:"center",margin:"0 calc(11px)",cursor:"pointer",userSelect:"none","& .rhap_progress-bar-show-download":{backgroundColor:"rgba(221, 221, 221, 0.5)"},"& .rhap_progress-bar":{boxSizing:"border-box",position:"relative",zIndex:0,width:"100%",height:"5px",backgroundColor:"#dddddd",borderRadius:"2px","& .rhap_progress-indicator":{boxSizing:"border-box",position:"absolute",zIndex:3,width:"20px",height:"20px",marginLeft:"-10px",top:"-8px",background:"#868686",borderRadius:"50px",boxShadow:"rgb(134 134 134 / 50%) 0 0 5px"},"& .rhap_progress-filled":{height:"100%",position:"absolute",zIndex:2,backgroundColor:"#868686",borderRadius:"2px",left:0},"& .rhap_download-progress":{width:"100%",height:"100%",position:"absolute",zIndex:1,backgroundColor:"#dddddd",borderRadius:"2px"}}}},"& .rhap_controls-section":{marginTop:"8px",flex:"1 1 auto",display:"flex",alignItems:"center",justifyContent:"space-between","& .rhap_additional-controls":{display:"none",flex:"1 1 auto",alignItems:"center"},"& .rhap_main-controls":{paddingRight:"8px",flex:"none",display:"flex",alignItems:"center",justifyContent:"center","& .rhap_play-pause-button":{fontSize:"40px",width:"40px",height:"40px"},"& .rhap_main-controls-button":{margin:"0 3px",color:"#868686",fontSize:"35px",width:"35px",height:"35px",display:"inline-flex",alignItems:"center"}},"& .rhap_volume-controls":{flex:1,minWidth:0,flexBasis:"auto",paddingRight:"8px",display:"flex",alignItems:"center",justifyContent:"flex-end","& .rhap_volume-container":{display:"flex",alignItems:"center",flex:"0 1 100px",userSelect:"none","& .rhap_volume-button":{flex:"0 0 26px",fontSize:"26px",width:"26px",height:"26px",color:"#868686",marginRight:"6px"},"& .rhap_volume-bar-area":{display:"flex",alignItems:"center",width:"100%",height:"14px",cursor:"pointer","& .rhap_volume-bar":{boxSizing:"border-box",position:"relative",width:"100%",height:"4px",background:"#dddddd",borderRadius:"2px","& .rhap_volume-indicator":{boxSizing:"border-box",position:"absolute",width:"12px",height:"12px",marginLeft:"-6px",left:0,top:"-4px",background:"#868686",opacity:.9,borderRadius:"50px",boxShadow:"rgb(134 134 134 / 50%) 0 0 3px",cursor:"pointer"}}}}}}})),d=(0,n.ZP)({resolved:{},chunkName(){return"AudioPlayer"},isReady(e){let t=this.resolve(e);return!0===this.resolved[t]&&!!r.m[t]},importAsync:()=>r.e("AudioPlayer").then(r.bind(r,8886)),requireAsync(e){let t=this.resolve(e);return this.resolved[t]=!1,this.importAsync(e).then(e=>{return this.resolved[t]=!0,e})},requireSync(e){let t=this.resolve(e);return r(t)},resolve(){return 8886}});t.Z=o.memo(function({audio_url:e,msgType:t,isOwner:r,isOther:n}){let{usePageParams:a,useMediaPlaying:c}=(0,i.OgA)(),p=a(),u=(null==p?void 0:p.rid)||!1,g=o.useRef(),m=`msg-audio-${e}`,[h,f]=c(m),v=()=>{f(!0)},x=()=>{f(!1)},b=()=>{f(!1)};return o.useEffect(()=>{if(!h){var e,t,r;f(!1),(null===(e=g.current)||void 0===e?void 0:null===(t=e.audio)||void 0===t?void 0:t.current)&&(null===(r=g.current.audio.current)||void 0===r||r.pause())}},[m,h]),o.createElement(s,{isPageAllMessages:u,msgType:t,isOwner:r,isOther:n},o.createElement(l,{controls:!0,src:e,preload:"metadata"}),o.createElement(d,{ref:g,src:e,preload:"metadata",onPlay:v,onPause:x,onEnded:b}))},(e,t)=>{return(null==e?void 0:e.audio_url)===(null==t?void 0:t.audio_url)})},71348:function(e,t,r){r.d(t,{Z:function(){return C}});var n=r(85597),i=r(67294),a=r(96211),o=r(81719);let l="MsgAttachmentImage",s=(0,o.ZP)("div",{name:l})(({theme:e})=>({position:"relative",width:"100%"})),d=(0,o.ZP)("figure",{name:l})(({theme:e})=>({margin:0,display:"block"})),c=(0,o.ZP)("div",{name:l,shouldForwardProp:e=>"isOwner"!==e&&"isPageAllMessages"!==e})(({theme:e,isOwner:t,isPageAllMessages:r})=>({width:"144px",maxWidth:"100%",cursor:"pointer",...t&&{width:"186px"},...r&&{width:"300px"}})),p=(0,o.ZP)("img",{name:l})(({theme:e})=>({position:"absolute",left:0,top:0,width:"100%",borderRadius:e.spacing(1)}));function u({isOwner:e,title:t,image_url:r,image_dimensions:a}){let{chatplus:o,usePageParams:l}=(0,n.OgA)(),u=l(),g=(null==u?void 0:u.rid)||!1,m=!!a&&a.height/a.width;return i.createElement(s,{className:"uiMsgAttachmentImgWrapper"},i.createElement(d,null,i.createElement(c,{isOwner:!!e,isPageAllMessages:g,style:{paddingBottom:`${m?100*m:100}%`}},i.createElement(p,{src:`${o.sanitizeRemoteFileUrl(r)}&width=300`,onClick:e=>{o.presentImageView(e,{id:"img0",src:o.sanitizeRemoteFileUrl(r)})},alt:t}))))}let g="MsgAttachmentImage",m=(0,o.ZP)("div",{name:g,slot:"UIMsgAttachmentImgWrapper"})(({theme:e})=>({position:"relative",width:"100%"})),h=(0,o.ZP)("figure",{name:g})(({theme:e})=>({margin:0,display:"block"})),f=(0,o.ZP)("div",{name:g,shouldForwardProp:e=>"isOwner"!==e&&"isPageAllMessages"!==e&&"typeGridLayout"!==e&&"msgType"!==e&&"isOther"!==e})(({theme:e,isOwner:t,isPageAllMessages:r,typeGridLayout:n,msgType:i,isOther:a})=>({width:"94px",height:"94px",maxWidth:"100%",cursor:"pointer",marginBottom:"1px",..."type-2"===n&&!t&&{width:"72px",height:"72px"},..."type-1"===n&&{width:"52px",height:"52px"},..."type-1"===n&&t&&{width:"62px",height:"62px"},...("message_pinned"===i||"message_unpinned"===i||a)&&"type-2"===n&&{width:"84px",height:"84px"},...("message_pinned"===i||"message_unpinned"===i||a)&&"type-2"===n&&!t&&{width:"62px",height:"62px"},...("message_pinned"===i||"message_unpinned"===i||a)&&"type-1"===n&&{width:"45px",height:"45px"},...("message_pinned"===i||"message_unpinned"===i||a)&&"type-1"===n&&t&&{width:"58px",height:"58px"},...r&&"type-2"===n&&{width:"190px",height:"190px"},...r&&"type-1"===n&&{width:"126px",height:"126px"},...("message_pinned"===i||"message_unpinned"===i||a)&&r&&"type-2"===n&&{width:"120px",height:"120px"},...("message_pinned"===i||"message_unpinned"===i||a)&&r&&"type-1"===n&&{width:"100px",height:"100px"}})),v=(0,o.ZP)("img",{name:g,shouldForwardProp:e=>"typeGridLayout"!==e})(({theme:e,typeGridLayout:t})=>({position:"absolute",left:0,top:0,width:"100%",objectFit:"cover",borderRadius:e.spacing(1),...t&&{width:"98%",height:"98%"}}));function x({image_url:e,images:t,keyIndex:r,isOwner:a,typeGridLayout:o,msgType:l,isOther:s}){let{chatplus:d,usePageParams:c}=(0,n.OgA)(),p=c(),u=(null==p?void 0:p.rid)||!1,g=t&&t.length?t.map((e,t)=>{return{id:t,src:d.sanitizeRemoteFileUrl(e.image_url)}}):[];return i.createElement(m,null,i.createElement(h,null,i.createElement(f,{isOwner:!!a,isPageAllMessages:u,typeGridLayout:o,msgType:l,isOther:s},i.createElement(v,{typeGridLayout:o,src:`${d.sanitizeRemoteFileUrl(e)}&width=150`,onClick:t=>{d.presentImageView(t,{id:parseInt(r),src:d.sanitizeRemoteFileUrl(e)},g)},alt:"dwq"}))))}var b=r(41547);let y="MsgAttachmentVideo",w=(0,o.ZP)("figure",{name:y})(({theme:e})=>({margin:0,display:"block"})),P=(0,o.ZP)("video",{slot:"AudioPre"})(({theme:e})=>({visibility:"hidden",height:0})),O=(0,o.ZP)("video",{name:y,slot:"VideoStyled",shouldForwardProp:e=>"isPageAllMessages"!==e})(({theme:e,isPageAllMessages:t})=>({width:"186px",maxWidth:"100%",display:"inline-block",...t&&{width:"300px"}})),Z=(0,o.ZP)("div",{name:y,slot:"VideoPreviewWrapper",shouldForwardProp:e=>"isOwner"!==e&&"isPageAllMessages"!==e})(({theme:e,isOwner:t,isPageAllMessages:r})=>({width:"144px",maxWidth:"100%",position:"relative",cursor:"pointer",...t&&{width:"186px"},...r&&{width:"300px"}})),_=(0,o.ZP)("span",{name:y,slot:"VideoPreviewSrc"})(({theme:e})=>({display:"block",position:"relative",backgroundSize:"cover",backgroundPosition:"center center",backgroundRepeat:"no-repeat",backgroundOrigin:"border-box",border:"1px solid rgba(0, 0, 0, 0.1)","&:before":{content:'""',display:"block",paddingBottom:"56.25%"}})),k=(0,o.ZP)(b.zb,{name:y,slot:"CustomPlayButton"})({position:"absolute",top:0,bottom:0,left:0,right:0,margin:"auto",fontSize:"40px",display:"flex",alignItems:"center",justifyContent:"center",zIndex:2,background:"rgba(0, 0, 0, 0.3)",color:"#fff",pointerEvents:"none"});var E=i.memo(function({video_url:e,thumb_url:t,isOwner:r}){let{chatplus:a,usePageParams:o,useMediaPlaying:l}=(0,n.OgA)(),s=o(),d=(null==s?void 0:s.rid)||!1,[c,p]=i.useState(!1),u=i.useRef(),g=`msg-video-${e}`,[m,h]=l(g),f=()=>{h(!0)},v=()=>{h(!1)},x=()=>{h(!1)};return(i.useEffect(()=>{if(!m){var e;h(!1),u.current&&(null===(e=u.current)||void 0===e||e.pause())}},[g,m]),c)?i.createElement(w,null,i.createElement(P,{src:a.sanitizeRemoteFileUrl(e)}),i.createElement(O,{ref:u,isPageAllMessages:d,src:a.sanitizeRemoteFileUrl(e),controls:!0,autoPlay:!0,onPlay:f,onPause:v,onEnded:x})):i.createElement(Z,{isOwner:r,isPageAllMessages:d,onClick:()=>p(!0)},i.createElement(_,{style:{backgroundImage:`url(${t})`}}),i.createElement(k,{icon:"ico-play-circle-o"}))},(e,t)=>{return(null==e?void 0:e.video_url)===(null==t?void 0:t.video_url)});function C({image_url:e,image_dimensions:t,video_url:r,title:o,video_type:l,video_thumb_url:s,audio_url:d,layout:c,totalImages:p,isOwner:g,typeGridLayout:m,msgType:h,isOther:f,keyIndex:v}){let{chatplus:b}=(0,n.OgA)();return d?i.createElement(a.Z,{audio_url:b.sanitizeRemoteFileUrl(d),msgType:h,isOwner:g,isOther:f}):r?i.createElement(E,{isOwner:g,video_url:r,video_type:b.sanitizeRemoteFileUrl(l),thumb_url:b.sanitizeRemoteFileUrl(s)}):e&&"multi-image"===c?i.createElement(x,{title:o,images:p,image_url:e,isOwner:g,typeGridLayout:m,msgType:h,isOther:f,keyIndex:v}):e?i.createElement(u,{isOwner:g,title:o,image_dimensions:t,image_url:e}):null}},72637:function(e,t,r){r.d(t,{Z:function(){return $}});var n=r(67294),i=r(39312),a=r(38781),o=r(42977),l=r(85597),s=r(41547),d=r(30120),c=r(81719),p=r(83940),u=r(71348);function g(){return(g=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var n in r)Object.prototype.hasOwnProperty.call(r,n)&&(e[n]=r[n])}return e}).apply(this,arguments)}let m="MsgAttachmentMultiMedia",h=(0,c.ZP)("div",{name:m,slot:"uiMsgImageAttachmentWrapper",shouldForwardProp:e=>"isOwner"!==e})(({theme:e,isOwner:t})=>({display:"flex",alignItems:"center",flexWrap:"wrap",justifyContent:t?"flex-end":"flex-start",marginTop:e.spacing(.25)})),f=(0,c.ZP)("div",{name:m,slot:"uiMsgImageAttachmentItem",shouldForwardProp:e=>"typeGridLayout"!==e&&"isPageAllMessages"!==e&&"isLastItem"!==e})(({theme:e,typeGridLayout:t,isPageAllMessages:r,isLastItem:n})=>({..."type-1"===t&&{flexBasis:"33.333333%"},..."type-2"===t&&{flexBasis:"50%"}}));function v({mediaItems:e,isOwner:t,msgType:r,isOther:i}){let{usePageParams:a}=(0,l.OgA)(),o=a(),s=(null==o?void 0:o.rid)||!1,d=e.length,c=d%3==0?"type-1":d%3==1?"type-1":"type-2";return n.createElement(h,{isOwner:t},e.map((a,o)=>n.createElement(f,{typeGridLayout:c,isPageAllMessages:s,isLastItem:o===e.length-1,key:`k${o}`},n.createElement(u.Z,g({},a,{layout:"multi-image",keyIndex:o,totalImages:e,typeGridLayout:c,isOwner:t,msgType:r,isOther:i})))))}function x(){return(x=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var n in r)Object.prototype.hasOwnProperty.call(r,n)&&(e[n]=r[n])}return e}).apply(this,arguments)}let b="MsgAttachment",y=(0,c.ZP)("div",{name:b,slot:"UIMsgAttachment",shouldForwardProp:e=>"isOwner"!==e&&"isAudio"!==e&&"isTypeFile"!==e&&"isOther"!==e&&"msgType"!==e})(({theme:e,isOwner:t,isAudio:r,isTypeFile:n,isOther:i,msgType:a})=>({maxWidth:"100%",display:"flex",flexDirection:"column",alignItems:"flex-start",marginBottom:e.spacing(.5),marginTop:e.spacing(.25),overflow:"hidden",...(r||"message_pinned"===a||"message_unpinned"===a||i)&&{backgroundColor:e.palette.grey["100"],..."dark"===e.palette.mode&&{backgroundColor:e.palette.grey["600"]},padding:e.spacing(.5,.75),borderRadius:e.spacing(1.25),"& a":{color:t?"#fff":e.palette.text.primary,textDecoration:"underline",cursor:"pointer"},...t&&{backgroundColor:e.palette.primary.main,color:"#fff !important"}}})),w=(0,c.ZP)("div",{name:b,slot:"TitleStyled",shouldForwardProp:e=>"isOwner"!==e&&"isVideo"!==e&&"isImage"!==e&&"isAudio"!==e&&"isTypeFile"!==e})(({theme:e,isOwner:t,isVideo:r,isImage:n,isAudio:i,isTypeFile:a})=>({display:"none"})),P=(0,c.ZP)("div",{name:b,slot:"TitleLinkStyled",shouldForwardProp:e=>"isOwner"!==e&&"isVideo"!==e&&"isImage"!==e&&"isAudio"!==e&&"isTypeFile"!==e})(({theme:e,isOwner:t,isVideo:r,isImage:n,isAudio:i,isTypeFile:a})=>({borderRadius:e.spacing(1),fontSize:e.spacing(1.75),padding:e.spacing(1),overflow:"hidden",whiteSpace:"nowrap",textOverflow:"ellipsis",display:"inline-block",maxWidth:"100%",wordBreak:"break-word",wordWrap:"break-word",backgroundColor:e.palette.grey["100"],..."dark"===e.palette.mode&&{backgroundColor:e.palette.grey["600"]},...t&&{backgroundColor:e.palette.primary.main,color:"#fff"},...(r||n)&&{display:"none"},...i&&{width:"300px"}})),O=(0,c.ZP)("div",{name:b,slot:"DescriptionStyled",shouldForwardProp:e=>"isOwner"!==e&&"isVideo"!==e})(({theme:e,isOwner:t,isVideo:r})=>({backgroundColor:e.palette.grey["100"],..."dark"===e.palette.mode&&{backgroundColor:e.palette.grey["600"]},borderRadius:e.spacing(1),fontSize:e.spacing(1.75),padding:e.spacing(1),display:"inline-block",...t&&{backgroundColor:e.palette.primary.main,color:"#fff"}})),Z=(0,c.ZP)("div")(({theme:e})=>({cursor:"pointer","& .ico":{marginRight:e.spacing(1)}})),_=(0,c.ZP)("div",{name:b,slot:"uiMsgAttachmentAuthor"})(({theme:e})=>({display:"flex",justifyContent:"flex-start",alignItems:"center","& .MuiAvatar-root":{fontSize:`${e.spacing(1.5)} !important`},strong:{padding:e.spacing(0,.5,0,.25)}})),k=(0,c.ZP)("div",{name:b,slot:"uiMsgAttachmentFlex"})(({theme:e})=>({textAlign:"start"})),E=(0,c.ZP)("div",{name:b,slot:"uiMsgAttachmentText"})(({theme:e})=>({margin:e.spacing(.5,0)})),C=(0,c.ZP)("div",{name:b,slot:"UIMsgAttachmentTextDelete"})(({theme:e})=>({margin:e.spacing(.5),marginLeft:0,fontStyle:"italic"})),I=(0,c.ZP)("div",{name:b,slot:"uiMsgAttachmentInfoWrapper",shouldForwardProp:e=>"msgType"!==e&&"isOther"!==e})(({theme:e,msgType:t,isOther:r})=>({...("message_pinned"===t||"message_unpinned"===t||r)&&{borderLeft:"2px solid #a2a2a2",paddingLeft:e.spacing(1)}})),A=(0,c.ZP)(s.Ys,{name:b,slot:"uiMsgAttachmentLink"})(({theme:e})=>({cursor:"pointer","& .ico":{marginRight:e.spacing(1)}}));function z({mentions:e=[],title:t,author_real_name:r,author_name:c,author_id:g,text:m,image_url:h,image_dimensions:f,audio_url:b,video_url:z,video_type:R,title_link:$,description:S,video_thumb_url:M,attachments:T,type:F,layout:B,isOwner:W,msgType:L,t:j}){let{chatplus:U,i18n:N}=(0,l.OgA)(),D="file"===F,{count:V,data:G}=(0,o.HT)(T),H=(0,i.ID)(g);return n.createElement(y,{isOwner:W,isAudio:!!b,isTypeFile:!!D,isOther:!b&&!D,msgType:L},n.createElement(I,{msgType:L,isOther:!b&&!D},c?n.createElement(_,null,n.createElement(p.Z,{size:16,name:(null==H?void 0:H.name)||c,username:(null==H?void 0:H.username)||c,avatarETag:null==H?void 0:H.avatarETag}),n.createElement("strong",null,r||c)):null,n.createElement(k,null,"rm"===j?n.createElement(C,null,N.formatMessage({id:"message_was_deleted"})):null,m&&"rm"!==j?n.createElement(E,{dangerouslySetInnerHTML:{__html:(0,a.ZP)(m,{mentions:e})}}):null,T&&T.length?n.createElement(d.Z,{mt:1},V>1?n.createElement(v,{mediaItems:G,isOwner:W,msgType:L,isOther:!b&&!D}):n.createElement(n.Fragment,null,T.map((e,t)=>n.createElement(n.Fragment,{key:`k${t}`},e.video_url||e.image_url||e.audio_url?n.createElement(u.Z,x({},e,{key:`k${t}`,msgType:L,isOther:!b&&!D})):n.createElement(n.Fragment,null,e.title_link?n.createElement(A,{lines:1},n.createElement("div",{onClick:()=>(0,o.Sv)(U.sanitizeRemoteFileUrl(e.title_link),!0)},n.createElement(s.zb,{icon:"ico-arrow-down-circle"}),e.title)):null))))):null)),t?n.createElement(w,{isOwner:W,isVideo:!!z,isImage:!!h,isAudio:!!b,isTypeFile:!!D},t):null,n.createElement(u.Z,{image_url:h,video_url:z,audio_url:b,title:t,video_type:R,video_thumb_url:M,layout:B,image_dimensions:f,isOwner:W}),$?n.createElement(P,{isOwner:W,isVideo:!!z,isImage:!!h,isAudio:!!b,isTypeFile:!!D},n.createElement(Z,{onClick:()=>(0,o.Sv)(U.sanitizeRemoteFileUrl($),!0)},n.createElement("i",{className:"ico ico-arrow-down-circle mr-1"}),t)):null,S?n.createElement(O,{isOwner:W,isVideo:!!z,isImage:!!h,isAudio:!!b,isTypeFile:!!D},S):null)}function R(){return(R=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var n in r)Object.prototype.hasOwnProperty.call(r,n)&&(e[n]=r[n])}return e}).apply(this,arguments)}function $({message:e,isOwner:t,msgType:r,dataQuote:i,isQuote:a}){var o;if(!(null===(o=e.attachments)||void 0===o?void 0:o.length))return null;let l=e.attachments;if(a&&i&&(l=l.filter(e=>!e.message_id)),!l&&!l.length)return null;let s=l.filter(e=>e.image_url);return s.length>1?n.createElement(v,{mediaItems:s,isOwner:t}):l.map((e,i)=>n.createElement(z,R({},e,{key:`k${i}`,isOwner:t,msgType:r})))}},35556:function(e,t,r){r.r(t),r.d(t,{default:function(){return p}});var n=r(85597),i=r(81719),a=r(67294),o=r(72637),l=r(28838);let s="MesagePinned",d=(0,i.ZP)("div",{name:s,slot:"uiChatMsgItemBodyInnerWrapper"})(({theme:e})=>({})),c=(0,i.ZP)("div",{name:s,slot:"uiChatMsgItemPin",shouldForwardProp:e=>"isOwner"!==e})(({theme:e,isOwner:t})=>({textAlign:"start",...t&&{textAlign:"end"}}));function p({message:e,isOwner:t,msgType:r}){let{i18n:i}=(0,n.OgA)(),s=i.formatMessage({id:"message_pinned"===r?"user_message_pinned":"user_message_unpinned"},{msg:e.msg,user:a.createElement("b",null,e.u.name)});return a.createElement(d,null,a.createElement(o.Z,{message:e,msgType:r,isOwner:t}),a.createElement(l.Z,{message:e}),a.createElement(c,{isOwner:t},s))}},28838:function(e,t,r){r.d(t,{Z:function(){return y}});var n=r(67294),i=r(42977),a=r(41547),o=r(81719),l=r(41609),s=r.n(l);let d="MsgEmbed",c=(0,o.ZP)("div",{name:d,slot:"uiMsgEmbed"})(({theme:e})=>({marginTop:"4px",marginBottom:"8px",width:"400px",maxWidth:"100%",cursor:"pointer"})),p=(0,o.ZP)("div",{name:d,slot:"uiMsgEmbedOuter"})(({theme:e})=>({display:"flex",flexDirection:"column",borderRadius:"8px",border:e.mixins.border("secondary"),color:"#555555"})),u=(0,o.ZP)("div",{name:d,slot:"ItemMedia"})(({theme:e})=>({width:"100%"})),g=(0,o.ZP)("div",{name:d,slot:"ItemMediaSrc"})(({theme:e})=>({width:"100%",display:"block",position:"relative",backgroundSize:"cover",backgroundPosition:"center center",backgroundRepeat:"no-repeat",backgroundOrigin:"border-box",border:"1px solid rgba(0, 0, 0, 0.1)",borderRadius:"8px 8px 0 0",backgroundColor:"transparent",borderBottom:"1px solid rgba(0, 0, 0, 0.1)","&:before":{content:"''",display:"block",paddingBottom:"56%"}})),m=(0,o.ZP)("div",{name:d,slot:"ItemInner"})(({theme:e})=>({flex:1,minWidth:0,padding:"8px"})),h=(0,o.ZP)(a.Ys,{name:d,slot:"ItemTitle"})(({theme:e})=>({marginBottom:"2px",color:"light"===e.palette.mode?e.palette.grey["900"]:e.palette.text.primary})),f=(0,o.ZP)(a.Ys,{name:d,slot:"ItemUrl"})(({theme:e})=>({marginBottom:"2px",color:e.palette.text.secondary})),v=(0,o.ZP)(a.Ys,{name:d,slot:"ItemDescription"})(({theme:e})=>({color:"light"===e.palette.mode?e.palette.grey["900"]:e.palette.text.primary}));function x({url:e,meta:t,parsedUrl:r,ignoreParse:a}){let o=!a&&(t&&!s()(t)||r&&!s()(r));if(!o)return null;let{ogTitle:l,ogDescription:d,ogImage:x,oembedThumbnailUrl:b,oembedTitle:y,oembedDescription:w}=t,P=r.host,O=l||y,Z=d||w;return n.createElement(c,null,n.createElement(p,{onClick:()=>(0,i.Sv)(e,!0)},x||b?n.createElement(u,null,n.createElement(g,{style:{backgroundImage:`url(${x||b})`}})):n.createElement(u,null,n.createElement("span",{className:"item-media-src media-default"})),n.createElement(m,null,O?n.createElement(h,{lines:2,variant:"h6"},O):null,n.createElement(f,{lines:2,variant:"body2"},P),Z?n.createElement(v,{lines:2,variant:"h6"},Z):null)))}function b(){return(b=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var n in r)Object.prototype.hasOwnProperty.call(r,n)&&(e[n]=r[n])}return e}).apply(this,arguments)}function y({message:e}){var t;return(null===(t=e.urls)||void 0===t?void 0:t.length)?e.urls.filter(e=>{return!(null==e?void 0:e.ignoreParse)&&e.meta&&e.parsedUrl}).map((e,t)=>n.createElement(x,b({},e,{key:`k${t}`}))):null}},38781:function(e,t,r){r.d(t,{Hj:function(){return c},ZP:function(){return p},kK:function(){return g}});var n=r(7187),i=r.n(n),a=r(42977);function o(e){return e.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,"$1<br/>$2")}function l(e,t={}){let{mentions:r=[]}=t,n=e.replace(/\[(\s)*\]\(([^\)]+)\)/gm,"");return r&&r.length>0&&(n=n.replace(/@([^\s\\#@:]+)/giu,(e,t)=>{let n=r.find(e=>e.username===t);return n&&n.name?`@${n.name}`:e})),n}function s(e,t={}){let{mentions:r=[]}=t,n=e.replace(/@\[([^\]]+)\]\(([^#@:]+)\)/giu,"<a onclick=\"triggerClick('$2')\">@$1</a>");return r&&r.length>0&&(n=n.replace(/@([^\s\\#@:]+)/giu,(e,t)=>{let n=r.find(e=>e.username===t);return n&&n.name?`<a onclick="triggerClick('/${t}')">@${n.name}</a>`:e})),n}window.triggerClick=a.Sv;let d=e=>e.replace(/:\w+:/gi,e=>e),c=(e,t={})=>{return e?l(e,t):null};function p(e,t={}){var r;return e?o(d(s((r=l(e),i()(r)).replace(/(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim,'<a onclick="triggerClick(\'$1\',true)" target="_blank">$1</a>'),t))):null}let u=(e={})=>{return/^(\w+)_(audio|video)_call_([cpd]$)/.test((null==e?void 0:e.t)||(null==e?void 0:e.type))};function g(e){var t;let r=null==e?void 0:e.t;return["rm"].includes(r)?[{id:"message_was_deleted"}]:u(e)?/(invite)_/.test(r)||/(start)_/.test(r)?[{id:"started_a_call"}]:/(miss)_/.test(r)?[{id:"missed_a_call"}]:[{id:"call_ended"}]:(null==e?void 0:e.msg)?o(d(s((t=l(null==e?void 0:e.msg),i()(t)),{mentions:(null==e?void 0:e.mentions)||[]}))):null}}}]);