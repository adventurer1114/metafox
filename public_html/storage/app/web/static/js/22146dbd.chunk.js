"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-poll-blocks-PollDetail-Block"],{18037:function(e,t,a){a.d(t,{Z:function(){return Z}});var n=a(63366),r=a(87462),l=a(67294),o=a(86010),i=a(94780),s=a(59711),c=a(91647),d=a(36622),m=a(81719),p=a(78884),u=a(1588),g=a(34867);function v(e){return(0,g.Z)("MuiFormControlLabel",e)}let f=(0,u.Z)("MuiFormControlLabel",["root","labelPlacementStart","labelPlacementTop","labelPlacementBottom","disabled","label","error"]);var h=a(56594),b=a(85893);let w=["checked","className","componentsProps","control","disabled","disableTypography","inputRef","label","labelPlacement","name","onChange","value"],x=e=>{let{classes:t,disabled:a,labelPlacement:n,error:r}=e,l={root:["root",a&&"disabled",`labelPlacement${(0,d.Z)(n)}`,r&&"error"],label:["label",a&&"disabled"]};return(0,i.Z)(l,v,t)},y=(0,m.ZP)("label",{name:"MuiFormControlLabel",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[{[`& .${f.label}`]:t.label},t.root,t[`labelPlacement${(0,d.Z)(a.labelPlacement)}`]]}})(({theme:e,ownerState:t})=>(0,r.Z)({display:"inline-flex",alignItems:"center",cursor:"pointer",verticalAlign:"middle",WebkitTapHighlightColor:"transparent",marginLeft:-11,marginRight:16,[`&.${f.disabled}`]:{cursor:"default"}},"start"===t.labelPlacement&&{flexDirection:"row-reverse",marginLeft:16,marginRight:-11},"top"===t.labelPlacement&&{flexDirection:"column-reverse",marginLeft:16},"bottom"===t.labelPlacement&&{flexDirection:"column",marginLeft:16},{[`& .${f.label}`]:{[`&.${f.disabled}`]:{color:(e.vars||e).palette.text.disabled}}})),E=l.forwardRef(function(e,t){let a=(0,p.Z)({props:e,name:"MuiFormControlLabel"}),{className:i,componentsProps:d={},control:m,disabled:u,disableTypography:g,label:v,labelPlacement:f="end"}=a,E=(0,n.Z)(a,w),Z=(0,s.Z)(),_=u;void 0===_&&void 0!==m.props.disabled&&(_=m.props.disabled),void 0===_&&Z&&(_=Z.disabled);let C={disabled:_};["checked","name","onChange","value","inputRef"].forEach(e=>{void 0===m.props[e]&&void 0!==a[e]&&(C[e]=a[e])});let S=(0,h.Z)({props:a,muiFormControl:Z,states:["error"]}),k=(0,r.Z)({},a,{disabled:_,labelPlacement:f,error:S.error}),N=x(k),R=v;return null==R||R.type===c.Z||g||(R=(0,b.jsx)(c.Z,(0,r.Z)({component:"span",className:N.label},d.typography,{children:R}))),(0,b.jsxs)(y,(0,r.Z)({className:(0,o.default)(N.root,i),ownerState:k,ref:t},E,{children:[l.cloneElement(m,C),R]}))});var Z=E},52922:function(e,t,a){a.d(t,{Z:function(){return x}});var n=a(63366),r=a(87462),l=a(67294),o=a(86010),i=a(94780),s=a(81719),c=a(78884),d=a(1588),m=a(34867);function p(e){return(0,m.Z)("MuiFormGroup",e)}(0,d.Z)("MuiFormGroup",["root","row","error"]);var u=a(59711),g=a(56594),v=a(85893);let f=["className","row"],h=e=>{let{classes:t,row:a,error:n}=e;return(0,i.Z)({root:["root",a&&"row",n&&"error"]},p,t)},b=(0,s.ZP)("div",{name:"MuiFormGroup",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,a.row&&t.row]}})(({ownerState:e})=>(0,r.Z)({display:"flex",flexDirection:"column",flexWrap:"wrap"},e.row&&{flexDirection:"row"})),w=l.forwardRef(function(e,t){let a=(0,c.Z)({props:e,name:"MuiFormGroup"}),{className:l,row:i=!1}=a,s=(0,n.Z)(a,f),d=(0,u.Z)(),m=(0,g.Z)({props:a,muiFormControl:d,states:["error"]}),p=(0,r.Z)({},a,{row:i,error:m.error}),w=h(p);return(0,v.jsx)(b,(0,r.Z)({className:(0,o.default)(w.root,l),ownerState:p,ref:t},s))});var x=w},42853:function(e,t,a){a.d(t,{Z:function(){return Z}});var n,r=a(63366),l=a(87462),o=a(67294),i=a(86010),s=a(94780),c=a(56594),d=a(59711),m=a(81719),p=a(36622),u=a(1588),g=a(34867);function v(e){return(0,g.Z)("MuiFormHelperText",e)}let f=(0,u.Z)("MuiFormHelperText",["root","error","disabled","sizeSmall","sizeMedium","contained","focused","filled","required"]);var h=a(78884),b=a(85893);let w=["children","className","component","disabled","error","filled","focused","margin","required","variant"],x=e=>{let{classes:t,contained:a,size:n,disabled:r,error:l,filled:o,focused:i,required:c}=e,d={root:["root",r&&"disabled",l&&"error",n&&`size${(0,p.Z)(n)}`,a&&"contained",i&&"focused",o&&"filled",c&&"required"]};return(0,s.Z)(d,v,t)},y=(0,m.ZP)("p",{name:"MuiFormHelperText",slot:"Root",overridesResolver:(e,t)=>{let{ownerState:a}=e;return[t.root,a.size&&t[`size${(0,p.Z)(a.size)}`],a.contained&&t.contained,a.filled&&t.filled]}})(({theme:e,ownerState:t})=>(0,l.Z)({color:(e.vars||e).palette.text.secondary},e.typography.caption,{textAlign:"left",marginTop:3,marginRight:0,marginBottom:0,marginLeft:0,[`&.${f.disabled}`]:{color:(e.vars||e).palette.text.disabled},[`&.${f.error}`]:{color:(e.vars||e).palette.error.main}},"small"===t.size&&{marginTop:4},t.contained&&{marginLeft:14,marginRight:14})),E=o.forwardRef(function(e,t){let a=(0,h.Z)({props:e,name:"MuiFormHelperText"}),{children:o,className:s,component:m="p"}=a,p=(0,r.Z)(a,w),u=(0,d.Z)(),g=(0,c.Z)({props:a,muiFormControl:u,states:["variant","size","disabled","error","filled","focused","required"]}),v=(0,l.Z)({},a,{component:m,contained:"filled"===g.variant||"outlined"===g.variant,variant:g.variant,size:g.size,disabled:g.disabled,error:g.error,filled:g.filled,focused:g.focused,required:g.required}),f=x(v);return(0,b.jsx)(y,(0,l.Z)({as:m,ownerState:v,className:(0,i.default)(f.root,s),ref:t},p,{children:" "===o?n||(n=(0,b.jsx)("span",{className:"notranslate",children:"​"})):o}))});var Z=E},41470:function(e,t,a){var n=a(87462),r=a(63366),l=a(67294),o=a(52922),i=a(84771),s=a(42293),c=a(17361),d=a(49669),m=a(85893);let p=["actions","children","defaultValue","name","onChange","value"],u=l.forwardRef(function(e,t){let{actions:a,children:u,defaultValue:g,name:v,onChange:f,value:h}=e,b=(0,r.Z)(e,p),w=l.useRef(null),[x,y]=(0,s.Z)({controlled:h,default:g,name:"RadioGroup"});l.useImperativeHandle(a,()=>({focus:()=>{let e=w.current.querySelector("input:not(:disabled):checked");e||(e=w.current.querySelector("input:not(:disabled)")),e&&e.focus()}}),[]);let E=(0,i.Z)(t,w),Z=(0,d.Z)(v),_=l.useMemo(()=>({name:Z,onChange(e){y(e.target.value),f&&f(e,e.target.value)},value:x}),[Z,f,y,x]);return(0,m.jsx)(c.Z.Provider,{value:_,children:(0,m.jsx)(o.Z,(0,n.Z)({role:"radiogroup",ref:E},b,{children:u}))})});t.Z=u},35846:function(e,t,a){a.r(t),a.d(t,{default:function(){return k}});var n=a(85597),r=a(61019),l=a(84116),o=a(21241),i=a(76224),s=a(13478),c=a(30120),d=a(81719),m=a(91647),p=a(67294),u=a(22870),g=a(22410),v=a(73327);let f=(0,g.Z)(e=>(0,v.Z)({root:{backgroundColor:e.mixins.backgroundColor("paper"),[e.breakpoints.down("sm")]:{"& $bgCover":{height:179},"& $viewContainer":{borderRadius:0,marginTop:"0 !important"}}},bgCover:{backgroundRepeat:"no-repeat",backgroundPosition:"center",backgroundSize:"cover",height:320},hasBgCover:{},viewContainer:{width:"100%",backgroundColor:e.mixins.backgroundColor("paper"),padding:e.spacing(2),position:"relative"},actionMenu:{width:32,height:32,position:"absolute !important",top:e.spacing(-1),right:e.spacing(-1),"& .ico":{color:e.palette.text.secondary,fontSize:13},"& button:hover":{backgroundColor:e.palette.action.selected}},pendingNoticeWrapper:{padding:e.spacing(0,0,.5,0),marginBottom:e.spacing(2)},pendingNotice:{borderRadius:e.spacing(1),height:48,width:"auto",backgroundColor:e.palette.action.selected,display:"flex",alignItems:"center",padding:e.spacing(2),justifyContent:"space-between"},pendingTitle:{fontSize:e.mixins.pxToRem(15),color:e.palette.text.secondary},pendingAction:{display:"flex"},pendingButton:{fontSize:e.mixins.pxToRem(15),color:e.palette.primary.main,textTransform:"uppercase",marginLeft:e.spacing(2),cursor:"pointer",fontWeight:e.typography.fontWeightBold,"&:hover":{color:e.palette.primary.light}},contentWrapper:{position:"relative"},titleWrapper:{paddingRight:e.spacing(2)},itemFlag:{display:"inline-flex",margin:e.spacing(0,.5,0,-.5)},viewTitle:{fontSize:e.spacing(3),lineHeight:1,fontWeight:e.typography.fontWeightBold,display:"inline",verticalAlign:"middle"},author:{display:"flex",marginTop:e.spacing(2)},authorInfo:{marginLeft:e.spacing(1.2)},userName:{fontSize:15,fontWeight:"bold",color:e.palette.text.primary,display:"block"},date:{fontSize:13,color:e.palette.text.secondary,marginTop:e.spacing(.5)},itemContent:{fontSize:15,lineHeight:1.33,marginTop:e.spacing(3),"& p + p":{marginBottom:e.spacing(2.5)}},voteForm:{[e.breakpoints.up("sm")]:{maxWidth:320}},attachmentTitle:{fontSize:e.mixins.pxToRem(18),marginTop:e.spacing(4),color:e.palette.text.secondary,fontWeight:e.typography.fontWeightBold},attachment:{width:"100%",display:"flex",flexWrap:"wrap",marginTop:e.spacing(2),justifyContent:"space-between"},attachmentItem:{marginTop:e.spacing(2),flexGrow:0,flexShrink:0,flexBasis:"calc(50% - 8px)",minWidth:300}}),{name:"MuiPollViewDetail"});var h=a(86706),b=a(43847);let w="PollDetail",x=(0,d.ZP)("div",{name:w,slot:"ContentWrapper"})(({theme:e})=>({backgroundColor:e.mixins.backgroundColor("paper"),[e.breakpoints.down("sm")]:{"& $bgCover":{height:179},"& $viewContainer":{borderRadius:0,marginTop:"0 !important"}}})),y=(0,d.ZP)("div",{name:w,slot:"MessageWrapper"})(({theme:e})=>({borderRadius:8,height:e.spacing(6),width:"auto",backgroundColor:e.palette.action.hover,display:"flex",justifyContent:"space-between",alignItems:"center",padding:e.spacing(0,2),marginBottom:e.spacing(2)})),E=(0,d.ZP)("div",{name:w,slot:"bgCover"})(({theme:e})=>({backgroundRepeat:"no-repeat",backgroundPosition:"center",backgroundSize:"cover",height:320})),Z=(0,d.ZP)("span",{name:"HeadlineSpan"})(({theme:e})=>({paddingRight:e.spacing(.5),color:e.palette.text.secondary})),_=(0,d.ZP)(n.rUS,{name:w,slot:"profileLink"})(({theme:e})=>({fontSize:e.mixins.pxToRem(15),fontWeight:e.typography.fontWeightBold,paddingRight:e.spacing(.5),color:e.palette.text.primary})),C=(0,d.ZP)(b.Z,{name:"OwnerStyled"})(({theme:e})=>({fontWeight:e.typography.fontWeightBold,color:e.palette.text.primary,fontSize:e.mixins.pxToRem(15),"&:hover":{textDecoration:"underline"}})),S=(0,n.Uh$)((0,r.Y)(function({item:e,user:t,attachments:a,actions:r,answers:d,identity:g,handleAction:v,state:b}){let w=f(),{ItemActionMenu:S,ItemDetailInteraction:k,i18n:N,jsxBackend:R,assetUrl:A}=(0,n.OgA)(),M=(0,h.v9)(t=>{return(0,n.AV9)(t,null==e?void 0:e.owner)}),P=R.get("core.itemView.pendingReviewCard");if(!e||!t)return null;let{item_id:T,is_user_voted:W,is_multiple:z,public_vote:I,is_featured:V,is_sponsor:L,is_pending:B,is_closed:$,extra:F}=e,D=(0,s.Q4)(null==e?void 0:e.image,"500",A("poll.no_image"));return p.createElement(o.gO,{testid:`detailview ${e.resource_name}`},p.createElement(o.sU,null,p.createElement(x,null,D&&p.createElement(E,{style:{backgroundImage:`url(${D})`}}),p.createElement("div",{className:w.viewContainer},P&&p.createElement(c.Z,{sx:{marginBottom:2}},p.createElement(P,{sx:!0,item:e})),$&&p.createElement(y,null,p.createElement(m.Z,{variant:"h5",color:"text.hint"},N.formatMessage({id:"voting_for_the_poll_was_closed"}))),p.createElement("div",{className:w.contentWrapper},p.createElement("div",{className:w.actionMenu},p.createElement(S,{identity:g,icon:"ico-dottedmore-vertical-o",state:b,handleAction:v})),p.createElement(i.XQ,{variant:"h3",component:"div",showFull:!0},p.createElement(i.K6,{variant:"itemView",value:V}),p.createElement(i.k5,{variant:"itemView",value:L}),p.createElement(m.Z,{component:"h1",variant:"h3",sx:{pr:2.5,display:{sm:"inline",xs:"block"},mt:{sm:0,xs:1},verticalAlign:"middle"}},null==e?void 0:e.question)),p.createElement("div",{className:w.author},p.createElement("div",null,p.createElement(i.Yt,{user:t,size:48})),p.createElement("div",{className:w.authorInfo},t?p.createElement(_,{to:t.link,children:t.full_name,hoverCard:`/user/${t.id}`,"data-testid":"headline"}):null,(null==M?void 0:M.resource_name)!==(null==t?void 0:t.resource_name)&&p.createElement(Z,null,N.formatMessage({id:"to_parent_user"},{icon:()=>p.createElement(i.zb,{icon:"ico-caret-right"}),parent_user:()=>p.createElement(C,{user:M})})),p.createElement(i.Ee,{sx:{color:"text.secondary",mt:.5}},p.createElement(i.r2,{value:e.creation_date,format:"MMMM DD, yyyy","data-testid":"creationDate"}),p.createElement(i.$k,{values:null==e?void 0:e.statistic,display:"total_view",component:"span",skipZero:!1}),p.createElement(i.Cd,{value:null==e?void 0:e.privacy,item:null==e?void 0:e.privacy_detail})))),(null==e?void 0:e.text)&&p.createElement(c.Z,{component:"div",mt:3,className:w.itemContent},p.createElement(i.jK,{maxHeight:"300px"},p.createElement(l.ZP,{html:e.text||""}))),p.createElement("div",{className:w.voteForm},p.createElement(u.Z,{isMultiple:z,isVoted:W,pollId:T,answers:d,statistic:e.statistic,closeTime:e.close_time,publicVote:I,identity:g,isPending:B,isClosed:$,canVoteAgain:F.can_change_vote,canVote:F.can_vote,canViewResult:F.can_view_result,canViewResultAfter:F.can_view_result_after_vote,canViewResultBefore:F.can_view_result_before_vote})),(null==a?void 0:a.length)>0&&p.createElement(p.Fragment,null,p.createElement("div",{className:w.attachmentTitle},N.formatMessage({id:"attachments"})),p.createElement("div",{className:w.attachment},a.map(e=>{return p.createElement("div",{className:w.attachmentItem,key:e.id.toString()},p.createElement(i.M$,{fileName:e.file_name,downloadUrl:e.download_url,isImage:e.is_image,fileSizeText:e.file_size_text,image:null==e?void 0:e.image,size:"large"}))}))),p.createElement(k,{identity:g,state:b,handleAction:v}))))))},r.c,{poll_answer:!0,attachments:!0}));var k=(0,n.j4Z)({extendBlock:S,defaults:{placeholder:"Search",blockProps:{variant:"plained",titleComponent:"h2",titleVariant:"subtitle1",titleColor:"textPrimary",noFooter:!0,noHeader:!0,blockStyle:{},contentStyle:{borderRadius:"base",pt:0,pb:0},headerStyle:{},footerStyle:{}}}})},22870:function(e,t,a){a.d(t,{Z:function(){return A}});var n=a(85597),r=a(76224),l=a(30120),o=a(21822),i=a(81719),s=a(67294),c=a(19382),d=a(91647),m=a(86010),p=a(22410),u=a(73327);let g=(0,p.Z)(e=>(0,u.Z)({root:{border:e.mixins.border("secondary"),borderRadius:e.shape.borderRadius,marginTop:e.spacing(2),padding:e.spacing(2)},answerWrapper:{},answerItem:{marginLeft:0,marginBottom:e.spacing(1),"&:last-child":{marginBottom:e.spacing(0)}},answerItemChecked:{color:e.palette.text.primary},radioAnswer:{padding:e.spacing(0,1)},radioAnswerChecked:{color:`${e.palette.primary.main} !important`},votedAnswer:{fontWeight:"bold",color:e.palette.text.primary},btnToggle:{color:e.palette.text.primary,fontWeight:"bold","&:hover":{textDecoration:"underline"}},formControl:{},button:{fontWeight:"bold",textTransform:"capitalize"},cancelButton:{fontWeight:"bold",marginLeft:e.spacing(1)},progressAnswerWrapper:{paddingRight:e.spacing(1),"&:last-child":{marginBottom:e.spacing(0)}},progressAnswer:{color:e.palette.text.hint,marginBottom:e.spacing(1),"&:last-child":{marginBottom:e.spacing(0)}},answerLabel:{},yourAnswer:{fontWeight:"bold"},progressItem:{height:e.spacing(2.5),display:"flex",alignItems:"center"},progress:{flex:1,minWidth:0,margin:0,marginRight:e.spacing(1),height:`${e.spacing(1)} !important`,borderRadius:e.spacing(.5),backgroundColor:e.palette.action.selected,"& > div":{borderRadius:e.spacing(.5)}},progressPercent:{width:20,marginLeft:e.spacing(1),color:e.palette.text.secondary,fontSize:e.mixins.pxToRem(13)},voteStatistic:{marginTop:e.spacing(2),color:e.palette.text.hint,fontSize:e.mixins.pxToRem(13),display:"flex"},buttonWrapper:{display:"flex",marginTop:e.spacing(2)},totalVote:{"&:hover":{textDecoration:"underline"}},activeTotalVote:{fontSize:e.mixins.pxToRem(13),color:e.palette.primary.main,cursor:"pointer","&:hover":{textDecoration:"underline"}},timeLeft:{marginLeft:e.spacing(2)},buttonWrapperVoteCancel:{display:"flex"},noShowAnswer:{display:"flex",alignItems:"center",paddingBottom:e.spacing(2),"&:last-child":{paddingBottom:0}},iconNoShowAnswer:{padding:e.spacing(1,1,1,0),fontSize:e.mixins.pxToRem(16)}}),{name:"PollVoteForm"}),v=({item:e,classes:t,settings:a})=>{return s.createElement("div",{className:t.progressAnswer},s.createElement(d.Z,{variant:e.voted?"h5":"body1",className:(0,m.default)(e.voted?t.votedAnswer:t.answerLabel)},e.answer),s.createElement("div",{className:t.progressItem},s.createElement(c.Z,{variant:"determinate",value:e.vote_percentage||0,className:t.progress}),(null==a?void 0:a.publicVote)&&s.createElement(d.Z,{component:"span",className:t.progressPercent},`${e.vote_percentage}%`)))},f=({item:e,classes:t,settings:a})=>{let{user:l}=(0,n.kPO)(),o=()=>{if(null==e?void 0:e.some_votes){var t;return!!(null==e?void 0:null===(t=e.some_votes)||void 0===t?void 0:t.map(e=>e.id===l.id))}return!1},i=(null==a?void 0:a.isMultiple)?"ico-square-o":" ico-circle-o";return(null==a?void 0:a.isMultiple)&&e.voted&&o()&&(i="ico-check-square"),!(null==a?void 0:a.isMultiple)&&e.voted&&o()&&(i="ico-check-circle"),s.createElement("div",{className:t.noShowAnswer},s.createElement(r.zb,{className:t.iconNoShowAnswer,icon:i}),s.createElement(d.Z,{variant:e.voted?"h5":"body1",className:(0,m.default)(e.voted?t.votedAnswer:t.answerLabel)},e.answer))};var h=(0,s.memo)(function({displayAnswers:e,answers:t,publicVote:a,LIMIT_ANSWER_DISPLAY:r,hideAnswers:l,isMultiple:o,isCanViewVoteAnswer:i,isCanViewResult:c}){let d=g(),{i18n:m}=(0,n.OgA)(),[p,u]=s.useState(!1);return s.createElement("div",{className:d.progressAnswerWrapper},(null==e?void 0:e.length)>0&&e.map((e,t)=>{return i&&c?s.createElement(v,{key:t,item:e,classes:d,settings:{publicVote:a}}):s.createElement(f,{key:t,item:e,classes:d,settings:{publicVote:a,isMultiple:o}})}),p&&(null==l?void 0:l.length)>0?l.map((e,t)=>{return i&&c?s.createElement(v,{key:t,item:e,classes:d,settings:{publicVote:a}}):s.createElement(f,{key:t,item:e,classes:d,settings:{publicVote:a,isMultiple:o}})}):null,t.length>r?s.createElement("span",{className:d.btnToggle,onClick:()=>u(!p),role:"button"},m.formatMessage({id:p?"view_less":"view_more"})):null)}),b=a(49960),w=a(18037),x=a(52922),y=a(42853),E=a(20399),Z=a(41470),_=a(12902);let C=({displayAnswers:e,classes:t,isClosed:a,handleCheckboxChange:n,viewMore:r,hideAnswers:l})=>{return s.createElement(x.Z,null,(null==e?void 0:e.length)>0?e.map(e=>s.createElement(w.Z,{key:e.id.toString(),className:t.answerItem,control:s.createElement(b.Z,{disabled:a,color:"primary",size:"small",name:e.id.toString(),onChange:n,className:t.radioAnswer}),label:e.answer})):null,r&&(null==l?void 0:l.length)>0?l.map(e=>s.createElement(w.Z,{key:e.id.toString(),className:t.answerItem,control:s.createElement(b.Z,{disabled:a,color:"primary",size:"small",name:e.id.toString(),onChange:n,className:t.radioAnswer}),label:e.answer})):null)},S=({classes:e,value:t,handleRadioChange:a,displayAnswers:n,isClosed:r,viewMore:l,hideAnswers:o})=>{return s.createElement(Z.Z,{className:e.answerWrapper,value:t,onChange:a},(null==n?void 0:n.length)>0&&n.map(t=>s.createElement(w.Z,{className:e.answerItem,key:t.id.toString(),value:t.id.toString(),control:s.createElement(E.Z,{className:e.radioAnswer,color:"primary",size:"small",disabled:r}),label:t.answer})),l&&(null==o?void 0:o.length)>0?o.map(t=>s.createElement(w.Z,{className:e.answerItem,key:t.id.toString(),value:t.id.toString(),control:s.createElement(E.Z,{className:e.radioAnswer,color:"primary",size:"small",disabled:r}),label:t.answer})):null)};var k=(0,s.memo)(function({voteAgain:e,pollId:t,identity:a,displayAnswers:r,isClosed:l,hideAnswers:i,isMultiple:c,answers:d,LIMIT_ANSWER_DISPLAY:m,isPending:p,canVote:u,canVoteAgain:v,isEmbedInFeed:f,setVoteAgain:h,setShowPoll:b,setIsCanViewVoteAnswer:w,canViewResultAfter:x,setIsCanViewResult:E}){let Z=g(),{i18n:k,dispatch:N,useSession:R}=(0,n.OgA)(),{loggedIn:A}=R(),[M,P]=s.useState(!1),[T,W]=s.useState(""),[z,I]=s.useState(""),[V,L]=s.useState([]),B=e=>{let{value:t}=e.target,a=parseInt(t);I(t),L([a]),W("")},$=e=>{let{checked:t,name:a}=e.target,n=parseInt(a);t?(W(""),L(e=>(0,_.ZP)(e,e=>{let t=e.findIndex(e=>e===n);t<0&&e.push(n)}))):L(e=>(0,_.ZP)(e,e=>{let t=e.findIndex(e=>e===n);t>-1&&e.splice(t,1)}))},F=()=>{h(!1),b(!1)},D=n=>{if(n.preventDefault(),!V.length)return W(k.formatMessage({id:"please_select_an_option"})),null;N({type:"submitPoll",payload:{voteAgain:e,pollId:t,answers:V,identity:a},meta:{onSuccess:e=>{b(!1),w(null==e?void 0:e.can_view_result_after_vote),E(null==e?void 0:e.can_view_result)}}})};return s.createElement("form",{onSubmit:D},c?s.createElement(C,{displayAnswers:r,classes:Z,isClosed:l,handleCheckboxChange:$,viewMore:M,hideAnswers:i}):s.createElement(S,{classes:Z,value:z,handleRadioChange:B,displayAnswers:r,isClosed:l,viewMore:M,hideAnswers:i}),d.length>m?s.createElement("span",{className:Z.btnToggle,onClick:()=>P(!M),role:"button"},k.formatMessage({id:M?"view_less":"view_more"})):null,T?s.createElement(y.Z,null,T):null,(u||v)&&s.createElement("div",{className:Z.buttonWrapper},!p&&A&&s.createElement("div",{className:Z.button},s.createElement(o.Z,{type:"submit",variant:"outlined",disabled:l,size:f?"smaller":"medium",color:"primary",sx:{fontWeight:"bold"}},k.formatMessage({id:"vote"}))),e&&s.createElement("div",{className:Z.cancelButton},s.createElement(o.Z,{variant:"outlined",disabled:l,size:f?"smaller":"medium",color:"primary",sx:{fontWeight:"bold"},onClick:F},k.formatMessage({id:"cancel"})))))});let N=(0,i.ZP)("span",{name:"PollVoteFormRoot",slot:"flagWrapper"})(({theme:e})=>({marginLeft:"auto","& > .MuiFlag-root":{marginLeft:e.spacing(.5)}})),R=(0,i.ZP)(l.Z,{name:"ClosedStyled"})(({theme:e})=>({"&:before":{color:e.palette.text.secondary,content:'"\xb7"',paddingLeft:"0.25em",paddingRight:"0.25em"}}));function A(e){let{answers:t,statistic:a,closeTime:l,pollId:i,isVoted:c,isMultiple:d,publicVote:m,identity:p,isPending:u,canVoteAgain:v,canVote:f,canViewResult:b,canViewResultAfter:w,canViewResultBefore:x,isEmbedInFeed:y,isFeatured:E,isSponsor:Z,isClosed:_}=e,C=t.sort((e,t)=>{return(null==e?void 0:e.ordering)-(null==t?void 0:t.ordering)}),S=g(),{i18n:A,dialogBackend:M}=(0,n.OgA)(),[P,T]=s.useState(!0),[W,z]=s.useState(!1),[I,V]=s.useState(!1),[L,B]=s.useState(b),$=y?3:C.length,F=C.slice(0,$),D=C.slice($,C.length);s.useEffect(()=>{L&&(c?V(w):V(x))},[L,b,c,w,x]),s.useEffect(()=>{0===a.total_vote&&V(!1)},[a]),s.useEffect(()=>{T(Boolean(!c))},[c]);let j=()=>{z(!0),T(!0)};return s.createElement("div",{className:S.root},P?s.createElement(k,{voteAgain:W,pollId:i,identity:p,displayAnswers:F,isClosed:_,hideAnswers:D,isMultiple:d,answers:C,LIMIT_ANSWER_DISPLAY:$,isPending:u,canVote:f,canVoteAgain:v,isEmbedInFeed:y,setShowPoll:T,setVoteAgain:z,setIsCanViewVoteAnswer:V,setIsCanViewResult:B,canViewResultAfter:w}):s.createElement(h,{displayAnswers:F,answers:C,publicVote:m,LIMIT_ANSWER_DISPLAY:$,hideAnswers:D,isMultiple:d,isCanViewVoteAnswer:I,isCanViewResult:L}),!P&&v&&s.createElement("div",{className:S.buttonWrapper},s.createElement(o.Z,{variant:"outlined",size:y?"smaller":"medium",color:"primary",className:S.button,onClick:j,sx:{fontWeight:"bold"}},A.formatMessage({id:"vote_again"}))),s.createElement("div",{className:S.voteStatistic},I&&L?s.createElement("div",{className:S.activeTotalVote,onClick:()=>M.present({component:"poll.dialog.PeopleWhoVotedAnswer",props:{listAnswers:C}})},A.formatMessage({id:"total_vote"},{value:a.total_vote})):s.createElement(r.$k,{className:S.totalVote,color:"textHint",values:a,display:"total_vote",skipZero:!1}),"0"===l||_?null:s.createElement(r.Lt,{value:l,className:S.timeLeft,shorten:!1}),_?s.createElement(R,{color:"text.hint"},A.formatMessage({id:"closed"})):null,y?s.createElement(N,null,s.createElement(r.K6,{variant:"text",value:E,color:"primary"}),s.createElement(r.k5,{variant:"text",value:Z,color:"yellow"})):null))}}}]);