"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-photo-components-StatusComposerControlAttatchedPhotos-PreviewPhotos"],{17080:function(e,t,o){o.r(t),o.d(t,{default:function(){return k}});var a=o(85597),i=o(16473),l=o(27274),n=o(21822),r=o(50130),s=o(30120),c=o(86010),d=o(27361),m=o.n(d),p=o(67294),u=o(79924),h=o(22410),v=o(73327);let g=(0,h.Z)(e=>(0,v.Z)({root:{display:"block",position:"relative",overflow:"hidden",margin:e.spacing(1.5)},listContainer:{position:"relative",display:"flex",flexWrap:"wrap"},itemRoot:{position:"relative",display:"flex",flexBasis:"50%",padding:e.spacing(.5)},item0:{},item1:{},item2:{},item3:{},item4:{},videoItem:{width:"100%",maxWidth:"100%",borderRadius:e.spacing(1)},item:{display:"block",padding:e.spacing(.25)},preset1:{"& $item0":{flexBasis:"100%"}},preset2:{},preset3:{"& $item0":{flexBasis:"100%"}},preset4:{},removeBtn:{position:"absolute !important",top:e.spacing(2),right:e.spacing(2),zIndex:1,opacity:.9},remainBackdrop:{position:"absolute",left:0,right:0,top:0,bottom:0,backgroundColor:"rgba(0,0,0,0.3)",borderRadius:e.shape.borderRadius,"&:hover":{backgroundColor:"rgba(0,0,0,0.1)"}},remainText:{color:"white",position:"absolute",left:"50%",top:"50%",fontSize:"2rem",transform:"translate(-50%,-50%)"},actionBar:{position:"absolute",top:0,right:0,left:0,display:"flex",flexDirection:"row",padding:e.spacing(2),justifyContent:"space-between"},buttonGroup:{"& > *":{marginRight:`${e.spacing(1)} !important`},[e.breakpoints.down("sm")]:{display:"flex",flexDirection:"column",margin:e.spacing(-1.5,-1,-1),"& button":{marginTop:e.spacing(.5)}}}}),{name:"PhotoPreviews"});var f=o(12902);let b=({acl:e})=>{let t=[];return m()(e,"photo.photo.create")&&t.push("image/*"),m()(e,"video.video.create")&&t.push("video/*"),t.join(", ")},C=({acl:e})=>{let t=m()(e,"photo.photo.create"),o=m()(e,"video.video.create");return t&&o?"add_photos_video":o?"add_videos":"add_photos"};function k({composerRef:e,isEdit:t}){let o=g(),{i18n:d,dialogBackend:h,getAcl:v}=(0,a.OgA)(),k=p.useRef(),[E,x]=(0,u.Z)(e,k),w=m()(e.current.state,"attachments.photo.value"),y=m()(e.current.state,"extra"),_=v(),P=p.useCallback((t,o)=>{let a=(0,f.ZP)(w,e=>{let a=e.find(e=>{return(null==t?void 0:t.uid)&&(null==e?void 0:e.uid)===t.uid||(null==t?void 0:t.id)&&(null==e?void 0:e.id)===(null==t?void 0:t.id)});a&&Object.assign(a,o)});e.current.setAttachments("photo","photo",{as:"StatusComposerControlAttachedPhotos",value:a})},[w,e]),z=p.useCallback((t,o)=>{let a=t.filter(e=>e.uid!==o.uid||e.id!==o.id);a.length>0?e.current.setAttachments("photo","photo",{as:"StatusComposerControlAttachedPhotos",value:a}):e.current.removeAttachments()},[e]),N=p.useCallback(()=>{h.present({component:"photo.dialog.EditPreviewPhotosDialog",props:{composerRef:e}})},[e,h]),B=p.useCallback(()=>{h.present({component:"photo.dialog.EditPreviewPhotoDialog",props:{item:w[0],hideTextField:!0}}).then(e=>{e&&P(w[0],e)})},[h,w,P]),Z=b({acl:_}),A=C({acl:_}),R=(null==w?void 0:w.length)||0;if(!R)return null;let I=Math.min(R,4)%5,$=R-I;return p.createElement("div",{className:(0,c.default)(o.root),"data-testid":"previewAttachPhoto"},p.createElement("div",{className:(0,c.default)(o.listContainer,o[`preset${I}`])},R?w.slice(0,I).map((e,a)=>{var n;return p.createElement("div",{className:(0,c.default)(o.itemRoot,o[`item${a}`]),key:a.toString()},p.createElement(s.Z,{sx:{position:"relative",width:"100%"}},(0,l.yh)(null===(n=e.file)||void 0===n?void 0:n.type)?p.createElement("video",{src:e.source,draggable:!1,controls:!1,autoPlay:!1,muted:!0,className:o.videoItem}):p.createElement(i.Gy,{draggable:!1,src:e.base64||e.source||(0,l.Q4)(null==e?void 0:e.image),aspectRatio:"169",shape:"radius"}),0<$&&I===a+1?p.createElement("div",{className:o.remainBackdrop},p.createElement("div",{className:o.remainText},`+ ${$}`)):!t||(null==y?void 0:y.can_edit_feed_item)&&(null==w?void 0:w.length)>1?p.createElement(r.Z,{size:"smallest",onClick:()=>z(w,e),variant:"blacked",className:o.removeBtn,title:d.formatMessage({id:"remove"})},p.createElement(i.zb,{icon:"ico-close"})):null))}):null),p.createElement("div",{className:o.actionBar},!t||(null==y?void 0:y.can_edit_feed_item)?p.createElement("div",{className:o.buttonGroup},1===R?p.createElement(n.Z,{variant:"contained",size:"smaller",color:"default",onClick:B,startIcon:p.createElement(i.zb,{icon:"ico-pencil"})},d.formatMessage({id:"edit"})):p.createElement(n.Z,{variant:"contained",size:"smaller",color:"default",onClick:N,startIcon:p.createElement(i.zb,{icon:"ico-pencil"})},d.formatMessage({id:"edit_all"})),p.createElement(n.Z,{variant:"contained",size:"smaller",color:"default",onClick:x,startIcon:p.createElement(i.zb,{icon:"ico-plus"})},d.formatMessage({id:A}))):null,p.createElement("input",{type:"file",className:"srOnly",ref:k,onChange:E,multiple:!0,accept:Z})))}}}]);