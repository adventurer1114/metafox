"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-core-blocks-TabContainer-Block"],{70214:function(e,t,l){l.d(t,{Z:function(){return S}});var r=l(63366),o=l(87462),a=l(67294),n=l(86010),i=l(94780),s=l(45355),c=l(36622),d=l(78884),u=l(81719),p=l(1588),b=l(34867);function f(e){return(0,b.Z)("MuiTab",e)}let m=(0,p.Z)("MuiTab",["root","labelIcon","textColorInherit","textColorPrimary","textColorSecondary","selected","disabled","fullWidth","wrapped","iconWrapper"]);var h=l(85893);let v=["className","disabled","disableFocusRipple","fullWidth","icon","iconPosition","indicator","label","onChange","onClick","onFocus","selected","selectionFollowsFocus","textColor","value","wrapped"],g=e=>{let{classes:t,textColor:l,fullWidth:r,wrapped:o,icon:a,label:n,selected:s,disabled:d}=e,u={root:["root",a&&n&&"labelIcon",`textColor${(0,c.Z)(l)}`,r&&"fullWidth",o&&"wrapped",s&&"selected",d&&"disabled"],iconWrapper:["iconWrapper"]};return(0,i.Z)(u,f,t)},x=(0,u.ZP)(s.Z,{name:"MuiTab",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:l}=e;return[t.root,l.label&&l.icon&&t.labelIcon,t[`textColor${(0,c.Z)(l.textColor)}`],l.fullWidth&&t.fullWidth,l.wrapped&&t.wrapped]}})(({theme:e,ownerState:t})=>(0,o.Z)({},e.typography.button,{maxWidth:360,minWidth:90,position:"relative",minHeight:48,flexShrink:0,padding:"12px 16px",overflow:"hidden",whiteSpace:"normal",textAlign:"center"},t.label&&{flexDirection:"top"===t.iconPosition||"bottom"===t.iconPosition?"column":"row"},{lineHeight:1.25},t.icon&&t.label&&{minHeight:72,paddingTop:9,paddingBottom:9,[`& > .${m.iconWrapper}`]:(0,o.Z)({},"top"===t.iconPosition&&{marginBottom:6},"bottom"===t.iconPosition&&{marginTop:6},"start"===t.iconPosition&&{marginRight:e.spacing(1)},"end"===t.iconPosition&&{marginLeft:e.spacing(1)})},"inherit"===t.textColor&&{color:"inherit",opacity:.6,[`&.${m.selected}`]:{opacity:1},[`&.${m.disabled}`]:{opacity:(e.vars||e).palette.action.disabledOpacity}},"primary"===t.textColor&&{color:(e.vars||e).palette.text.secondary,[`&.${m.selected}`]:{color:(e.vars||e).palette.primary.main},[`&.${m.disabled}`]:{color:(e.vars||e).palette.text.disabled}},"secondary"===t.textColor&&{color:(e.vars||e).palette.text.secondary,[`&.${m.selected}`]:{color:(e.vars||e).palette.secondary.main},[`&.${m.disabled}`]:{color:(e.vars||e).palette.text.disabled}},t.fullWidth&&{flexShrink:1,flexGrow:1,flexBasis:0,maxWidth:"none"},t.wrapped&&{fontSize:e.typography.pxToRem(12)})),w=a.forwardRef(function(e,t){let l=(0,d.Z)({props:e,name:"MuiTab"}),{className:i,disabled:s=!1,disableFocusRipple:c=!1,fullWidth:u,icon:p,iconPosition:b="top",indicator:f,label:m,onChange:w,onClick:S,onFocus:Z,selected:y,selectionFollowsFocus:C,textColor:E="inherit",value:B,wrapped:k=!1}=l,R=(0,r.Z)(l,v),T=(0,o.Z)({},l,{disabled:s,disableFocusRipple:c,selected:y,icon:!!p,iconPosition:b,label:!!m,fullWidth:u,textColor:E,wrapped:k}),M=g(T),W=p&&m&&a.isValidElement(p)?a.cloneElement(p,{className:(0,n.default)(M.iconWrapper,p.props.className)}):p,N=e=>{!y&&w&&w(e,B),S&&S(e)},$=e=>{C&&!y&&w&&w(e,B),Z&&Z(e)};return(0,h.jsxs)(x,(0,o.Z)({focusRipple:!c,className:(0,n.default)(M.root,i),ref:t,role:"tab","aria-selected":y,disabled:s,onClick:N,onFocus:$,ownerState:T,tabIndex:y?0:-1},R,{children:["top"===b||"start"===b?(0,h.jsxs)(a.Fragment,{children:[W,m]}):(0,h.jsxs)(a.Fragment,{children:[m,W]}),f]}))});var S=w},99164:function(e,t,l){l.d(t,{Z:function(){return U}});var r,o,a=l(63366),n=l(87462),i=l(67294);l(59864);var s=l(86010),c=l(94780),d=l(81719),u=l(78884),p=l(62097),b=l(75400),f=l(6528);function m(e){return(1+Math.sin(Math.PI*e-Math.PI/2))/2}var h=l(57577),v=l(85893);let g=["onChange"],x={width:99,height:99,position:"absolute",top:-9999,overflow:"scroll"};var w=l(60376),S=l(79476),Z=l(45355),y=l(1588),C=l(34867);function E(e){return(0,C.Z)("MuiTabScrollButton",e)}let B=(0,y.Z)("MuiTabScrollButton",["root","vertical","horizontal","disabled"]),k=["className","direction","orientation","disabled"],R=e=>{let{classes:t,orientation:l,disabled:r}=e;return(0,c.Z)({root:["root",l,r&&"disabled"]},E,t)},T=(0,d.ZP)(Z.Z,{name:"MuiTabScrollButton",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:l}=e;return[t.root,l.orientation&&t[l.orientation]]}})(({ownerState:e})=>(0,n.Z)({width:40,flexShrink:0,opacity:.8,[`&.${B.disabled}`]:{opacity:0}},"vertical"===e.orientation&&{width:"100%",height:40,"& svg":{transform:`rotate(${e.isRtl?-90:90}deg)`}})),M=i.forwardRef(function(e,t){let l=(0,u.Z)({props:e,name:"MuiTabScrollButton"}),{className:i,direction:c}=l,d=(0,a.Z)(l,k),b=(0,p.Z)(),f="rtl"===b.direction,m=(0,n.Z)({isRtl:f},l),h=R(m);return(0,v.jsx)(T,(0,n.Z)({component:"div",className:(0,s.default)(h.root,i),ref:t,role:null,ownerState:m,tabIndex:null},d,{children:"left"===c?r||(r=(0,v.jsx)(w.Z,{fontSize:"small"})):o||(o=(0,v.jsx)(S.Z,{fontSize:"small"}))}))});var W=l(26432);function N(e){return(0,C.Z)("MuiTabs",e)}let $=(0,y.Z)("MuiTabs",["root","vertical","flexContainer","flexContainerVertical","centered","scroller","fixed","scrollableX","scrollableY","hideScrollbar","scrollButtons","scrollButtonsHideMobile","indicator"]);var z=l(47505);let P=["aria-label","aria-labelledby","action","centered","children","className","component","allowScrollButtonsMobile","indicatorColor","onChange","orientation","ScrollButtonComponent","scrollButtons","selectionFollowsFocus","TabIndicatorProps","TabScrollButtonProps","textColor","value","variant","visibleScrollbar"],I=(e,t)=>{return e===t?e.firstChild:t&&t.nextElementSibling?t.nextElementSibling:e.firstChild},j=(e,t)=>{return e===t?e.lastChild:t&&t.previousElementSibling?t.previousElementSibling:e.lastChild},A=(e,t,l)=>{let r=!1,o=l(e,t);for(;o;){if(o===e.firstChild){if(r)return;r=!0}let a=o.disabled||"true"===o.getAttribute("aria-disabled");if(!o.hasAttribute("tabindex")||a)o=l(e,o);else{o.focus();return}}},L=e=>{let{vertical:t,fixed:l,hideScrollbar:r,scrollableX:o,scrollableY:a,centered:n,scrollButtonsHideMobile:i,classes:s}=e;return(0,c.Z)({root:["root",t&&"vertical"],scroller:["scroller",l&&"fixed",r&&"hideScrollbar",o&&"scrollableX",a&&"scrollableY"],flexContainer:["flexContainer",t&&"flexContainerVertical",n&&"centered"],indicator:["indicator"],scrollButtons:["scrollButtons",i&&"scrollButtonsHideMobile"],scrollableX:[o&&"scrollableX"],hideScrollbar:[r&&"hideScrollbar"]},N,s)},H=(0,d.ZP)("div",{name:"MuiTabs",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:l}=e;return[{[`& .${$.scrollButtons}`]:t.scrollButtons},{[`& .${$.scrollButtons}`]:l.scrollButtonsHideMobile&&t.scrollButtonsHideMobile},t.root,l.vertical&&t.vertical]}})(({ownerState:e,theme:t})=>(0,n.Z)({overflow:"hidden",minHeight:48,WebkitOverflowScrolling:"touch",display:"flex"},e.vertical&&{flexDirection:"column"},e.scrollButtonsHideMobile&&{[`& .${$.scrollButtons}`]:{[t.breakpoints.down("sm")]:{display:"none"}}})),D=(0,d.ZP)("div",{name:"MuiTabs",slot:"Scroller",overridesResolver:(e,t)=>{let{ownerState:l}=e;return[t.scroller,l.fixed&&t.fixed,l.hideScrollbar&&t.hideScrollbar,l.scrollableX&&t.scrollableX,l.scrollableY&&t.scrollableY]}})(({ownerState:e})=>(0,n.Z)({position:"relative",display:"inline-block",flex:"1 1 auto",whiteSpace:"nowrap"},e.fixed&&{overflowX:"hidden",width:"100%"},e.hideScrollbar&&{scrollbarWidth:"none","&::-webkit-scrollbar":{display:"none"}},e.scrollableX&&{overflowX:"auto",overflowY:"hidden"},e.scrollableY&&{overflowY:"auto",overflowX:"hidden"})),F=(0,d.ZP)("div",{name:"MuiTabs",slot:"FlexContainer",overridesResolver:(e,t)=>{let{ownerState:l}=e;return[t.flexContainer,l.vertical&&t.flexContainerVertical,l.centered&&t.centered]}})(({ownerState:e})=>(0,n.Z)({display:"flex"},e.vertical&&{flexDirection:"column"},e.centered&&{justifyContent:"center"})),X=(0,d.ZP)("span",{name:"MuiTabs",slot:"Indicator",overridesResolver:(e,t)=>t.indicator})(({ownerState:e,theme:t})=>(0,n.Z)({position:"absolute",height:2,bottom:0,width:"100%",transition:t.transitions.create()},"primary"===e.indicatorColor&&{backgroundColor:(t.vars||t).palette.primary.main},"secondary"===e.indicatorColor&&{backgroundColor:(t.vars||t).palette.secondary.main},e.vertical&&{height:"100%",width:2,right:0})),Y=(0,d.ZP)(function(e){let{onChange:t}=e,l=(0,a.Z)(e,g),r=i.useRef(),o=i.useRef(null),s=()=>{r.current=o.current.offsetHeight-o.current.clientHeight};return i.useEffect(()=>{let e=(0,b.Z)(()=>{let e=r.current;s(),e!==r.current&&t(r.current)}),l=(0,h.Z)(o.current);return l.addEventListener("resize",e),()=>{e.clear(),l.removeEventListener("resize",e)}},[t]),i.useEffect(()=>{s(),t(r.current)},[t]),(0,v.jsx)("div",(0,n.Z)({style:x,ref:o},l))},{name:"MuiTabs",slot:"ScrollbarSize"})({overflowX:"auto",overflowY:"hidden",scrollbarWidth:"none","&::-webkit-scrollbar":{display:"none"}}),O={},G=i.forwardRef(function(e,t){let l=(0,u.Z)({props:e,name:"MuiTabs"}),r=(0,p.Z)(),o="rtl"===r.direction,{"aria-label":c,"aria-labelledby":d,action:g,centered:x=!1,children:w,className:S,component:Z="div",allowScrollButtonsMobile:y=!1,indicatorColor:C="primary",onChange:E,orientation:B="horizontal",ScrollButtonComponent:k=M,scrollButtons:R="auto",selectionFollowsFocus:T,TabIndicatorProps:N={},TabScrollButtonProps:$={},textColor:G="primary",value:U,variant:V="standard",visibleScrollbar:_=!1}=l,q=(0,a.Z)(l,P),K="scrollable"===V,Q="vertical"===B,J=Q?"scrollTop":"scrollLeft",ee=Q?"top":"left",et=Q?"bottom":"right",el=Q?"clientHeight":"clientWidth",er=Q?"height":"width",eo=(0,n.Z)({},l,{component:Z,allowScrollButtonsMobile:y,indicatorColor:C,orientation:B,vertical:Q,scrollButtons:R,textColor:G,variant:V,visibleScrollbar:_,fixed:!K,hideScrollbar:K&&!_,scrollableX:K&&!Q,scrollableY:K&&Q,centered:x&&!K,scrollButtonsHideMobile:!y}),ea=L(eo),[en,ei]=i.useState(!1),[es,ec]=i.useState(O),[ed,eu]=i.useState({start:!1,end:!1}),[ep,eb]=i.useState({overflow:"hidden",scrollbarWidth:0}),ef=new Map,em=i.useRef(null),eh=i.useRef(null),ev=()=>{let e,t;let l=em.current;if(l){let o=l.getBoundingClientRect();e={clientWidth:l.clientWidth,scrollLeft:l.scrollLeft,scrollTop:l.scrollTop,scrollLeftNormalized:(0,f.T)(l,r.direction),scrollWidth:l.scrollWidth,top:o.top,bottom:o.bottom,left:o.left,right:o.right}}if(l&&!1!==U){let a=eh.current.children;if(a.length>0){let n=a[ef.get(U)];t=n?n.getBoundingClientRect():null}}return{tabsMeta:e,tabMeta:t}},eg=(0,W.Z)(()=>{let e;let{tabsMeta:t,tabMeta:l}=ev(),r=0;if(Q)e="top",l&&t&&(r=l.top-t.top+t.scrollTop);else if(e=o?"right":"left",l&&t){let a=o?t.scrollLeftNormalized+t.clientWidth-t.scrollWidth:t.scrollLeft;r=(o?-1:1)*(l[e]-t[e]+a)}let n={[e]:r,[er]:l?l[er]:0};if(isNaN(es[e])||isNaN(es[er]))ec(n);else{let i=Math.abs(es[e]-n[e]),s=Math.abs(es[er]-n[er]);(i>=1||s>=1)&&ec(n)}}),ex=(e,{animation:t=!0}={})=>{t?function(e,t,l,r={},o=()=>{}){let{ease:a=m,duration:n=300}=r,i=null,s=t[e],c=!1,d=()=>{c=!0},u=r=>{if(c){o(Error("Animation cancelled"));return}null===i&&(i=r);let d=Math.min(1,(r-i)/n);if(t[e]=a(d)*(l-s)+s,d>=1){requestAnimationFrame(()=>{o(null)});return}requestAnimationFrame(u)};return s===l?(o(Error("Element already at target position")),d):(requestAnimationFrame(u),d)}(J,em.current,e,{duration:r.transitions.duration.standard}):em.current[J]=e},ew=e=>{let t=em.current[J];Q?t+=e:(t+=e*(o?-1:1),t*=o&&"reverse"===(0,f.E)()?-1:1),ex(t)},eS=()=>{let e=em.current[el],t=0,l=Array.from(eh.current.children);for(let r=0;r<l.length;r+=1){let o=l[r];if(t+o[el]>e){0===r&&(t=e);break}t+=o[el]}return t},eZ=()=>{ew(-1*eS())},ey=()=>{ew(eS())},eC=i.useCallback(e=>{eb({overflow:null,scrollbarWidth:e})},[]),eE=(0,W.Z)(e=>{let{tabsMeta:t,tabMeta:l}=ev();if(l&&t){if(l[ee]<t[ee]){let r=t[J]+(l[ee]-t[ee]);ex(r,{animation:e})}else if(l[et]>t[et]){let o=t[J]+(l[et]-t[et]);ex(o,{animation:e})}}}),eB=(0,W.Z)(()=>{if(K&&!1!==R){let e,t;let{scrollTop:l,scrollHeight:a,clientHeight:n,scrollWidth:i,clientWidth:s}=em.current;if(Q)e=l>1,t=l<a-n-1;else{let c=(0,f.T)(em.current,r.direction);e=o?c<i-s-1:c>1,t=o?c>1:c<i-s-1}(e!==ed.start||t!==ed.end)&&eu({start:e,end:t})}});i.useEffect(()=>{let e;let t=(0,b.Z)(()=>{em.current&&(eg(),eB())}),l=(0,h.Z)(em.current);return l.addEventListener("resize",t),"undefined"!=typeof ResizeObserver&&(e=new ResizeObserver(t),Array.from(eh.current.children).forEach(t=>{e.observe(t)})),()=>{t.clear(),l.removeEventListener("resize",t),e&&e.disconnect()}},[eg,eB]);let ek=i.useMemo(()=>(0,b.Z)(()=>{eB()}),[eB]);i.useEffect(()=>{return()=>{ek.clear()}},[ek]),i.useEffect(()=>{ei(!0)},[]),i.useEffect(()=>{eg(),eB()}),i.useEffect(()=>{eE(O!==es)},[eE,es]),i.useImperativeHandle(g,()=>({updateIndicator:eg,updateScrollButtons:eB}),[eg,eB]);let eR=(0,v.jsx)(X,(0,n.Z)({},N,{className:(0,s.default)(ea.indicator,N.className),ownerState:eo,style:(0,n.Z)({},es,N.style)})),eT=0,eM=i.Children.map(w,e=>{if(!i.isValidElement(e))return null;let t=void 0===e.props.value?eT:e.props.value;ef.set(t,eT);let l=t===U;return eT+=1,i.cloneElement(e,(0,n.Z)({fullWidth:"fullWidth"===V,indicator:l&&!en&&eR,selected:l,selectionFollowsFocus:T,onChange:E,textColor:G,value:t},1!==eT||!1!==U||e.props.tabIndex?{}:{tabIndex:0}))}),eW=e=>{let t=eh.current,l=(0,z.Z)(t).activeElement,r=l.getAttribute("role");if("tab"!==r)return;let a="horizontal"===B?"ArrowLeft":"ArrowUp",n="horizontal"===B?"ArrowRight":"ArrowDown";switch("horizontal"===B&&o&&(a="ArrowRight",n="ArrowLeft"),e.key){case a:e.preventDefault(),A(t,l,j);break;case n:e.preventDefault(),A(t,l,I);break;case"Home":e.preventDefault(),A(t,null,I);break;case"End":e.preventDefault(),A(t,null,j)}},eN=(()=>{let e={};e.scrollbarSizeListener=K?(0,v.jsx)(Y,{onChange:eC,className:(0,s.default)(ea.scrollableX,ea.hideScrollbar)}):null;let t=ed.start||ed.end,l=K&&("auto"===R&&t||!0===R);return e.scrollButtonStart=l?(0,v.jsx)(k,(0,n.Z)({orientation:B,direction:o?"right":"left",onClick:eZ,disabled:!ed.start},$,{className:(0,s.default)(ea.scrollButtons,$.className)})):null,e.scrollButtonEnd=l?(0,v.jsx)(k,(0,n.Z)({orientation:B,direction:o?"left":"right",onClick:ey,disabled:!ed.end},$,{className:(0,s.default)(ea.scrollButtons,$.className)})):null,e})();return(0,v.jsxs)(H,(0,n.Z)({className:(0,s.default)(ea.root,S),ownerState:eo,ref:t,as:Z},q,{children:[eN.scrollButtonStart,eN.scrollbarSizeListener,(0,v.jsxs)(D,{className:ea.scroller,ownerState:eo,style:{overflow:ep.overflow,[Q?`margin${o?"Left":"Right"}`:"marginBottom"]:_?void 0:-ep.scrollbarWidth},ref:em,onScroll:ek,children:[(0,v.jsx)(F,{"aria-label":c,"aria-labelledby":d,"aria-orientation":"vertical"===B?"vertical":null,className:ea.flexContainer,ownerState:eo,onKeyDown:eW,ref:eh,role:"tablist",children:eM}),en&&eR]}),eN.scrollButtonEnd]}))});var U=G},60376:function(e,t,l){l(67294);var r=l(54235),o=l(85893);t.Z=(0,r.Z)((0,o.jsx)("path",{d:"M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"}),"KeyboardArrowLeft")},79476:function(e,t,l){l(67294);var r=l(54235),o=l(85893);t.Z=(0,r.Z)((0,o.jsx)("path",{d:"M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"}),"KeyboardArrowRight")},37487:function(e,t,l){l.r(t),l.d(t,{default:function(){return S}});var r=l(85597),o=l(21241),a=l(16473),n=l(27274),i=l(12551),s=l(70214),c=l(99164),d=l(86010),u=l(12902),p=l(1469),b=l.n(p),f=l(17673),m=l(67294),h=l(22410),v=l(73327);let g=(0,h.Z)(e=>(0,v.Z)({root:{},disableGutter:{padding:0},tab:{color:e.palette.text.secondary,fontWeight:"bold",fontSize:e.mixins.pxToRem(15),padding:"0 !important",width:"fit-content !important",minWidth:"fit-content !important",textTransform:"uppercase","& + $tab":{marginLeft:e.spacing(2.5)}},header:{width:"100%",display:"flex",justifyContent:"space-between",alignItems:"baseline"},subTabWrapper:{paddingBottom:e.spacing(1.875),display:"flex",justifyContent:"space-between",alignItems:"center",[e.breakpoints.down("sm")]:{flexDirection:"column-reverse",alignItems:"flex-start"}},popperMenu:{width:240,boxShadow:e.shadows[20],borderRadius:e.shape.borderRadius,overflow:"hidden",backgroundColor:e.palette.background.paper,zIndex:9999},menuItem:{width:240,minHeight:"40px",display:"block",padding:e.spacing(1,2),alignItems:"center",justifyContent:"center",textDecoration:"none",textTransform:"uppercase",fontSize:"15px",color:e.palette.text.secondary,"&:hover":{textDecoration:"none !important",backgroundColor:e.palette.action.hover,cursor:"pointer"}},secondMenu:{listStyle:"none none outside",margin:0,padding:0,display:"inline-flex"},tabItem:{height:"100%",display:"flex",alignItems:"center",justifyContent:"center",float:"left",textDecoration:"none",textTransform:"uppercase",fontSize:"15px",fontWeight:"bold",color:`${e.palette.text.secondary} !important`,position:"relative",whiteSpace:"nowrap","&:hover":{textDecoration:"none",color:`${e.palette.primary.main} !important`},[e.breakpoints.down("xs")]:{padding:`26px ${e.spacing(1)}px`,marginBottom:0},minWidth:60,cursor:"pointer",marginRight:0,flexGrow:1},tabSelect:{padding:`${e.spacing(1)} ${e.spacing(2)} `},tabItemActive:{color:`${e.palette.primary.main} !important`},hiddenTabs:{visibility:"hidden",position:"absolute"}}),{name:"TabMenuBlock"}),x=e=>{return b()(e)?e.filter(Boolean):[]},w=(0,r.Uh$)((0,r.YUM)(function({title:e,tabProps:t={tabs:[],tabsNoSearchBox:[],disableGutter:!0,activeTab:"",placeholderSearch:"search_dot"},elements:l,hasSearchBox:p,item:h,user:v,compose:w}){var S,Z;let{tabs:y,tabsNoSearchBox:C=[],disableGutter:E,activeTab:B}=t,{navigate:k,jsxBackend:R,useSession:T,usePageParams:M,i18n:W,useIsMobile:N,getAcl:$}=(0,r.OgA)(),z=N(),P=$(),I=m.useMemo(()=>{var e;return B||(null===(e=x(t.tabs)[0])||void 0===e?void 0:e.tab)||""},[B,t.tabs]),[j,A]=m.useState(I),L=(0,r.THL)(),H=(0,m.useRef)(null),[D,F]=m.useState(""),X=T(),{user:Y,loggedIn:O}=X,G=M(),U=g(),V=(0,r.z88)(`user.entities.user.${null==Y?void 0:Y.id}`),_=(0,d.default)(E&&U.disableGutter),[q,K]=m.useState(!1),Q=l.find(e=>e.props.name===j),J=((null==Y?void 0:Y.id)===(null==h?void 0:h.id)||(null==v?void 0:v.id)===(null==Y?void 0:Y.id))&&O,[ee,et]=m.useState(0),el=(0,m.useRef)(),er=(0,m.useRef)(),eo=(0,m.useRef)(),ea=(0,m.useRef)(),en=(0,m.useRef)(),ei=(0,m.useRef)(),[es,ec]=m.useState(null),ed=(e,t)=>{e.stopPropagation(),A(t);let l=f.stringify({stab:t});k({pathname:L.pathname,search:`?${l}`},{keepScroll:!0,state:L.state}),eg()},eu=(0,n.W$)(y,{isAuthUser:J,session:X,item:h,authUser:V,acl:P}),ep=eu.map(e=>e.tab),eb=y.find(e=>e.tab===j);m.useEffect(()=>{!ep.includes(j)&&eb&&(null==eb?void 0:eb.redirectWhenNoPermission)&&ep.includes(null==eb?void 0:eb.redirectWhenNoPermission)&&k({pathname:L.pathname,search:`?stab=${(null==eb?void 0:eb.redirectWhenNoPermission)||I}`})},[j,eu,y]);let ef=(0,m.useCallback)(e=>m.createElement("div",{className:U.secondMenu,ref:H},m.createElement("div",{className:U.tabItem},W.formatMessage({id:"more"}),"\xa0",m.createElement(a.zb,{icon:"ico-caret-down"})),m.createElement(i.Z,{open:q,anchorEl:es,onClose:eg},(ee>=eu.length?eu:eu.slice(ee)).map((e,t)=>m.createElement("div",{className:(0,d.default)(U.menuItem,e.tab===j&&U.tabItemActive),key:t.toString(),onClick:t=>ed(t,e.tab)},W.formatMessage({id:e.label}))))),[es,eu,q,ed,ee,j]),em=x(C),eh=p&&!em.includes(j),ev=m.useCallback(()=>{var e,t,l,r,o;let a=null===(e=ea.current)||void 0===e?void 0:e.children;if(!a||!a.length)return;let n=eo.current.getBoundingClientRect().width,i=null===(t=en.current)||void 0===t?void 0:t.getBoundingClientRect().width,s=eh&&!z?null===(l=er.current)||void 0===l?void 0:l.getBoundingClientRect().width:0,c=0,d=-1;for(;c+i+s<n;)c+=null===(o=null===(r=a[++d])||void 0===r?void 0:r.getBoundingClientRect())||void 0===o?void 0:o.width;let u=d<3?d:3;3===d&&4===a.length?et(2):et(d>=a.length?a.length:u)},[eh,z]);(0,m.useEffect)(()=>{ev()},[ev,j,eu]),(0,m.useEffect)(()=>{let{stab:e}=G,l=eu.some(t=>t.tab===e);if(!l&&e){A(B||x(t.tabs)[0].tab),k({search:`?stab=${B||x(t.tabs)[0].tab}`});return}b()(null==t?void 0:t.tabs)&&A(e||B||x(t.tabs)[0].tab)},[t.tabs,B,G]);let eg=()=>K(!1),ex=e=>{K(!0),ec(e.currentTarget)},ew=(e,t)=>{if("more"!==t){let l=f.stringify({stab:t});k({pathname:L.pathname,search:`?${l}`},{keepScroll:!0,state:L.state})}},eS=(null==Q?void 0:null===(S=Q.props)||void 0===S?void 0:null===(Z=S.elements)||void 0===Z?void 0:Z.length)&&(0,u.ZP)(Q,e=>{for(let t=0;t<e.props.elements.length;t++)e.props.elements[t].props.query=D});return w(e=>{e.hasSearchBox=!1}),m.createElement(o.gO,null,m.createElement(o.ti,{title:e}),m.createElement(o.sU,null,m.createElement("div",{className:U.subTabWrapper,ref:eo},m.createElement("div",{ref:ea,className:U.hiddenTabs},eu.map((e,t)=>m.createElement(s.Z,{ref:ei,"aria-label":e.tab,value:e.tab,label:W.formatMessage({id:e.label}),key:t.toString(),className:U.tab}))),m.createElement(c.Z,{value:j,onChange:ew,textColor:"primary",indicatorColor:"primary"},(ee>=eu.length?eu:eu.slice(0,ee)).map((e,t)=>m.createElement(s.Z,{"aria-label":e.tab,value:e.tab,label:W.formatMessage({id:e.label}),key:t.toString(),className:U.tab})),ee<eu.length&&m.createElement(s.Z,{onClick:ex,value:"more",label:ef(!0),ref:H})),m.createElement("div",{className:U.hiddenTabs,ref:en},m.createElement(s.Z,{value:"more",label:ef(!1),ref:en})),m.createElement("div",{className:U.hiddenTabs,ref:er},m.createElement(o.Rj,{placeholder:null==t?void 0:t.placeholderSearch,onQueryChange:F,sx:{width:{sm:"auto",xs:"100%"},margin:{sm:"initial",xs:"16px 0 0 0"},padding:"10px"}})),eh?m.createElement(o.Rj,{ref:el,placeholder:(null==eb?void 0:eb.placeholderSearch)||(null==t?void 0:t.placeholderSearch),onQueryChange:F,sx:{width:{sm:"auto",xs:"100%"},margin:{sm:"initial",xs:"16px 0 0 0"}}}):null),m.createElement("div",{className:_},eS?R.render(eS):null)))},()=>{}));var S=(0,r.j4Z)({extendBlock:w,name:"core.block.tabContainer",defaults:{title:"Tab Container",blockProps:{blockStyle:{borderRadius:"base"}}},overrides:{blockProps:{noFooter:!0}},custom:{tabs:{name:"tabProps.tabs",component:"Text",label:"Tabs",fullWidth:!0,margin:"normal",variant:"outlined"},hasSearchBox:{name:"hasSearchBox",component:"Checkbox",label:"Has Search Box?",margin:"normal"}}})}}]);