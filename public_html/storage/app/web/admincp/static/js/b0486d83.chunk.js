"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-core-blocks-SidebarAppCategory-Block"],{96594:function(e,t,l){l.r(t),l.d(t,{default:function(){return E}});var n=l(85597),a=l(21241),i=l(30120),r=l(67294),o=l(41547),u=l(50130),c=l(81719),d=l(86010),s=l(41609),m=l.n(s);let p=(e,t)=>{var l;return(null==e?void 0:e.resourceName)===(null==t?void 0:t.resource_name)&&(null==e?void 0:e.appName)===(null==t?void 0:t.module_name)&&(null==e?void 0:e.id)===(null==t?void 0:null===(l=t.id)||void 0===l?void 0:l.toString())};function g(){return(g=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var l=arguments[t];for(var n in l)Object.prototype.hasOwnProperty.call(l,n)&&(e[n]=l[n])}return e}).apply(this,arguments)}let v=(0,c.ZP)("div")(({theme:e})=>({"& a":{height:e.spacing(7),flex:1,minWidth:0,padding:e.spacing(0,2),"&:hover":{backgroundColor:e.palette.action.selected,borderRadius:e.shape.borderRadius}}})),f=(0,c.ZP)("div")(({theme:e})=>({"&.hasSubs":{display:"flex",alignItems:"center","& a":{paddingRight:e.spacing(5)}},"&.itemActive":{"& > a":{color:e.palette.primary.main,fontWeight:e.typography.fontWeightBold}}})),h=(0,c.ZP)(u.Z)(({theme:e})=>({position:"absolute",width:32,height:32,right:e.spacing(1),"& .ico":{fontSize:e.mixins.pxToRem(15),paddingLeft:0}})),b=(0,c.ZP)(n.QVN)(({theme:e})=>({fontSize:e.mixins.pxToRem(15),display:"flex",flexDirection:"row",alignItems:"center",color:"light"===e.palette.mode?e.palette.text.primary:e.palette.text.secondary,textDecoration:"none",position:"relative"})),y=(0,c.ZP)("ul")(({theme:e})=>({borderLeft:`1px solid ${e.palette.border.secondary}`,listStyle:"none",margin:e.spacing(0,0,0,2),padding:0})),k=(e,t,l)=>{return(null==e?void 0:e.length)>0&&e.some(e=>{var n;return l===(null==e?void 0:null===(n=e.id)||void 0===n?void 0:n.toString())||p(t,e)||k(e.subs,t,l)})};function x({name:e,id:t,subs:l,resource_name:a,active:i,classes:u,link:c}){let{usePageParams:s}=(0,n.OgA)(),S=s(),{category:E}=S||{},[_,C]=r.useState(k(l,S,E)),N=r.useCallback(()=>{C(e=>!e)},[]);return r.useEffect(()=>{k(l,S,E)&&C(!0)},[E,null==S?void 0:S.id]),r.createElement(v,null,r.createElement(f,{className:(0,d.default)(!m()(l)&&"hasSubs",i&&"itemActive")},r.createElement(b,{to:c,"data-testid":"itemCategory",color:"inherit","aria-selected":i,"aria-label":e},r.createElement("span",null,e)),m()(l)?null:r.createElement(h,{size:"small",onClick:N},r.createElement(o.zb,{icon:_?"ico-angle-up":"ico-angle-down"}))),_&&l&&(null==l?void 0:l.length)>0?r.createElement(y,null,l.map((e,t)=>{return r.createElement(x,g({key:t.toString(),classes:u,link:(null==e?void 0:e.link)||(null==e?void 0:e.url),active:E===e.id.toString()||p(S,e)},e))})):null)}let S=(0,n.LeK)(function({title:e,sidebarCategory:t,blockProps:l,appName:o}){let{useFetchItems:u,usePageParams:c}=(0,n.OgA)(),d=(null==t?void 0:t.appName)||o,s=(0,n.oHF)(d,(null==t?void 0:t.resourceName)||`${d}_category`,(null==t?void 0:t.actionName)||"viewAll"),[m]=u({dataSource:s||(null==t?void 0:t.dataSource),data:[],cache:!0,normalize:!1}),g=c(),{category:v}=g||{};if(!t)return null;let{title:f}=t;return r.createElement(r.Fragment,null,m.length?r.createElement(i.Z,{sx:{pb:3}},r.createElement(a.gO,{testid:"blockSidebarCategory"},r.createElement(a.ti,{title:f||e}),r.createElement(a.sU,null,m.map(e=>{return r.createElement(x,{id:e.id,name:e.name,resource_name:e.resource_name,key:e.id.toString(),active:v===e.id.toString()||p(g,e),subs:e.subs,link:(null==e?void 0:e.link)||(null==e?void 0:e.url)})})))):null)});var E=(0,n.j4Z)({extendBlock:S,defaults:{blockLayout:"sidebar app category"}})}}]);