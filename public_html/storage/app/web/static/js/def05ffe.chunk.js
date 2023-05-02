"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-ui-SidebarMenuItem-LinkMenuItem"],{29894:function(e,t,r){var a=r(63366),n=r(87462),o=r(67294),i=r(86010),s=r(94780),l=r(81719),m=r(78884),c=r(8164),d=r(57742),u=r(85893);let p=["className"],y=e=>{let{alignItems:t,classes:r}=e;return(0,s.Z)({root:["root","flex-start"===t&&"alignItemsFlexStart"]},c.f,r)},f=(0,l.ZP)("div",{name:"MuiListItemIcon",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:r}=e;return[t.root,"flex-start"===r.alignItems&&t.alignItemsFlexStart]}})(({theme:e,ownerState:t})=>(0,n.Z)({minWidth:56,color:(e.vars||e).palette.action.active,flexShrink:0,display:"inline-flex"},"flex-start"===t.alignItems&&{marginTop:8})),Z=o.forwardRef(function(e,t){let r=(0,m.Z)({props:e,name:"MuiListItemIcon"}),{className:s}=r,l=(0,a.Z)(r,p),c=o.useContext(d.Z),Z=(0,n.Z)({},r,{alignItems:c.alignItems}),x=y(Z);return(0,u.jsx)(f,(0,n.Z)({className:(0,i.default)(x.root,s),ownerState:Z,ref:t},l))});t.Z=Z},61702:function(e,t,r){var a=r(63366),n=r(87462),o=r(67294),i=r(86010),s=r(94780),l=r(91647),m=r(57742),c=r(78884),d=r(81719),u=r(97484),p=r(85893);let y=["children","className","disableTypography","inset","primary","primaryTypographyProps","secondary","secondaryTypographyProps"],f=e=>{let{classes:t,inset:r,primary:a,secondary:n,dense:o}=e;return(0,s.Z)({root:["root",r&&"inset",o&&"dense",a&&n&&"multiline"],primary:["primary"],secondary:["secondary"]},u.L,t)},Z=(0,d.ZP)("div",{name:"MuiListItemText",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:r}=e;return[{[`& .${u.Z.primary}`]:t.primary},{[`& .${u.Z.secondary}`]:t.secondary},t.root,r.inset&&t.inset,r.primary&&r.secondary&&t.multiline,r.dense&&t.dense]}})(({ownerState:e})=>(0,n.Z)({flex:"1 1 auto",minWidth:0,marginTop:4,marginBottom:4},e.primary&&e.secondary&&{marginTop:6,marginBottom:6},e.inset&&{paddingLeft:56})),x=o.forwardRef(function(e,t){let r=(0,c.Z)({props:e,name:"MuiListItemText"}),{children:s,className:d,disableTypography:u=!1,inset:x=!1,primary:v,primaryTypographyProps:I,secondary:g,secondaryTypographyProps:h}=r,b=(0,a.Z)(r,y),{dense:k}=o.useContext(m.Z),N=null!=v?v:s,T=g,L=(0,n.Z)({},r,{disableTypography:u,inset:x,primary:!!N,secondary:!!T,dense:k}),M=f(L);return null==N||N.type===l.Z||u||(N=(0,p.jsx)(l.Z,(0,n.Z)({variant:k?"body2":"body1",className:M.primary,component:null!=I&&I.variant?void 0:"span",display:"block"},I,{children:N}))),null==T||T.type===l.Z||u||(T=(0,p.jsx)(l.Z,(0,n.Z)({variant:"body2",className:M.secondary,color:"text.secondary",display:"block"},h,{children:T}))),(0,p.jsxs)(Z,(0,n.Z)({className:(0,i.default)(M.root,d),ownerState:L,ref:t},b,{children:[N,T]}))});t.Z=x},78046:function(e,t,r){r.r(t),r.d(t,{default:function(){return c}});var a=r(85597),n=r(76224),o=r(86010),i=r(67294),s=r(74825),l=r(29894),m=r(61702);function c({item:e,active:t,classes:r,variant:c}){let{label:d,to:u,icon:p,onClick:y,testid:f}=e;return i.createElement(s.Z,{role:"menuitem",className:(0,o.default)(r.menuItem,t&&r.activeMenuItem),component:a.QVN,to:u,onClick:y,selected:t,"data-testid":f||d||p,variant:c},p?i.createElement(l.Z,null,i.createElement(n.zb,{icon:p})):null,i.createElement(m.Z,{className:r.menuItemText,primary:d}))}}}]);