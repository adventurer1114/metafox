"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-core-components-Error404Block"],{77589:function(e,t,o){o.r(t);var r=o(85597),n=o(30120),a=o(91647),i=o(81719),l=o(67294);let m=(0,i.ZP)(n.Z,{name:"Error404",slot:"Root"})(({theme:e})=>({display:"flex",alignItems:"center",flexDirection:"column",padding:e.spacing(5,2),"& + .error404Block":{display:"none"}})),c=(0,i.ZP)("img",{name:"Error404",slot:"Image"})(({theme:e})=>({maxWidth:"100%"})),s=(0,i.ZP)(a.Z,{name:"Error404",slot:"Title"})(({theme:e})=>({maxWidth:"100%",marginBottom:e.spacing(6),fontWeight:"bold",textAlign:"center",[e.breakpoints.down("sm")]:{fontSize:e.mixins.pxToRem(40)},[e.breakpoints.down("xs")]:{fontSize:e.mixins.pxToRem(20)}})),p=({title:e="404 - Page Not Found",color:t="primary"})=>{let{usePageParams:o,assetUrl:n}=(0,r.OgA)(),a=o(),i=n("layout.image_error_404");return l.createElement(m,{className:"error404Block","data-testid":"error404"},l.createElement(s,{color:t,component:"h1",variant:"h1"},(null==a?void 0:a.title)||e),l.createElement(c,{src:i,alt:e}))};t.default=p}}]);