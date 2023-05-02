"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-event-components-EventItem-EmbedCard-index"],{7570:function(e,t,a){a.r(t),a.d(t,{default:function(){return k}});var n=a(36324),r=a(96338),i=a(85597),l=a(41547),o=a(27274),m=a(30120),c=a(81719),s=a(91647),d=a(18446),p=a.n(d),u=a(67294),g=a(24456),f=a(22410),v=a(73327),b=(0,f.Z)(e=>(0,v.Z)({item:{display:"block"},itemOuter:{display:"flex",borderRadius:"8px",border:e.mixins.border("secondary"),backgroundColor:e.mixins.backgroundColor("paper"),overflow:"hidden"},grid:{"& $itemOuter":{flexDirection:"column"},"& $media":{width:"100% !important",height:"200px !important"}},list:{"& $itemOuter":{flexDirection:"row","& $media":{width:"200px"}},"& $wrapperInfoFlag":{marginTop:"auto"}},media:{backgroundRepeat:"no-repeat",backgroundPosition:"center",backgroundSize:"cover"},title:{"& a":{color:e.palette.text.primary}},description:{color:e.palette.text.hint,"& p":{margin:0}},hostLink:{color:e.palette.text.secondary},subInfo:{textTransform:"uppercase"},itemInner:{flex:1,minWidth:0,padding:e.spacing(3),display:"flex",flexDirection:"column"},price:{fontWeight:e.typography.fontWeightBold,color:e.palette.warning.main},flagWrapper:{marginLeft:"auto"},highlightSubInfo:{textTransform:"uppercase"},actions:{marginRight:e.spacing(1.5)},wrapperInfoFlag:{}}),{name:"MuiFeedEventTemplate"});let E="EventEmbedItemView",h=(0,c.ZP)("div",{name:E,slot:"bgCover"})(({theme:e})=>({backgroundRepeat:"no-repeat",backgroundPosition:"center",backgroundSize:"cover",height:200,[e.breakpoints.down("sm")]:{height:160}})),x=(0,c.ZP)(s.Z,{name:E,slot:"TypographyStyled"})(({theme:e})=>({fontSize:e.spacing(1.625),fontWeight:"700",lineHeight:e.spacing(2.5)}));function y({item:e,handleAction:t,actions:a,identity:n}){var c;let d=b(),{i18n:f,useSession:v}=(0,i.OgA)(),{loggedIn:E}=v();if(!e)return null;let{title:y,location:k,image:w,link:I,statistic:Z,start_time:_,end_time:N,rsvp:C,is_online:S,is_featured:W}=e,T=(0,r.nd)(N);return u.createElement(l.gu,{variant:"grid"},w?u.createElement(i.rUS,{to:I},u.createElement(h,{style:{backgroundImage:`url(${(0,o.Q4)(w,"200")})`}})):null,u.createElement("div",{className:d.itemInner},u.createElement(m.Z,{mb:1.25,fontWeight:600,className:d.title},u.createElement(i.rUS,{to:I},u.createElement(l.Ys,{variant:"h4",lines:1},y))),u.createElement(m.Z,{mb:1.25},u.createElement(s.Z,{component:"div",variant:"body1",textTransform:"uppercase",color:"primary"},u.createElement(l.Ee,null,u.createElement(l.r2,{"data-testid":"startedDate",value:_,format:"LL"}),u.createElement(l.r2,{"data-testid":"startedDate",value:_,format:"LT"})))),S?u.createElement(m.Z,{className:d.description},u.createElement(l.Ys,{variant:"subtitle2",lines:1},f.formatMessage({id:"online"}))):u.createElement(m.Z,{className:d.description},u.createElement(l.Ys,{variant:"body1",lines:1},null==k?void 0:k.address)),u.createElement(m.Z,{className:d.wrapperInfoFlag,display:"flex",justifyContent:"space-between",alignItems:"center",mt:2},u.createElement(m.Z,{display:"flex",alignItems:"center"},E?u.createElement("div",{className:d.actions},u.createElement(g.Z,{disabled:T||p()(null===(c=e.extra)||void 0===c?void 0:c.can_rsvp,!1),actions:a,handleAction:t,identity:n,rsvp:C})):null,u.createElement(x,{variant:"body2",color:"text.hint"},(null==Z?void 0:Z.total_member)?u.createElement(u.Fragment,null,f.formatMessage({id:"people_going"},{value:Z.total_member})):null)),u.createElement("div",{className:d.flagWrapper},W?u.createElement(l.WN,{"data-testid":"featured",type:"is_featured",value:W}):null))))}y.LoadingSkeleton=()=>null,y.displayName="EventItem_EmbedCard";var k=(0,n.Y)(y,n.c)}}]);