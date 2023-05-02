"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-group-blocks-Announcement-Block"],{94609:function(e,t,n){n.r(t),n.d(t,{default:function(){return h}});var l=n(85597),r=n(21241),a=n(77029),o=n(50130),i=n(81719),c=n(67294),u=n(46066);n(16651),n(91259);var m=n(86706);function s(){return(s=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var l in n)Object.prototype.hasOwnProperty.call(n,l)&&(e[l]=n[l])}return e}).apply(this,arguments)}let d=(0,i.ZP)("div",{name:"NavButtonWrapper",shouldForwardProp:e=>"themeId"!==e})(({theme:e,themeId:t})=>({color:e.palette.primary.main,display:"flex",alignItems:"center","& > span":{fontSize:e.mixins.pxToRem(18),cursor:"pointer","&:hover":{color:e.palette.primary.dark}}})),p=(0,i.ZP)("div",{name:"SlideCount"})(({theme:e})=>({fontSize:e.mixins.pxToRem(15),color:e.palette.text.secondary,marginLeft:e.spacing(1.5),marginRight:e.spacing(1.5)})),g=(0,i.ZP)("span",{shouldForwardProp:e=>"isRead"!==e})(({theme:e,isRead:t})=>({right:e.spacing(2),bottom:e.spacing(2),position:"absolute",color:e.palette.primary.main,borderBottom:"solid 1px transparent",userSelect:"none","&:hover":{borderBottom:`solid 1px ${e.palette.primary.main}`,cursor:"pointer"},...t&&{color:e.palette.grey["400"],"&:hover":{}}})),f=(0,i.ZP)(r.sU)(({theme:e})=>({"& .slick-list":{marginBottom:e.spacing(2.5)}}));var h=(0,l.j4Z)({extendBlock:function({title:e}){let t=c.useRef(),{jsxBackend:n,usePageParams:i,useGetItems:h}=(0,l.OgA)(),[v,y]=c.useState(!0),{i18n:k,dispatch:E}=(0,l.OgA)(),[_,b]=c.useState(0),w=i(),x=`groupAnnouncement/${w.id}`,A=(0,m.v9)(e=>(0,l.Drc)(e,x))||(0,l.rjd)(),{loading:C,pagesOffset:S,dirty:O,ids:P}=null!=A?A:{},R=h(P);c.useEffect(()=>{if(E({type:"group-announcement/list"}),O){E({type:l._Mp,payload:{pagingId:x}});return}},[O]);let Z=e=>{0!==e&&t.current.click()},z=n.get("group_announcement.itemView.mainCard"),B=n.get("group_announcement.itemView.mainCard.skeleton"),M=R[_],T=()=>{null!=M&&M.is_marked_read||E({type:"group-announcement/markAsRead",payload:M._identity})},j=e=>{let{onClick:t,currentSlide:n}=e,{usePreference:r}=(0,l.OgA)(),{themeId:o}=r();return c.createElement(d,{themeId:o},c.createElement(a.zb,{icon:"ico-angle-left",onClick:()=>Z(n)}),c.createElement(p,null,n+1,"/",null==R?void 0:R.length),c.createElement(a.zb,{icon:"ico-angle-right",onClick:t}))},L=e=>{let{onClick:n}=e;return c.createElement("div",{style:{display:"none"},onClick:n,ref:t})},I=(e,t)=>{b(t),!C&&(null==S?void 0:S.total)>(null==R?void 0:R.length)&&(null==S?void 0:S.total)-t<2&&E({type:"group-announcement/list"})},$={dots:!1,infinite:!0,speed:500,slidesToShow:1,slidesToScroll:1,useTransform:!1,adaptiveHeight:!0,nextArrow:c.createElement(j,null),prevArrow:c.createElement(L,null),beforeChange:I},F=c.useCallback(()=>y(e=>!e),[]);return C&&!R.length?c.createElement(r.gO,null,c.createElement(r.ti,null,c.createElement(r.bi,null,k.formatMessage({id:e}))),c.createElement(r.sU,null,c.createElement(B,null))):R.length?c.createElement(r.gO,null,c.createElement(r.ti,null,c.createElement(r.bi,null,k.formatMessage({id:e})),c.createElement(o.Z,{size:"small",color:"default",onClick:F},c.createElement(a.zb,{icon:v?"ico-angle-up":"ico-angle-down"}))),v?c.createElement(f,null,c.createElement(u.Z,s({},$),R.length&&R.map(e=>c.createElement(z,{identity:`group.entities.group_announcement.${e.id}`,key:e.id}))),c.createElement(g,{isRead:null==M?void 0:M.is_marked_read,onClick:T},(null==M?void 0:M.is_marked_read)?k.formatMessage({id:"i_have_read_this"}):k.formatMessage({id:"mark_as_read"}))):null):null},name:"AnnouncementListing",defaults:{gridLayout:"Announcement - Slider",itemLayout:"Announcement - Slider"},overrides:{showWhen:["truthy","acl.announcement.announcement.view"]}})}}]);