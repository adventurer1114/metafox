"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-feed-blocks-StatusComposer-Block"],{14527:function(e,t,a){a.d(t,{Z:function(){return _}});var n=a(52886),r=a(85597),i=a(21241),o=a(41547),l=a(13478),s=a(30120),p=a(81719),d=a(41609),c=a.n(d),m=a(67294),u=a(18974),f=a(74996),g=a(50130);function v(){return(v=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}let h=(0,p.ZP)(g.Z,{name:"StatusControl",slot:"Control"})(({theme:e})=>({width:32,height:32,fontSize:e.mixins.pxToRem(20),[e.breakpoints.up("sm")]:{width:40,height:40}}));function b({icon:e,...t}){return m.createElement(h,v({color:"primary"},t),m.createElement(o.zb,{icon:e}))}let y="block",x=(0,p.ZP)("div",{name:"AvatarWrapper"})(({theme:e})=>({marginRight:e.spacing(1.5)})),k=(0,p.ZP)("div",{name:"ComposerWrapper"})(({theme:e})=>({display:"flex",width:"100%",[e.breakpoints.down("sm")]:{display:"block",width:"100%"}})),C=(0,p.ZP)("div",{name:"ComposerInput"})(({theme:e})=>({flex:1,backgroundColor:e.palette.action.hover,height:e.spacing(6),borderRadius:24,padding:e.spacing(0,3),cursor:"pointer",color:e.palette.text.secondary,fontSize:e.mixins.pxToRem(15),fontWeight:e.typography.fontWeightRegular,letterSpacing:0,WebkitBoxOrient:"vertical",WebkitLineClamp:"1",display:"-webkit-box",overflow:"hidden",textOverflow:"ellipsis",lineHeight:e.mixins.pxToRem(48),[e.breakpoints.down("sm")]:{height:e.spacing(4),lineHeight:e.mixins.pxToRem(32),padding:e.spacing(0,2)}})),E=(0,p.ZP)("div",{name:"ComposerToolbar"})(({theme:e})=>({display:"flex",marginTop:e.spacing(1),marginLeft:e.spacing(1.5),[e.breakpoints.down("sm")]:{marginLeft:e.spacing(0)}})),w=(0,p.ZP)("div",{name:"ComposerToolbarExpand"})(({theme:e})=>{var t;return{display:"flex",borderTop:"solid 1px",borderTopColor:null===(t=e.palette.border)||void 0===t?void 0:t.secondary,marginTop:e.spacing(2),marginLeft:e.spacing(8),paddingTop:e.spacing(1)}});function _({item:e,title:t,variant:a,blockProps:p,showWhen:d}){var g,v;let h;let[_,,Z]=(0,f.Z)(),{i18n:T,useSession:S,dispatch:P,jsxBackend:W,usePageParams:O,getAcl:R,getSetting:z,useIsMobile:j}=(0,r.OgA)(),A=R(),H=R("activity.feed.create"),U=z(),{user:I,loggedIn:L}=S(),B=O(),D=T.formatMessage({id:"what_s_your_mind"},{user:null==I?void 0:I.first_name}),M=(0,r.z88)("formValues.dialogStatusComposer")||D,{identity:Y,item_type:$}=B,q=Y?Y.split(".")[3]:"",V="user"===$&&q&&(null==I?void 0:I.id)!==parseInt(q);m.useEffect(()=>{L&&P({type:"setting/sharingItemPrivacy/FETCH",payload:{id:I.id}})},[L]),Z.current.requestComposerUpdate=m.useCallback(()=>{setImmediate(()=>{let{attachmentType:e,attachments:t}=Z.current.state;P({type:"statusComposer/onPress/status",payload:{data:{attachmentType:e,attachments:{[e]:t[e]}},parentIdentity:Y,parentType:$}})})},[Z,P,Y,$]);let F=(0,n.q)(Y,$);F&&(null==e?void 0:e.privacy_detail)&&(h={privacy_detail:e.privacy_detail});let G=()=>{P({type:"statusComposer/onPress/status",payload:{parentIdentity:Y,parentType:$,data:h}})},J=()=>{Z.current.removeAttachments()},K=m.useMemo(()=>({strategy:y,acl:A,setting:U,isUserProfileOther:V,item:e,parentType:$}),[A,U,V,e,$]),N=(0,l.W$)(u.Z.attachers,K),Q=j();if(c()(I)||!H||(null==e?void 0:null===(g=e.profile_settings)||void 0===g?void 0:g.profile_view_profile)===!1||(null==e?void 0:null===(v=e.profile_settings)||void 0===v?void 0:v.feed_share_on_wall)===!1)return null;let X=!!(0,l.W$)([{showWhen:d}],{item:e}).length;return X?"expanded"===a?m.createElement(i.gO,{testid:"blockStatusComposer"},m.createElement(i.ti,{title:t}),m.createElement(i.sU,null,m.createElement(s.Z,{display:"flex",flexDirection:"row"},m.createElement(x,null,m.createElement(o.Yt,{user:I,size:Q?32:48,"data-testid":"userAvatar"})),m.createElement(C,{"data-testid":"whatsHappening",color:"info",onClick:G},M)),m.createElement(w,{onClick:J},N.map(t=>W.render({component:t.as,props:{key:t.as,strategy:y,composerRef:Z,composerState:_,control:b,subject:e}}))))):m.createElement(i.gO,{testid:"blockStatusComposer"},m.createElement(i.ti,{title:t}),m.createElement(i.sU,null,m.createElement(s.Z,{display:"flex",flexDirection:"row"},m.createElement(x,null,m.createElement(o.Yt,{user:I,size:Q?32:48,"data-testid":"userAvatar"})),m.createElement(k,null,m.createElement(C,{"data-testid":"whatsHappening",onClick:G},M),m.createElement(E,{onClick:J},N.map(t=>W.render({component:t.as,props:{key:t.as,strategy:y,composerRef:Z,composerState:_,control:b,subject:e}}))))))):null}},48501:function(e,t,a){a.r(t);var n=a(85597),r=a(14527);let i=(0,n.Uh$)((0,n.YUM)(r.Z,()=>{}));t.default=(0,n.j4Z)({extendBlock:i,name:"StatusComposer",defaults:{variant:"default",title:"Status Composer"},custom:{variant:{name:"variant",component:"Select",label:"Variant",fullWidth:!0,margin:"normal",variant:"outlined",options:[{label:"Default",value:"default"},{label:"Expanded",value:"expanded"}]}}})}}]);