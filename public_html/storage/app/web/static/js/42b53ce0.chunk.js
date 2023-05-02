"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-photo-components-UploadMultiPhoto-UploadMultiAlbumPhotoField"],{90798:function(e,t,o){o.d(t,{Z:function(){return a}});var l=o(67294),i=o(13218),r=o.n(i);let n=e=>{return e?r()(e)?n(Object.values(e)[0]):e.toString():null};function a({error:e,className:t="invalid-feedback order-last"}){return e?l.createElement("div",{"data-testid":"error",className:t},n(e)):null}},97252:function(e,t,o){var l=o(13478),i=o(81719),r=o(67294);let n=(0,i.ZP)("div",{name:"MultipleUploadField",slot:"PreviewImg",shouldForwardProp:e=>"radio"!==e})(({theme:e,radio:t})=>({position:"absolute",left:0,top:0,right:0,bottom:0,backgroundRepeat:"no-repeat",backgroundPosition:"center",backgroundSize:"auto 100%",maxWidth:"100%",...t&&"horizontal"===t&&{backgroundSize:"cover"}})),a=({item:e})=>{let[t,o]=r.useState({}),i=(e,t)=>{try{let o=new Image;o.src=t,o.onload=()=>{e({height:o.height,width:o.width})},o.onerror=e=>{}}catch(l){}},a=(null==e?void 0:e.source)||e.image_url||(0,l.Q4)(null==e?void 0:e.image,"1024");r.useEffect(()=>{i(o,a)},[]);let d="horizontal";return d=t&&t.width/t.height<=1?"vertical":"horizontal",r.createElement(n,{radio:d,style:{backgroundImage:`url(${a})`}})};t.Z=a},13352:function(e,t,o){o.r(t),o.d(t,{default:function(){return M}});var l=o(90798),i=o(85597),r=o(76224),n=o(13478),a=o(21822),d=o(17888),s=o(30030),u=o(38790),c=o(91647),m=o(81719),p=o(18948),h=o(68929),g=o.n(h),f=o(41609),v=o.n(f),b=o(67294),x=o(97252),y=o(2597);let E=(0,m.ZP)("div",{name:"MultipleUploadField",slot:"Control",shouldForwardProp:e=>"haveError"!==e})(({theme:e,haveError:t})=>({height:120,borderRadius:4,display:"flex",alignItems:"center",justifyContent:"center",border:e.mixins.border("secondary"),borderColor:"light"===e.palette.mode?"#0000003b":"rgba(255, 255, 255, 0.23)","& .ico-photos-plus-o":{fontSize:`${e.mixins.pxToRem(15)} !important`},"&:hover":{borderColor:"light"===e.palette.mode?"#000":"#fff"},"& button":{[e.breakpoints.down("sm")]:{flexDirection:"column",height:"auto",maxWidth:"calc(100% - 40px)",padding:"10px"}},...t&&{borderColor:`${e.palette.error.main} !important`}})),z=(0,m.ZP)(a.Z,{name:"DropButton",slot:"DropButton"})(({theme:e,isOver:t})=>({...t&&{backgroundColor:e.palette.action.hover}})),k=(0,m.ZP)("div",{name:"MultipleUploadField",slot:"PreviewItem"})(({theme:e})=>({width:"100%",paddingBottom:"56%",borderRadius:8,background:e.palette.grey.A700,position:"relative",overflow:"hidden"})),P=(0,m.ZP)("video",{name:"MultipleUploadField",slot:"PreviewVideo"})({position:"absolute",left:0,top:0,right:0,bottom:0,backgroundRepeat:"no-repeat",backgroundPosition:"center",maxWidth:"100%"}),_=(0,m.ZP)("div",{name:"MultipleUploadField",slot:"removeBtn"})(({theme:e})=>({width:e.spacing(3),height:e.spacing(3),borderRadius:e.spacing(1.5),backgroundColor:"rgba(0,0,0,0.89)",color:"#fff",position:"absolute",top:e.spacing(2),right:e.spacing(2),display:"flex",alignItems:"center",justifyContent:"center",cursor:"pointer"})),Z=(0,m.ZP)("div",{name:"MultipleUploadField",slot:"AddMoreBtnWrapper"})(({theme:e})=>({display:"flex",alignItems:"center",justifyContent:"center",border:e.mixins.border("primary"),borderRadius:e.shape.borderRadius,height:"100%"})),w=(0,m.ZP)("div",{name:"MultipleUploadField",slot:"PreviewVideoWrapper"})(({theme:e})=>({display:"flex",alignItems:"center",justifyContent:"center",height:"100%"})),C=(0,m.ZP)("div",{name:"MultipleUploadField",slot:"MaskPlay"})(({theme:e})=>({position:"absolute",width:e.spacing(5),height:e.spacing(5),color:"#fff",backgroundColor:"rgba(0,0,0,0.4)",borderRadius:"50%",left:"50%",top:"50%",marginLeft:e.spacing(-2.5),marginTop:e.spacing(-2.5),display:"flex",alignItems:"center",justifyContent:"center",fontSize:e.mixins.pxToRem(24)}));function M({name:e,formik:t,config:o}){var a;let{upload_url:m,max_upload_filesize:h,allowUploadItems:f=!0,allowRemoveItems:M=!0}=o,{i18n:I,dialogBackend:F}=(0,i.OgA)(),[,,{setValue:S}]=(0,p.U$)("fileItemType"),[U,R,{setValue:j,setTouched:A}]=(0,p.U$)(null!=e?e:"ItemPhotoField"),[W,B,D]=(0,y.Z)({initialValues:U.value||[],upload_url:m,maxSizeLimit:h}),O=o.placeholder||"add_photo",$=(e,t)=>{let o=e.filter(e=>e.uid!==t.uid||e.id!==t.id);B(o)};b.useEffect(()=>{v()(W)&&S(void 0),R.touched||A(!0),j(W)},[W]);let T=e=>{e.length&&D(e)},V=(0,b.useCallback)(()=>{F.present({component:"photo.dialog.ChooseAlbumItemDialog",props:{config:o,fileItems:W,setFileItems:B,formik:t}})},[W]),L=Boolean(R.error&&(R.touched||t.submitCount)),K=f;return W.length&&(K=!0),b.createElement(b.Fragment,null,K?b.createElement(d.Z,{error:L,fullWidth:!0,margin:"normal"},b.createElement(c.Z,{sx:{fontSize:"13px"},color:"text.hint",mb:1},o.label),(null===(a=U.value)||void 0===a?void 0:a.length)?null:b.createElement(r.Kb,{onDrop:e=>T(e),render:({canDrop:t,isOver:o})=>b.createElement(E,{haveError:L,role:"button",onClick:V},b.createElement(z,{size:"small",color:"primary",isOver:o,variant:"outlined","data-testid":g()(`field ${e}`),startIcon:b.createElement(r.zb,{icon:"ico-photos-plus-o"})},I.formatMessage({id:O})))})):null,b.createElement(s.ZP,{container:!0,columnSpacing:1,rowSpacing:1},f&&W.length?b.createElement(s.ZP,{item:!0,sm:6,md:3,xs:6},b.createElement(r.Kb,{style:{height:"100%"},onDrop:e=>T(e),render:({canDrop:e,isOver:t})=>{return b.createElement(Z,null,b.createElement(z,{size:"large",color:"primary",isOver:t,startIcon:b.createElement(r.zb,{icon:"ico-photos-plus-o"}),sx:{fontWeight:"bold"},onClick:V},null==o?void 0:o.label))}})):null,null==W?void 0:W.map((e,t)=>{var o;return b.createElement(s.ZP,{item:!0,key:t,sm:6,md:3,xs:6},b.createElement(k,null,(0,n.yh)(null==e?void 0:null===(o=e.file)||void 0===o?void 0:o.type)?b.createElement(w,null,b.createElement(P,{src:null==e?void 0:e.source,controls:!1}),b.createElement(C,null,b.createElement(r.zb,{icon:"ico-play"}))):b.createElement(x.Z,{item:e}),M?b.createElement(u.Z,{title:I.formatMessage({id:"remove"})},b.createElement(_,{onClick:()=>$(W,e)},b.createElement(r.zb,{icon:"ico-close"}))):null))})),L?b.createElement(l.Z,{error:R.error}):null)}},2597:function(e,t,o){o.d(t,{Z:function(){return u}});var l=o(85597),i=o(67294),r=o(41609),n=o.n(r),a=o(73955),d=o.n(a),s=o(13478);function u({initialValues:e,upload_url:t="",maxSizeLimit:o,isAcceptVideo:r=!0,messageAcceptFail:a}){let{dialogBackend:u,i18n:c}=(0,l.OgA)(),m=i.useRef(!0),[p,h]=i.useState([]),[g,f]=i.useState(e||[]),v=(null==o?void 0:o.photo)||(null==o?void 0:o.other)||0,b=(null==o?void 0:o.video)||(null==o?void 0:o.other)||0,x=e=>{let o=[],l=[];for(let i=0;i<e.length;++i){let n=e[i],m=n.size,p=(0,s.yh)(null==n?void 0:n.type)?b:v,h={id:0,uid:d()(),source:URL.createObjectURL(n),file_name:n.name,file_size:n.size,file_type:n.type,file:n,upload_url:t,type:n.type.match("image/*")?"photo":"video",status:"create"};if("video"===h.type&&!r){u.alert({message:a});break}m>p&&0!==p?(h.max_size=p,l.push(h)):o.push(h)}if(o.length&&f(e=>[...e||[],...o]),l.length>0){var g;u.alert({message:1===l.length?c.formatMessage({id:"warning_upload_limit_one_file"},{fileName:(0,s.TX)(l[0].file_name,30),fileSize:(0,s.jA)(l[0].file_size),maxSize:(0,s.jA)(null===(g=l[0])||void 0===g?void 0:g.max_size)}):c.formatMessage({id:"warning_upload_limit_multi_file"},{numberFile:l.length,photoMaxSize:(0,s.jA)(v),videoMaxSize:(0,s.jA)(b)})})}};return(0,i.useEffect)(()=>{if(m.current=!0,!n()(p))return x(p),()=>{m.current=!1}},[p]),[g,f,h]}}}]);