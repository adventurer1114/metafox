"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-user-blocks-ProfileAlbumDetail-Block"],{87624:function(e,t,a){a.r(t),a.d(t,{default:function(){return y}});var i=a(85597),o=a(93052),n=a(90910),l=a(84116),r=a(21241),s=a(89773),m=a(41547),p=a(30120),c=a(81719),d=a(91647),g=a(67294),u=a(22410),b=a(73327);let h=(0,u.Z)(e=>(0,b.Z)({root:{borderRadius:e.shape.borderRadius,maxWidth:"100%",margin:"auto"},albumContent:{padding:e.spacing(2,2,1,2),position:"relative"},category:{fontSize:e.mixins.pxToRem(13),color:e.palette.primary.main,marginBottom:e.spacing(1.5),display:"inline-block"},title:{color:e.palette.text.primary,fontSize:e.mixins.pxToRem(24),fontWeight:e.typography.fontWeightBold},albumContainer:{},info:{color:e.palette.text.primary,fontSize:e.mixins.pxToRem(15),padding:e.spacing(1,0),"& p":{margin:e.spacing(1.5,0)}},profileLink:{color:e.palette.text.primary,fontSize:e.mixins.pxToRem(15),fontWeight:e.typography.fontWeightBold},owner:{overflow:"hidden",padding:e.spacing(1.5,0),display:"flex",alignItems:"center",width:"100%"},ownerInfo:{overflow:"hidden",flexGrow:1},ownerAvatar:{float:"left",marginRight:e.spacing(2)},date:{fontSize:e.mixins.pxToRem(13),color:e.palette.text.secondary,paddingTop:e.spacing(.5)},listingActions:{display:"inline-flex",marginTop:e.spacing(1),marginBottom:e.spacing(.5),[e.breakpoints.down("xs")]:{display:"flex"}},actionMenu:{border:"1px solid",width:"40px",height:"40px",borderRadius:e.spacing(.5),display:"flex",alignItems:"center",color:e.palette.primary.main,justifyContent:"center"},listingHeader:{display:"flex",[e.breakpoints.down("xs")]:{display:"block"}},listingComment:{marginTop:e.spacing(2),[e.breakpoints.down("xs")]:{padding:e.spacing(0,2)}},hasPhotos:{marginBottom:e.spacing(2)},actionsDropdown:{position:"absolute",top:e.spacing(1),right:e.spacing(1)},iconButton:{fontSize:e.mixins.pxToRem(13)},dropdownButton:{padding:e.spacing(1),width:30,height:30,textAlign:"center"}}),{name:"MuiUserPhotoViewDetail"}),f=(0,c.ZP)(p.Z,{name:"ProfileAlbumViewDetail"})(({theme:e})=>({backgroundColor:e.palette.background.paper,marginBottom:e.spacing(2),borderRadius:e.spacing(1)})),x=(0,i.Uh$)((0,o.Y)(function({item:e,user:t,identity:a,handleAction:o,state:p}){let c=h(),{jsxBackend:u,i18n:b,useFetchDetail:x,ItemDetailInteraction:y,dispatch:v,ItemActionMenu:k,useActionControl:w}=(0,i.OgA)(),E=u.get("photo.block.pinView"),P=(0,r.N6)(),{album_id:C}=P,{user:_={}}=(0,i.kPO)();g.useEffect(()=>{C&&v((0,i.RR$)(`photo-album/${C}`,{id:C}))},[v,P,C]);let R=`photo.entities.photo_album.${C}`,[S,A]=w(R,{}),[T,B,Z]=x({dataSource:{apiUrl:`photo-album/${C}`}}),$=(0,i.oHF)(s.T7,s.Gk,"getAlbumItems");if(!C)return null;let{apiMethod:N}=$||{};if(!e)return null;let V=`photo-album/${C}`,{is_featured:z,is_sponsor:U,name:D,text:I,extra:W}=Object.assign({},T);return g.createElement(r.gO,{testid:`detailview ${null==T?void 0:T.resource_name}`},g.createElement(r.sU,null,g.createElement(n.Z,{loading:B,error:Z,sx:{backgroundColor:"background.paper"}},g.createElement("div",{className:c.root},g.createElement(f,null,g.createElement("div",{className:c.albumContent},g.createElement("div",{className:c.actionsDropdown}),g.createElement("div",{className:c.albumContainer},g.createElement(i.rUS,{to:`/user/${e.id}/photo?stab=albums`,color:"primary",children:(null==_?void 0:_.id)===e.id?b.formatMessage({id:"my_albums"}):b.formatMessage({id:"user_s_albums"},{value:e.full_name}),className:c.category}),g.createElement(m.XQ,{variant:"h3",component:"div",showFull:!0},g.createElement(m.K6,{variant:"itemView",value:z}),g.createElement(m.k5,{variant:"itemView",value:U}),g.createElement(d.Z,{component:"h1",variant:"h3",sx:{display:{sm:"inline",xs:"block"},mt:{sm:0,xs:1},verticalAlign:"middle"}},D)),g.createElement(m.tQ,{sx:{position:"absolute",top:8,right:8}},g.createElement(k,{identity:R,state:A,handleAction:S,icon:"ico-dottedmore-vertical-o"})),I&&g.createElement("div",{className:c.info},g.createElement(m.oA,{truncateProps:{variant:"body1",lines:5}},g.createElement(l.ZP,{html:I})))),g.createElement(y,{identity:R,handleAction:o,hideListComment:!0}))),g.createElement(f,{pt:2},g.createElement(E,{title:"",numColumns:3,pagingId:V,dataSource:{apiUrl:"/photo-album/items/:album_id",apiMethod:N,apiParams:"sort=latest"},contentType:"photo_album",gridContainerProps:{spacing:1},emptyPage:"photo.block.EmptyPhotoAlbum",emptyPageProps:{isVisible:null==W?void 0:W.can_upload_media}}))))))},o.c,{tags:!0,categories:!0}));var y=(0,i.j4Z)({extendBlock:x,defaults:{blockProps:{variant:"plained",titleComponent:"h2",titleVariant:"subtitle1",titleColor:"textPrimary",noFooter:!0,noHeader:!0,blockStyle:{sx:{bgcolor:"background.default",pt:0,mt:2}},contentStyle:{},headerStyle:{},footerStyle:{}}}})}}]);