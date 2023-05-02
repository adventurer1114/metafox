"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-chat-components-MsgContent-MessageText"],{49908:function(e,t,a){a.r(t),a.d(t,{default:function(){return X}});var n=a(40263),i=a(81719),r=a(38790),l=a(67294),o=a(85597),s=a(2475),m=a(13478);let g="MsgAttachmentImage",d=(0,i.ZP)("div",{name:g})(({theme:e})=>({position:"relative",width:"100%"})),p=(0,i.ZP)("figure",{name:g})(({theme:e})=>({margin:0,display:"block"})),c=(0,i.ZP)("div",{name:g,shouldForwardProp:e=>"isOwner"!==e&&"isPageAllMessages"!==e})(({theme:e,isOwner:t,isPageAllMessages:a})=>({width:"144px",maxWidth:"100%",cursor:"pointer",...t&&{width:"186px"},...a&&{width:"300px"}})),u=(0,i.ZP)("img",{name:g})(({theme:e})=>({position:"absolute",left:0,top:0,width:"100%",borderRadius:e.spacing(1)})),h=(e,t)=>{let a=new Image;a.src=t,a.onload=()=>{e({height:a.height,width:a.width})},a.onerror=t=>{e(t)}};function f({isOwner:e,image:t,download_url:a,file_name:n}){let{dispatch:i,usePageParams:r,assetUrl:s}=(0,o.OgA)(),g=r(),f=(null==g?void 0:g.rid)||!1,[w,y]=l.useState(null),x=(0,m.Q4)(t,"240",s("photo.no_image")),v=(0,m.Q4)(t,"origin",s("photo.no_image"));l.useEffect(()=>{x&&h(y,x)},[x]);let P=!!w&&w.height/w.width,E=()=>{i({type:"chat/room/presentImageView",payload:{image:{id:"img0",src:v,download_url:a,is_image:!0,file_name:n}}})};return l.createElement(d,{className:"uiMsgAttachmentImgWrapper"},l.createElement(p,null,l.createElement(c,{isOwner:!!e,isPageAllMessages:f,style:{paddingBottom:`${P?100*P:100}%`}},l.createElement(u,{src:x,onClick:E,alt:n}))))}let w="MsgAttachmentImage",y=(0,i.ZP)("div",{name:w,slot:"UIMsgAttachmentImgWrapper"})(({theme:e})=>({position:"relative",width:"100%"})),x=(0,i.ZP)("figure",{name:w})(({theme:e})=>({margin:0,display:"block"})),v=(0,i.ZP)("div",{name:w,shouldForwardProp:e=>"isOwner"!==e&&"isPageAllMessages"!==e&&"typeGridLayout"!==e})(({theme:e,isOwner:t,isPageAllMessages:a,typeGridLayout:n})=>({width:"94px",height:"94px",maxWidth:"100%",cursor:"pointer",marginBottom:"1px",..."type-2"===n&&!t&&{width:"72px",height:"72px"},..."type-1"===n&&{width:"52px",height:"52px"},..."type-1"===n&&t&&{width:"62px",height:"62px"},...a&&"type-2"===n&&{width:"190px",height:"190px"},...a&&"type-1"===n&&{width:"126px",height:"126px"}})),P=(0,i.ZP)("img",{name:w,shouldForwardProp:e=>"typeGridLayout"!==e})(({theme:e,typeGridLayout:t})=>({position:"absolute",left:0,top:0,width:"100%",objectFit:"cover",borderRadius:e.spacing(1),...t&&{width:"98%",height:"98%"}}));function E({image:e,download_url:t,images:a,typeGridLayout:n,isOwner:i,file_name:r,keyIndex:s}){let{dispatch:g,usePageParams:d,assetUrl:p}=(0,o.OgA)(),c=d(),u=(null==c?void 0:c.rid)||!1,h=l.useMemo(()=>{return a&&a.length?a.map((e,t)=>{let a=(0,m.Q4)(e.image,"origin",p("photo.no_image"));return{id:t,src:a,download_url:e.download_url,file_name:e.file_name}}):[]},[a]),f=(0,m.Q4)(e,"500",p("photo.no_image")),w=(0,m.Q4)(e,"origin",p("photo.no_image")),E=()=>{g({type:"chat/room/presentImageView",payload:{image:{id:parseInt(s),src:w,download_url:t,file_name:r},images:h}})};return l.createElement(y,null,l.createElement(x,null,l.createElement(v,{isOwner:!!i,isPageAllMessages:u,typeGridLayout:n},l.createElement(P,{typeGridLayout:n,src:f,onClick:E,alt:"dwq"}))))}let M=(0,i.ZP)("div",{name:"MsgAttachmentMedia",slot:"TitleLink",shouldForwardProp:e=>"isOwner"!==e})(({theme:e,isOwner:t})=>({borderRadius:e.spacing(1),fontSize:e.spacing(1.75),padding:e.spacing(1),overflow:"hidden",whiteSpace:"nowrap",textOverflow:"ellipsis",display:"inline-block",maxWidth:"100%",wordBreak:"break-word",wordWrap:"break-word",backgroundColor:e.palette.grey["100"],..."dark"===e.palette.mode&&{backgroundColor:e.palette.grey["600"]},...t&&{backgroundColor:e.palette.primary.main,color:"#fff"}})),k=(0,i.ZP)("div")(({theme:e})=>({display:"flex",alignItems:"center",cursor:"pointer","& .ico":{marginRight:e.spacing(1)}}));function O({isOwner:e,layout:t,totalImages:a,typeGridLayout:n,is_image:i,image:r,file_name:o,download_url:m,file_size_text:g,keyIndex:d}){return r&&"multi-image"===t?l.createElement(E,{images:a,file_name:o,image:r,download_url:m,isOwner:e,typeGridLayout:n,keyIndex:d}):i&&r?l.createElement(f,{isOwner:e,image:r,file_name:o,download_url:m}):i?null:l.createElement(M,{isOwner:e},l.createElement(k,{onClick:()=>(0,s.Sv)(m)},l.createElement("i",{className:"ico ico-arrow-down-circle mr-1"}),o))}let I=(0,i.ZP)("div",{name:"MsgAttachment",slot:"UIMsgAttachment",shouldForwardProp:e=>"isOwner"!==e&&"isAudio"!==e&&"isTypeFile"!==e&&"isOther"!==e})(({theme:e})=>({maxWidth:"100%",display:"flex",flexDirection:"column",alignItems:"flex-start",marginBottom:e.spacing(.5),marginTop:e.spacing(.25),overflow:"hidden"}));function _({item:e,isOwner:t}){let{is_image:a,image:n,file_name:i,download_url:r,file_size_text:o}=e;return l.createElement(I,{isOwner:t},l.createElement(O,{isOwner:t,image:n,is_image:a,download_url:r,file_name:i,file_size_text:o}))}function A(){return(A=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}let b="MsgAttachmentMultiMedia",Z=(0,i.ZP)("div",{name:b,slot:"uiMsgImageAttachmentWrapper",shouldForwardProp:e=>"isOwner"!==e})(({theme:e,isOwner:t})=>({display:"flex",alignItems:"center",flexWrap:"wrap",justifyContent:t?"flex-end":"flex-start",marginTop:e.spacing(.25)})),C=(0,i.ZP)("div",{name:b,slot:"uiMsgImageAttachmentItem",shouldForwardProp:e=>"typeGridLayout"!==e&&"isPageAllMessages"!==e&&"isLastItem"!==e})(({theme:e,typeGridLayout:t,isPageAllMessages:a,isLastItem:n})=>({..."type-1"===t&&{flexBasis:"33.333333%"},..."type-2"===t&&{flexBasis:"50%"}}));function L({mediaItems:e,isOwner:t}){let{usePageParams:a}=(0,o.OgA)(),n=a(),i=(null==n?void 0:n.rid)||!1,r=e.length,s=r%3==0?"type-1":r%3==1?"type-1":"type-2";return l.createElement(Z,{isOwner:t},e.map((a,n)=>l.createElement(C,{typeGridLayout:s,isPageAllMessages:i,isLastItem:n===e.length-1,key:`k${n}`},l.createElement(O,A({},a,{keyIndex:n,layout:"multi-image",totalImages:e,typeGridLayout:s,isOwner:t})))))}let F=e=>{let{useGetItem:t}=(0,o.OgA)(),a=e.map(e=>{if("string"!=typeof e)return e;{let a=t(e);return a}}),n=[],i=[];return a.map(e=>{e.is_image?n.push(e):i.push(e)}),{multiImageFile:n,otherFile:i,attachments:a}};function T({message:e,isOwner:t}){var a;let{multiImageFile:n,otherFile:i,attachments:r}=F(null==e?void 0:e.attachments);return(null===(a=e.attachments)||void 0===a?void 0:a.length)&&(n.length||i.length)?n.length>1?l.createElement(l.Fragment,null,l.createElement(L,{mediaItems:n,isOwner:t}),i.length?i.map((e,a)=>l.createElement(_,{item:e,key:`k${a}`,isOwner:t})):null):r.map((e,a)=>l.createElement(_,{item:e,key:`k${a}`,isOwner:t})):null}var W=a(30120),R=a(91647),B=a(41547);let S="MsgAttachment",D=(0,i.ZP)("div",{name:S,slot:"UIMsgAttachment",shouldForwardProp:e=>"isOwner"!==e})(({theme:e,isOwner:t})=>({marginBottom:e.spacing(-2),marginLeft:e.spacing(1),marginRight:0,marginTop:e.spacing(.25),paddingBottom:e.spacing(3),maxWidth:"calc(100% - 8px)",display:"flex",flexDirection:"row",overflow:"hidden",border:e.mixins.border("secondary"),borderRadius:e.spacing(1),minHeight:"70px","& a:not(.MuiAvatar-root)":{color:e.palette.text.primary,textDecoration:"underline",cursor:"pointer"},...t&&{color:e.palette.text.secondary,marginRight:e.spacing(1),marginLeft:0}})),G=(0,i.ZP)("div",{name:S,slot:"Author"})(({theme:e})=>({display:"flex",justifyContent:"flex-start",alignItems:"center","& .MuiAvatar-root":{fontSize:e.mixins.pxToRem(7)},strong:{padding:e.spacing(0,.5)}})),Q=(0,i.ZP)("div",{name:S,slot:"MsgAttachmentFlex"})(({theme:e})=>({textAlign:"start"})),j=(0,i.ZP)(B.Ys,{name:S,slot:"Text"})(({theme:e})=>({color:e.palette.text.secondary,margin:e.spacing(.5,0)})),z=(0,i.ZP)("div",{name:S,slot:"TextDelete"})(({theme:e})=>({margin:e.spacing(.5),marginLeft:0,fontStyle:"italic"})),$=(0,i.ZP)("div",{name:S,slot:"AttachmentInfo"})(({theme:e})=>({overflow:"hidden",margin:e.spacing(1,1,0,0)})),H=(0,i.ZP)("div")(({theme:e})=>({width:e.spacing(.5),height:"100%",backgroundColor:e.palette.grey["100"]})),N=(0,i.ZP)("div")(({theme:e})=>({width:e.spacing(3),minWidth:e.spacing(3),marginTop:e.spacing(1.5),display:"flex",justifyContent:"center",alignItems:"center"})),U=(0,i.ZP)(R.Z)(({theme:e})=>({color:"light"===e.palette.mode?e.palette.grey.A200:e.palette.text.primary})),V=(0,i.ZP)(B.Ys,{name:S,slot:"uiMsgAttachmentLink"})(({theme:e})=>({"& .ico":{fontSize:e.mixins.pxToRem(12),marginRight:e.spacing(.5)}}));function Y({dataQuote:e,isOwner:t}){var a;let{i18n:i}=(0,o.OgA)(),{type:r,user:s,message:m}=e,{multiImageFile:g,otherFile:d,attachments:p}=F(null==e?void 0:e.attachments);return l.createElement(D,{isOwner:t},l.createElement(N,null,l.createElement(H,null)),l.createElement($,null,s?l.createElement(G,null,l.createElement(U,{component:"h2",variant:"h5"},s.full_name||s.user_name)):null,l.createElement(Q,null,"messageDeleted"===r?l.createElement(z,null,i.formatMessage({id:"message_was_deleted"})):null,m&&"messageDeleted"!==r?l.createElement(j,{lines:1,dangerouslySetInnerHTML:{__html:(0,n.ZP)(m)}}):null,m||(null===(a=e.attachments)||void 0===a||!a.length)&&(g.length||d.length)?null:l.createElement(W.Z,{sx:{mt:1}},g.length>1?l.createElement(j,{lines:1},i.formatMessage({id:"total_photo"},{value:g.length})):l.createElement(l.Fragment,null,p.map((e,a)=>l.createElement(l.Fragment,{key:`k${a}`},e.is_image&&e.image?l.createElement(_,{item:e,isOwner:t}):l.createElement(V,{lines:1},l.createElement(B.zb,{icon:"ico-paperclip-alt"}),e.file_name))))))))}let q="MessageText",J=(0,i.ZP)("div",{name:q,slot:"uiChatMsgItemMsg",shouldForwardProp:e=>"isOwner"!==e&&"isQuote"!==e})(({theme:e,isOwner:t,isQuote:a})=>({borderRadius:e.spacing(1),fontSize:e.mixins.pxToRem(15),padding:e.spacing(1.25),display:"flex",alignItems:"center",backgroundColor:e.palette.grey["100"],..."dark"===e.palette.mode&&{backgroundColor:e.palette.grey["600"]},overflowWrap:"break-word","& a":{color:t?"#fff":e.palette.text.primary,textDecoration:"underline",cursor:"pointer",overflowWrap:"break-word"},...t&&{backgroundColor:e.palette.primary.main,color:"#fff !important"}})),K=(0,i.ZP)("div",{name:q,slot:"uiChatMsgItemBodyInnerWrapper",shouldForwardProp:e=>"isOwner"!==e&&"filter"!==e})(({theme:e,isOwner:t,filter:a})=>({display:"flex",flexDirection:"column",alignItems:"flex-start",...t&&!a&&{alignItems:"flex-end"}}));function X({message:e,isOwner:t,createdDate:a,tooltipPosition:i}){let o=e.message?(0,n.ZP)(e.message,{mentions:e.mentions}):null,s=null==e?void 0:e.extra;return l.createElement(r.Z,{title:a,placement:i,PopperProps:{disablePortal:!0}},l.createElement(K,{isOwner:t,filter:null==e?void 0:e.filtered},s?l.createElement(Y,{dataQuote:s,isOwner:t}):null,o?l.createElement(J,{isQuote:Boolean(s),isOwner:t,className:"uiChatMsgItemMsg",dangerouslySetInnerHTML:{__html:o}}):null,l.createElement(T,{message:e,isOwner:t})))}}}]);