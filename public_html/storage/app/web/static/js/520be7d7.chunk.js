"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-subscription-blocks-SubscriptionDetail-Block"],{13150:function(e,t,a){a.d(t,{Z:function(){return h}});var r=a(63366),o=a(87462),n=a(67294),l=a(86010),i=a(94780),s=a(21109),d=a(78884),c=a(81719),p=a(1588),u=a(34867);function m(e){return(0,u.Z)("MuiTable",e)}(0,p.Z)("MuiTable",["root","stickyHeader"]);var g=a(85893);let f=["className","component","padding","size","stickyHeader"],b=e=>{let{classes:t,stickyHeader:a}=e;return(0,i.Z)({root:["root",a&&"stickyHeader"]},m,t)},v=(0,c.ZP)("table",{name:"MuiTable",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,a.stickyHeader&&t.stickyHeader]}})(({theme:e,ownerState:t})=>(0,o.Z)({display:"table",width:"100%",borderCollapse:"collapse",borderSpacing:0,"& caption":(0,o.Z)({},e.typography.body2,{padding:e.spacing(2),color:(e.vars||e).palette.text.secondary,textAlign:"left",captionSide:"bottom"})},t.stickyHeader&&{borderCollapse:"separate"})),y="table",Z=n.forwardRef(function(e,t){let a=(0,d.Z)({props:e,name:"MuiTable"}),{className:i,component:c=y,padding:p="normal",size:u="medium",stickyHeader:m=!1}=a,Z=(0,r.Z)(a,f),h=(0,o.Z)({},a,{component:c,padding:p,size:u,stickyHeader:m}),x=b(h),k=n.useMemo(()=>({padding:p,size:u,stickyHeader:m}),[p,u,m]);return(0,g.jsx)(s.Z.Provider,{value:k,children:(0,g.jsx)(v,(0,o.Z)({as:c,role:c===y?null:"table",ref:t,className:(0,l.default)(x.root,i),ownerState:h},Z))})});var h=Z},21109:function(e,t,a){var r=a(67294);let o=r.createContext();t.Z=o},80858:function(e,t,a){var r=a(67294);let o=r.createContext();t.Z=o},66140:function(e,t,a){a.d(t,{Z:function(){return x}});var r=a(87462),o=a(63366),n=a(67294),l=a(86010),i=a(94780),s=a(80858),d=a(78884),c=a(81719),p=a(1588),u=a(34867);function m(e){return(0,u.Z)("MuiTableBody",e)}(0,p.Z)("MuiTableBody",["root"]);var g=a(85893);let f=["className","component"],b=e=>{let{classes:t}=e;return(0,i.Z)({root:["root"]},m,t)},v=(0,c.ZP)("tbody",{name:"MuiTableBody",slot:"Root",overridesResolver:(e,t)=>t.root})({display:"table-row-group"}),y={variant:"body"},Z="tbody",h=n.forwardRef(function(e,t){let a=(0,d.Z)({props:e,name:"MuiTableBody"}),{className:n,component:i=Z}=a,c=(0,o.Z)(a,f),p=(0,r.Z)({},a,{component:i}),u=b(p);return(0,g.jsx)(s.Z.Provider,{value:y,children:(0,g.jsx)(v,(0,r.Z)({className:(0,l.default)(u.root,n),as:i,ref:t,role:i===Z?null:"rowgroup",ownerState:p},c))})});var x=h},3030:function(e,t,a){a.d(t,{Z:function(){return E}});var r=a(63366),o=a(87462),n=a(67294),l=a(86010),i=a(94780),s=a(41796),d=a(36622),c=a(21109),p=a(80858),u=a(78884),m=a(81719),g=a(1588),f=a(34867);function b(e){return(0,f.Z)("MuiTableCell",e)}let v=(0,g.Z)("MuiTableCell",["root","head","body","footer","sizeSmall","sizeMedium","paddingCheckbox","paddingNone","alignLeft","alignCenter","alignRight","alignJustify","stickyHeader"]);var y=a(85893);let Z=["align","className","component","padding","scope","size","sortDirection","variant"],h=e=>{let{classes:t,variant:a,align:r,padding:o,size:n,stickyHeader:l}=e,s={root:["root",a,l&&"stickyHeader","inherit"!==r&&`align${(0,d.Z)(r)}`,"normal"!==o&&`padding${(0,d.Z)(o)}`,`size${(0,d.Z)(n)}`]};return(0,i.Z)(s,b,t)},x=(0,m.ZP)("td",{name:"MuiTableCell",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,t[a.variant],t[`size${(0,d.Z)(a.size)}`],"normal"!==a.padding&&t[`padding${(0,d.Z)(a.padding)}`],"inherit"!==a.align&&t[`align${(0,d.Z)(a.align)}`],a.stickyHeader&&t.stickyHeader]}})(({theme:e,ownerState:t})=>(0,o.Z)({},e.typography.body2,{display:"table-cell",verticalAlign:"inherit",borderBottom:e.vars?`1px solid ${e.vars.palette.TableCell.border}`:`1px solid
    ${"light"===e.palette.mode?(0,s.$n)((0,s.Fq)(e.palette.divider,1),.88):(0,s._j)((0,s.Fq)(e.palette.divider,1),.68)}`,textAlign:"left",padding:16},"head"===t.variant&&{color:(e.vars||e).palette.text.primary,lineHeight:e.typography.pxToRem(24),fontWeight:e.typography.fontWeightMedium},"body"===t.variant&&{color:(e.vars||e).palette.text.primary},"footer"===t.variant&&{color:(e.vars||e).palette.text.secondary,lineHeight:e.typography.pxToRem(21),fontSize:e.typography.pxToRem(12)},"small"===t.size&&{padding:"6px 16px",[`&.${v.paddingCheckbox}`]:{width:24,padding:"0 12px 0 16px","& > *":{padding:0}}},"checkbox"===t.padding&&{width:48,padding:"0 0 0 4px"},"none"===t.padding&&{padding:0},"left"===t.align&&{textAlign:"left"},"center"===t.align&&{textAlign:"center"},"right"===t.align&&{textAlign:"right",flexDirection:"row-reverse"},"justify"===t.align&&{textAlign:"justify"},t.stickyHeader&&{position:"sticky",top:0,zIndex:2,backgroundColor:(e.vars||e).palette.background.default})),k=n.forwardRef(function(e,t){let a;let i=(0,u.Z)({props:e,name:"MuiTableCell"}),{align:s="inherit",className:d,component:m,padding:g,scope:f,size:b,sortDirection:v,variant:k}=i,E=(0,r.Z)(i,Z),w=n.useContext(c.Z),C=n.useContext(p.Z),M=C&&"head"===C.variant;a=m||(M?"th":"td");let T=f;!T&&M&&(T="col");let R=k||C&&C.variant,$=(0,o.Z)({},i,{align:s,component:a,padding:g||(w&&w.padding?w.padding:"normal"),size:b||(w&&w.size?w.size:"medium"),sortDirection:v,stickyHeader:"head"===R&&w&&w.stickyHeader,variant:R}),z=h($),H=null;return v&&(H="asc"===v?"ascending":"descending"),(0,y.jsx)(x,(0,o.Z)({as:a,ref:t,className:(0,l.default)(z.root,d),"aria-sort":H,scope:T,ownerState:$},E))});var E=k},93406:function(e,t,a){a.d(t,{Z:function(){return y}});var r=a(87462),o=a(63366),n=a(67294),l=a(86010),i=a(94780),s=a(78884),d=a(81719),c=a(1588),p=a(34867);function u(e){return(0,p.Z)("MuiTableContainer",e)}(0,c.Z)("MuiTableContainer",["root"]);var m=a(85893);let g=["className","component"],f=e=>{let{classes:t}=e;return(0,i.Z)({root:["root"]},u,t)},b=(0,d.ZP)("div",{name:"MuiTableContainer",slot:"Root",overridesResolver:(e,t)=>t.root})({width:"100%",overflowX:"auto"}),v=n.forwardRef(function(e,t){let a=(0,s.Z)({props:e,name:"MuiTableContainer"}),{className:n,component:i="div"}=a,d=(0,o.Z)(a,g),c=(0,r.Z)({},a,{component:i}),p=f(c);return(0,m.jsx)(b,(0,r.Z)({ref:t,as:i,className:(0,l.default)(p.root,n),ownerState:c},d))});var y=v},48736:function(e,t,a){a.d(t,{Z:function(){return x}});var r=a(87462),o=a(63366),n=a(67294),l=a(86010),i=a(94780),s=a(41796),d=a(80858),c=a(78884),p=a(81719),u=a(1588),m=a(34867);function g(e){return(0,m.Z)("MuiTableRow",e)}let f=(0,u.Z)("MuiTableRow",["root","selected","hover","head","footer"]);var b=a(85893);let v=["className","component","hover","selected"],y=e=>{let{classes:t,selected:a,hover:r,head:o,footer:n}=e;return(0,i.Z)({root:["root",a&&"selected",r&&"hover",o&&"head",n&&"footer"]},g,t)},Z=(0,p.ZP)("tr",{name:"MuiTableRow",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,a.head&&t.head,a.footer&&t.footer]}})(({theme:e})=>({color:"inherit",display:"table-row",verticalAlign:"middle",outline:0,[`&.${f.hover}:hover`]:{backgroundColor:(e.vars||e).palette.action.hover},[`&.${f.selected}`]:{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / ${e.vars.palette.action.selectedOpacity})`:(0,s.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity),"&:hover":{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / calc(${e.vars.palette.action.selectedOpacity} + ${e.vars.palette.action.hoverOpacity}))`:(0,s.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity+e.palette.action.hoverOpacity)}}})),h=n.forwardRef(function(e,t){let a=(0,c.Z)({props:e,name:"MuiTableRow"}),{className:i,component:s="tr",hover:p=!1,selected:u=!1}=a,m=(0,o.Z)(a,v),g=n.useContext(d.Z),f=(0,r.Z)({},a,{component:s,hover:p,selected:u,head:g&&"head"===g.variant,footer:g&&"footer"===g.variant}),h=y(f);return(0,b.jsx)(Z,(0,r.Z)({as:s,ref:t,className:(0,l.default)(h.root,i),role:"tr"===s?null:"row",ownerState:f},m))});var x=h},56954:function(e,t,a){a.r(t),a.d(t,{default:function(){return H}});var r=a(85597),o=a(40138),n=a(84116),l=a(21241),i=a(76224),s=a(13478),d=a(30120),c=a(81719),p=a(38790),u=a(91647),m=a(67294),g=a(13150),f=a(66140),b=a(3030),v=a(93406),y=a(48736),Z=a(41609),h=a.n(Z);let x="TransactionBlock",k=(0,c.ZP)(f.Z,{name:x,slot:"tableCustom"})(({theme:e})=>({minWidth:958,"& .MuiTableCell-root":{fontSize:e.mixins.pxToRem(15),color:e.palette.text.secondary,borderBottom:0,minWidth:"200px",height:"56px",background:e.palette.background.default,"&:first-of-type":{borderRadius:"8px 0 0 8px"},"&:last-child":{borderRadius:"0 8px 8px 0"}}})),E=(0,c.ZP)(y.Z,{name:x,slot:"RowTitle"})(({theme:e})=>({"& .MuiTableCell-root":{color:e.palette.text.primary,fontSize:e.mixins.pxToRem(15),fontWeight:e.typography.fontWeightBold,background:e.palette.background.paper}}));function w({tableFields:e,transactions:t}){return h()(e)||h()(t)?null:m.createElement(d.Z,{sx:{position:"relative"}},m.createElement(v.Z,null,m.createElement(g.Z,{sx:{width:"100%",borderCollapse:"separate",borderSpacing:"0 8px"}},m.createElement(k,null,m.createElement(E,null,e.map(e=>m.createElement(b.Z,{key:e.value},m.createElement(i.Ys,{lines:1,variant:"body1",sx:{maxWidth:"300px"},fontWeight:600},e.label)))),t.map(t=>{return m.createElement(y.Z,{key:`r${t.id}`},e.map(e=>{return m.createElement(b.Z,{key:`${t.id}${e.value}`},(null==e?void 0:e.isDate)?m.createElement(i.r2,{"data-testid":"publishedDate",value:t[e.value],format:"MMMM DD,YYYY HH:mm:ss"}):t[e.value])}))})))))}w.displayName="TransactionBlock";let C="SubscriptionDetailView",M=(0,c.ZP)(d.Z,{name:C,slot:"transactionContainer"})(({theme:e})=>({width:"100%"})),T=(0,c.ZP)(d.Z,{name:C,slot:"imageWrapper"})(({theme:e})=>({width:"100%",maxWidth:100,img:{width:100,height:100}})),R=(0,c.ZP)("div",{slot:"Price"})(({theme:e})=>({display:"flex",alignItems:"center",marginTop:e.spacing(1),[e.breakpoints.down("sm")]:{alignItems:"flex-start",flexDirection:"column","& p":{marginLeft:"0",flexWrap:"wrap",height:"auto",justifyContent:"flex-start",marginBottom:e.spacing(1)}}}));function $({user:e,identity:t,item:a,state:o,actions:c,handleAction:g}){let{i18n:f,jsxBackend:b,assetUrl:v,useTheme:y}=(0,r.OgA)(),Z=y();if(!a)return null;let{package_title:h,price:x,recurring_price:k,expired_at:E,payment_status:C,payment_status_label:$,upgraded_membership:z,payment_buttons:H,table_fields:S,transactions:P,activated_at:_,expired_description:B}=a,N=(0,s.Q4)(a.image,"240",v("subscription.no_image"));return m.createElement(l.gO,{testid:`detailview ${a.resource_name}`},m.createElement(l.sU,null,m.createElement(d.Z,{mb:2},m.createElement(r.rUS,{color:"primary",to:"/subscription/my"},f.formatMessage({id:"back_to_all_subscriptions"}))),m.createElement(d.Z,{sx:{display:"flex"}},m.createElement(T,{mr:2},m.createElement(i.Gy,{src:N,alt:h,aspectRatio:"11"})),m.createElement(d.Z,null,m.createElement(u.Z,{variant:"h4",color:"text.primary"},h),m.createElement(R,null,m.createElement(u.Z,{variant:"body1",color:"primary.main",fontWeight:"bold"},x),k?m.createElement(i.NZ,{backgroundColor:"dark"===Z.palette.mode?Z.palette.grey[600]:Z.palette.grey[100],variant:"body2",color:"text.primary",sx:{marginLeft:"4px"}},m.createElement(n.ZP,{html:k})):null),m.createElement(i.Ee,{mt:1},$&&C?b.render({component:"subscription.ui.statusLabel",props:{label:$,type:C}}):null,_?m.createElement(u.Z,{component:"span",variant:"body2",color:"text.secondary"},m.createElement(i.r2,{"data-testid":"expiredDate",value:_,format:"ll",phrase:"activation_date_time"}),E?m.createElement(d.Z,{sx:{display:"inline-flex",marginLeft:"4px"}},"(",m.createElement(i.r2,{"data-testid":"expiredDate",value:E,format:"ll",phrase:"expires_on_time"}),")"):null,B?m.createElement(d.Z,{sx:{display:"inline-flex",marginLeft:"4px"}},"(",B,")"):null):null),m.createElement(d.Z,{mt:1},m.createElement(u.Z,{variant:"body2",color:"text.secondary"},f.formatMessage({id:"acquired_membership"})," ",m.createElement(p.Z,{title:f.formatMessage({id:"membership_question_mark"})},m.createElement(i.zb,{icon:"ico-question-circle-o"})),": ",m.createElement("b",null,z))),H&&H.length?m.createElement(d.Z,{mt:2,sx:{button:{marginRight:Z.spacing(1),marginBottom:Z.spacing(1)}}},H.map((e,a)=>b.render({component:"subscription.ui.paymentButton",props:{...e,identity:t,key:`k${a}`}}))):null)),P&&P.length?m.createElement(M,{mt:5},m.createElement(w,{tableFields:S,transactions:P})):null))}$.LoadingSkeleton=function({wrapAs:e,wrapProps:t}){return m.createElement(i.Az,{testid:"skeleton",wrapAs:e,wrapProps:t})},$.displayName="SubscriptionDetailView";let z=(0,r.Uh$)((0,o.Y)($,o.c,{}));var H=(0,r.j4Z)({extendBlock:z,defaults:{blockProps:{titleComponent:"h2",titleVariant:"subtitle1",titleColor:"textPrimary",noFooter:!0,noHeader:!0,blockStyle:{pl:2,pt:3,pr:2,pb:3,mt:0,mb:0,sx:{bgcolor:"background.paper",borderRadius:0}}}}})}}]);