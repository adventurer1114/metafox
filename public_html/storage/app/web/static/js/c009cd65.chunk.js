"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-core-blocks-ListingOnMap-Block"],{26349:function(e,t,a){a.r(t),a.d(t,{default:function(){return F}});var o=a(85597),n=a(76224),l=a(30120),r=a(68354),i=a(25722),s=a(62937),c=a(71682),p=a(81719),g=a(22949),d=a(38790),u=a(68929),m=a.n(u),h=a(27361),f=a.n(h),v=a(67294),w=a(96974),x=a(27274),E=a(37054),b=a(21241),k=a(86706),y=a(17563),S=a(62808);let Z=(0,p.ZP)("div",{name:"titleStyled"})(({theme:e})=>({color:"dark"===e.palette.mode?e.palette.action.focus:e.palette.text.secondary})),C=(0,p.ZP)("div")(({theme:e})=>({position:"absolute",right:e.spacing(7.5),top:e.spacing(1.2),backgroundColor:"#ffffff",height:e.spacing(5),borderRadius:e.spacing(1),'[dir="rtl"] &':{right:"inherit",left:e.spacing(7.5)},"& .MuiInputBase-root":{width:"400px",height:"40px !important",borderRadius:e.spacing(1)},"& input":{color:"black",marginRight:e.spacing(3)},"& input::placeholder":{color:e.palette.grey[700]},[e.breakpoints.down("sm")]:{position:"absolute!important",top:"-50px",width:"100%!important",left:0,"& .MuiInputBase-root":{width:"100%!important"}}})),P=(0,p.ZP)("span")(({theme:e})=>({paddingLeft:e.spacing(1),color:e.palette.text.secondary})),$=(0,p.ZP)("span")(({theme:e})=>({color:e.palette.text.primary})),R=(0,p.ZP)(n.zb)(({theme:e})=>({color:e.palette.text.secondary,fontSize:e.mixins.pxToRem(13),paddingRight:e.spacing(1)})),_=(0,p.ZP)("div")(({theme:e})=>({flexDirection:"row",alignItems:"center",margin:e.spacing(1,1),fontSize:e.mixins.pxToRem(15),cursor:"pointer","&:hover":{backgroundColor:e.palette.action.focus},overflow:"hidden",textOverflow:"ellipsis",display:"-webkit-box!important",WebkitBoxOrient:"vertical",WebkitLineClamp:1})),z=(0,p.ZP)(i.Z)(({theme:e})=>({padding:e.spacing(1,1),marginTop:"-10px"})),B=(0,p.ZP)(s.Z)(({theme:e})=>({zIndex:1e3,width:"400px",maxHeight:"300px",overflowY:"auto",[e.breakpoints.down("sm")]:{width:"100%"}})),H=(0,p.ZP)("div")(({theme:e})=>({position:"absolute",top:"50%",right:0,transform:"translate(-14px, -50%)",cursor:"pointer",color:e.palette.grey.A700})),M=(0,p.ZP)("div")(({theme:e})=>({display:"block",position:"relative","& .map":{backgroundColor:e.palette.action.focus,height:"100%",width:"100%",position:"absolute",top:0,left:0},"& .gm-ui-hover-effect":{background:"white !important",width:"20px !important",height:"20px !important",right:"2px !important"},"& .gm-style-iw + button":{display:"none"}}));var F=(0,o.j4Z)({extendBlock:function({moduleName:e}){let t=(0,o.dD4)(),a=v.useRef(),[i,s]=v.useState(""),{getSetting:p,usePageParams:u,navigate:h,dispatch:F,assetUrl:A,i18n:I}=(0,o.OgA)(),[O]=(0,v.useState)(["places"]),T=u(),{appName:D}=T,L=(0,k.v9)(e=>f()(e,`${D}.${D}Active`)),U=(0,o.oHF)(D,D,"viewEventsMap"),V=(0,x.Su)(T,U.apiRules),{search:W}=(0,w.TH)(),N=(0,v.useMemo)(()=>new URLSearchParams(W),[W]),j=v.useRef(),K=v.useRef(),Q=(0,k.v9)(e=>(0,o.Drc)(e,`${U.apiUrl}?${y.stringify(V)}`)),q=(0,k.v9)(e=>{return(0,o.Flc)(e,null==Q?void 0:Q.ids)}),G=p("core.google.google_map_api_key"),{isLoaded:J}=(0,E.Db)({googleMapsApiKey:G,libraries:O}),[Y,X]=v.useState([]),[ee,et]=v.useState({loading:!1,error:!1}),[ea,eo]=v.useState(!1),en=(0,v.useMemo)(()=>parseInt(N.get(S.HV))||15,[N.get(S.HV)]),el=e=>{let t=e.address.search(","),a={address:e.address.slice(0,t)===e.name?`${e.address}`:`${e.name}, ${e.address}`,lat:e.lat,lng:e.lng};eo(!1),s(a.address),ei({lat:e.lat,lng:e.lng})},[er,ei]=(0,v.useState)({lat:(parseFloat(N.get(S.UH))+parseFloat(N.get(S.lQ)))/2||0,lng:(parseFloat(N.get(S.Ms))+parseFloat(N.get(S.P5)))/2||0}),[es,ec]=(0,v.useState)(null),ep=v.useCallback(e=>{ec(e),K.current=new google.maps.Geocoder,K.current.geocode({location:er}).then(e=>{e.results[0]?s(e.results[0].formatted_address):s("")}),N.get(S.UH)||navigator.geolocation.getCurrentPosition(e=>{ei({lat:e.coords.latitude,lng:e.coords.longitude})},()=>{}),j.current=new google.maps.places.PlacesService(e)},[]),eg=(0,v.useRef)(null),ed=(0,o.zgj)(eg).style,eu=e=>{s(e),eo(!0)},em=v.useCallback(e=>{j.current&&(null==e?void 0:e.query)&&(et(e=>({...e,loading:!0})),j.current.textSearch(e,(e,t)=>{"OK"===t&&(X(e.map(e=>({icon:e.icon,name:e.name,address:e.formatted_address,lat:e.geometry.location.lat(),lng:e.geometry.location.lng()}))),et(e=>({...e,loading:!1})))}))},[i]);v.useEffect(()=>{em({query:i})},[i]),(0,v.useEffect)(()=>{i&&eh()},[er]);let eh=()=>{if(J&&es){let e=es.getBounds().getSouthWest().lng(),t=es.getBounds().getNorthEast().lng(),a=es.getBounds().getSouthWest().lat(),o=es.getBounds().getNorthEast().lat();N.set(S.P5,e),N.set(S.Ms,t),N.set(S.UH,a),N.set(S.lQ,o),N.set(S.HV,es.getZoom()),h({search:N.toString()})}},ef=()=>{setTimeout(()=>{eh()},1e3)},ev=e=>{let t=document.getElementById(e);t.scrollIntoView(),e!==L&&F({type:`${D}/hover`,payload:e})},ew=()=>{W&&setTimeout(()=>{eh()},1e3)},ex=()=>{F({type:`${D}/hover`,payload:""})};if((0,v.useEffect)(()=>{return()=>{eg.current=null}},[]),!J)return;let eE={url:A(`${D}.map_marker_hover`),scaledSize:new google.maps.Size(35,35)},eb={url:A(`${D}.map_marker`),scaledSize:new google.maps.Size(30,30)},ek=()=>{navigator.geolocation.getCurrentPosition(e=>{ei({lat:e.coords.latitude,lng:e.coords.longitude})},()=>{})};return v.createElement(b.gO,null,v.createElement(M,{"data-testid":"itemCategory"},v.createElement("div",{ref:eg,style:{...ed,...t?{top:180}:{}}},J?v.createElement(E.b6,{mapContainerStyle:{},center:er,zoom:en,onLoad:ep,mapContainerClassName:"map",onDragEnd:ef,onZoomChanged:ew,options:{gestureHandling:"cooperative"}},q.map(({id:e,title:t,location:a})=>{return v.createElement(E.Jx,{icon:L===`${D}Active${e}`?eE:eb,key:e,position:{lat:parseFloat(null==a?void 0:a.lat),lng:parseFloat(null==a?void 0:a.lng)},onClick:()=>ev(`${D}Active${e}`)},L===`${D}Active${e}`?v.createElement(E.nx,{onCloseClick:()=>ex()},v.createElement(Z,null,t)):null)})):null,v.createElement(C,null,v.createElement(g.Z,{sx:{width:"100%"},inputRef:a,onChange:e=>eu(e.target.value),value:i,inputProps:{"data-testid":m()("field")},placeholder:"Search by location..."}),v.createElement(d.Z,{title:I.formatMessage({id:"your_location"}),placement:"bottom"},v.createElement(H,{onClick:ek},v.createElement(n.zb,{icon:"ico-target-o"}))))),v.createElement(B,{open:ea,anchorEl:a.current,placement:"bottom-start"},v.createElement(z,null,v.createElement(r.Z,{in:ea},ee.loading?v.createElement(l.Z,null,v.createElement(c.Z,{animation:"wave",height:30,width:"70%"}),v.createElement(c.Z,{animation:"wave",height:30,width:"70%"}),v.createElement(c.Z,{animation:"wave",height:30,width:"70%"})):Y.map(e=>{return v.createElement(_,{key:e.lat,onClick:()=>el(e)},v.createElement(R,{icon:"ico-checkin"}),v.createElement($,null,e.name),v.createElement(P,null,e.address))}))))))}})}}]);