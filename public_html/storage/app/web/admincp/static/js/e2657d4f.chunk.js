"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-group-blocks-PendingPost-PendingMode-Block"],{85964:function(e,t,a){a.d(t,{ZP:function(){return A}});var o=a(63366),r=a(87462),n=a(67294),i=a(86010),s=a(94780),l=a(28442),c=a(41796),d=a(81719),p=a(78884),u=a(45355),m=a(70061),h=a(63289),g=a(84771),v=a(57742),b=a(1588),f=a(34867);function Z(e){return(0,f.Z)("MuiListItem",e)}let y=(0,b.Z)("MuiListItem",["root","container","focusVisible","dense","alignItemsFlexStart","disabled","divider","gutters","padding","button","secondaryAction","selected"]);var x=a(94960),$=a(39193),w=a(85893);let k=["className"],S=["alignItems","autoFocus","button","children","className","component","components","componentsProps","ContainerComponent","ContainerProps","dense","disabled","disableGutters","disablePadding","divider","focusVisibleClassName","secondaryAction","selected"],C=(e,t)=>{let{ownerState:a}=e;return[t.root,a.dense&&t.dense,"flex-start"===a.alignItems&&t.alignItemsFlexStart,a.divider&&t.divider,!a.disableGutters&&t.gutters,!a.disablePadding&&t.padding,a.button&&t.button,a.hasSecondaryAction&&t.secondaryAction]},I=e=>{let{alignItems:t,button:a,classes:o,dense:r,disabled:n,disableGutters:i,disablePadding:l,divider:c,hasSecondaryAction:d,selected:p}=e;return(0,s.Z)({root:["root",r&&"dense",!i&&"gutters",!l&&"padding",c&&"divider",n&&"disabled",a&&"button","flex-start"===t&&"alignItemsFlexStart",d&&"secondaryAction",p&&"selected"],container:["container"]},Z,o)},P=(0,d.ZP)("div",{name:"MuiListItem",slot:"Root",overridesResolver:C})(({theme:e,ownerState:t})=>(0,r.Z)({display:"flex",justifyContent:"flex-start",alignItems:"center",position:"relative",textDecoration:"none",width:"100%",boxSizing:"border-box",textAlign:"left"},!t.disablePadding&&(0,r.Z)({paddingTop:8,paddingBottom:8},t.dense&&{paddingTop:4,paddingBottom:4},!t.disableGutters&&{paddingLeft:16,paddingRight:16},!!t.secondaryAction&&{paddingRight:48}),!!t.secondaryAction&&{[`& > .${x.Z.root}`]:{paddingRight:48}},{[`&.${y.focusVisible}`]:{backgroundColor:(e.vars||e).palette.action.focus},[`&.${y.selected}`]:{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / ${e.vars.palette.action.selectedOpacity})`:(0,c.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity),[`&.${y.focusVisible}`]:{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / calc(${e.vars.palette.action.selectedOpacity} + ${e.vars.palette.action.focusOpacity}))`:(0,c.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity+e.palette.action.focusOpacity)}},[`&.${y.disabled}`]:{opacity:(e.vars||e).palette.action.disabledOpacity}},"flex-start"===t.alignItems&&{alignItems:"flex-start"},t.divider&&{borderBottom:`1px solid ${(e.vars||e).palette.divider}`,backgroundClip:"padding-box"},t.button&&{transition:e.transitions.create("background-color",{duration:e.transitions.duration.shortest}),"&:hover":{textDecoration:"none",backgroundColor:(e.vars||e).palette.action.hover,"@media (hover: none)":{backgroundColor:"transparent"}},[`&.${y.selected}:hover`]:{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / calc(${e.vars.palette.action.selectedOpacity} + ${e.vars.palette.action.hoverOpacity}))`:(0,c.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity+e.palette.action.hoverOpacity),"@media (hover: none)":{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / ${e.vars.palette.action.selectedOpacity})`:(0,c.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity)}}},t.hasSecondaryAction&&{paddingRight:48})),M=(0,d.ZP)("li",{name:"MuiListItem",slot:"Container",overridesResolver:(e,t)=>t.container})({position:"relative"}),O=n.forwardRef(function(e,t){let a=(0,p.Z)({props:e,name:"MuiListItem"}),{alignItems:s="center",autoFocus:c=!1,button:d=!1,children:b,className:f,component:Z,components:x={},componentsProps:C={},ContainerComponent:O="li",ContainerProps:{className:A}={},dense:R=!1,disabled:j=!1,disableGutters:L=!1,disablePadding:_=!1,divider:B=!1,focusVisibleClassName:N,secondaryAction:z,selected:F=!1}=a,E=(0,o.Z)(a.ContainerProps,k),G=(0,o.Z)(a,S),T=n.useContext(v.Z),V=n.useMemo(()=>({dense:R||T.dense||!1,alignItems:s,disableGutters:L}),[s,T.dense,R,L]),q=n.useRef(null);(0,h.Z)(()=>{c&&q.current&&q.current.focus()},[c]);let D=n.Children.toArray(b),U=D.length&&(0,m.Z)(D[D.length-1],["ListItemSecondaryAction"]),X=(0,r.Z)({},a,{alignItems:s,autoFocus:c,button:d,dense:V.dense,disabled:j,disableGutters:L,disablePadding:_,divider:B,hasSecondaryAction:U,selected:F}),Y=I(X),W=(0,g.Z)(q,t),H=x.Root||P,J=C.root||{},K=(0,r.Z)({className:(0,i.default)(Y.root,J.className,f),disabled:j},G),Q=Z||"li";return(d&&(K.component=Z||"div",K.focusVisibleClassName=(0,i.default)(y.focusVisible,N),Q=u.Z),U)?(Q=K.component||Z?Q:"div","li"===O&&("li"===Q?Q="div":"li"===K.component&&(K.component="div")),(0,w.jsx)(v.Z.Provider,{value:V,children:(0,w.jsxs)(M,(0,r.Z)({as:O,className:(0,i.default)(Y.container,A),ref:W,ownerState:X},E,{children:[(0,w.jsx)(H,(0,r.Z)({},J,!(0,l.Z)(H)&&{as:Q,ownerState:(0,r.Z)({},X,J.ownerState)},K,{children:D})),D.pop()]}))})):(0,w.jsx)(v.Z.Provider,{value:V,children:(0,w.jsxs)(H,(0,r.Z)({},J,{as:Q,ref:W,ownerState:X},!(0,l.Z)(H)&&{ownerState:(0,r.Z)({},X,J.ownerState)},K,{children:[D,z&&(0,w.jsx)($.Z,{children:z})]}))})});var A=O},94960:function(e,t,a){a.d(t,{t:function(){return n}});var o=a(1588),r=a(34867);function n(e){return(0,r.Z)("MuiListItemButton",e)}let i=(0,o.Z)("MuiListItemButton",["root","focusVisible","dense","alignItemsFlexStart","disabled","divider","gutters","selected"]);t.Z=i},39193:function(e,t,a){a.d(t,{Z:function(){return Z}});var o=a(63366),r=a(87462),n=a(67294),i=a(86010),s=a(94780),l=a(81719),c=a(78884),d=a(57742),p=a(1588),u=a(34867);function m(e){return(0,u.Z)("MuiListItemSecondaryAction",e)}(0,p.Z)("MuiListItemSecondaryAction",["root","disableGutters"]);var h=a(85893);let g=["className"],v=e=>{let{disableGutters:t,classes:a}=e;return(0,s.Z)({root:["root",t&&"disableGutters"]},m,a)},b=(0,l.ZP)("div",{name:"MuiListItemSecondaryAction",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,a.disableGutters&&t.disableGutters]}})(({ownerState:e})=>(0,r.Z)({position:"absolute",right:16,top:"50%",transform:"translateY(-50%)"},e.disableGutters&&{right:0})),f=n.forwardRef(function(e,t){let a=(0,c.Z)({props:e,name:"MuiListItemSecondaryAction"}),{className:s}=a,l=(0,o.Z)(a,g),p=n.useContext(d.Z),u=(0,r.Z)({},a,{disableGutters:p.disableGutters}),m=v(u);return(0,h.jsx)(b,(0,r.Z)({className:(0,i.default)(m.root,s),ownerState:u,ref:t},l))});f.muiName="ListItemSecondaryAction";var Z=f},26569:function(e,t,a){a.d(t,{Z:function(){return S}});var o=a(63366),r=a(87462),n=a(67294),i=a(86010),s=a(94780),l=a(41796),c=a(36622),d=a(69317),p=a(78884),u=a(81719),m=a(1588),h=a(34867);function g(e){return(0,h.Z)("MuiSwitch",e)}let v=(0,m.Z)("MuiSwitch",["root","edgeStart","edgeEnd","switchBase","colorPrimary","colorSecondary","sizeSmall","sizeMedium","checked","disabled","input","thumb","track"]);var b=a(85893);let f=["className","color","edge","size","sx"],Z=e=>{let{classes:t,edge:a,size:o,color:n,checked:i,disabled:l}=e,d={root:["root",a&&`edge${(0,c.Z)(a)}`,`size${(0,c.Z)(o)}`],switchBase:["switchBase",`color${(0,c.Z)(n)}`,i&&"checked",l&&"disabled"],thumb:["thumb"],track:["track"],input:["input"]},p=(0,s.Z)(d,g,t);return(0,r.Z)({},t,p)},y=(0,u.ZP)("span",{name:"MuiSwitch",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,a.edge&&t[`edge${(0,c.Z)(a.edge)}`],t[`size${(0,c.Z)(a.size)}`]]}})(({ownerState:e})=>(0,r.Z)({display:"inline-flex",width:58,height:38,overflow:"hidden",padding:12,boxSizing:"border-box",position:"relative",flexShrink:0,zIndex:0,verticalAlign:"middle","@media print":{colorAdjust:"exact"}},"start"===e.edge&&{marginLeft:-8},"end"===e.edge&&{marginRight:-8},"small"===e.size&&{width:40,height:24,padding:7,[`& .${v.thumb}`]:{width:16,height:16},[`& .${v.switchBase}`]:{padding:4,[`&.${v.checked}`]:{transform:"translateX(16px)"}}})),x=(0,u.ZP)(d.Z,{name:"MuiSwitch",slot:"SwitchBase",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.switchBase,{[`& .${v.input}`]:t.input},"default"!==a.color&&t[`color${(0,c.Z)(a.color)}`]]}})(({theme:e})=>({position:"absolute",top:0,left:0,zIndex:1,color:e.vars?e.vars.palette.Switch.defaultColor:`${"light"===e.palette.mode?e.palette.common.white:e.palette.grey[300]}`,transition:e.transitions.create(["left","transform"],{duration:e.transitions.duration.shortest}),[`&.${v.checked}`]:{transform:"translateX(20px)"},[`&.${v.disabled}`]:{color:e.vars?e.vars.palette.Switch.defaultDisabledColor:`${"light"===e.palette.mode?e.palette.grey[100]:e.palette.grey[600]}`},[`&.${v.checked} + .${v.track}`]:{opacity:.5},[`&.${v.disabled} + .${v.track}`]:{opacity:e.vars?e.vars.opacity.switchTrackDisabled:`${"light"===e.palette.mode?.12:.2}`},[`& .${v.input}`]:{left:"-100%",width:"300%"}}),({theme:e,ownerState:t})=>(0,r.Z)({"&:hover":{backgroundColor:e.vars?`rgba(${e.vars.palette.action.activeChannel} / ${e.vars.palette.action.hoverOpacity})`:(0,l.Fq)(e.palette.action.active,e.palette.action.hoverOpacity),"@media (hover: none)":{backgroundColor:"transparent"}}},"default"!==t.color&&{[`&.${v.checked}`]:{color:(e.vars||e).palette[t.color].main,"&:hover":{backgroundColor:e.vars?`rgba(${e.vars.palette[t.color].mainChannel} / ${e.vars.palette.action.hoverOpacity})`:(0,l.Fq)(e.palette[t.color].main,e.palette.action.hoverOpacity),"@media (hover: none)":{backgroundColor:"transparent"}},[`&.${v.disabled}`]:{color:e.vars?e.vars.palette.Switch[`${t.color}DisabledColor`]:`${"light"===e.palette.mode?(0,l.$n)(e.palette[t.color].main,.62):(0,l._j)(e.palette[t.color].main,.55)}`}},[`&.${v.checked} + .${v.track}`]:{backgroundColor:(e.vars||e).palette[t.color].main}})),$=(0,u.ZP)("span",{name:"MuiSwitch",slot:"Track",overridesResolver:(e,t)=>t.track})(({theme:e})=>({height:"100%",width:"100%",borderRadius:7,zIndex:-1,transition:e.transitions.create(["opacity","background-color"],{duration:e.transitions.duration.shortest}),backgroundColor:e.vars?e.vars.palette.common.onBackground:`${"light"===e.palette.mode?e.palette.common.black:e.palette.common.white}`,opacity:e.vars?e.vars.opacity.switchTrack:`${"light"===e.palette.mode?.38:.3}`})),w=(0,u.ZP)("span",{name:"MuiSwitch",slot:"Thumb",overridesResolver:(e,t)=>t.thumb})(({theme:e})=>({boxShadow:(e.vars||e).shadows[1],backgroundColor:"currentColor",width:20,height:20,borderRadius:"50%"})),k=n.forwardRef(function(e,t){let a=(0,p.Z)({props:e,name:"MuiSwitch"}),{className:n,color:s="primary",edge:l=!1,size:c="medium",sx:d}=a,u=(0,o.Z)(a,f),m=(0,r.Z)({},a,{color:s,edge:l,size:c}),h=Z(m),g=(0,b.jsx)(w,{className:h.thumb,ownerState:m});return(0,b.jsxs)(y,{className:(0,i.default)(h.root,n),sx:d,ownerState:m,children:[(0,b.jsx)(x,(0,r.Z)({type:"checkbox",icon:g,checkedIcon:g,ref:t,ownerState:m},u,{classes:(0,r.Z)({},h,{root:h.switchBase})})),(0,b.jsx)($,{className:h.track,ownerState:m})]})});var S=k},29378:function(e,t,a){a.r(t),a.d(t,{default:function(){return h}});var o=a(85597),r=a(21241),n=a(76482),i=a(85964),s=a(26569),l=a(91647),c=a(67294),d=a(86706);function p(){return(p=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var o in a)Object.prototype.hasOwnProperty.call(a,o)&&(e[o]=a[o])}return e}).apply(this,arguments)}let u=({title:e,identity:t,item:a,...u})=>{let{i18n:m}=(0,o.OgA)(),h=(0,d.I0)(),[g,v]=(0,c.useState)(!!(null==a?void 0:a.pending_mode)),[b,f]=(0,c.useState)(!1);(0,c.useEffect)(()=>{a&&v(!!(null==a?void 0:a.pending_mode))},[a]);let Z=e=>{f(!0),v(e),h({type:"group/updatePendingMode",payload:{identity:t,pending_mode:e},meta:{onSuccess:()=>f(!1),onFailure:()=>f(!1)}})};return c.createElement(r.gO,p({},u),c.createElement(r.ti,{title:e}),c.createElement(l.Z,{variant:"body2",paragraph:!0},m.formatMessage({id:"pending_mode_description"})),c.createElement(r.sU,null,c.createElement(n.Z,{sx:{boxShadow:"none"}},c.createElement(i.ZP,{sx:{py:4},secondaryAction:c.createElement(s.Z,{onChange:(e,t)=>Z(t),checked:g,size:"medium",color:"primary",disabled:b})},c.createElement(l.Z,{component:"div",variant:"body1"},m.formatMessage({id:"enable_pending_mode"}))))))},m=(0,o.Uh$)((0,o.YUM)(u,()=>{}));var h=(0,o.j4Z)({extendBlock:m,overrides:{title:"pending_mode",blockLayout:"App Lists Pending Posts",contentType:"group",showWhen:["truthy","profile.extra.can_manage_pending_mode"]}})}}]);