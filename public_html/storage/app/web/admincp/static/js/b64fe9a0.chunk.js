(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-core-components-SiteBarMobileBlock-SiteBarMobileBlock"],{5512:function(e,t,a){var n=a(42118);e.exports=function(e,t){for(var a=e.length;a--&&n(t,e[a],0)>-1;);return a}},89817:function(e,t,a){var n=a(42118);e.exports=function(e,t){for(var a=-1,o=e.length;++a<o&&n(t,e[a],0)>-1;);return a}},92742:function(e,t,a){var n=a(80531),o=a(27561),r=a(40180),i=a(5512),l=a(89817),c=a(83140),s=a(79833);e.exports=function(e,t,a){if((e=s(e))&&(a||void 0===t))return o(e);if(!e||!(t=n(t)))return e;var p=c(e),d=c(t),m=l(p,d),u=i(p,d)+1;return r(p,m,u).join("")}},7607:function(e,t,a){"use strict";a.d(t,{Z:function(){return C}});var n=a(85597),o=a(41547),r=a(81674),i=a(86010),l=a(92742),c=a.n(l),s=a(67294),p=a(22410),d=a(73327);let m=(0,p.Z)(e=>{var t,a,n,o;return(0,d.Z)({root:{width:320,position:"absolute",left:"50%",transform:"translateX(-50%)",top:0,paddingTop:13,overflow:"hidden"},rootOpen:{background:e.palette.background.paper,borderBottomLeftRadius:e.shape.borderRadius,borderBottomRightRadius:e.shape.borderRadius,boxShadow:e.shadows[8],"& $searchIcon":{color:e.palette.primary.main},"& $resultWrapper":{display:"block"}},form:{width:284,position:"relative",background:e.palette.action.hover,borderRadius:"999px",height:e.spacing(4),margin:e.spacing(0,2,0,2),"& .MuiOutlinedInput-notchedOutline":{border:"none"},"& .MuiInputBase-input":{height:e.spacing(4),boxSizing:"border-box"}},formFocused:{border:e.mixins.border("primary"),borderColor:e.palette.primary.main,background:"none"},searchIcon:{color:e.palette.text.secondary,transition:"all .2s ease",cursor:"pointer"},inputRoot:{color:`${e.palette.text.secondary} !important`,width:"100%"},inputInput:{padding:e.spacing(1,2,1,0),boxSizing:"border-box",paddingLeft:"30px",transition:"all .2s ease",width:"100%",height:"32px"},guestBar:{justifyContent:"space-between"},textField:{marginRight:e.spacing(1),height:e.spacing(5),width:"203px","& input":{color:e.palette.text.secondary}},button:{marginRight:e.spacing(2),textTransform:"capitalize",fontWeight:e.typography.fontWeightBold},account:{textTransform:"none",fontWeight:e.typography.fontWeightBold},searchIconFocused:{color:e.palette.primary.main},renderOption:{position:"relative",width:"100%",padding:e.spacing(1,0),display:"flex",alignItems:"center",justifyContent:"space-between"},removeButton:{minWidth:"16px",cursor:"pointer",height:"26px",borderRadius:"26px",color:e.palette.text.secondary,fontSize:e.mixins.pxToRem(14)},labelItem:{flexGrow:1,paddingRight:e.spacing(1)},resultWrapper:{display:"none"},menuList:{margin:0,padding:0},menuItem:{"& + $menuItem":{borderTop:"solid 1px",borderTopColor:null===(t=e.palette.border)||void 0===t?void 0:t.secondary}},menuItemLink:{padding:e.spacing(1.2,2),display:"flex",flexDirection:"row",alignItems:"center",fontWeight:"bold",color:e.palette.text.primary,transition:"all .2s","&:hover":{backgroundColor:e.palette.action.selected,borderRadius:e.shape.borderRadius}},menuItemAvatar:{width:e.spacing(6),height:e.spacing(6),marginRight:e.spacing(2)},menuItemIcon:{display:"inline-block",width:e.spacing(4),height:e.spacing(4),lineHeight:"32px",backgroundColor:"rgba(0,0,0,0.1)",borderRadius:"50%",color:"#828080",textAlign:"center",marginRight:e.spacing(2)},headerPopup:{padding:e.spacing(1,1,1,2),display:"flex",justifyContent:"space-between",alignItems:"center",textTransform:"capitalize",fontWeight:e.typography.fontWeightBold,fontSize:e.mixins.pxToRem(18)},headerRecent:{borderBottom:"1px solid",borderBottomColor:null===(a=e.palette.border)||void 0===a?void 0:a.secondary},clearButton:{fontWeight:"normal !important"},recentItem:{padding:e.spacing(1,2),display:"flex",alignItems:"center",color:e.palette.text.secondary,fontSize:e.mixins.pxToRem(15),cursor:"pointer","& + $recentItem":{borderTop:"solid 1px",borderTopColor:null===(n=e.palette.border)||void 0===n?void 0:n.secondary},"&:hover":{background:e.palette.action.selected}},focusedItem:{backgroundColor:e.palette.action.selected},recentLabel:{flexGrow:1,padding:e.spacing(1,0),overflow:"hidden",whiteSpace:"nowrap",textOverflow:"ellipsis"},searchItem:{display:"flex",flexDirection:"row",padding:e.spacing(1.5),color:e.palette.text.primary,alignItems:"center",borderTop:"solid 1px",borderTopColor:null===(o=e.palette.border)||void 0===o?void 0:o.secondary,"&:hover":{background:e.palette.action.selected}},searchContent:{paddingLeft:e.spacing(1),flexGrow:1,overflow:"hidden"},searchAvatar:{width:48,height:48},searchNote:{fontSize:e.mixins.pxToRem(13),color:e.palette.text.secondary,fontWeight:"normal"},searchTitle:{padding:e.spacing(.5,0),overflow:"hidden",whiteSpace:"nowrap",textOverflow:"ellipsis",display:"block",maxWidth:"100%",fontWeight:e.typography.fontWeightBold,fontSize:e.mixins.pxToRem(15),color:e.palette.text.primary},itemName:{fontSize:e.mixins.pxToRem(13),color:e.palette.text.secondary,paddingTop:e.spacing(.5),fontWeight:"normal"},noOptions:{fontSize:e.mixins.pxToRem(15),padding:e.spacing(2),display:"block"},searchMinimize:{width:"32px",height:"32px",transition:"all .1s",transform:"translateZ(0)","& $searchIcon":{position:"relative",pointerEvents:"inherit"},"& $inputRoot":{position:"absolute",left:0,top:0,width:"32px",height:"30px",opacity:0,zIndex:2,cursor:"pointer"},[e.breakpoints.down("md")]:{}},divider:{margin:`${e.spacing(0,2,0,1)} !important`,height:"32px !important"}})},{name:"AppBar"});var u=a(21822),g=a(50130),h=a(41609),f=a.n(h),x=a(86706),b=s.forwardRef(function({onSearch:e,limit:t=5,classes:a},r){let{dispatch:l,i18n:c}=(0,n.OgA)(),[p,d]=s.useState(-1),m=[],h=(0,x.v9)(e=>e.search.recentSearch),{data:b,loaded:v}=h;s.useImperativeHandle(r,()=>{return{moveNext:()=>{if(!(null==b?void 0:b.length))return;let e=Math.min(b.length,t);e&&d(t=>{return(t+1+e)%e})},movePrev:()=>{if(!(null==b?void 0:b.length))return;let e=Math.min(b.length,t);e&&d(t=>{return(t-1+e)%e})},selected:()=>{}}}),s.useEffect(()=>{d(-1)},[b]),s.useEffect(()=>{v||l({type:"recentSearch/INIT",payload:{}})},[l,v]);let y=s.useCallback((e,t)=>{e&&(e.preventDefault(),e.stopPropagation()),l({type:"recentSearch/REMOVE",payload:{text:t}})},[]),k=s.useCallback(()=>{l({type:"recentSearch/CLEAR",payload:{}})},[l]);return f()(b)?s.createElement("div",{className:a.menuList,"data-testid":"noRecentSearches"},s.createElement("span",{className:a.noOptions},c.formatMessage({id:"no_recent_searches"}))):v?s.createElement(s.Fragment,null,s.createElement("div",{className:(0,i.default)(a.headerPopup,a.headerRecent)},s.createElement("span",null,c.formatMessage({id:"recents"})),s.createElement(u.Z,{onClick:k,className:a.clearButton,tabIndex:-1,size:"medium"},c.formatMessage({id:"clear"}))),s.createElement("div",{className:a.menuList},b.slice(0,5).map((t,n)=>s.createElement("div",{role:"option",tabIndex:-1,ref:e=>{m[n]=e},"aria-selected":p===n,key:t.toString(),onClick:()=>e(t),className:(0,i.default)(a.recentItem,p===n&&a.focusedItem)},s.createElement("span",{className:a.recentLabel},t),s.createElement(g.Z,{tabIndex:-1,size:"smaller",onClick:e=>y(e,t),sx:{fontSize:16}},s.createElement(o.zb,{color:"textSecondary",icon:"ico-close"})))))):null}),v=a(27274),y=a(7961),k=a(30120),E=a(35705),I=a(91647);let S=e=>e.search.suggestions;var w=s.forwardRef(function({limit:e=6,text:t,classes:a,onSearch:o,isActionSearch:r},l){let{i18n:c}=(0,n.OgA)(),[p,d]=s.useState(-1),m=(0,x.v9)(S),{data:u,loaded:g}=Object.assign({},m[t]);s.useImperativeHandle(l,()=>{return{moveNext:()=>{if(!(null==u?void 0:u.length))return;let t=Math.min(u.length,e);t&&d(e=>{return(e+1+t)%t})},movePrev:()=>{if(!(null==u?void 0:u.length))return;let t=Math.min(u.length,e);t&&d(e=>{return(e-1+t)%t})},selected:()=>{}}});let h=()=>{"function"==typeof o&&o(t)};return s.createElement(s.Fragment,null,!!t&&s.createElement(I.Z,{paragraph:!0,padding:"16px",margin:"0",sx:{cursor:"pointer"}},r&&s.createElement("span",{onClick:h},`${c.formatMessage({id:"search"})} “${t}”`)),s.createElement("div",{className:a.menuList,"data-testid":"suggestions"},g&&u.length?u.slice(0,e).map((e,t)=>{return s.createElement(n.QVN,{role:"option","data-testid":"suggestion",key:e.to,to:e.to,className:(0,i.default)(a.searchItem,p===t&&a.focusedItem)},s.createElement(y.Z,{className:a.searchAvatar,src:(0,v.Q4)(null==e?void 0:e.image,"200"),children:(0,v.vK)(e.title),style:{backgroundColor:v.kU.hex((0,v.vK)(e.title)||"")}}),s.createElement("div",{className:a.searchContent},s.createElement(I.Z,{variant:"h5",className:a.searchTitle},e.title),s.createElement("div",{className:a.searchNote},e.note)))}):null,g?null:s.createElement(k.Z,{sx:{display:"flex",justifyContent:"center",pb:2,color:"text.disabled"}},s.createElement(E.Z,{variant:"indeterminate",color:"inherit"}))))});function C({openSearch:e,minimize:t,closeSearch:a}){var l;let p=m(),{dispatch:d,usePageParams:u,i18n:g,eventCenter:h,navigate:f,location:x,getSetting:v}=(0,n.OgA)(),{q:y,pathname:k}=u(),[E,I]=s.useState({open:!1,text:/^\/search/.test(k)?y:"",focus:!1,minimize:!!t}),S=v("search"),C=g.formatMessage({id:"search"}),R=s.useRef(),N=s.useRef(),z=s.useRef();s.useEffect(()=>{let e=h.on("minimizeGlobalSearchForm",()=>{I(e=>({...e,minimize:!0}))});return()=>{h.off("minimizeGlobalSearchForm",e)}},[]),s.useEffect(()=>{!0===e&&$()},[e]);let B=s.useCallback(e=>{e.startsWith("#")&&e.substring(1)?f(`/hashtag/search?q=${e.substring(1)}`):f(`/search/?q=${e}`),(null==N?void 0:N.current)&&N.current.blur(),A()},[]),W=s.useCallback(()=>{I(e=>({...e,focus:!1,open:!1}))},[]),T=s.useCallback(()=>{var e;(null===(e=R.current)||void 0===e?void 0:e.movePrev)&&R.current.movePrev()},[]),M=s.useCallback(()=>{var e;(null===(e=R.current)||void 0===e?void 0:e.moveNext)&&R.current.moveNext()},[]),A=s.useCallback(()=>{I(e=>({...e,focus:!1,open:!1}))},[]),Z=s.useCallback(e=>{var t;let n=null===(t=N.current)||void 0===t?void 0:t.value;if("#"===n)return;e&&(e.preventDefault(),e.stopPropagation());let o=c()(n);o||A(),o&&(d({type:"recentSearch/ADD",payload:{text:o}}),B(o)),a&&a()},[]),L=s.useCallback(e=>{let t=e.currentTarget.value;I(e=>({...e,text:t,selectedIndex:0})),d({type:"suggestions/QUERY",payload:{text:t}})},[]),$=s.useCallback(()=>{E.open||I(e=>({...e,focus:!0,open:!0})),d({type:"suggestions/QUERY",payload:{text:E.text}})},[E.open,E.text]),O=s.useCallback(()=>{a&&a(),I(e=>({...e,open:!1,focus:!1}))},[]),H=s.useCallback(()=>{Z()},[]),P=s.useCallback(e=>{e.preventDefault(),e.stopPropagation()},[]),D=s.useCallback(e=>{let t=null==e?void 0:e.keyCode;if(!e.metaKey&&!e.ctrlKey)switch(t){case 9:W();break;case 27:W(),P(e);break;case 13:H(),P(e);break;case 38:T(),P(e);break;case 40:M(),P(e);break;default:E.open||$()}},[E.open]);return(s.useEffect(()=>{x.pathname.startsWith("/search")||I(e=>({...e,text:"",open:!1}))},[x]),S)?s.createElement(o.Qn,{onClickAway:O},s.createElement("div",{ref:z,"data-testid":"formSearch",className:(0,i.default)(p.root,E.open&&p.rootOpen),role:"search",id:"globalSearchBox"},s.createElement("form",{className:(0,i.default)(p.form,E.open&&p.formFocused),method:"get","aria-expanded":!E.minimize,onSubmit:Z},s.createElement(r.ZP,{startAdornment:s.createElement(o.zb,{className:p.searchIcon,icon:"ico-search-o"}),placeholder:C,classes:{root:p.inputRoot,input:p.inputInput},autoComplete:"off",value:E.text,name:"search",inputProps:{"aria-label":"search","data-testid":"searchBox",autoComplete:"off",autoCapitalize:"off"},inputRef:N,onFocus:$,onChange:L,onKeyDown:D})),E.open?s.createElement("div",{className:(0,i.default)(p.resultWrapper)},E.text?s.createElement(w,{ref:R,onSearch:B,classes:p,text:E.text,isActionSearch:(null===(l=N.current)||void 0===l?void 0:l.value)!=="#"}):s.createElement(b,{onSearch:B,ref:R,classes:p})):null)):null}},49618:function(e,t,a){"use strict";a.d(t,{Z:function(){return p}});var n=a(85597),o=a(21241),r=a(27274),i=a(67294),l=a(22410),c=a(73327);let s=(0,l.Z)(e=>(0,c.Z)({root:{},menu:{},text:{flexGrow:1},colapseIcon:{},icon:{color:"#fff",backgroundColor:e.palette.primary.main,borderRadius:"32px",width:"32px",height:"32px",textAlign:"center",marginRight:e.spacing(2),lineHeight:"32px"},dropDownIcon:{},menuItemAvatar:{},menuItemIcon:{fontSize:e.mixins.pxToRem(18),display:"inline-block",textAlign:"center",marginRight:e.spacing(1),backgroundColor:"#e0dddd",borderRadius:"50%",lineHeight:"32px",width:e.spacing(4)},menuItem:{height:e.spacing(7),fontWeight:e.typography.fontWeightRegular,margin:e.spacing(0,-1),"& a":{padding:e.spacing(0,1)},"&:hover":{backgroundColor:e.palette.action.selected,borderRadius:e.shape.borderRadius}},menuItemText:{fontSize:e.mixins.pxToRem(15)},menuItemLink:{color:"light"===e.palette.mode?e.palette.text.primary:e.palette.text.secondary,display:"block",height:"100%",lineHeight:`${e.spacing(7)}px`},activeMenuItem:{"& $menuItemLink":{color:e.palette.primary.main,fontWeight:"bold"},"& $menuItemIcon":{color:e.palette.background.paper,backgroundColor:e.palette.primary.main}},menuItemButton:{marginTop:e.spacing(2),"& .ico":{marginRight:e.spacing(1)}},buttonLink:{fontSize:e.mixins.pxToRem(18),fontWeight:"bold",height:e.spacing(5)},menuHeading:{fontSize:18,lineHeight:2,fontWeight:"bold",color:e.palette.text.secondary,margin:e.spacing(1,0)},headerBlock:{fontSize:e.mixins.pxToRem(18),fontWeight:"bold",color:"light"===e.palette.mode?e.palette.text.secondary:e.palette.text.primary,padding:e.spacing(2,0)}}),{name:"SidebarAppMenu"});function p({displayTitle:e,title:t,blockProps:a,appName:l,menuName:c,item:p}){let d=s(),{usePageParams:m,useSession:u,getAcl:g,jsxBackend:h,compactUrl:f,getSetting:x}=(0,n.OgA)(),b=g(),{tab:v,pathname:y,id:k}=m(),E=u(),I=x(),{appName:S,module_name:w}=m(),C=(0,n.oRt)(l||S||w||"core",c);if(!(null==C?void 0:C.items)||!C.items.length)return null;let R=(0,r.W$)(C.items,{setting:I,session:E,acl:b,item:p});return R.length?i.createElement(o.gO,{testid:"blockSidebarMenu"},e&&i.createElement(o.ti,{title:t}),i.createElement(o.sU,null,i.createElement("div",{role:"menu"},R.map((e,t)=>h.render({component:`menuItem.as.${e.as||"sidebarLink"}`,props:{key:t.toString(),variant:"contained",item:{...e,to:e.to?f(e.to,{id:k}):void 0},classes:d,active:v&&v===e.tab||y===e.to}}))))):null}},92351:function(e,t,a){"use strict";var n=a(22410),o=a(73327);let r=(0,n.Z)(e=>{var t,a,n;return(0,o.Z)({blockHeader:{minHeight:60},menuWrapper:{display:"flex",height:60,boxShadow:"0px 2px 1px 0 rgba(0, 0, 0, 0.05)",backgroundColor:e.mixins.backgroundColor("paper"),position:"fixed",left:0,right:0,top:0,zIndex:1300},menuButton:{flex:"1",minWidth:"64px",display:"flex",alignItems:"center",justifyContent:"center",color:e.palette.text.secondary,borderBottom:"solid 2px #fff"},active:{color:`${e.palette.primary.main} !important`,borderBottomColor:e.palette.primary.main},menuButtonIcon:{width:"24px",height:"24px",fontSize:"24px"},menuInner:{position:"relative"},number:{width:20,height:20,backgroundColor:e.palette.error.main,color:"#fff",fontSize:e.mixins.pxToRem(13),borderRadius:"50%",display:"flex",justifyContent:"center",position:"absolute",top:-10,right:-10},dot:{width:8,height:8,backgroundColor:e.palette.error.main,borderRadius:"50%",position:"absolute",top:0,right:-4},logo:{height:"35px",display:"inline-block"},menuGuestWrapper:{display:"flex",alignItems:"center",justifyContent:"space-between",height:"60px",boxShadow:"0px 2px 1px 0 rgba(0, 0, 0, 0.05)",backgroundColor:e.mixins.backgroundColor("paper"),padding:e.spacing(0,2),position:"fixed",left:0,right:0,top:0,zIndex:2,"& $menuButton":{flex:"none"}},button:{fontSize:15,height:32,padding:e.spacing(0,3),textTransform:"capitalize"},popover:{"& .MuiPopover-paper":{maxWidth:"100%",width:"100%",borderRadius:0,top:"60px !important",bottom:0,boxShadow:"none !important",borderTop:`solid 1px ${null===(t=e.palette.border)||void 0===t?void 0:t.secondary}`,marginTop:"-1px"}},dropdownMenuWrapper:{width:"100%",minHeight:"calc(100vh - 60px)"},menuItem:{color:e.palette.text.primary,padding:e.spacing(1.5,2)},menuItemLink:{padding:0},link:{color:e.palette.text.primary,textDecoration:"none",padding:e.spacing(1.5,2),display:"block",flexGrow:1},icon:{textAlign:"center",marginRight:e.spacing(1.5)},toggleDarkMode:{fontSize:"32px",marginLeft:"auto",lineHeight:"20px",width:"32px",height:"20px",color:e.palette.text.disabled},userBlock:{display:"flex",alignItems:"center",padding:e.spacing(2),borderBottom:`solid 1px ${null===(a=e.palette.border)||void 0===a?void 0:a.secondary}`},userAvatar:{width:48,marginRight:e.spacing(1)},userInner:{flex:1,minWidth:0},userName:{fontSize:e.mixins.pxToRem(18),lineHeight:1.5,color:e.palette.text.primary,fontWeight:"bold"},linkInfo:{fontSize:e.mixins.pxToRem(15),lineHeight:1,color:e.palette.text.secondary},userAction:{marginLeft:"auto","& > .ico":{width:32,height:32,fontSize:16,color:e.palette.text.secondary,cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center",marginRight:-16}},profile:{},menuApp:{borderTop:"solid 8px",borderTopColor:null===(n=e.palette.border)||void 0===n?void 0:n.secondary,paddingTop:16,"& ul":{marginLeft:e.spacing(-1),paddingBottom:e.spacing(4)},"& ul > li":{marginLeft:0,marginRight:0,marginBottom:e.spacing(1),paddingLeft:0,"& > a":{display:"flex",padding:e.spacing(1),fontSize:"0.9375rem",alignItems:"center",fontWeight:"bold"}}},dialog:{padding:"0 !important"},searchMobile:{display:"block",zIndex:"1301",position:"absolute",top:"0",width:"100%",height:"100%","& > div":{width:"100%"},"& form":{width:"calc(100% - 96px)"}},cancelButton:{position:"absolute",zIndex:"1",right:"16px",top:"50%",transform:"translateY(-50%)"}})},{name:"SiteBarMobileBlock"});t.Z=r},64143:function(e,t,a){"use strict";a.r(t);var n=a(49618),o=a(85597),r=a(21241),i=a(41547),l=a(27274),c=a(21822),s=a(14262),p=a(62097),d=a(86010),m=a(41609),u=a.n(m),g=a(67294),h=a(7607),f=a(92351);let x=[{to:"/home",icon:"ico-home-alt",appName:"feed"},{to:"/messages",icon:"ico-comment-o",appName:"chatplus",showWhen:["and",["truthy","setting.chatplus.server"]]},{to:"/messages",icon:"ico-comment-o",appName:"chat",showWhen:["and",["truthy","setting.broadcast.connections.pusher.key"],["falsy","setting.chatplus.server"]]},{to:"/notification",icon:"ico-bell-o",appName:"notification"},{icon:"ico-search-o",appName:"",style:"search"}],b=(0,o.j4Z)({name:"SiteBarMobileBlock",extendBlock:function({blockProps:e}){let t=(0,f.Z)(),{i18n:a,useSession:m,navigate:b,assetUrl:v,usePageParams:y,dialogBackend:k,getSetting:E}=(0,o.OgA)(),I=(0,o.THL)(),{user:S}=m(),[w,C]=g.useState(!1),[R,N]=g.useState(!1),z=g.useRef(),B=(0,p.Z)(),W=E(),T=(0,l.W$)(x,{setting:W}),M=v("dark"===B.palette.mode?"layout.image_logo_dark":"layout.image_logo"),{appName:A="feed",soft:Z=null}=y(),L=()=>{C(e=>!e)},$=()=>{C(e=>!e)},O=()=>{N(e=>!e)};g.useEffect(()=>{C(!1)},[I.pathname]);let H=()=>{k.present({component:"core.dialog.profileMenuMobile",props:{}})},P=()=>{b({pathname:"/login"})};return u()(S)?g.createElement(r.gO,null,g.createElement(r.sU,null,g.createElement("div",{className:t.blockHeader},g.createElement("div",{className:t.menuGuestWrapper},g.createElement(o.rUS,{to:"/",className:t.logo,title:a.formatMessage({id:"home"})},g.createElement("img",{src:M,height:"35",alt:"home"})),g.createElement(c.Z,{variant:"contained",color:"primary",size:"small",onClick:P,disableElevation:!0,type:"submit",className:t.button},a.formatMessage({id:"sign_in"})))))):g.createElement(r.gO,null,g.createElement(r.sU,null,g.createElement("div",{className:t.blockHeader},g.createElement("div",{className:t.menuWrapper},T.map((e,n)=>"search"!==e.style?g.createElement(o.rUS,{key:n,role:"button",to:e.to,className:(0,d.default)(t.menuButton,A===e.appName&&!Z&&t.active),underline:"none"},g.createElement(i.zb,{className:t.menuButtonIcon,icon:e.icon})):g.createElement(g.Fragment,null,g.createElement(o.rUS,{key:n,role:"button",to:e.to,className:(0,d.default)(t.menuButton,A===e.appName&&!Z&&t.active),underline:"none",onClick:O},g.createElement(i.zb,{className:t.menuButtonIcon,icon:e.icon})),R?g.createElement("div",{className:(0,d.default)(t.searchMobile)},g.createElement(h.Z,{openSearch:R,closeSearch:()=>N(!1)}),g.createElement(o.rUS,{onClick:O,className:(0,d.default)(t.cancelButton)},a.formatMessage({id:"Cancel"}))):null)),g.createElement(o.rUS,{role:"button",ref:z,className:(0,d.default)(t.menuButton,Z&&t.active),onClick:L,underline:"none"},g.createElement(i.zb,{className:t.menuButtonIcon,icon:"ico-navbar"})))),g.createElement(s.ZP,{id:w?"dropdownMenuMobile":void 0,open:Boolean(w),anchorEl:z.current,onClose:$,disableScrollLock:!0,anchorReference:"anchorPosition",anchorPosition:{top:60,left:0},style:{maxWidth:"100%"},marginThreshold:0,transitionDuration:0,className:t.popover,anchorOrigin:{vertical:"top",horizontal:"right"},transformOrigin:{vertical:"top",horizontal:"right"}},g.createElement("div",{className:t.dropdownMenuWrapper},g.createElement("div",{className:t.userBlock},g.createElement("div",{className:t.userAvatar},g.createElement(i.Yt,{user:S,size:48})),g.createElement("div",{className:t.userInner},g.createElement("div",{className:t.userName},S.full_name),g.createElement(o.rUS,{className:t.linkInfo,to:S.link},a.formatMessage({id:"view_profile"}))),g.createElement("div",{className:t.userAction},g.createElement(i.zb,{icon:"ico ico-angle-right",onClick:H}))),g.createElement("div",{className:t.menuApp},g.createElement(n.Z,{appName:"core",menuName:"primaryMenu"}))))))}});t.default=b}}]);