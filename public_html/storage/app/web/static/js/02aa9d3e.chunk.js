"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-photo-components-CommentComposerAttachPhotoButton"],{25318:function(e,t,n){n.r(t),n.d(t,{default:function(){return i}});var a=n(85597),r=n(67294),o=n(13478);function i({onAttachFiles:e,previewRef:t,control:n}){let{i18n:i,useLimitFileSize:l,dialogBackend:c}=(0,a.OgA)(),u=r.useRef(),{photo:s}=l(),f=()=>{u.current.click()},m=()=>{if(!u.current.files.length)return;let n=u.current.files,a=n[0],r=a.size;if(r>s&&0!==s){c.alert({message:i.formatMessage({id:"warning_upload_limit_one_file"},{fileName:(0,o.TX)(a.name,30),fileSize:(0,o.jA)(a.size),maxSize:(0,o.jA)(s)})}),u.current.value=null;return}if(t){var l;null===(l=t.current)||void 0===l||l.attachFiles(u.current.files)}e&&e(u.current.files)};return r.createElement(r.Fragment,null,r.createElement(n,{title:i.formatMessage({id:"attach_a_photo"}),onClick:f,testid:"buttonAttachPhoto",icon:"ico-photo-o"}),r.createElement("input",{"data-testid":"inputAttachPhoto",onChange:m,multiple:!1,ref:u,className:"srOnly",type:"file",accept:"image/*"}))}}}]);