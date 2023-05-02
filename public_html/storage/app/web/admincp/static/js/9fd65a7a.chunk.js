"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-notification-blocks-NotificationListingMobile-Block"],{70214:function(e,t,l){l.d(t,{Z:function(){return y}});var r=l(63366),o=l(87462),i=l(67294),n=l(86010),a=l(94780),s=l(45355),c=l(36622),d=l(78884),u=l(81719),f=l(1588),p=l(34867);function b(e){return(0,p.Z)("MuiTab",e)}let m=(0,f.Z)("MuiTab",["root","labelIcon","textColorInherit","textColorPrimary","textColorSecondary","selected","disabled","fullWidth","wrapped","iconWrapper"]);var h=l(85893);let v=["className","disabled","disableFocusRipple","fullWidth","icon","iconPosition","indicator","label","onChange","onClick","onFocus","selected","selectionFollowsFocus","textColor","value","wrapped"],g=e=>{let{classes:t,textColor:l,fullWidth:r,wrapped:o,icon:i,label:n,selected:s,disabled:d}=e,u={root:["root",i&&n&&"labelIcon",`textColor${(0,c.Z)(l)}`,r&&"fullWidth",o&&"wrapped",s&&"selected",d&&"disabled"],iconWrapper:["iconWrapper"]};return(0,a.Z)(u,b,t)},x=(0,u.ZP)(s.Z,{name:"MuiTab",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:l}=e;return[t.root,l.label&&l.icon&&t.labelIcon,t[`textColor${(0,c.Z)(l.textColor)}`],l.fullWidth&&t.fullWidth,l.wrapped&&t.wrapped]}})(({theme:e,ownerState:t})=>(0,o.Z)({},e.typography.button,{maxWidth:360,minWidth:90,position:"relative",minHeight:48,flexShrink:0,padding:"12px 16px",overflow:"hidden",whiteSpace:"normal",textAlign:"center"},t.label&&{flexDirection:"top"===t.iconPosition||"bottom"===t.iconPosition?"column":"row"},{lineHeight:1.25},t.icon&&t.label&&{minHeight:72,paddingTop:9,paddingBottom:9,[`& > .${m.iconWrapper}`]:(0,o.Z)({},"top"===t.iconPosition&&{marginBottom:6},"bottom"===t.iconPosition&&{marginTop:6},"start"===t.iconPosition&&{marginRight:e.spacing(1)},"end"===t.iconPosition&&{marginLeft:e.spacing(1)})},"inherit"===t.textColor&&{color:"inherit",opacity:.6,[`&.${m.selected}`]:{opacity:1},[`&.${m.disabled}`]:{opacity:(e.vars||e).palette.action.disabledOpacity}},"primary"===t.textColor&&{color:(e.vars||e).palette.text.secondary,[`&.${m.selected}`]:{color:(e.vars||e).palette.primary.main},[`&.${m.disabled}`]:{color:(e.vars||e).palette.text.disabled}},"secondary"===t.textColor&&{color:(e.vars||e).palette.text.secondary,[`&.${m.selected}`]:{color:(e.vars||e).palette.secondary.main},[`&.${m.disabled}`]:{color:(e.vars||e).palette.text.disabled}},t.fullWidth&&{flexShrink:1,flexGrow:1,flexBasis:0,maxWidth:"none"},t.wrapped&&{fontSize:e.typography.pxToRem(12)})),w=i.forwardRef(function(e,t){let l=(0,d.Z)({props:e,name:"MuiTab"}),{className:a,disabled:s=!1,disableFocusRipple:c=!1,fullWidth:u,icon:f,iconPosition:p="top",indicator:b,label:m,onChange:w,onClick:y,onFocus:Z,selected:S,selectionFollowsFocus:C,textColor:M="inherit",value:B,wrapped:E=!1}=l,L=(0,r.Z)(l,v),k=(0,o.Z)({},l,{disabled:s,disableFocusRipple:c,selected:S,icon:!!f,iconPosition:p,label:!!m,fullWidth:u,textColor:M,wrapped:E}),P=g(k),R=f&&m&&i.isValidElement(f)?i.cloneElement(f,{className:(0,n.default)(P.iconWrapper,f.props.className)}):f,N=e=>{!S&&w&&w(e,B),y&&y(e)},T=e=>{C&&!S&&w&&w(e,B),Z&&Z(e)};return(0,h.jsxs)(x,(0,o.Z)({focusRipple:!c,className:(0,n.default)(P.root,a),ref:t,role:"tab","aria-selected":S,disabled:s,onClick:N,onFocus:T,ownerState:k,tabIndex:S?0:-1},L,{children:["top"===p||"start"===p?(0,h.jsxs)(i.Fragment,{children:[R,m]}):(0,h.jsxs)(i.Fragment,{children:[m,R]}),b]}))});var y=w},99164:function(e,t,l){l.d(t,{Z:function(){return O}});var r,o,i=l(63366),n=l(87462),a=l(67294);l(59864);var s=l(86010),c=l(94780),d=l(81719),u=l(78884),f=l(62097),p=l(75400),b=l(6528);function m(e){return(1+Math.sin(Math.PI*e-Math.PI/2))/2}var h=l(57577),v=l(85893);let g=["onChange"],x={width:99,height:99,position:"absolute",top:-9999,overflow:"scroll"};var w=l(60376),y=l(79476),Z=l(45355),S=l(1588),C=l(34867);function M(e){return(0,C.Z)("MuiTabScrollButton",e)}let B=(0,S.Z)("MuiTabScrollButton",["root","vertical","horizontal","disabled"]),E=["className","direction","orientation","disabled"],L=e=>{let{classes:t,orientation:l,disabled:r}=e;return(0,c.Z)({root:["root",l,r&&"disabled"]},M,t)},k=(0,d.ZP)(Z.Z,{name:"MuiTabScrollButton",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:l}=e;return[t.root,l.orientation&&t[l.orientation]]}})(({ownerState:e})=>(0,n.Z)({width:40,flexShrink:0,opacity:.8,[`&.${B.disabled}`]:{opacity:0}},"vertical"===e.orientation&&{width:"100%",height:40,"& svg":{transform:`rotate(${e.isRtl?-90:90}deg)`}})),P=a.forwardRef(function(e,t){let l=(0,u.Z)({props:e,name:"MuiTabScrollButton"}),{className:a,direction:c}=l,d=(0,i.Z)(l,E),p=(0,f.Z)(),b="rtl"===p.direction,m=(0,n.Z)({isRtl:b},l),h=L(m);return(0,v.jsx)(k,(0,n.Z)({component:"div",className:(0,s.default)(h.root,a),ref:t,role:null,ownerState:m,tabIndex:null},d,{children:"left"===c?r||(r=(0,v.jsx)(w.Z,{fontSize:"small"})):o||(o=(0,v.jsx)(y.Z,{fontSize:"small"}))}))});var R=l(26432);function N(e){return(0,C.Z)("MuiTabs",e)}let T=(0,S.Z)("MuiTabs",["root","vertical","flexContainer","flexContainerVertical","centered","scroller","fixed","scrollableX","scrollableY","hideScrollbar","scrollButtons","scrollButtonsHideMobile","indicator"]);var W=l(47505);let j=["aria-label","aria-labelledby","action","centered","children","className","component","allowScrollButtonsMobile","indicatorColor","onChange","orientation","ScrollButtonComponent","scrollButtons","selectionFollowsFocus","TabIndicatorProps","TabScrollButtonProps","textColor","value","variant","visibleScrollbar"],z=(e,t)=>{return e===t?e.firstChild:t&&t.nextElementSibling?t.nextElementSibling:e.firstChild},A=(e,t)=>{return e===t?e.lastChild:t&&t.previousElementSibling?t.previousElementSibling:e.lastChild},I=(e,t,l)=>{let r=!1,o=l(e,t);for(;o;){if(o===e.firstChild){if(r)return;r=!0}let i=o.disabled||"true"===o.getAttribute("aria-disabled");if(!o.hasAttribute("tabindex")||i)o=l(e,o);else{o.focus();return}}},H=e=>{let{vertical:t,fixed:l,hideScrollbar:r,scrollableX:o,scrollableY:i,centered:n,scrollButtonsHideMobile:a,classes:s}=e;return(0,c.Z)({root:["root",t&&"vertical"],scroller:["scroller",l&&"fixed",r&&"hideScrollbar",o&&"scrollableX",i&&"scrollableY"],flexContainer:["flexContainer",t&&"flexContainerVertical",n&&"centered"],indicator:["indicator"],scrollButtons:["scrollButtons",a&&"scrollButtonsHideMobile"],scrollableX:[o&&"scrollableX"],hideScrollbar:[r&&"hideScrollbar"]},N,s)},_=(0,d.ZP)("div",{name:"MuiTabs",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:l}=e;return[{[`& .${T.scrollButtons}`]:t.scrollButtons},{[`& .${T.scrollButtons}`]:l.scrollButtonsHideMobile&&t.scrollButtonsHideMobile},t.root,l.vertical&&t.vertical]}})(({ownerState:e,theme:t})=>(0,n.Z)({overflow:"hidden",minHeight:48,WebkitOverflowScrolling:"touch",display:"flex"},e.vertical&&{flexDirection:"column"},e.scrollButtonsHideMobile&&{[`& .${T.scrollButtons}`]:{[t.breakpoints.down("sm")]:{display:"none"}}})),F=(0,d.ZP)("div",{name:"MuiTabs",slot:"Scroller",overridesResolver:(e,t)=>{let{ownerState:l}=e;return[t.scroller,l.fixed&&t.fixed,l.hideScrollbar&&t.hideScrollbar,l.scrollableX&&t.scrollableX,l.scrollableY&&t.scrollableY]}})(({ownerState:e})=>(0,n.Z)({position:"relative",display:"inline-block",flex:"1 1 auto",whiteSpace:"nowrap"},e.fixed&&{overflowX:"hidden",width:"100%"},e.hideScrollbar&&{scrollbarWidth:"none","&::-webkit-scrollbar":{display:"none"}},e.scrollableX&&{overflowX:"auto",overflowY:"hidden"},e.scrollableY&&{overflowY:"auto",overflowX:"hidden"})),$=(0,d.ZP)("div",{name:"MuiTabs",slot:"FlexContainer",overridesResolver:(e,t)=>{let{ownerState:l}=e;return[t.flexContainer,l.vertical&&t.flexContainerVertical,l.centered&&t.centered]}})(({ownerState:e})=>(0,n.Z)({display:"flex"},e.vertical&&{flexDirection:"column"},e.centered&&{justifyContent:"center"})),X=(0,d.ZP)("span",{name:"MuiTabs",slot:"Indicator",overridesResolver:(e,t)=>t.indicator})(({ownerState:e,theme:t})=>(0,n.Z)({position:"absolute",height:2,bottom:0,width:"100%",transition:t.transitions.create()},"primary"===e.indicatorColor&&{backgroundColor:(t.vars||t).palette.primary.main},"secondary"===e.indicatorColor&&{backgroundColor:(t.vars||t).palette.secondary.main},e.vertical&&{height:"100%",width:2,right:0})),V=(0,d.ZP)(function(e){let{onChange:t}=e,l=(0,i.Z)(e,g),r=a.useRef(),o=a.useRef(null),s=()=>{r.current=o.current.offsetHeight-o.current.clientHeight};return a.useEffect(()=>{let e=(0,p.Z)(()=>{let e=r.current;s(),e!==r.current&&t(r.current)}),l=(0,h.Z)(o.current);return l.addEventListener("resize",e),()=>{e.clear(),l.removeEventListener("resize",e)}},[t]),a.useEffect(()=>{s(),t(r.current)},[t]),(0,v.jsx)("div",(0,n.Z)({style:x,ref:o},l))},{name:"MuiTabs",slot:"ScrollbarSize"})({overflowX:"auto",overflowY:"hidden",scrollbarWidth:"none","&::-webkit-scrollbar":{display:"none"}}),q={},D=a.forwardRef(function(e,t){let l=(0,u.Z)({props:e,name:"MuiTabs"}),r=(0,f.Z)(),o="rtl"===r.direction,{"aria-label":c,"aria-labelledby":d,action:g,centered:x=!1,children:w,className:y,component:Z="div",allowScrollButtonsMobile:S=!1,indicatorColor:C="primary",onChange:M,orientation:B="horizontal",ScrollButtonComponent:E=P,scrollButtons:L="auto",selectionFollowsFocus:k,TabIndicatorProps:N={},TabScrollButtonProps:T={},textColor:D="primary",value:O,variant:Y="standard",visibleScrollbar:U=!1}=l,K=(0,i.Z)(l,j),G="scrollable"===Y,J="vertical"===B,Q=J?"scrollTop":"scrollLeft",ee=J?"top":"left",et=J?"bottom":"right",el=J?"clientHeight":"clientWidth",er=J?"height":"width",eo=(0,n.Z)({},l,{component:Z,allowScrollButtonsMobile:S,indicatorColor:C,orientation:B,vertical:J,scrollButtons:L,textColor:D,variant:Y,visibleScrollbar:U,fixed:!G,hideScrollbar:G&&!U,scrollableX:G&&!J,scrollableY:G&&J,centered:x&&!G,scrollButtonsHideMobile:!S}),ei=H(eo),[en,ea]=a.useState(!1),[es,ec]=a.useState(q),[ed,eu]=a.useState({start:!1,end:!1}),[ef,ep]=a.useState({overflow:"hidden",scrollbarWidth:0}),eb=new Map,em=a.useRef(null),eh=a.useRef(null),ev=()=>{let e,t;let l=em.current;if(l){let o=l.getBoundingClientRect();e={clientWidth:l.clientWidth,scrollLeft:l.scrollLeft,scrollTop:l.scrollTop,scrollLeftNormalized:(0,b.T)(l,r.direction),scrollWidth:l.scrollWidth,top:o.top,bottom:o.bottom,left:o.left,right:o.right}}if(l&&!1!==O){let i=eh.current.children;if(i.length>0){let n=i[eb.get(O)];t=n?n.getBoundingClientRect():null}}return{tabsMeta:e,tabMeta:t}},eg=(0,R.Z)(()=>{let e;let{tabsMeta:t,tabMeta:l}=ev(),r=0;if(J)e="top",l&&t&&(r=l.top-t.top+t.scrollTop);else if(e=o?"right":"left",l&&t){let i=o?t.scrollLeftNormalized+t.clientWidth-t.scrollWidth:t.scrollLeft;r=(o?-1:1)*(l[e]-t[e]+i)}let n={[e]:r,[er]:l?l[er]:0};if(isNaN(es[e])||isNaN(es[er]))ec(n);else{let a=Math.abs(es[e]-n[e]),s=Math.abs(es[er]-n[er]);(a>=1||s>=1)&&ec(n)}}),ex=(e,{animation:t=!0}={})=>{t?function(e,t,l,r={},o=()=>{}){let{ease:i=m,duration:n=300}=r,a=null,s=t[e],c=!1,d=()=>{c=!0},u=r=>{if(c){o(Error("Animation cancelled"));return}null===a&&(a=r);let d=Math.min(1,(r-a)/n);if(t[e]=i(d)*(l-s)+s,d>=1){requestAnimationFrame(()=>{o(null)});return}requestAnimationFrame(u)};return s===l?(o(Error("Element already at target position")),d):(requestAnimationFrame(u),d)}(Q,em.current,e,{duration:r.transitions.duration.standard}):em.current[Q]=e},ew=e=>{let t=em.current[Q];J?t+=e:(t+=e*(o?-1:1),t*=o&&"reverse"===(0,b.E)()?-1:1),ex(t)},ey=()=>{let e=em.current[el],t=0,l=Array.from(eh.current.children);for(let r=0;r<l.length;r+=1){let o=l[r];if(t+o[el]>e){0===r&&(t=e);break}t+=o[el]}return t},eZ=()=>{ew(-1*ey())},eS=()=>{ew(ey())},eC=a.useCallback(e=>{ep({overflow:null,scrollbarWidth:e})},[]),eM=(0,R.Z)(e=>{let{tabsMeta:t,tabMeta:l}=ev();if(l&&t){if(l[ee]<t[ee]){let r=t[Q]+(l[ee]-t[ee]);ex(r,{animation:e})}else if(l[et]>t[et]){let o=t[Q]+(l[et]-t[et]);ex(o,{animation:e})}}}),eB=(0,R.Z)(()=>{if(G&&!1!==L){let e,t;let{scrollTop:l,scrollHeight:i,clientHeight:n,scrollWidth:a,clientWidth:s}=em.current;if(J)e=l>1,t=l<i-n-1;else{let c=(0,b.T)(em.current,r.direction);e=o?c<a-s-1:c>1,t=o?c>1:c<a-s-1}(e!==ed.start||t!==ed.end)&&eu({start:e,end:t})}});a.useEffect(()=>{let e;let t=(0,p.Z)(()=>{em.current&&(eg(),eB())}),l=(0,h.Z)(em.current);return l.addEventListener("resize",t),"undefined"!=typeof ResizeObserver&&(e=new ResizeObserver(t),Array.from(eh.current.children).forEach(t=>{e.observe(t)})),()=>{t.clear(),l.removeEventListener("resize",t),e&&e.disconnect()}},[eg,eB]);let eE=a.useMemo(()=>(0,p.Z)(()=>{eB()}),[eB]);a.useEffect(()=>{return()=>{eE.clear()}},[eE]),a.useEffect(()=>{ea(!0)},[]),a.useEffect(()=>{eg(),eB()}),a.useEffect(()=>{eM(q!==es)},[eM,es]),a.useImperativeHandle(g,()=>({updateIndicator:eg,updateScrollButtons:eB}),[eg,eB]);let eL=(0,v.jsx)(X,(0,n.Z)({},N,{className:(0,s.default)(ei.indicator,N.className),ownerState:eo,style:(0,n.Z)({},es,N.style)})),ek=0,eP=a.Children.map(w,e=>{if(!a.isValidElement(e))return null;let t=void 0===e.props.value?ek:e.props.value;eb.set(t,ek);let l=t===O;return ek+=1,a.cloneElement(e,(0,n.Z)({fullWidth:"fullWidth"===Y,indicator:l&&!en&&eL,selected:l,selectionFollowsFocus:k,onChange:M,textColor:D,value:t},1!==ek||!1!==O||e.props.tabIndex?{}:{tabIndex:0}))}),eR=e=>{let t=eh.current,l=(0,W.Z)(t).activeElement,r=l.getAttribute("role");if("tab"!==r)return;let i="horizontal"===B?"ArrowLeft":"ArrowUp",n="horizontal"===B?"ArrowRight":"ArrowDown";switch("horizontal"===B&&o&&(i="ArrowRight",n="ArrowLeft"),e.key){case i:e.preventDefault(),I(t,l,A);break;case n:e.preventDefault(),I(t,l,z);break;case"Home":e.preventDefault(),I(t,null,z);break;case"End":e.preventDefault(),I(t,null,A)}},eN=(()=>{let e={};e.scrollbarSizeListener=G?(0,v.jsx)(V,{onChange:eC,className:(0,s.default)(ei.scrollableX,ei.hideScrollbar)}):null;let t=ed.start||ed.end,l=G&&("auto"===L&&t||!0===L);return e.scrollButtonStart=l?(0,v.jsx)(E,(0,n.Z)({orientation:B,direction:o?"right":"left",onClick:eZ,disabled:!ed.start},T,{className:(0,s.default)(ei.scrollButtons,T.className)})):null,e.scrollButtonEnd=l?(0,v.jsx)(E,(0,n.Z)({orientation:B,direction:o?"left":"right",onClick:eS,disabled:!ed.end},T,{className:(0,s.default)(ei.scrollButtons,T.className)})):null,e})();return(0,v.jsxs)(_,(0,n.Z)({className:(0,s.default)(ei.root,y),ownerState:eo,ref:t,as:Z},K,{children:[eN.scrollButtonStart,eN.scrollbarSizeListener,(0,v.jsxs)(F,{className:ei.scroller,ownerState:eo,style:{overflow:ef.overflow,[J?`margin${o?"Left":"Right"}`:"marginBottom"]:U?void 0:-ef.scrollbarWidth},ref:em,onScroll:eE,children:[(0,v.jsx)($,{"aria-label":c,"aria-labelledby":d,"aria-orientation":"vertical"===B?"vertical":null,className:ei.flexContainer,ownerState:eo,onKeyDown:eR,ref:eh,role:"tablist",children:eP}),en&&eL]}),eN.scrollButtonEnd]}))});var O=D},60376:function(e,t,l){l(67294);var r=l(54235),o=l(85893);t.Z=(0,r.Z)((0,o.jsx)("path",{d:"M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"}),"KeyboardArrowLeft")},79476:function(e,t,l){l(67294);var r=l(54235),o=l(85893);t.Z=(0,r.Z)((0,o.jsx)("path",{d:"M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"}),"KeyboardArrowRight")},62038:function(e,t,l){l.r(t),l.d(t,{default:function(){return y}});var r=l(85597),o=l(96454),i=l(21241),n=l(81719),a=l(70214),s=l(99164),c=l(67294),d=l(86706),u=l(22410),f=l(73327),p=(0,u.Z)(e=>(0,f.Z)({root:{},customTabs:{"& .MuiTabs-flexContainer":{margin:e.spacing(1,1.5,1,1.5)},"& .MuiTab-root":{fontSize:e.spacing(2),minHeight:"auto",borderRadius:4,marginRight:"0.25em",padding:e.spacing(.5),fontWeight:e.typography.fontWeightSemiBold}}}),{name:"Notifications"});function b(){return(b=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var l=arguments[t];for(var r in l)Object.prototype.hasOwnProperty.call(l,r)&&(e[r]=l[r])}return e}).apply(this,arguments)}let m="notifications",h=()=>{return{notifications:{id:"notifications",value:m,invisible:!0,itemView:"notification.itemView.mainCard",gridLayout:"Notification - Small Lists",itemLayout:"Notification - Small Lists",emptyPage:"core.block.no_content_with_icon",emptyPageProps:{noHeader:!0,contentStyle:{bgColor:"0"},noBlock:1,title:"No Notifications",image:"ico-bell2-off-o"},dataSource:{apiUrl:"/notification"},pagingId:"/notifications/notifications"},friend_requests:{id:"friend_requests",value:"friend_requests",itemView:"friend_request.itemView.smallCard",gridLayout:"Friend - Small List",itemLayout:"Friend - Small List",invisible:!0,dataSource:{apiUrl:"/friend/request",apiParams:{view:"pending",limit:10}},pagingId:"/friend/request?view=pending",emptyPage:"core.block.no_content",emptyPageProps:{title:"no_friend_request"},total:"new_friend_request"}}},v=(0,n.ZP)("div")(({theme:e})=>({margin:e.spacing(2),marginLeft:0,display:"flex",justifyContent:"flex-end"})),g=(0,n.ZP)("span")(({theme:e})=>({color:e.palette.error.main}));function x({title:e,gridVariant:t="listView",gridLayout:l,itemLayout:n,itemView:u,displayLimit:f,...x}){let{ListView:w,jsxBackend:y,i18n:Z}=(0,r.OgA)(),S=(0,d.v9)(o.A3),C=h(),[M,B]=(0,c.useState)(m),E=(0,c.useRef)(),L=p(),k=y.get("notification.markAllAsRead"),P=y.get("notification.editNotificationSetting"),R=(e,t)=>{B(t)};return c.createElement(i.gO,b({},x),c.createElement(s.Z,{centered:!0,value:M,onChange:R,className:L.customTabs,variant:"fullWidth","aria-label":"full width tabs example"},Object.keys(C).filter(e=>C[e].invisible).map(e=>c.createElement(a.Z,{key:C[e].id,disableRipple:!0,label:c.createElement("span",null,Z.formatMessage({id:C[e].id})," ",S[C[e].total]?c.createElement(g,null,"(",S[C[e].total],")"):null),value:C[e].value,"aria-label":C[e].value}))),C[M].value===m?c.createElement(v,null,c.createElement(k,null),c.createElement(P,null)):null,c.createElement(i.sU,null,c.createElement("div",{ref:E},c.createElement(i.ID,{scrollRef:E},c.createElement(w,{dataSource:C[M].dataSource,canLoadMore:!0,clearDataOnUnMount:!0,gridContainerProps:{spacing:0},gridLayout:C[M].gridLayout,itemLayout:C[M].itemLayout,emptyPage:C[M].emptyPage,emptyPageProps:C[M].emptyPageProps,itemView:C[M].itemView,pagingId:C[M].pagingId})))))}x.displayName="NotificationListingBlockMobile";let w=(0,r.j4Z)({name:"NotificationListingBlock",extendBlock:x,defaults:{canLoadMore:!0,blockLayout:"Large Main Lists"}});var y=w}}]);