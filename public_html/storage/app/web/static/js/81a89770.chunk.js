"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-layout-LayoutBlock-BlockWithEditingPage"],{66598:function(e,t,r){let n;r.r(t),r.d(t,{default:function(){return S}});var i=r(85597),a=r(76224),o=r(30120),s=r(50130),c=r(81719),l=r(38790),d=r(67294),g=r(86487),u=r(66320),h=r(30106);let p=(0,c.ZP)(o.Z,{name:"BlockEditingDragLayer",slot:"Block"})(({theme:e,currentOffset:t,mouseOffset:r,width:n})=>({backgroundColor:e.palette.background.paper,display:"flex",alignItems:"center",padding:"4px 8px",margin:"8px 0",borderRadius:"8px",minWidth:"300px",minHeight:"40px",cursor:"move",border:"2px dashed transparent",boxSizing:"border-box",opacity:.8,marginLeft:r&&t?`${r.x-t.x-(n?n/2:0)}px`:0})),f=({item:e})=>{return d.createElement(p,null,e.title)},m=(0,c.ZP)("div",{name:"EditBlock",slot:"root"})({}),D=(0,c.ZP)("div",{name:"EditBlock",slot:"controller",shouldForwardProp:e=>"dragging"!==e&&"canDrag"!==e})(({theme:e,canDrag:t,dragging:r,disabled:n})=>({display:"flex",alignItems:"center",justifyContent:"space-between",padding:e.spacing(.5,1),backgroundColor:"#fff",margin:e.spacing(1,0),borderRadius:4,boxShadow:e.shadows[1],cursor:"pointer",border:"2px dashed transparent",boxSizing:"border-box",'&[draggable="true"]':{cursor:t?"move":"default"},...n?{background:"rgba(0,0,0,0.1)",cursor:"default"}:{"&:hover":{opacity:.8}},...r&&{opacity:0,height:0,margin:0,padding:0,"&:hover":{opacity:0}}})),b=(0,c.ZP)("div",{name:"EditBlock",slot:"actions"})(({theme:e})=>({"& button":{marginLeft:e.spacing(.5)}})),v=(0,c.ZP)("div",{name:"EditBlock",slot:"content"})(({theme:e})=>({paddingLeft:e.spacing(2)}));function S(e){let{blockId:t,title:r,layoutEditMode:o,blockDisabled:c,blockName:p,blockOrigin:S,slotName:w,elements:C}=e,{layoutBackend:P,jsxBackend:I,dispatch:O,i18n:y}=(0,i.OgA)(),{title:k,container:M}=d.useMemo(()=>P.getBlockView(p)||{title:p,container:!1},[p,P]),E=r||k||p,A="layout"===S&&o===h.G.editLayout||"page"===S&&o===h.G.editPageContent||"site"===S&&o===h.G.editSiteContent,x="page"!==S&&o===h.G.editPageContent;d.useEffect(()=>{_((n||((n=new Image).src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="),n),{captureDraggingState:!1})},[]);let[R,B,_]=(0,g.c)({type:u.C,item:{type:u.C,blockId:t,blockName:p,slotName:w,draggingComponent:f,title:E},canDrag:A,collect:e=>({isDragging:!!e.isDragging()})}),H=t=>O({type:t,payload:e});return d.createElement(m,null,d.createElement(D,{canDrag:A,ref:B,dragging:R.isDragging,disabled:c&&o===h.G.editPageContent},E,d.createElement(b,null,M?d.createElement(l.Z,{title:y.formatMessage({id:"add_layout_block"})},d.createElement(s.Z,{onClick:()=>H("@layout/createBlock"),size:"smaller"},d.createElement(a.zb,{icon:"ico-plus"}))):null,A?d.createElement(l.Z,{title:y.formatMessage({id:"edit_layout_block"})},d.createElement(s.Z,{onClick:()=>H("@layout/editBlock"),size:"smaller"},d.createElement(a.zb,{icon:"ico-pencil"}))):null,A?d.createElement(l.Z,{title:y.formatMessage({id:"delete_layout_block"})},d.createElement(s.Z,{onClick:()=>H("@layout/deleteBlock"),size:"smaller"},d.createElement(a.zb,{icon:"ico-trash"}))):null,x?d.createElement(l.Z,{title:y.formatMessage({id:c?"layout_enable_layout_block":"layout_disable_layout_block"})},d.createElement(s.Z,{onClick:()=>H("@layout/toggleBlock"),size:"smaller"},d.createElement(a.zb,{icon:"ico-eye-off"}))):null)),M?d.createElement(v,null,I.render(C)):null)}},86487:function(e,t,r){r.d(t,{c:function(){return b}});var n=r(14912),i=r(41563),a=r(72203),o=r(67294),s=r(46580),c=r(98697),l=r(41317);class d{receiveHandlerId(e){this.handlerId!==e&&(this.handlerId=e,this.reconnect())}get connectTarget(){return this.dragSource}get dragSourceOptions(){return this.dragSourceOptionsInternal}set dragSourceOptions(e){this.dragSourceOptionsInternal=e}get dragPreviewOptions(){return this.dragPreviewOptionsInternal}set dragPreviewOptions(e){this.dragPreviewOptionsInternal=e}reconnect(){let e=this.reconnectDragSource();this.reconnectDragPreview(e)}reconnectDragSource(){let e=this.dragSource,t=this.didHandlerIdChange()||this.didConnectedDragSourceChange()||this.didDragSourceOptionsChange();return(t&&this.disconnectDragSource(),this.handlerId)?e?(t&&(this.lastConnectedHandlerId=this.handlerId,this.lastConnectedDragSource=e,this.lastConnectedDragSourceOptions=this.dragSourceOptions,this.dragSourceUnsubscribe=this.backend.connectDragSource(this.handlerId,e,this.dragSourceOptions)),t):(this.lastConnectedDragSource=e,t):t}reconnectDragPreview(e=!1){let t=this.dragPreview,r=e||this.didHandlerIdChange()||this.didConnectedDragPreviewChange()||this.didDragPreviewOptionsChange();if(r&&this.disconnectDragPreview(),this.handlerId){if(!t){this.lastConnectedDragPreview=t;return}r&&(this.lastConnectedHandlerId=this.handlerId,this.lastConnectedDragPreview=t,this.lastConnectedDragPreviewOptions=this.dragPreviewOptions,this.dragPreviewUnsubscribe=this.backend.connectDragPreview(this.handlerId,t,this.dragPreviewOptions))}}didHandlerIdChange(){return this.lastConnectedHandlerId!==this.handlerId}didConnectedDragSourceChange(){return this.lastConnectedDragSource!==this.dragSource}didConnectedDragPreviewChange(){return this.lastConnectedDragPreview!==this.dragPreview}didDragSourceOptionsChange(){return!(0,s.w)(this.lastConnectedDragSourceOptions,this.dragSourceOptions)}didDragPreviewOptionsChange(){return!(0,s.w)(this.lastConnectedDragPreviewOptions,this.dragPreviewOptions)}disconnectDragSource(){this.dragSourceUnsubscribe&&(this.dragSourceUnsubscribe(),this.dragSourceUnsubscribe=void 0)}disconnectDragPreview(){this.dragPreviewUnsubscribe&&(this.dragPreviewUnsubscribe(),this.dragPreviewUnsubscribe=void 0,this.dragPreviewNode=null,this.dragPreviewRef=null)}get dragSource(){return this.dragSourceNode||this.dragSourceRef&&this.dragSourceRef.current}get dragPreview(){return this.dragPreviewNode||this.dragPreviewRef&&this.dragPreviewRef.current}clearDragSource(){this.dragSourceNode=null,this.dragSourceRef=null}clearDragPreview(){this.dragPreviewNode=null,this.dragPreviewRef=null}constructor(e){this.hooks=(0,l.p)({dragSource:(e,t)=>{this.clearDragSource(),this.dragSourceOptions=t||null,(0,c.d)(e)?this.dragSourceRef=e:this.dragSourceNode=e,this.reconnectDragSource()},dragPreview:(e,t)=>{this.clearDragPreview(),this.dragPreviewOptions=t||null,(0,c.d)(e)?this.dragPreviewRef=e:this.dragPreviewNode=e,this.reconnectDragPreview()}}),this.handlerId=null,this.dragSourceRef=null,this.dragSourceOptionsInternal=null,this.dragPreviewRef=null,this.dragPreviewOptionsInternal=null,this.lastConnectedHandlerId=null,this.lastConnectedDragSource=null,this.lastConnectedDragSourceOptions=null,this.lastConnectedDragPreview=null,this.lastConnectedDragPreviewOptions=null,this.backend=e}}var g=r(5109),u=r(66618);let h=!1,p=!1;class f{receiveHandlerId(e){this.sourceId=e}getHandlerId(){return this.sourceId}canDrag(){(0,n.k)(!h,"You may not call monitor.canDrag() inside your canDrag() implementation. Read more: http://react-dnd.github.io/react-dnd/docs/api/drag-source-monitor");try{return h=!0,this.internalMonitor.canDragSource(this.sourceId)}finally{h=!1}}isDragging(){if(!this.sourceId)return!1;(0,n.k)(!p,"You may not call monitor.isDragging() inside your isDragging() implementation. Read more: http://react-dnd.github.io/react-dnd/docs/api/drag-source-monitor");try{return p=!0,this.internalMonitor.isDraggingSource(this.sourceId)}finally{p=!1}}subscribeToStateChange(e,t){return this.internalMonitor.subscribeToStateChange(e,t)}isDraggingSource(e){return this.internalMonitor.isDraggingSource(e)}isOverTarget(e,t){return this.internalMonitor.isOverTarget(e,t)}getTargetIds(){return this.internalMonitor.getTargetIds()}isSourcePublic(){return this.internalMonitor.isSourcePublic()}getSourceId(){return this.internalMonitor.getSourceId()}subscribeToOffsetChange(e){return this.internalMonitor.subscribeToOffsetChange(e)}canDragSource(e){return this.internalMonitor.canDragSource(e)}canDropOnTarget(e){return this.internalMonitor.canDropOnTarget(e)}getItemType(){return this.internalMonitor.getItemType()}getItem(){return this.internalMonitor.getItem()}getDropResult(){return this.internalMonitor.getDropResult()}didDrop(){return this.internalMonitor.didDrop()}getInitialClientOffset(){return this.internalMonitor.getInitialClientOffset()}getInitialSourceClientOffset(){return this.internalMonitor.getInitialSourceClientOffset()}getSourceClientOffset(){return this.internalMonitor.getSourceClientOffset()}getClientOffset(){return this.internalMonitor.getClientOffset()}getDifferenceFromInitialOffset(){return this.internalMonitor.getDifferenceFromInitialOffset()}constructor(e){this.sourceId=null,this.internalMonitor=e.getMonitor()}}var m=r(55872);class D{beginDrag(){let e=this.spec,t=this.monitor,r=null;return null!=(r="object"==typeof e.item?e.item:"function"==typeof e.item?e.item(t):{})?r:null}canDrag(){let e=this.spec,t=this.monitor;return"boolean"==typeof e.canDrag?e.canDrag:"function"!=typeof e.canDrag||e.canDrag(t)}isDragging(e,t){let r=this.spec,n=this.monitor,{isDragging:i}=r;return i?i(n):t===e.getSourceId()}endDrag(){let e=this.spec,t=this.monitor,r=this.connector,{end:n}=e;n&&n(t.getItem(),t),r.reconnect()}constructor(e,t,r){this.spec=e,this.monitor=t,this.connector=r}}function b(e,t){let r=(0,a.w)(e,t);(0,n.k)(!r.begin,"useDrag::spec.begin was deprecated in v14. Replace spec.begin() with spec.item(). (see more here - https://react-dnd.github.io/react-dnd/docs/api/use-drag)");let s=function(){let e=(0,g.N)();return(0,o.useMemo)(()=>new f(e),[e])}(),c=function(e,t){let r=(0,g.N)(),n=(0,o.useMemo)(()=>new d(r.getBackend()),[r]);return(0,u.L)(()=>{return n.dragSourceOptions=e||null,n.reconnect(),()=>n.disconnectDragSource()},[n,e]),(0,u.L)(()=>{return n.dragPreviewOptions=t||null,n.reconnect(),()=>n.disconnectDragPreview()},[n,t]),n}(r.options,r.previewOptions);return!function(e,t,r){let i=(0,g.N)(),a=function(e,t,r){let n=(0,o.useMemo)(()=>new D(e,t,r),[t,r]);return(0,o.useEffect)(()=>{n.spec=e},[e]),n}(e,t,r),s=(0,o.useMemo)(()=>{let t=e.type;return(0,n.k)(null!=t,"spec.type must be defined"),t},[e]);(0,u.L)(function(){if(null!=s){let[e,n]=(0,m.w)(s,a,i);return t.receiveHandlerId(e),r.receiveHandlerId(e),n}},[i,t,r,a,s])}(r,s,c),[(0,i.J)(r.collect,s,c),(0,o.useMemo)(()=>c.hooks.dragSource(),[c]),(0,o.useMemo)(()=>c.hooks.dragPreview(),[c])]}}}]);