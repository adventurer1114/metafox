"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-livestreaming-components-FlyReaction"],{85832:function(t,e,o){o.r(e);var a=o(67294),i=o(81719),l=o(91647),n=o(30120),r=o(85597),s=o(70917);let c=s.F4`
    0% {
        bottom:0;
        opacity: 1;
    }
    30% {
        transform:translateX(30px);
        bottom: 30%;
        opacity: 1
    }
    70% {
       transform:translateX(0px);
       bottom: 70%;
       opacity: 1
    }
    100% {
        transform:translateX(30px);
        bottom: 100%;
        opacity: 0;
    }
`,m="FlyReaction",p=(0,i.ZP)(n.Z,{name:m,slot:"ReactionIcon",shouldForwardProp:t=>"index"!==t})(({theme:t,index:e})=>({display:"flex",alignItems:"center",justifyContent:"center",position:"absolute",animation:`${c} linear 2s forwards `,animationDelay:`${20*Math.floor(99*Math.random())}ms`,left:`calc(50% - ${Math.max(10*Math.floor(10*Math.random()),30)}%)`,bottom:"-32px",width:"24px",height:"24px","& img":{width:"100%",height:"100%"}})),d=(0,i.ZP)(l.Z,{name:m,slot:"Wrapper",shouldForwardProp:t=>"backgroundColor"!==t})(({theme:t})=>({position:"absolute",left:"24px",bottom:0,width:"100px",height:"70%",pointerEvents:"none"}));e.default=function({streamKey:t,identity:e}){let{firebaseBackend:o,dispatch:i}=(0,r.OgA)(),l=o.getFirestore(),n=(0,r.zjB)(l,{collection:"live_video_like",docID:t}),s=(null==n?void 0:n.like)||[],c=s.slice(Math.max(s.length-20,0));return(a.useEffect(()=>{i({type:"livestreaming/updateStatistic",payload:{identity:e,most_reactions:null==n?void 0:n.most_reactions,statistic:(null==n?void 0:n.statistic)||{total_like:null==n?void 0:n.total_like}}})},[null==n?void 0:n.total_like,null==n?void 0:n.most_reactions,null==n?void 0:n.statistic]),t&&(null==c?void 0:c.length))?a.createElement(d,null,c.map(({reaction:{icon:t,title:e,id:o}},i)=>a.createElement(p,{key:`live_reaction_${i}`,index:i},a.createElement("img",{src:t,alt:e})))):null}}}]);