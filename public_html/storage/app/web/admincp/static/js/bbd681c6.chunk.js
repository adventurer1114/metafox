"use strict";(self.webpackChunk_metafox_react=self.webpackChunk_metafox_react||[]).push([["metafox-group-components-GroupManager-GroupRules-AddNewRuleButton"],{51342:function(e,t,a){a.r(t);var r=a(85597),u=a(13478),o=a(21822),l=a(67294),n=a(17563),p=a(27361),i=a.n(p);let d=()=>{let{i18n:e,dispatch:t,usePageParams:a,getSetting:p}=(0,r.OgA)(),{identity:d}=a(),s=(0,r.z88)("pagination"),{apiUrl:g,apiParams:m}=(0,r.oHF)("group","group_rule","viewAll"),c=p(),f=i()(c,"group.maximum_number_group_rule")||0,_=d.split(".")[3],k=`${g}?${n.stringify((0,u.aZ)(m,{id:_}))}`,x=f<=s[k].ids.length,b=()=>{t({type:"addGroupRule",payload:{identity:d}})};return l.createElement(o.Z,{onClick:b,variant:"text",disabled:x},e.formatMessage({id:"add"}))};t.default=d}}]);