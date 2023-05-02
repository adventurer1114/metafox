"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-music-components-SongItem-ListingCard-LoadingSkeleton"],{21109:function(e,t,a){var r=a(67294);let n=r.createContext();t.Z=n},80858:function(e,t,a){var r=a(67294);let n=r.createContext();t.Z=n},3030:function(e,t,a){a.d(t,{Z:function(){return k}});var r=a(63366),n=a(87462),i=a(67294),o=a(86010),l=a(94780),d=a(41796),c=a(36622),s=a(21109),p=a(80858),u=a(78884),g=a(81719),m=a(1588),v=a(34867);function h(e){return(0,v.Z)("MuiTableCell",e)}let f=(0,m.Z)("MuiTableCell",["root","head","body","footer","sizeSmall","sizeMedium","paddingCheckbox","paddingNone","alignLeft","alignCenter","alignRight","alignJustify","stickyHeader"]);var y=a(85893);let Z=["align","className","component","padding","scope","size","sortDirection","variant"],x=e=>{let{classes:t,variant:a,align:r,padding:n,size:i,stickyHeader:o}=e,d={root:["root",a,o&&"stickyHeader","inherit"!==r&&`align${(0,c.Z)(r)}`,"normal"!==n&&`padding${(0,c.Z)(n)}`,`size${(0,c.Z)(i)}`]};return(0,l.Z)(d,h,t)},b=(0,g.ZP)("td",{name:"MuiTableCell",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,t[a.variant],t[`size${(0,c.Z)(a.size)}`],"normal"!==a.padding&&t[`padding${(0,c.Z)(a.padding)}`],"inherit"!==a.align&&t[`align${(0,c.Z)(a.align)}`],a.stickyHeader&&t.stickyHeader]}})(({theme:e,ownerState:t})=>(0,n.Z)({},e.typography.body2,{display:"table-cell",verticalAlign:"inherit",borderBottom:e.vars?`1px solid ${e.vars.palette.TableCell.border}`:`1px solid
    ${"light"===e.palette.mode?(0,d.$n)((0,d.Fq)(e.palette.divider,1),.88):(0,d._j)((0,d.Fq)(e.palette.divider,1),.68)}`,textAlign:"left",padding:16},"head"===t.variant&&{color:(e.vars||e).palette.text.primary,lineHeight:e.typography.pxToRem(24),fontWeight:e.typography.fontWeightMedium},"body"===t.variant&&{color:(e.vars||e).palette.text.primary},"footer"===t.variant&&{color:(e.vars||e).palette.text.secondary,lineHeight:e.typography.pxToRem(21),fontSize:e.typography.pxToRem(12)},"small"===t.size&&{padding:"6px 16px",[`&.${f.paddingCheckbox}`]:{width:24,padding:"0 12px 0 16px","& > *":{padding:0}}},"checkbox"===t.padding&&{width:48,padding:"0 0 0 4px"},"none"===t.padding&&{padding:0},"left"===t.align&&{textAlign:"left"},"center"===t.align&&{textAlign:"center"},"right"===t.align&&{textAlign:"right",flexDirection:"row-reverse"},"justify"===t.align&&{textAlign:"justify"},t.stickyHeader&&{position:"sticky",top:0,zIndex:2,backgroundColor:(e.vars||e).palette.background.default})),C=i.forwardRef(function(e,t){let a;let l=(0,u.Z)({props:e,name:"MuiTableCell"}),{align:d="inherit",className:c,component:g,padding:m,scope:v,size:h,sortDirection:f,variant:C}=l,k=(0,r.Z)(l,Z),w=i.useContext(s.Z),$=i.useContext(p.Z),R=$&&"head"===$.variant;a=g||(R?"th":"td");let z=v;!z&&R&&(z="col");let T=C||$&&$.variant,M=(0,n.Z)({},l,{align:d,component:a,padding:m||(w&&w.padding?w.padding:"normal"),size:h||(w&&w.size?w.size:"medium"),sortDirection:f,stickyHeader:"head"===T&&w&&w.stickyHeader,variant:T}),E=x(M),H=null;return f&&(H="asc"===f?"ascending":"descending"),(0,y.jsx)(b,(0,n.Z)({as:a,ref:t,className:(0,o.default)(E.root,c),"aria-sort":H,scope:z,ownerState:M},k))});var k=C},48736:function(e,t,a){a.d(t,{Z:function(){return b}});var r=a(87462),n=a(63366),i=a(67294),o=a(86010),l=a(94780),d=a(41796),c=a(80858),s=a(78884),p=a(81719),u=a(1588),g=a(34867);function m(e){return(0,g.Z)("MuiTableRow",e)}let v=(0,u.Z)("MuiTableRow",["root","selected","hover","head","footer"]);var h=a(85893);let f=["className","component","hover","selected"],y=e=>{let{classes:t,selected:a,hover:r,head:n,footer:i}=e;return(0,l.Z)({root:["root",a&&"selected",r&&"hover",n&&"head",i&&"footer"]},m,t)},Z=(0,p.ZP)("tr",{name:"MuiTableRow",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,a.head&&t.head,a.footer&&t.footer]}})(({theme:e})=>({color:"inherit",display:"table-row",verticalAlign:"middle",outline:0,[`&.${v.hover}:hover`]:{backgroundColor:(e.vars||e).palette.action.hover},[`&.${v.selected}`]:{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / ${e.vars.palette.action.selectedOpacity})`:(0,d.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity),"&:hover":{backgroundColor:e.vars?`rgba(${e.vars.palette.primary.mainChannel} / calc(${e.vars.palette.action.selectedOpacity} + ${e.vars.palette.action.hoverOpacity}))`:(0,d.Fq)(e.palette.primary.main,e.palette.action.selectedOpacity+e.palette.action.hoverOpacity)}}})),x=i.forwardRef(function(e,t){let a=(0,s.Z)({props:e,name:"MuiTableRow"}),{className:l,component:d="tr",hover:p=!1,selected:u=!1}=a,g=(0,n.Z)(a,f),m=i.useContext(c.Z),v=(0,r.Z)({},a,{component:d,hover:p,selected:u,head:m&&"head"===m.variant,footer:m&&"footer"===m.variant}),x=y(v);return(0,h.jsx)(Z,(0,r.Z)({as:d,ref:t,className:(0,o.default)(x.root,l),role:"tr"===d?null:"row",ownerState:v},g))});var b=x},31125:function(e,t,a){a.r(t),a.d(t,{default:function(){return l}});var r=a(71682),n=a(48736),i=a(3030),o=a(67294);function l({wrapAs:e,wrapProps:t}){return o.createElement(n.Z,null,o.createElement(i.Z,{width:"50px"},o.createElement(r.Z,null)),o.createElement(i.Z,{width:"80%"},o.createElement(r.Z,null)),o.createElement(i.Z,null,o.createElement(r.Z,null)),o.createElement(i.Z,null,o.createElement(r.Z,null)))}}}]);