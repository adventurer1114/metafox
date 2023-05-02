"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-photo-components-PhotoModalView-PhotoItemModalView"],{22980:function(e,t,i){i.r(t),i.d(t,{default:function(){return E}});var a=i(85597),n=i(41547),o=i(27274),l=i(30120),r=i(21822),s=i(50130),d=i(81719),c=i(38790),p=i(86010),m=i(67294),g=i(49249),u=i(22410),h=i(73327);let f=(0,u.Z)(e=>(0,h.Z)({root:{},image:{maxWidth:"100%",maxHeight:"100vh",float:"left",opacity:0,[e.breakpoints.down("sm")]:{maxHeight:"350px"}},imageHeightAuto:{maxHeight:"initial",[e.breakpoints.down("sm")]:{maxHeight:"350px"}},visibleImage:{opacity:1},boxFake:{position:"absolute",top:0,right:0,left:0,objectFit:"cover",display:"flex",alignItems:"center",justifyContent:"center"},imageFake:{width:"100%",height:"auto"},actionBar:{position:"absolute",right:0,top:0,padding:e.spacing(1),display:"flex",justifyContent:"flex-end",zIndex:1,alignItems:"center"},tagFriend:{color:"#fff !important",width:32,height:32,fontSize:e.mixins.pxToRem(15)},dropDown:{color:"#fff",minWidth:"32px",height:"32px",fontSize:e.mixins.pxToRem(15)},taggedBox:{position:"absolute",left:0,top:0,width:"100px",height:"100px",border:"2px solid",borderColor:e.palette.background.paper,boxShadow:e.shadows[20],transform:"translate(-50%,-50%)"},suggestFriend:{backgroundColor:e.mixins.backgroundColor("paper"),marginTop:"10px",marginLeft:"-70px",border:e.mixins.border("secondary"),borderRadius:e.shape.borderRadius,"&  .MuiFilledInput-root":{background:"none"}},smallAvatar:{width:"24px",height:"24px",marginRight:e.spacing(1)},whoIsThis:{height:"100px",width:"100%"},popper:{zIndex:e.zIndex.snackbar,backgroundColor:e.mixins.backgroundColor("paper"),padding:e.spacing(1),boxShadow:e.shadows[20],borderRadius:e.shape.borderRadius,margin:e.spacing(1,0),width:"240px","& .MuiAutocomplete-paper":{boxShadow:"none"},"& .MuiAutocomplete-listbox":{padding:0},"& .MuiAutocomplete-option":{borderRadius:e.shape.borderRadius,padding:"6px 8px"},"& .MuiAutocomplete-root":{maxWidth:"224px"},"& .MuiFilledInput-underline:after":{display:"none"},"& .MuiAutocomplete-endAdornment":{display:"none"},"& .MuiFilledInput-underline:before":{display:"none"},"& .MuiFilledInput-root":{background:"none",border:e.mixins.border("secondary"),borderRadius:e.shape.borderRadius},"& .MuiAutocomplete-noOptions":{padding:e.spacing(1.5,1)}},tagItem:{display:"flex",alignItems:"center",width:"100%",padding:e.spacing(.5,0)},userName:{flexGrow:1,whiteSpace:"nowrap",overflow:"hidden",textOverflow:"ellipsis",display:"block",fontSize:e.mixins.pxToRem(14)},inputBaseBox:{"& input":{width:"100%",border:e.mixins.border("secondary"),borderRadius:e.shape.borderRadius,padding:e.spacing(1),fontSize:e.mixins.pxToRem(15),"&:focus":{border:e.mixins.border("secondary"),outline:"none"}}},message:{color:"#fff",margin:e.spacing(0,1)},iconButton:{color:"white"},clear:{clear:"both"}}),{name:"MuiPhotoItemViewModal"});var x=i(33651),b=i(11770);let v="PhotoItemModalView",y=(0,d.ZP)("div",{name:v,slot:"ImageBox",shouldForwardProp:e=>"tagging"!==e})(({theme:e,tagging:t})=>({position:"relative",maxWidth:"100%",maxHeight:"100vh",...t&&{cursor:"pointer"}})),w=(0,d.ZP)("div",{name:v,slot:"actionBar"})(({theme:e})=>({position:"absolute",right:0,top:0,width:"100%",padding:e.spacing(1),display:"flex",justifyContent:"space-between",zIndex:1,alignItems:"center"}));function E({item:e,identity:t,imageHeightAuto:i=!1,enablePhotoTags:d=!0,taggedFriends:u=[],isModal:h=!0,onAddPhotoTag:v,onRemovePhotoTag:E,onMinimizePhoto:k}){var Z,M;let I=f(),{i18n:R,assetUrl:_,useDialog:S,useIsMobile:C}=(0,a.OgA)(),{closeDialog:F}=S(),A=(0,a.Pk8)(),P=C(),N=m.useRef(),z=m.useRef(),[B,T]=m.useState(!1),[H,O]=m.useState({width:0,height:0}),[W,$]=m.useState({px:0,py:0}),[j,L]=m.useState(!1),[Q,V]=m.useState(!1),[D,X]=m.useState(!0);if(!e)return null;let G=(0,o.Q4)(e.image,"origin",_("photo.no_image")),K=(0,o.Q4)(e.image,"240",_("photo.no_image")),U=(e,t)=>{e.stopPropagation(),E(t)},Y=e=>{O({width:e.target.width,height:e.target.height}),T(!0)},q=()=>{L(e=>!e),j||V(!1)},J=e=>{let t=e.currentTarget.getBoundingClientRect(),i=Math.max(Math.min(e.clientX-t.left,H.width-50),50)/H.width*100,a=Math.max(Math.min(e.clientY-t.top,H.height-50),50)/H.height*100;$({px:i,py:a}),j&&V(!0)},ee=e=>{let{px:t,py:i}=W;v({content:e,px:t,py:i}),V(!1)},et=()=>{V(!1),L(!1)},ei=()=>{X(!D),k&&k(D)},ea=()=>{F(),k&&k(!1)};return m.createElement(n.Qn,{onClickAway:et},m.createElement("div",null,m.createElement(y,{tagging:j,onClick:J},m.createElement("img",{onLoad:Y,ref:z,className:(0,p.default)(I.image,B&&I.visibleImage,i&&I.imageHeightAuto),alt:e.title,src:G}),B?null:m.createElement("div",{className:I.boxFake},m.createElement("img",{className:I.imageFake,alt:e.title,src:K})),m.createElement(b.Z,{open:j&&Q,px:W.px,py:W.py,classes:I,ref:N}),j&&Q?m.createElement(x.Z,{onItemClick:ee,classes:I,anchorRef:N,identity:t,open:!0}):null,(null===(Z=e.tagged_friends)||void 0===Z?void 0:Z.length)?e.tagged_friends.map(t=>m.createElement(g.Z,{extra:e.extra,tagging:j,identity:t,key:t.toString(),onRemove:U,classes:I})):null,m.createElement("div",{className:I.clear})),m.createElement(w,null,m.createElement(l.Z,null,h&&!P&&m.createElement(c.Z,{title:R.formatMessage({id:"close"})},m.createElement(s.Z,{className:I.tagFriend,onClick:ea},m.createElement(n.zb,{icon:"ico-close",color:"white"})))),A&&d&&!j?m.createElement(l.Z,null,(null===(M=e.extra)||void 0===M?void 0:M.can_tag_friend)&&m.createElement(c.Z,{title:R.formatMessage({id:"start_tagging"})},m.createElement(s.Z,{className:I.tagFriend,onClick:q},m.createElement(n.zb,{icon:"ico-price-tag",color:"white"}))),h&&m.createElement(c.Z,{title:R.formatMessage({id:D?"switch_to_full_screen":"exit_full_screen"})},m.createElement(s.Z,{className:I.tagFriend,onClick:ei},m.createElement(n.zb,{icon:D?"ico-arrow-expand":"ico-arrow-collapse",color:"white"})))):null,d&&j?m.createElement(c.Z,{title:R.formatMessage({id:"done_tagging"})},m.createElement(r.Z,{variant:"contained",color:"primary",size:"small",onClick:q},R.formatMessage({id:"done"}))):null)))}},33651:function(e,t,i){i.d(t,{Z:function(){return x}});var a=i(85597),n=i(62937),o=i(67294),l=i(27361),r=i.n(l),s=i(27274),d=i(7961),c=i(35705),p=i(81719);let m="SuggestionList",g=(0,p.ZP)("div",{name:m,slot:"RootNoFound"})(({theme:e})=>({minHeight:"100px",textAlign:"center"})),u=(0,p.ZP)("div",{name:m,slot:"Root"})(({theme:e})=>({minHeight:"100px"})),h=(e,{text:t,identity:i})=>{let a=r()(e,i);return{...e.friend.suggestions[`:${t}`],item_id:null==a?void 0:a.id}};var f=(0,a.$jX)(h)(function(e){let{dispatch:t,i18n:i}=(0,a.OgA)(),{text:n,item_id:l,data:r,loaded:p,onItemClick:m,classes:h,excludeIds:f,isFullFriend:x}=e,b=o.useMemo(()=>{return x?{excludeIds:f}:{item_type:"photo",item_id:l}},[f,l,x]);return(o.useEffect(()=>{t({type:"friend/suggestions/LOAD",payload:{text:n,...b}})},[t,n,b]),p)?(null==r?void 0:r.length)?o.createElement(u,null,r.map(e=>o.createElement("div",{onClick:t=>{t.preventDefault(),t.stopPropagation(),m(e)},className:h.tagItem,key:e.id.toString()},o.createElement(d.Z,{src:(0,s.Q4)(e.image,"240"),children:(0,s.vK)(e.label),alt:e.label,className:h.smallAvatar,style:{backgroundColor:s.kU.hex((0,s.vK)(e.label))}}),o.createElement("span",{className:h.userName},e.label)))):o.createElement(g,null,o.createElement("div",{style:{padding:16}},i.formatMessage({id:"no_people_found"}))):o.createElement(g,{sx:{display:"flex",alignItems:"center",justifyContent:"center"}},o.createElement(c.Z,{color:"secondary",size:32}))});function x({classes:e,onItemClick:t,anchorRef:i,identity:l,isFullFriend:r,excludeIds:s}){let{i18n:d}=(0,a.OgA)(),[c,p]=o.useState(""),m=e=>{p(e)};return o.createElement(n.Z,{id:"suggest-friends",open:Boolean(i.current),disablePortal:!0,anchorEl:i.current,placement:"bottom",className:e.popper},o.createElement("div",{className:e.inputBaseBox},o.createElement("input",{type:"text",placeholder:d.formatMessage({id:"type_any_name"}),onChange:e=>m(e.target.value),autoFocus:!0,onBlur:e=>e.target.focus()})),o.createElement(f,{onItemClick:t,text:c,classes:e,identity:l,isFullFriend:r,excludeIds:s}))}},11770:function(e,t,i){var a=i(86010),n=i(67294);t.Z=n.forwardRef(function({classes:e,px:t,py:i,open:o},l){let r=n.useMemo(()=>{return{left:`${t}%`,top:`${i}%`}},[t,i]);return n.createElement("div",{className:(0,a.default)(e.taggedBox,o?"":"srOnly"),style:r,ref:l},n.createElement("div",{className:e.whoIsThis}))})},49249:function(e,t,i){i.d(t,{Z:function(){return x}});var a=i(85597),n=i(27361),o=i.n(n),l=i(41547),r=i(21822),s=i(81719),d=i(67294);let c="PhotoTag",p=(0,s.ZP)("div",{name:c,slot:"root",shouldForwardProp:e=>"tagging"!==e&&"px"!==e&&"py"!==e&&"hovering"!==e&&"forceShow"!==e})(({theme:e,tagging:t,px:i,py:a,hovering:n,forceShow:o})=>({position:"absolute",width:t?10:100,height:t?10:100,transform:"translate(-50%,-50%)","&:hover > div:first-of-type":{visibility:"visible"},...(t||o)&&{"& > div:first-of-type":{visibility:"visible"}},zIndex:n?10:"auto",...i&&a&&{left:`${i}%`,top:`${a}%`}})),m=(0,s.ZP)("div",{name:c,slot:"Item"})(({theme:e})=>({position:"absolute",backgroundColor:"rgba(255,255,255,.8)",borderRadius:e.shape.borderRadius,overflow:"hidden",textOverflow:"ellipsis",maxWidth:"120px",textAlign:"center",display:"flex",alignItems:"center",padding:e.spacing(.5),transform:"translate(-50%,-50%)",left:"50%",top:"50%",visibility:"hidden"})),g=(0,s.ZP)(a.QVN,{name:c,slot:"LinkStyled"})(({theme:e})=>({color:"light"===e.palette.mode?e.palette.grey.A700:e.palette.grey["800"],fontSize:e.mixins.pxToRem(13),fontWeight:e.typography.fontWeightBold,maxWidth:120,whiteSpace:"nowrap",textOverflow:"ellipsis",overflow:"hidden",flexGrow:1,margin:e.spacing(0,.5)})),u=(0,s.ZP)(r.Z,{name:c,slot:"ButtonStyled"})(({theme:e})=>({color:"light"===e.palette.mode?e.palette.grey.A700:e.palette.grey["800"],minWidth:"11px",padding:e.spacing(.5,1),margin:e.spacing(-.5),fontSize:e.mixins.pxToRem(11)})),h=(0,s.ZP)("span",{name:c,slot:"LabelStyled"})(({theme:e})=>({margin:"0 4px"})),f=(e,{identity:t})=>{let i=o()(e,t);return i?{item:i,user:i.user?o()(e,i.user):void 0}:{}};var x=(0,a.$jX)(f)(function({item:e,user:t,tagging:i,extra:a,onRemove:n,isTypePreview:o,forceShow:r}){var s;let c=i&&(null==e?void 0:null===(s=e.extra)||void 0===s?void 0:s.can_remove_tag_friend),[f,x]=d.useState(!1);return d.createElement(p,{px:e.px,py:e.py,tagging:i,hovering:f,forceShow:r},d.createElement(m,{onMouseEnter:()=>x(!0),onMouseLeave:()=>x(!1)},t&&!o?d.createElement(g,{to:`/${t.user_name}`,children:t.full_name}):d.createElement(h,null,(null==t?void 0:t.full_name)||(null==e?void 0:e.content)),c?d.createElement(u,{disableRipple:!0,disableFocusRipple:!0,onClick:t=>{n(t,e.id)}},d.createElement(l.zb,{icon:"ico-close"})):null))})}}]);