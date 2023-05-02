"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-user-blocks-SharingItemSettings-Block"],{85964:function(e,t,a){a.d(t,{ZP:function(){return w}});var n=a(63366),r=a(87462),o=a(67294),i=a(86010),s=a(94780),l=a(28442),d=a(41796),c=a(81719),p=a(78884),u=a(45355),m=a(70061),y=a(63289),g=a(84771),v=a(57742),b=a(1588),f=a(34867);function Z(e){return(0,f.Z)("MuiListItem",e)}let h=(0,b.Z)("MuiListItem",["root","container","focusVisible","dense","alignItemsFlexStart","disabled","divider","gutters","padding","button","secondaryAction","selected"]);var x=a(94960),C=a(39193),I=a(85893);let S=["className"],k=["alignItems","autoFocus","button","children","className","component","components","componentsProps","ContainerComponent","ContainerProps","dense","disabled","disableGutters","disablePadding","divider","focusVisibleClassName","secondaryAction","selected"],P=(e,t)=>{let{ownerState:a}=e;return[t.root,a.dense&&t.dense,"flex-start"===a.alignItems&&t.alignItemsFlexStart,a.divider&&t.divider,!a.disableGutters&&t.gutters,!a.disablePadding&&t.padding,a.button&&t.button,a.hasSecondaryAction&&t.secondaryAction]},$=e=>{let{alignItems:t,button:a,classes:n,dense:r,disabled:o,disableGutters:i,disablePadding:l,divider:d,hasSecondaryAction:c,selected:p}=e;return(0,s.Z)({root:["root",r&&"dense",!i&&"gutters",!l&&"padding",d&&"divider",o&&"disabled",a&&"button","flex-start"===t&&"alignItemsFlexStart",c&&"secondaryAction",p&&"selected"],container:["container"]},Z,n)},A=(0,c.ZP)("div",{name:"MuiListItem",slot:"Root",overridesResolver:P})(({theme:e,ownerState:t})=>(0,r.Z)({display:"flex",justifyContent:"flex-start",alignItems:"center",position:"relative",textDecoration:"none",width:"100%",boxSizing:"border-box",textAlign:"left"},!t.disablePadding&&(0,r.Z)({paddingTop:8,paddingBottom:8},t.dense&&{paddingTop:4,paddingBottom:4},!t.disableGutters&&{paddingLeft:16,paddingRight:16},!!t.secondaryAction&&{paddingRight:48}),!!t.secondaryAction&&{[`& > .${x.Z.root}`]:{paddingRight:48}},{[`&.${h.focusVisible}`]:{backgroundColor:(e.vars||e).palette.action.focus},[`&.${h.selected}`]:{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / ${e.vars.palette.action.selectedOpacity})`:(0,d.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity),[`&.${h.focusVisible}`]:{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / calc(${e.vars.palette.action.selectedOpacity} + ${e.vars.palette.action.focusOpacity}))`:(0,d.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity+e.palette.action.focusOpacity)}},[`&.${h.disabled}`]:{opacity:(e.vars||e).palette.action.disabledOpacity}},"flex-start"===t.alignItems&&{alignItems:"flex-start"},t.divider&&{borderBottom:`1px solid ${(e.vars||e).palette.divider}`,backgroundClip:"padding-box"},t.button&&{transition:e.transitions.create("background-color",{duration:e.transitions.duration.shortest}),"&:hover":{textDecoration:"none",backgroundColor:(e.vars||e).palette.action.hover,"@media (hover: none)":{backgroundColor:"transparent"}},[`&.${h.selected}:hover`]:{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / calc(${e.vars.palette.action.selectedOpacity} + ${e.vars.palette.action.hoverOpacity}))`:(0,d.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity+e.palette.action.hoverOpacity),"@media (hover: none)":{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / ${e.vars.palette.action.selectedOpacity})`:(0,d.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity)}}},t.hasSecondaryAction&&{paddingRight:48})),L=(0,c.ZP)("li",{name:"MuiListItem",slot:"Container",overridesResolver:(e,t)=>t.container})({position:"relative"}),N=o.forwardRef(function(e,t){let a=(0,p.Z)({props:e,name:"MuiListItem"}),{alignItems:s="center",autoFocus:d=!1,button:c=!1,children:b,className:f,component:Z,components:x={},componentsProps:P={},ContainerComponent:N="li",ContainerProps:{className:w}={},dense:E=!1,disabled:M=!1,disableGutters:O=!1,disablePadding:R=!1,divider:j=!1,focusVisibleClassName:B,secondaryAction:T,selected:_=!1}=a,F=(0,n.Z)(a.ContainerProps,S),G=(0,n.Z)(a,k),V=o.useContext(v.Z),q=o.useMemo(()=>({dense:E||V.dense||!1,alignItems:s,disableGutters:O}),[s,V.dense,E,O]),D=o.useRef(null);(0,y.Z)(()=>{d&&D.current&&D.current.focus()},[d]);let U=o.Children.toArray(b),z=U.length&&(0,m.Z)(U[U.length-1],["ListItemSecondaryAction"]),H=(0,r.Z)({},a,{alignItems:s,autoFocus:d,button:c,dense:q.dense,disabled:M,disableGutters:O,disablePadding:R,divider:j,hasSecondaryAction:z,selected:_}),W=$(H),Y=(0,g.Z)(D,t),J=x.Root||A,K=P.root||{},Q=(0,r.Z)({className:(0,i.default)(W.root,K.className,f),disabled:M},G),X=Z||"li";return(c&&(Q.component=Z||"div",Q.focusVisibleClassName=(0,i.default)(h.focusVisible,B),X=u.Z),z)?(X=Q.component||Z?X:"div","li"===N&&("li"===X?X="div":"li"===Q.component&&(Q.component="div")),(0,I.jsx)(v.Z.Provider,{value:q,children:(0,I.jsxs)(L,(0,r.Z)({as:N,className:(0,i.default)(W.container,w),ref:Y,ownerState:H},F,{children:[(0,I.jsx)(J,(0,r.Z)({},K,!(0,l.Z)(J)&&{as:X,ownerState:(0,r.Z)({},H,K.ownerState)},Q,{children:U})),U.pop()]}))})):(0,I.jsx)(v.Z.Provider,{value:q,children:(0,I.jsxs)(J,(0,r.Z)({},K,{as:X,ref:Y,ownerState:H},!(0,l.Z)(J)&&{ownerState:(0,r.Z)({},H,K.ownerState)},Q,{children:[U,T&&(0,I.jsx)(C.Z,{children:T})]}))})});var w=N},94960:function(e,t,a){a.d(t,{t:function(){return o}});var n=a(1588),r=a(34867);function o(e){return(0,r.Z)("MuiListItemButton",e)}let i=(0,n.Z)("MuiListItemButton",["root","focusVisible","dense","alignItemsFlexStart","disabled","divider","gutters","selected"]);t.Z=i},39193:function(e,t,a){a.d(t,{Z:function(){return Z}});var n=a(63366),r=a(87462),o=a(67294),i=a(86010),s=a(94780),l=a(81719),d=a(78884),c=a(57742),p=a(1588),u=a(34867);function m(e){return(0,u.Z)("MuiListItemSecondaryAction",e)}(0,p.Z)("MuiListItemSecondaryAction",["root","disableGutters"]);var y=a(85893);let g=["className"],v=e=>{let{disableGutters:t,classes:a}=e;return(0,s.Z)({root:["root",t&&"disableGutters"]},m,a)},b=(0,l.ZP)("div",{name:"MuiListItemSecondaryAction",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,a.disableGutters&&t.disableGutters]}})(({ownerState:e})=>(0,r.Z)({position:"absolute",right:16,top:"50%",transform:"translateY(-50%)"},e.disableGutters&&{right:0})),f=o.forwardRef(function(e,t){let a=(0,d.Z)({props:e,name:"MuiListItemSecondaryAction"}),{className:s}=a,l=(0,n.Z)(a,g),p=o.useContext(c.Z),u=(0,r.Z)({},a,{disableGutters:p.disableGutters}),m=v(u);return(0,y.jsx)(b,(0,r.Z)({className:(0,i.default)(m.root,s),ownerState:u,ref:t},l))});f.muiName="ListItemSecondaryAction";var Z=f},61702:function(e,t,a){var n=a(63366),r=a(87462),o=a(67294),i=a(86010),s=a(94780),l=a(91647),d=a(57742),c=a(78884),p=a(81719),u=a(97484),m=a(85893);let y=["children","className","disableTypography","inset","primary","primaryTypographyProps","secondary","secondaryTypographyProps"],g=e=>{let{classes:t,inset:a,primary:n,secondary:r,dense:o}=e;return(0,s.Z)({root:["root",a&&"inset",o&&"dense",n&&r&&"multiline"],primary:["primary"],secondary:["secondary"]},u.L,t)},v=(0,p.ZP)("div",{name:"MuiListItemText",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[{[`& .${u.Z.primary}`]:t.primary},{[`& .${u.Z.secondary}`]:t.secondary},t.root,a.inset&&t.inset,a.primary&&a.secondary&&t.multiline,a.dense&&t.dense]}})(({ownerState:e})=>(0,r.Z)({flex:"1 1 auto",minWidth:0,marginTop:4,marginBottom:4},e.primary&&e.secondary&&{marginTop:6,marginBottom:6},e.inset&&{paddingLeft:56})),b=o.forwardRef(function(e,t){let a=(0,c.Z)({props:e,name:"MuiListItemText"}),{children:s,className:p,disableTypography:u=!1,inset:b=!1,primary:f,primaryTypographyProps:Z,secondary:h,secondaryTypographyProps:x}=a,C=(0,n.Z)(a,y),{dense:I}=o.useContext(d.Z),S=null!=f?f:s,k=h,P=(0,r.Z)({},a,{disableTypography:u,inset:b,primary:!!S,secondary:!!k,dense:I}),$=g(P);return null==S||S.type===l.Z||u||(S=(0,m.jsx)(l.Z,(0,r.Z)({variant:I?"body2":"body1",className:$.primary,component:null!=Z&&Z.variant?void 0:"span",display:"block"},Z,{children:S}))),null==k||k.type===l.Z||u||(k=(0,m.jsx)(l.Z,(0,r.Z)({variant:"body2",className:$.secondary,color:"text.secondary",display:"block"},x,{children:k}))),(0,m.jsxs)(v,(0,r.Z)({className:(0,i.default)($.root,p),ownerState:P,ref:t},C,{children:[S,k]}))});t.Z=b},80256:function(e,t,a){a.r(t),a.d(t,{default:function(){return h}});var n=a(85597),r=a(86706),o=a(21241),i=a(41547),s=a(78573),l=a(91647),d=a(67294),c=a(85964),p=a(61702),u=a(16681),m=a(74825),y=a(39193),g=a(22410),v=a(73327),b=(0,g.Z)(e=>{var t;return(0,v.Z)({root:{},listItem:{borderBottom:"solid 1px",borderBottomColor:null===(t=e.palette.border)||void 0===t?void 0:t.secondary,padding:"22px 0 !important",display:"flex",justifyContent:"space-between","&:first-of-type":{paddingTop:6},"&:last-child":{paddingBottom:6,borderBottom:"none"}},controlItem:{}})},{name:"SharingItemSetting"});function f({item:e,onChanged:t}){let a=b(),[n,r]=d.useState(e.value.toString()),o=d.useCallback(a=>{let n=a.target.value;r(n),t(parseInt(n,10),e.var_name)},[e.var_name,t]);return d.createElement(c.ZP,{className:a.listItem},d.createElement(p.Z,{primary:e.phrase}),d.createElement(y.Z,null,d.createElement(u.Z,{onChange:o,variant:"standard",value:n,disableUnderline:!0},e.options.map(e=>d.createElement(m.Z,{key:e.value.toString(),value:e.value.toString()},e.label)))))}let Z=(0,r.$j)(e=>e.user.sharingItemPrivacy)(function({data:e,title:t,blockProps:a}){let r={},{dispatch:c,useSession:p,i18n:u}=(0,n.OgA)(),{user:m}=p();d.useEffect(()=>{c({type:"setting/sharingItemPrivacy/FETCH",payload:{id:m.id}})},[c]);let y=(e,t)=>{c({type:"setting/sharingItemPrivacy/UPDATE",payload:{var_name:t,value:e}})};return d.createElement(o.gO,null,d.createElement(o.ti,{title:t}),d.createElement(o.sU,null,d.createElement(l.Z,{variant:"body1",paragraph:!0},u.formatMessage({id:"app_sharing_items_note"})),d.createElement(s.Z,{disablePadding:!0},e?e.map(e=>d.createElement(f,{classes:r,onChanged:y,item:e,key:e.var_name})):d.createElement(i.gb,{related:!0,center:!0}))))});var h=(0,n.j4Z)({extendBlock:Z,defaults:{title:"sharing_items",blockLayout:"Account Setting"}})}}]);