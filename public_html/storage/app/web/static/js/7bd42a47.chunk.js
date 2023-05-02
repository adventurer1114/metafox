"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-marketplace-blocks-InvoiceDetail-Block"],{13150:function(e,t,a){a.d(t,{Z:function(){return h}});var r=a(63366),o=a(87462),n=a(67294),l=a(86010),i=a(94780),s=a(21109),d=a(78884),c=a(81719),p=a(1588),u=a(34867);function m(e){return(0,u.Z)("MuiTable",e)}(0,p.Z)("MuiTable",["root","stickyHeader"]);var g=a(85893);let f=["className","component","padding","size","stickyHeader"],v=e=>{let{classes:t,stickyHeader:a}=e;return(0,i.Z)({root:["root",a&&"stickyHeader"]},m,t)},b=(0,c.ZP)("table",{name:"MuiTable",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,a.stickyHeader&&t.stickyHeader]}})(({theme:e,ownerState:t})=>(0,o.Z)({display:"table",width:"100%",borderCollapse:"collapse",borderSpacing:0,"& caption":(0,o.Z)({},e.typography.body2,{padding:e.spacing(2),color:(e.vars||e).palette.text.secondary,textAlign:"left",captionSide:"bottom"})},t.stickyHeader&&{borderCollapse:"separate"})),y="table",Z=n.forwardRef(function(e,t){let a=(0,d.Z)({props:e,name:"MuiTable"}),{className:i,component:c=y,padding:p="normal",size:u="medium",stickyHeader:m=!1}=a,Z=(0,r.Z)(a,f),h=(0,o.Z)({},a,{component:c,padding:p,size:u,stickyHeader:m}),x=v(h),k=n.useMemo(()=>({padding:p,size:u,stickyHeader:m}),[p,u,m]);return(0,g.jsx)(s.Z.Provider,{value:k,children:(0,g.jsx)(b,(0,o.Z)({as:c,role:c===y?null:"table",ref:t,className:(0,l.default)(x.root,i),ownerState:h},Z))})});var h=Z},21109:function(e,t,a){var r=a(67294);let o=r.createContext();t.Z=o},80858:function(e,t,a){var r=a(67294);let o=r.createContext();t.Z=o},66140:function(e,t,a){a.d(t,{Z:function(){return x}});var r=a(87462),o=a(63366),n=a(67294),l=a(86010),i=a(94780),s=a(80858),d=a(78884),c=a(81719),p=a(1588),u=a(34867);function m(e){return(0,u.Z)("MuiTableBody",e)}(0,p.Z)("MuiTableBody",["root"]);var g=a(85893);let f=["className","component"],v=e=>{let{classes:t}=e;return(0,i.Z)({root:["root"]},m,t)},b=(0,c.ZP)("tbody",{name:"MuiTableBody",slot:"Root",overridesResolver:(e,t)=>t.root})({display:"table-row-group"}),y={variant:"body"},Z="tbody",h=n.forwardRef(function(e,t){let a=(0,d.Z)({props:e,name:"MuiTableBody"}),{className:n,component:i=Z}=a,c=(0,o.Z)(a,f),p=(0,r.Z)({},a,{component:i}),u=v(p);return(0,g.jsx)(s.Z.Provider,{value:y,children:(0,g.jsx)(b,(0,r.Z)({className:(0,l.default)(u.root,n),as:i,ref:t,role:i===Z?null:"rowgroup",ownerState:p},c))})});var x=h},3030:function(e,t,a){a.d(t,{Z:function(){return w}});var r=a(63366),o=a(87462),n=a(67294),l=a(86010),i=a(94780),s=a(41796),d=a(36622),c=a(21109),p=a(80858),u=a(78884),m=a(81719),g=a(1588),f=a(34867);function v(e){return(0,f.Z)("MuiTableCell",e)}let b=(0,g.Z)("MuiTableCell",["root","head","body","footer","sizeSmall","sizeMedium","paddingCheckbox","paddingNone","alignLeft","alignCenter","alignRight","alignJustify","stickyHeader"]);var y=a(85893);let Z=["align","className","component","padding","scope","size","sortDirection","variant"],h=e=>{let{classes:t,variant:a,align:r,padding:o,size:n,stickyHeader:l}=e,s={root:["root",a,l&&"stickyHeader","inherit"!==r&&`align${(0,d.Z)(r)}`,"normal"!==o&&`padding${(0,d.Z)(o)}`,`size${(0,d.Z)(n)}`]};return(0,i.Z)(s,v,t)},x=(0,m.ZP)("td",{name:"MuiTableCell",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,t[a.variant],t[`size${(0,d.Z)(a.size)}`],"normal"!==a.padding&&t[`padding${(0,d.Z)(a.padding)}`],"inherit"!==a.align&&t[`align${(0,d.Z)(a.align)}`],a.stickyHeader&&t.stickyHeader]}})(({theme:e,ownerState:t})=>(0,o.Z)({},e.typography.body2,{display:"table-cell",verticalAlign:"inherit",borderBottom:e.vars?`1px solid ${e.vars.palette.TableCell.border}`:`1px solid
    ${"light"===e.palette.mode?(0,s.$n)((0,s.Fq)(e.palette.divider,1),.88):(0,s._j)((0,s.Fq)(e.palette.divider,1),.68)}`,textAlign:"left",padding:16},"head"===t.variant&&{color:(e.vars||e).palette.text.primary,lineHeight:e.typography.pxToRem(24),fontWeight:e.typography.fontWeightMedium},"body"===t.variant&&{color:(e.vars||e).palette.text.primary},"footer"===t.variant&&{color:(e.vars||e).palette.text.secondary,lineHeight:e.typography.pxToRem(21),fontSize:e.typography.pxToRem(12)},"small"===t.size&&{padding:"6px 16px",[`&.${b.paddingCheckbox}`]:{width:24,padding:"0 12px 0 16px","& > *":{padding:0}}},"checkbox"===t.padding&&{width:48,padding:"0 0 0 4px"},"none"===t.padding&&{padding:0},"left"===t.align&&{textAlign:"left"},"center"===t.align&&{textAlign:"center"},"right"===t.align&&{textAlign:"right",flexDirection:"row-reverse"},"justify"===t.align&&{textAlign:"justify"},t.stickyHeader&&{position:"sticky",top:0,zIndex:2,backgroundColor:(e.vars||e).palette.background.default})),k=n.forwardRef(function(e,t){let a;let i=(0,u.Z)({props:e,name:"MuiTableCell"}),{align:s="inherit",className:d,component:m,padding:g,scope:f,size:v,sortDirection:b,variant:k}=i,w=(0,r.Z)(i,Z),C=n.useContext(c.Z),M=n.useContext(p.Z),T=M&&"head"===M.variant;a=m||(T?"th":"td");let E=f;!E&&T&&(E="col");let R=k||M&&M.variant,$=(0,o.Z)({},i,{align:s,component:a,padding:g||(C&&C.padding?C.padding:"normal"),size:v||(C&&C.size?C.size:"medium"),sortDirection:b,stickyHeader:"head"===R&&C&&C.stickyHeader,variant:R}),H=h($),z=null;return b&&(z="asc"===b?"ascending":"descending"),(0,y.jsx)(x,(0,o.Z)({as:a,ref:t,className:(0,l.default)(H.root,d),"aria-sort":z,scope:E,ownerState:$},w))});var w=k},93406:function(e,t,a){a.d(t,{Z:function(){return y}});var r=a(87462),o=a(63366),n=a(67294),l=a(86010),i=a(94780),s=a(78884),d=a(81719),c=a(1588),p=a(34867);function u(e){return(0,p.Z)("MuiTableContainer",e)}(0,c.Z)("MuiTableContainer",["root"]);var m=a(85893);let g=["className","component"],f=e=>{let{classes:t}=e;return(0,i.Z)({root:["root"]},u,t)},v=(0,d.ZP)("div",{name:"MuiTableContainer",slot:"Root",overridesResolver:(e,t)=>t.root})({width:"100%",overflowX:"auto"}),b=n.forwardRef(function(e,t){let a=(0,s.Z)({props:e,name:"MuiTableContainer"}),{className:n,component:i="div"}=a,d=(0,o.Z)(a,g),c=(0,r.Z)({},a,{component:i}),p=f(c);return(0,m.jsx)(v,(0,r.Z)({ref:t,as:i,className:(0,l.default)(p.root,n),ownerState:c},d))});var y=b},48736:function(e,t,a){a.d(t,{Z:function(){return x}});var r=a(87462),o=a(63366),n=a(67294),l=a(86010),i=a(94780),s=a(41796),d=a(80858),c=a(78884),p=a(81719),u=a(1588),m=a(34867);function g(e){return(0,m.Z)("MuiTableRow",e)}let f=(0,u.Z)("MuiTableRow",["root","selected","hover","head","footer"]);var v=a(85893);let b=["className","component","hover","selected"],y=e=>{let{classes:t,selected:a,hover:r,head:o,footer:n}=e;return(0,i.Z)({root:["root",a&&"selected",r&&"hover",o&&"head",n&&"footer"]},g,t)},Z=(0,p.ZP)("tr",{name:"MuiTableRow",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,a.head&&t.head,a.footer&&t.footer]}})(({theme:e})=>({color:"inherit",display:"table-row",verticalAlign:"middle",outline:0,[`&.${f.hover}:hover`]:{backgroundColor:(e.vars||e).palette.action.hover},[`&.${f.selected}`]:{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / ${e.vars.palette.action.selectedOpacity})`:(0,s.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity),"&:hover":{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / calc(${e.vars.palette.action.selectedOpacity} + ${e.vars.palette.action.hoverOpacity}))`:(0,s.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity+e.palette.action.hoverOpacity)}}})),h=n.forwardRef(function(e,t){let a=(0,c.Z)({props:e,name:"MuiTableRow"}),{className:i,component:s="tr",hover:p=!1,selected:u=!1}=a,m=(0,o.Z)(a,b),g=n.useContext(d.Z),f=(0,r.Z)({},a,{component:s,hover:p,selected:u,head:g&&"head"===g.variant,footer:g&&"footer"===g.variant}),h=y(f);return(0,v.jsx)(Z,(0,r.Z)({as:s,ref:t,className:(0,l.default)(h.root,i),role:"tr"===s?null:"row",ownerState:f},m))});var x=h},4710:function(e,t,a){a.r(t),a.d(t,{default:function(){return $}});var r=a(85597),o=a(62984),n=a(21241),l=a(76224),i=a(27274),s=a(30120),d=a(81719),c=a(91647),p=a(67294),u=a(13150),m=a(66140),g=a(3030),f=a(93406),v=a(48736),b=a(41609),y=a.n(b);let Z="TransactionBlock",h=(0,d.ZP)(m.Z,{name:Z,slot:"tableCustom"})(({theme:e})=>({minWidth:958,"& .MuiTableCell-root":{fontSize:e.mixins.pxToRem(15),color:e.palette.text.secondary,borderBottom:0,minWidth:"200px",height:"56px",background:e.palette.background.default,"&:first-of-type":{borderRadius:"8px 0 0 8px"},"&:last-child":{borderRadius:"0 8px 8px 0"}}})),x=(0,d.ZP)(v.Z,{name:Z,slot:"RowTitle"})(({theme:e})=>({"& .MuiTableCell-root":{color:e.palette.text.primary,fontSize:e.mixins.pxToRem(15),fontWeight:e.typography.fontWeightBold,background:e.palette.background.paper}}));function k({tableFields:e,transactions:t}){return y()(e)||y()(t)?null:p.createElement(s.Z,{sx:{position:"relative"}},p.createElement(f.Z,null,p.createElement(u.Z,{sx:{width:"100%",borderCollapse:"separate",borderSpacing:"0 8px"}},p.createElement(h,null,p.createElement(x,null,e.map(e=>p.createElement(g.Z,{key:e.value},p.createElement(l.Ys,{lines:1,variant:"body1",sx:{maxWidth:"300px"},fontWeight:600},e.label)))),t.map(t=>p.createElement(v.Z,{key:`r${t.id}`},e.map(e=>p.createElement(g.Z,{key:`${t.id}${e.value}`},e.isDate?p.createElement(l.r2,{"data-testid":"publishedDate",value:t[e.value],format:"MMMM DD,YYYY HH:mm:ss"}):t[e.value]))))))))}k.displayName="TransactionBlock";let w="MarketplaceDetailView",C=(0,d.ZP)(s.Z,{name:w,slot:"transactionContainer"})(({theme:e})=>({width:"100%"})),M=(0,d.ZP)(s.Z,{name:w,slot:"imageWrapper"})(({theme:e})=>({width:"100%",maxWidth:100,img:{width:100,height:100}})),T=(0,d.ZP)("div",{slot:"Price"})(({theme:e})=>({display:"flex",alignItems:"center",marginTop:e.spacing(1),[e.breakpoints.down("sm")]:{alignItems:"flex-start",flexDirection:"column","& p":{marginLeft:"0",flexWrap:"wrap",height:"auto",justifyContent:"flex-start",marginBottom:e.spacing(1)}}}));function E({user:e,identity:t,item:a,state:o,actions:d,handleAction:u}){let{i18n:m,jsxBackend:g,assetUrl:f,useTheme:v,useGetItem:b,useGetItems:y}=(0,r.OgA)(),Z=v(),h=b(null==a?void 0:a.listing),x=y(null==a?void 0:a.transactions);if(!a)return null;let{status:w,status_label:E,payment_buttons:R,payment_date:$,price:H,table_fields:z}=a,{title:P,image:S,link:B}=h||{},N=(0,i.Q4)(S,"240",f("marketplace.no_image"));return p.createElement(n.gO,{testid:`detailview ${a.resource_name}`},p.createElement(n.sU,null,p.createElement(s.Z,{mb:2},p.createElement(r.rUS,{color:"primary",to:"/marketplace/invoice"},m.formatMessage({id:"back_to_invoices"}))),p.createElement(s.Z,{sx:{display:"flex"}},p.createElement(M,{mr:2},p.createElement(l.Gy,{link:B,src:N,alt:P,aspectRatio:"11"})),p.createElement(s.Z,null,p.createElement(c.Z,{variant:"h4",color:"text.primary"},B?p.createElement(r.rUS,{to:B},P):P),p.createElement(T,null,p.createElement(c.Z,{variant:"body1",color:"primary.main",fontWeight:"bold"},H)),p.createElement(l.Ee,{mt:1},E&&w?p.createElement(l.Ms,{label:E,type:w}):null,$?p.createElement(c.Z,{component:"span",variant:"body2",color:"text.secondary"},p.createElement(l.r2,{"data-testid":"paymentDate",value:$,format:"ll"})):null),R&&R.length?p.createElement(s.Z,{mt:2,sx:{button:{marginRight:Z.spacing(1),marginBottom:Z.spacing(1)}}},R.map((e,a)=>g.render({component:"marketplace.ui.paymentButton",props:{...e,identity:t,key:`k${a}`}}))):null)),x&&x.length?p.createElement(C,{mt:5},p.createElement(k,{tableFields:z,transactions:x})):null))}E.LoadingSkeleton=function({wrapAs:e,wrapProps:t}){return p.createElement(l.Az,{testid:"skeleton",wrapAs:e,wrapProps:t})},E.displayName="MarketplaceDetailView";let R=(0,r.Uh$)((0,o.Y)(E,o.c,{}));var $=(0,r.j4Z)({extendBlock:R,defaults:{blockProps:{titleComponent:"h2",titleVariant:"subtitle1",titleColor:"textPrimary",noFooter:!0,noHeader:!0,blockStyle:{pl:2,pt:3,pr:2,pb:3,mt:0,mb:0,sx:{bgcolor:"background.paper",borderRadius:0}}}}})}}]);