"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-admincp-blocks-AdminForm-Block"],{30408:function(e,t,a){a.r(t),a.d(t,{default:function(){return c}});var r=a(85597),o=a(84635),l=a(21241),n=a(15620),i=a(67294),m=a(85583);function d(){return(d=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var r in a)Object.prototype.hasOwnProperty.call(a,r)&&(e[r]=a[r])}return e}).apply(this,arguments)}var c=(0,r.j4Z)({extendBlock:function({title:e,dataSource:t,loadFrom:a,appName:c,resourceName:u,minHeight:p,formName:s,actionName:f,noHeader:h,...v}){var g;let{usePageParams:b}=(0,r.OgA)(),N="formName"!==a,F=(0,r.gM1)(c,u,s),E=(0,r.oHF)(c,u,f),q=b(),x=null!==(g=null!=E?E:q.dataSource)&&void 0!==g?g:t;return x||F?i.createElement(l.gO,null,i.createElement(l.ti,{title:e}),i.createElement(l.sU,{style:{minHeight:p}},i.createElement(o.dq,d({noTitle:!0,pageParams:q,formSchema:N?void 0:F,dataSource:N?x:void 0,loadingComponent:m.Z},v)))):i.createElement(l.gO,null,i.createElement(l.ti,{title:e}),i.createElement(l.sU,{style:{minHeight:p}},i.createElement(n.Z,{variant:"standard",color:"error"},"Oops!, could not find configuration.")))},defaults:{title:"Admin Form",loadFrom:"pageParams"},custom:{minHeight:{name:"minHeight",component:"Text",label:"Min Height",variant:"outlined",fullWidth:!0},loadFrom:{name:"loadFrom",component:"radio",label:"Load Form from Api",variant:"outlined",required:!0,fullWidth:!0,options:[{value:"pageParams",label:"Configured"},{value:"formName",label:"Fill appName, form Name"},{value:"apiUrl",label:"Fill apiUrl directly"}]},apiUrl:{name:"dataSource.apiUrl",component:"Text",label:"API Url",variant:"outlined",required:!0,fullWidth:!0,showWhen:["eq","loadFrom","apiUrl"]},appName:{name:"appName",component:"Text",label:"AppName",variant:"outlined",required:!0,fullWidth:!0,showWhen:["eq","loadFrom","formName"]},resourceName:{name:"resourceName",component:"Text",label:"Resource",variant:"outlined",required:!0,fullWidth:!0,showWhen:["eq","loadFrom","formName"]},formName:{name:"formName",component:"Text",label:"Form Name",variant:"outlined",required:!0,fullWidth:!0,showWhen:["eq","loadFrom","formName"]}}})},85583:function(e,t,a){a.d(t,{Z:function(){return c}});var r=a(15620),o=a(35705),l=a(30120),n=a(27361),i=a.n(n),m=a(67294);let d={backdrop:{position:"absolute",left:0,top:0,bottom:0,right:0},content:{position:"absolute",left:"50%",top:"50%",marginLeft:-20,marginTop:-20}};function c({error:e,size:t=40,minHeight:a=100}){let n=i()(e,"response.data.message")||i()(e,"message")||e;return m.createElement(l.Z,{sx:{minHeight:a}},m.createElement("div",{style:d.backdrop,"data-testid":"loadingIndicator"},m.createElement("div",{style:d.content},e?m.createElement(r.Z,{variant:"filled",color:"error",children:null==n?void 0:n.toString()}):m.createElement(o.Z,{color:"primary",size:"3rem"}))))}}}]);