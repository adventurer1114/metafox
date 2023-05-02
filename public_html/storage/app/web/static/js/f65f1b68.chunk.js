"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-layout-LayoutSlot-SlotWithEditingLayout"],{30986:function(t,e,o){o.d(e,{Z:function(){return $}});var r=o(63366),i=o(87462),n=o(67294),a=o(86010),l=o(94780),d=o(41796),u=o(36622),s=o(81719),c=o(78884),p=o(1588),g=o(34867);function m(t){return(0,g.Z)("MuiButtonGroup",t)}let h=(0,p.Z)("MuiButtonGroup",["root","contained","outlined","text","disableElevation","disabled","fullWidth","vertical","grouped","groupedHorizontal","groupedVertical","groupedText","groupedTextHorizontal","groupedTextVertical","groupedTextPrimary","groupedTextSecondary","groupedOutlined","groupedOutlinedHorizontal","groupedOutlinedVertical","groupedOutlinedPrimary","groupedOutlinedSecondary","groupedContained","groupedContainedHorizontal","groupedContainedVertical","groupedContainedPrimary","groupedContainedSecondary"]);var v=o(58907),f=o(85893);let b=["children","className","color","component","disabled","disableElevation","disableFocusRipple","disableRipple","fullWidth","orientation","size","variant"],y=(t,e)=>{let{ownerState:o}=t;return[{[`& .${h.grouped}`]:e.grouped},{[`& .${h.grouped}`]:e[`grouped${(0,u.Z)(o.orientation)}`]},{[`& .${h.grouped}`]:e[`grouped${(0,u.Z)(o.variant)}`]},{[`& .${h.grouped}`]:e[`grouped${(0,u.Z)(o.variant)}${(0,u.Z)(o.orientation)}`]},{[`& .${h.grouped}`]:e[`grouped${(0,u.Z)(o.variant)}${(0,u.Z)(o.color)}`]},e.root,e[o.variant],!0===o.disableElevation&&e.disableElevation,o.fullWidth&&e.fullWidth,"vertical"===o.orientation&&e.vertical]},x=t=>{let{classes:e,color:o,disabled:r,disableElevation:i,fullWidth:n,orientation:a,variant:d}=t,s={root:["root",d,"vertical"===a&&"vertical",n&&"fullWidth",i&&"disableElevation"],grouped:["grouped",`grouped${(0,u.Z)(a)}`,`grouped${(0,u.Z)(d)}`,`grouped${(0,u.Z)(d)}${(0,u.Z)(a)}`,`grouped${(0,u.Z)(d)}${(0,u.Z)(o)}`,r&&"disabled"]};return(0,l.Z)(s,m,e)},Z=(0,s.ZP)("div",{name:"MuiButtonGroup",slot:"Root",overridesResolver:y})(({theme:t,ownerState:e})=>(0,i.Z)({display:"inline-flex",borderRadius:(t.vars||t).shape.borderRadius},"contained"===e.variant&&{boxShadow:(t.vars||t).shadows[2]},e.disableElevation&&{boxShadow:"none"},e.fullWidth&&{width:"100%"},"vertical"===e.orientation&&{flexDirection:"column"},{[`& .${h.grouped}`]:(0,i.Z)({minWidth:40,"&:not(:first-of-type)":(0,i.Z)({},"horizontal"===e.orientation&&{borderTopLeftRadius:0,borderBottomLeftRadius:0},"vertical"===e.orientation&&{borderTopRightRadius:0,borderTopLeftRadius:0},"outlined"===e.variant&&"horizontal"===e.orientation&&{marginLeft:-1},"outlined"===e.variant&&"vertical"===e.orientation&&{marginTop:-1}),"&:not(:last-of-type)":(0,i.Z)({},"horizontal"===e.orientation&&{borderTopRightRadius:0,borderBottomRightRadius:0},"vertical"===e.orientation&&{borderBottomRightRadius:0,borderBottomLeftRadius:0},"text"===e.variant&&"horizontal"===e.orientation&&{borderRight:t.vars?`1px solid rgba(${t.vars.palette.common.onBackgroundChannel} / 0.23)`:`1px solid ${"light"===t.palette.mode?"rgba(0, 0, 0, 0.23)":"rgba(255, 255, 255, 0.23)"}`},"text"===e.variant&&"vertical"===e.orientation&&{borderBottom:t.vars?`1px solid rgba(${t.vars.palette.common.onBackgroundChannel} / 0.23)`:`1px solid ${"light"===t.palette.mode?"rgba(0, 0, 0, 0.23)":"rgba(255, 255, 255, 0.23)"}`},"text"===e.variant&&"inherit"!==e.color&&{borderColor:t.vars?`rgba(${t.vars.palette[e.color].mainChannel} / 0.5)`:(0,d.Fq)(t.palette[e.color].main,.5)},"outlined"===e.variant&&"horizontal"===e.orientation&&{borderRightColor:"transparent"},"outlined"===e.variant&&"vertical"===e.orientation&&{borderBottomColor:"transparent"},"contained"===e.variant&&"horizontal"===e.orientation&&{borderRight:`1px solid ${(t.vars||t).palette.grey[400]}`,[`&.${h.disabled}`]:{borderRight:`1px solid ${(t.vars||t).palette.action.disabled}`}},"contained"===e.variant&&"vertical"===e.orientation&&{borderBottom:`1px solid ${(t.vars||t).palette.grey[400]}`,[`&.${h.disabled}`]:{borderBottom:`1px solid ${(t.vars||t).palette.action.disabled}`}},"contained"===e.variant&&"inherit"!==e.color&&{borderColor:(t.vars||t).palette[e.color].dark},{"&:hover":(0,i.Z)({},"outlined"===e.variant&&"horizontal"===e.orientation&&{borderRightColor:"currentColor"},"outlined"===e.variant&&"vertical"===e.orientation&&{borderBottomColor:"currentColor"})}),"&:hover":(0,i.Z)({},"contained"===e.variant&&{boxShadow:"none"})},"contained"===e.variant&&{boxShadow:"none"})})),S=n.forwardRef(function(t,e){let o=(0,c.Z)({props:t,name:"MuiButtonGroup"}),{children:l,className:d,color:u="primary",component:s="div",disabled:p=!1,disableElevation:g=!1,disableFocusRipple:m=!1,disableRipple:h=!1,fullWidth:y=!1,orientation:S="horizontal",size:$="medium",variant:C="outlined"}=o,R=(0,r.Z)(o,b),W=(0,i.Z)({},o,{color:u,component:s,disabled:p,disableElevation:g,disableFocusRipple:m,disableRipple:h,fullWidth:y,orientation:S,size:$,variant:C}),k=x(W),E=n.useMemo(()=>({className:k.grouped,color:u,disabled:p,disableElevation:g,disableFocusRipple:m,disableRipple:h,fullWidth:y,size:$,variant:C}),[u,p,g,m,h,y,$,C,k.grouped]);return(0,f.jsx)(Z,(0,i.Z)({as:s,role:"group",className:(0,a.default)(k.root,d),ref:e,ownerState:W},R,{children:(0,f.jsx)(v.Z.Provider,{value:E,children:l})}))});var $=S},88256:function(t,e,o){o.d(e,{Z:function(){return a}});var r=o(30120),i=o(91647),n=o(67294);function a({slotName:t,children:e}){return n.createElement(r.Z,{sx:{display:"flex",flexDirection:"row",alignItems:"center",justifyContent:"space-between"}},n.createElement(i.Z,{component:"span",variant:"body1"},t),e)}},1058:function(t,e,o){o.r(e),o.d(e,{default:function(){return x}});var r=o(85597),i=o(76224),n=o(21822),a=o(30986),l=o(38790),d=o(81719),u=o(68929),s=o.n(u),c=o(67294),p=o(60994),g=o(66320),m=o(14293),h=o(88256),v=o(49341);function f(){return(f=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var o=arguments[e];for(var r in o)Object.prototype.hasOwnProperty.call(o,r)&&(t[r]=o[r])}return t}).apply(this,arguments)}let b=(0,d.ZP)("div")(({theme:t})=>({textAlign:"right",padding:t.spacing(1),"&:hover":{opacity:1}})),y=t=>{let{dispatch:e,i18n:o}=(0,r.OgA)(),d=o=>e({type:o,payload:t});return c.createElement(b,null,c.createElement(a.Z,{size:"small",color:"primary"},c.createElement(n.Z,{onClick:()=>d("@layout/editSlot")},c.createElement(l.Z,{enterDelay:0,title:o.formatMessage({id:"edit_layout_slot"})},c.createElement(i.zb,{icon:"ico-pencilline-o"}))),c.createElement(n.Z,{onClick:()=>d("@layout/deleteSlot")},c.createElement(l.Z,{title:o.formatMessage({id:"delete_layout_slot"})},c.createElement(i.zb,{icon:"ico-trash-o"})))))};function x({elementPath:t,templateName:e,slotName:o,flexWeight:i,xs:n,rootStyle:a,stageStyle:l,contentStyle:d}){let u=c.useRef(),{pageSize:b,pageName:x,blocks:Z}=(0,m.Z)(),{jsxBackend:S,layoutBackend:$}=(0,r.OgA)(),C=Z.filter(t=>t.slotName===o),R=C.length,[,W]=(0,p.L)({accept:g.C,collect:t=>({isOver:t.isOver(),canDrop:t.canDrop()}),canDrop:()=>!0,drop:(t,r)=>{if(r.didDrop())return;let i=function(t,e,o){if(!e)return 0;let r=o?t.height/o:-1;return Math.floor((e.y-t.y)/r)}(u.current.getBoundingClientRect(),r.getClientOffset(),R),n={blockId:t.blockId,order:i,slotName:o,templateName:e,pageSize:b,pageName:x};return $.updateBlockPosition(n),n}}),k="0"<i||!n;return c.createElement(v.g7,f({item:!0,useFlex:k,flexWeight:i,xs:k?void 0:n,ref:W,"data-testid":s()(`LayoutSlot_${o}`)},a),c.createElement(v.IL,f({controller:!0},l),c.createElement(h.Z,{slotName:o},c.createElement(y,{elementPath:t,pageName:x,templateName:e,slotName:o,pageSize:b})),c.createElement(v.x_,f({ref:u},d),S.render(C))))}},49341:function(t,e,o){o.d(e,{$8:function(){return c},IL:function(){return l},M5:function(){return s},UC:function(){return u},g7:function(){return a},x_:function(){return d}});var r=o(30120),i=o(30030),n=o(81719);let a=(0,n.ZP)(i.ZP,{name:"StyledSlot",slot:"root",shouldForwardProp:t=>"maxWidth"!==t&&"minWidth"!==t&&"minHeight"!==t&&"flexWeight"!==t&&"useFlex"!==t})(({theme:t,maxWidth:e,minWidth:o,minHeight:r,flexWeight:i,useFlex:n})=>({display:"block",flexBasis:"100%",position:"relative",..."screen"===r&&{minHeight:"100vh"},[t.breakpoints.down("xl")]:{...e&&{maxWidth:`${t.layoutSlot.points[e]}px !important`}},...o&&{minWidth:`${t.layoutSlot.points[o]}px !important`},...n&&{flex:null!=i?i:1,minWidth:0}})),l=(0,n.ZP)(r.Z,{name:"StyledSlot",slot:"stage",shouldForwardProp:t=>"maxWidth"!==t&&"minWidth"!==t&&"minHeight"!==t&&"controller"!==t&&"sticky"!==t&&"liveEdit"!==t})(({theme:t,minHeight:e,fixed:o,maxWidth:r,minWidth:i,liveEdit:n,controller:a,sticky:l})=>({display:"block",flexBasis:"100%",...o&&{position:"fixed"},..."screen"===e&&{minHeight:"100vh"},...r&&{maxWidth:t.layoutSlot.points[r]},...i&&{maxWidth:t.layoutSlot.points[i]},...n&&{position:"relative",minHeight:t.spacing(6)},...a&&{position:"relative",marginBottom:t.spacing(1),padding:t.spacing(1),minHeight:t.spacing(8),borderColor:t.palette.text.primary,borderStyle:"dotted",borderWidth:1},..."sideStickyStatic"===l&&{position:"sticky",top:0},..."sideSticky"===l&&{position:"sticky"}}));(0,n.ZP)(r.Z,{name:"StyledSlot",slot:"preview",shouldForwardProp:t=>"name"!==t})(({name:t,theme:e})=>({fontSize:"0.8125rem",fontWeight:e.typography.fontWeightMedium,height:80,textTransform:"lowercase",alignItems:"center",justifyContent:"center",display:"flex"}));let d=(0,n.ZP)(r.Z,{name:"StyledSlot",slot:"content",shouldForwardProp:t=>"maxWidth"!==t&&"minWidth"!==t&&"minHeight"!==t&&"fixed"!==t})(({theme:t,minHeight:e,maxWidth:o,minWidth:r})=>({display:"block",flexBasis:"100%",marginLeft:"auto",marginRight:"auto",..."screen"===e&&{minHeight:"100vh"},...o&&{maxWidth:t.layoutSlot.points[o]},...r&&{maxWidth:t.layoutSlot.points[r]}})),u=(0,n.ZP)("div",{name:"StyledSlot",slot:"stickyRoot"})({display:"flex",height:"100%",flexDirection:"column"}),s=(0,n.ZP)("div",{name:"StyledSlot",slot:"stickyHeader"})({}),c=(0,n.ZP)("div",{name:"StyledSlot",slot:"stickyContent"})({flex:1,minHeight:0})}}]);