"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-core-components-StatusComposerCheckinButton"],{6570:function(e,t,o){o.r(t),o.d(t,{default:function(){return i}});var a=o(85597),c=o(27361),n=o.n(c),r=o(67294);function i({control:e,disabled:t,composerRef:o}){let{i18n:c,dialogBackend:i}=(0,a.OgA)(),{setTags:l}=o.current,s=()=>i.present({component:"core.dialog.PlacePickerDialog",props:{defaultValue:n()(o.current.state,"tags.place.value")}}).then(e=>{!1===e?l("place",{as:"StatusComposerControlTaggedPlace",priority:3,value:void 0}):e&&l("place",{as:"StatusComposerControlTaggedPlace",priority:3,value:e})});return r.createElement(e,{disabled:t,onClick:s,testid:"buttonAttachLocation",icon:"ico-checkin-o",label:c.formatMessage({id:"checkin"}),title:c.formatMessage({id:t?"this_cant_be_combined":"checkin"})})}}}]);