"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["composer"],{84820:function(e,t,a){a.r(t),a.d(t,{default:function(){return C}});var n=a(37166),o=a(85597),r=a(76224),i=a(27274),l=a(50130),s=a(38790),c=a(86010),m=a(9041),p=a(67294),d={editorControls:[{as:"commentComposer.control.attachEmoji"},{as:"commentComposer.control.attachFile",showWhen:["and",["falsy","editing"],["falsy","previewFiles"]]},{as:"chatComposer.control.buttonSubmit"}]},u=a(22410),g=a(73327),f=(0,u.Z)(e=>(0,g.Z)({root:{width:"100%"},composeOuter:{display:"flex",padding:e.spacing(.5,.5,.5,2),minHeight:e.spacing(4.25),alignItems:"center",overflowY:"auto",height:"100%"},"composeOuter-dense":{padding:e.spacing(.5)},composeInner:{flex:1,minWidth:0},composeInputWrapper:{width:"100%",flexFlow:"wrap",display:"flex",alignItems:"center"},expand:{"& $composeInputWrapper":{flex:"none",width:"100%"}},composer:{maxHeight:"200px",overflowY:"auto",minHeight:0,flex:1,flexBasis:"auto",minWidth:0,display:"flex"," & .DraftEditor-root":{width:"100%"},"& .public-DraftEditorPlaceholder-root":{position:"absolute",color:e.palette.text.secondary,fontSize:e.mixins.pxToRem(15)},"& .DraftEditor-editorContainer":{minWidth:e.spacing(2),fontSize:e.mixins.pxToRem(15)},"& .mentionSuggestionsWrapper":{position:"absolute",zIndex:1,minWidth:180,backgroundColor:e.mixins.backgroundColor("paper"),borderRadius:4,padding:e.spacing(1),marginTop:e.spacing(1),boxShadow:"0px 10px 13px -6px rgba(0,0,0,0.2), 0px 20px 31px 3px rgba(0,0,0,0.14)"},"& .mentionText":{color:e.palette.primary.main},"& .hashtagStyle":{color:e.palette.primary.main}},attachIconsWrapper:{display:"inline-flex",alignItems:"flex-end",padding:"0 2px",marginLeft:"auto","& .ico-smile-o":{fontSize:e.mixins.pxToRem(15)}},attachBtn:{padding:0,display:"inline-flex",alignItems:"center",justifyContent:"center",width:e.spacing(3.5),height:e.spacing(3.5),minWidth:e.spacing(3.5),color:e.palette.text.secondary},attachBtnIcon:{fontSize:"14px"},extraDataPhoto:{maxWidth:e.spacing(26)},extraDataSticker:{width:e.spacing(10),height:e.spacing(10)},cancelBtn:{fontSize:"small",color:e.palette.text.secondary,marginLeft:e.spacing(5),marginBottom:e.spacing(1),"& span":{color:e.palette.primary.main}}}),{name:"ChatCompose"}),h=a(81719);let y=(0,h.ZP)(l.Z)(({theme:e})=>({padding:0,display:"inline-flex",alignItems:"center",justifyContent:"center",width:e.spacing(3.5),height:e.spacing(3.5),minWidth:e.spacing(3.5),color:e.palette.text.secondary})),x=(0,h.ZP)(r.zb)(({theme:e})=>({fontSize:e.spacing(1.75)}));var v=p.forwardRef(function({title:e,icon:t,onClick:a,testid:n},o){return p.createElement(s.Z,{title:e},p.createElement(y,{onClick:a,size:"small",ref:o,"data-testid":n,role:"button"},p.createElement(x,{icon:t})))}),C=p.forwardRef(function({rid:e,msgId:t,text:a="",focus:u,reactMode:g,onCancel:h,extra_data:y,onSuccess:x,onMarkAsRead:C,margin:E="normal",previewRef:S},w){let b=f(),{i18n:k,dispatch:I,jsxBackend:W}=(0,o.OgA)(),[F,K]=p.useState(m.EditorState.createWithContent((0,m.convertFromRaw)((0,i.DK)((0,i.s9)(a||""))))),[_,M]=p.useState("sticker"===(null==y?void 0:y.extra_type)?y.extra_id:void 0),[O,R]=p.useState("photo"===(null==y?void 0:y.extra_type)?y.extra_id:void 0),[T,B]=p.useState(!1),P=null==y?void 0:y.full_path,z=p.useRef(),[Z,D]=p.useState([]),N=e=>{let t=e.getCurrentContent(),a=t.getBlockMap(),n=a.last().getKey(),o=a.last().getLength(),r=new m.SelectionState({anchorKey:n,anchorOffset:o,focusKey:n,focusOffset:o});return r};p.useEffect(()=>{if("edit"===g){let e=m.ContentState.createFromText(a),t=m.EditorState.createWithContent(e);K(m.EditorState.forceSelection(t,N(t))),setImmediate(()=>{Q()})}},[a,g,e]);let L=p.useCallback(()=>{let e=F.getCurrentContent(),t=e.getFirstBlock(),a=e.getLastBlock(),n=new m.SelectionState({anchorKey:t.getKey(),anchorOffset:0,focusKey:a.getKey(),focusOffset:a.getLength(),hasFocus:!0}),o=m.EditorState.push(F,m.Modifier.removeRange(e,n,"backward"),"remove-range");K(o)},[F]),j=p.useMemo(()=>{return{onSuccess:()=>{L(),B(!1),"function"==typeof x&&x()}}},[L]),U=p.useMemo(()=>{return{editing:"edit"===g,previewFiles:Z.length}},[g,Z]),[H,$,G]=(0,o.sgG)(d,U),Y=k.formatMessage({id:"write_message"}),A=t=>{I({type:"chat/composer/SUBMIT",payload:{rid:e,sticker_id:t},meta:j})},q=()=>{var e;null===(e=S.current)||void 0===e||e.clear(),D([]),L(),B(!1),"no_react"!==g&&x()},J=()=>{B(!1)},Q=p.useCallback(()=>{C&&C(),setImmediate(()=>z.current.focus())},[e]),V=()=>{let a=F.getCurrentContent().getPlainText();a&&a.trim()&&!T&&(B(!0),I({type:"chat/composer/SUBMIT",payload:{reactMode:g,rid:e,msgId:t,text:a.trim(),sticker_id:_,photo_id:O},meta:j}))},X=e=>{return!e.keyCode||13!==e.keyCode||e.metaKey||e.shiftKey||e.altKey||e.ctrlKey?(0,m.getDefaultKeyBinding)(e):"composer-submit"},ee=a=>{if("composer-submit"===a){if(Z&&Z.length&&Object.values(Z).length){let n=F.getCurrentContent().getPlainText();if(T)return;B(!0),I({type:"chat/composer/upload",payload:{files:Object.values(Z),rid:e,text:n,reactMode:g,msgId:t},meta:{onSuccess:q,onFailure:J}})}else V();return"handled"}return"not-handled"};(0,p.useEffect)(()=>{u&&Q()},[u,Q]),(0,p.useEffect)(()=>{let e=e=>{h&&27===e.keyCode&&h()};return window.addEventListener("keydown",e),()=>{window.removeEventListener("keydown",e)}},[]),p.useImperativeHandle(w,()=>{return{attachFiles:e=>{(null==e?void 0:e.length)&&D(e)},removeFile:e=>{let t=[...Z];e>-1&&(t.splice(e,1),D([...t]))}}});let et=p.useMemo(()=>{let e=F.getCurrentContent().getPlainText(),t=e&&e.trim();return!!(!T&&(null==Z?void 0:Z.length)||t)},[Z,F,T]),ea=e=>{if(e.length&&S&&S.current){let t=e[0];if((0,i.rO)(t.type)){var a;D([...Z,t]),null===(a=S.current)||void 0===a||a.attachFiles([...Z,t])}}};return p.createElement("form",{className:b.root,role:"presentation","data-testid":"chatComposerForm"},p.createElement("div",{className:(0,c.default)(b.composeOuter,b[`composeOuter-${E}`])},p.createElement("div",{className:b.composeInner},p.createElement("div",{className:b.composeInputWrapper},p.createElement("div",{className:b.composer,onClick:Q,"data-testid":"draftEditor"},p.createElement(n.ZP,{stripPastedStyles:!0,ref:z,plugins:H,placeholder:Y,editorState:F,keyBindingFn:X,handleKeyCommand:ee,onChange:K,handlePastedFiles:ea}),W.render($)),p.createElement("div",{className:b.attachIconsWrapper},G.map(t=>W.render({component:t.as,props:{key:t.as,classes:b,previewRef:S,filesUploadRef:w,onStickerClick:A,control:v,editorRef:z,rid:e,disableSubmit:et,handleSubmit:()=>ee("composer-submit")}})))))),"edit"===g&&0<O?p.createElement("div",null,p.createElement("div",{className:b.extraDataPhoto},p.createElement(r.Gy,{src:P,alt:"photo"})),p.createElement(s.Z,{title:k.formatMessage({id:"remove"})},p.createElement(l.Z,{onClick:()=>R(void 0)},p.createElement(r.zb,{icon:"ico-close"})))):null,"edit"===g&&0<_?p.createElement("div",null,p.createElement("div",{className:b.extraDataSticker},p.createElement(r.Gy,{src:P,alt:"sticker",aspectRatio:"fixed",imageFit:"contain"})),p.createElement(s.Z,{title:k.formatMessage({id:"remove"})},p.createElement(l.Z,{onClick:()=>M(void 0)},p.createElement(r.zb,{icon:"ico-close"})))):null)})},95805:function(e,t,a){a.r(t),a.d(t,{default:function(){return b}});var n=a(37166),o=a(93836),r=a(85597),i=a(76224),l=a(27274),s=a(50130),c=a(38790),m=a(86010),p=a(9041),d=a(67294),u={editorPlugins:[{as:"statusComposerChat.plugin.mention"},{as:"statusComposer.plugin.hashtag"}],editorControls:[{as:"commentComposer.control.attachEmoji"},{as:"commentComposer.control.attachFile",showWhen:["and",["falsy","editing"],["falsy","previewFiles"]]},{as:"chatComposer.control.buttonSubmit"}]},g=a(81719);let f=(0,g.ZP)(s.Z)(({theme:e})=>({padding:0,display:"inline-flex",alignItems:"center",justifyContent:"center",width:e.spacing(3.5),height:e.spacing(3.5),minWidth:e.spacing(3.5),color:e.palette.text.secondary})),h=(0,g.ZP)(i.zb)(({theme:e})=>({fontSize:e.spacing(1.75)}));var y=d.forwardRef(function({title:e,icon:t,onClick:a,testid:n},o){return d.createElement(c.Z,{title:e},d.createElement(f,{onClick:a,size:"small",ref:o,"data-testid":n,role:"button"},d.createElement(h,{icon:t})))}),x=a(22410),v=a(73327),C=(0,x.Z)(e=>(0,v.Z)({root:{width:"100%"},composeOuter:{display:"flex",padding:e.spacing(.5,.5,.5,2),minHeight:e.spacing(4.25),alignItems:"center",overflowY:"auto",height:"100%"},"composeOuter-dense":{padding:e.spacing(.5)},composeInner:{flex:1,minWidth:0},composeInputWrapper:{width:"100%",flexFlow:"wrap",display:"flex",alignItems:"center"},expand:{"& $composeInputWrapper":{flex:"none",width:"100%"}},composer:{maxHeight:"200px",overflowY:"auto",flex:1,flexBasis:"auto",minWidth:0,display:"flex"," & .DraftEditor-root":{width:"100%"},"& .public-DraftEditorPlaceholder-root":{position:"absolute",color:e.palette.text.secondary,fontSize:e.mixins.pxToRem(15)},"& .DraftEditor-editorContainer":{minWidth:e.spacing(2),fontSize:e.mixins.pxToRem(15)},"& .mentionSuggestionsWrapper":{position:"absolute",zIndex:1,minWidth:180,backgroundColor:e.mixins.backgroundColor("paper"),borderRadius:4,padding:e.spacing(1),marginTop:e.spacing(1),boxShadow:"0px 10px 13px -6px rgba(0,0,0,0.2), 0px 20px 31px 3px rgba(0,0,0,0.14)"},"& .mentionText":{color:e.palette.primary.main},"& .hashtagStyle":{color:e.palette.primary.main}},attachIconsWrapper:{display:"inline-flex",alignItems:"flex-end",padding:"0 2px",marginLeft:"auto","& .ico-smile-o":{fontSize:e.mixins.pxToRem(15)}},attachBtn:{padding:0,display:"inline-flex",alignItems:"center",justifyContent:"center",width:e.spacing(3.5),height:e.spacing(3.5),minWidth:e.spacing(3.5),color:e.palette.text.secondary},attachBtnIcon:{fontSize:"14px"},extraDataPhoto:{maxWidth:e.spacing(26)},extraDataSticker:{width:e.spacing(10),height:e.spacing(10)},cancelBtn:{fontSize:"small",color:e.palette.text.secondary,marginLeft:e.spacing(5),marginBottom:e.spacing(1),"& span":{color:e.palette.primary.main}}}),{name:"ChatCompose"}),E=a(31313);let S=/<a href="(\w+)">([^<]+)<\/a>/gm;function w(e){return(0,E.E)(e.getCurrentContent(),{entityStyleFn:e=>{let t=e.getType().toUpperCase(),a=e.getData();if("MENTION"===t)return{element:"a",attributes:{href:a.mention.user_name}}}}).replace(S,"@$1").replace(/&nbsp;/gm," ").replace("&amp;","&").replace(/(<([^>]+)>)/gi,"")}var b=d.forwardRef(function({rid:e,room:t,user:a,msgId:g,text:f="",focus:h,reactMode:x,onCancel:v,extra_data:E,onSuccess:S,onMarkAsRead:b,margin:k="normal",subscription:I,previewRef:W,isAllPage:F},K){let _=C(),{i18n:M,dispatch:O,jsxBackend:R,chatplus:T}=(0,r.OgA)(),[B,P]=d.useState(p.EditorState.createWithContent((0,p.convertFromRaw)((0,l.DK)((0,l.s9)(f||""))))),[z,Z]=d.useState("sticker"===(null==E?void 0:E.extra_type)?E.extra_id:void 0),[D,N]=d.useState("photo"===(null==E?void 0:E.extra_type)?E.extra_id:void 0),[L,j]=d.useState(!1),U=null==E?void 0:E.full_path,H=d.useRef(),$=e=>{let t=e.getCurrentContent(),a=t.getBlockMap(),n=a.last().getKey(),o=a.last().getLength(),r=new p.SelectionState({anchorKey:n,anchorOffset:o,focusKey:n,focusOffset:o});return r},[G,Y]=d.useState([]);d.useEffect(()=>{if("edit"===x){let e=p.ContentState.createFromText(f),t=p.EditorState.createWithContent(e);P(p.EditorState.forceSelection(t,$(t))),setImmediate(()=>{ea()})}},[f,x,e]);let A=d.useCallback(()=>{let e=B.getCurrentContent(),t=e.getFirstBlock(),a=e.getLastBlock(),n=new p.SelectionState({anchorKey:t.getKey(),anchorOffset:0,focusKey:a.getKey(),focusOffset:a.getLength(),hasFocus:!0}),o=p.EditorState.push(B,p.Modifier.removeRange(e,n,"backward"),"remove-range");P(o)},[B]),q=d.useMemo(()=>{return{onSuccess:()=>{A(),j(!1),"function"==typeof S&&S()}}},[A]),J=d.useMemo(()=>{return{editing:"edit"===x,previewFiles:G.length}},[x,G]),[Q,V,X]=function(e,t,a,n,o){let{jsxBackend:i}=(0,r.OgA)(),s=(0,d.useRef)([]),c=(0,d.useRef)([]),m=o?[a,null==n?void 0:n.id]:[];return(0,d.useMemo)(()=>{n&&a&&e.editorPlugins.forEach(e=>{let t=i.get(e.as);t&&"function"==typeof t&&t(s.current,c.current,a,n)});let o=e.editorControls?(0,l.I)((0,l.W$)(e.editorControls,t),t):[],r=e.attachers?(0,l.I)((0,l.W$)(e.attachers,t),t):[];return[s.current,c.current,o,r]},m)}(u,J,e,t,F),ee=(null==t?void 0:t.t)!==o.n.Direct?M.formatMessage({id:"type_a_message_or__name"}):M.formatMessage({id:"write_message"}),et=t=>{O({type:"chatplus/composer/SUBMIT",payload:{rid:e,sticker_id:t},meta:q})},ea=d.useCallback(()=>{b&&b(),setImmediate(()=>H.current.focus())},[I]),en=()=>{let t=w(B).trim();t&&!L&&(j(!0),O({type:"chatplus/composer/SUBMIT",payload:{reactMode:x,rid:e,msgId:g,text:t.trim(),sticker_id:z,photo_id:D},meta:q}))},eo=()=>{var e;null===(e=W.current)||void 0===e||e.clear(),Y([]),A(),"no_react"!==x&&S()},er=d.useRef(!1),ei=d.useRef(null),el=t=>{let n={typingUser:{username:a.username}};return!t.keyCode||13!==t.keyCode||t.metaKey||t.shiftKey||t.altKey||t.ctrlKey?(er.current||(er.current=!0,T.typingMessage(e,a.username,!0,n)),clearTimeout(ei.current),ei.current=setTimeout(()=>{er.current=!1,T.typingMessage(e,a.username,!1,n)},5e3),(0,p.getDefaultKeyBinding)(t)):(clearTimeout(ei.current),T.typingMessage(e,a.username,!1,n),er.current=!1,"composer-submit")},es=t=>{if("composer-submit"===t){if(G&&G.length&&Object.values(G).length){let a=w(B).trim();O({type:"chatplus/composer/upload",payload:{files:Object.values(G),rid:e,text:a,reactMode:x,msgId:g},meta:{onSuccess:eo}})}else en();return"handled"}return"not-handled"};(0,d.useEffect)(()=>{h&&ea()},[h,ea]),(0,d.useEffect)(()=>{let e=e=>{v&&27===e.keyCode&&v()};return window.addEventListener("keydown",e),()=>{window.removeEventListener("keydown",e)}},[]),d.useImperativeHandle(K,()=>{return{attachFiles:e=>{(null==e?void 0:e.length)&&Y(e)},removeFile:e=>{let t=[...G];e>-1&&(t.splice(e,1),Y([...t]))}}});let ec=d.useMemo(()=>{let e=B.getCurrentContent().getPlainText(),t=e&&e.trim();return!!((null==G?void 0:G.length)||t)},[G,B]),em=e=>{if(e.length&&W&&W.current){let t=e[0];if((0,l.rO)(t.type)){var a;Y([...G,t]),null===(a=W.current)||void 0===a||a.attachFiles([...G,t])}}};return d.createElement("form",{className:_.root,role:"presentation","data-testid":"chatComposerForm"},d.createElement("div",{className:(0,m.default)(_.composeOuter,_[`composeOuter-${k}`])},d.createElement("div",{className:_.composeInner},d.createElement("div",{className:_.composeInputWrapper},d.createElement("div",{className:_.composer,onClick:ea,"data-testid":"draftEditor"},t?d.createElement(n.ZP,{key:e,stripPastedStyles:!0,ref:H,plugins:Q,placeholder:ee,editorState:B,keyBindingFn:el,handleKeyCommand:es,onChange:P,handlePastedFiles:em}):null,R.render(V)),d.createElement("div",{className:_.attachIconsWrapper},X.map(t=>R.render({component:t.as,props:{key:t.as,classes:_,previewRef:W,filesUploadRef:K,onStickerClick:et,control:y,editorRef:H,rid:e,disableSubmit:ec,handleSubmit:()=>es("composer-submit"),disablePortal:!0,placement:"top"}})))))),"edit"===x&&0<D?d.createElement("div",null,d.createElement("div",{className:_.extraDataPhoto},d.createElement(i.Gy,{src:U,alt:"photo"})),d.createElement(c.Z,{title:M.formatMessage({id:"remove"})},d.createElement(s.Z,{onClick:()=>N(void 0)},d.createElement(i.zb,{icon:"ico-close"})))):null,"edit"===x&&0<z?d.createElement("div",null,d.createElement("div",{className:_.extraDataSticker},d.createElement(i.Gy,{src:U,alt:"sticker",aspectRatio:"fixed",imageFit:"contain"})),d.createElement(c.Z,{title:M.formatMessage({id:"remove"})},d.createElement(s.Z,{onClick:()=>Z(void 0)},d.createElement(i.zb,{icon:"ico-close"})))):null)})}}]);