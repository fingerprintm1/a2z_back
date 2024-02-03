"use strict";!function(){let o,r,e,a;isDarkStyle?(o=config.colors_dark.cardColor,a=config.colors_dark.textMuted,e=config.colors_dark.bodyColor,r=config.colors_dark.headingColor):(o=config.colors.cardColor,a=config.colors.textMuted,e=config.colors.bodyColor,r=config.colors.headingColor);const l=document.querySelectorAll(".chart-progress");l&&l.forEach((function(o){const r=function(o,r){return{chart:{height:53,width:43,type:"radialBar"},plotOptions:{radialBar:{hollow:{size:"33%"},dataLabels:{show:!1},track:{background:config.colors_label.secondary}}},stroke:{lineCap:"round"},colors:[o],grid:{padding:{top:-15,bottom:-15,left:-5,right:-15}},series:[r],labels:["Progress"]}}(config.colors[o.dataset.color],o.dataset.series);new ApexCharts(o,r).render()}));const i=document.querySelector("#reportBarChart"),t={chart:{height:200,type:"bar",toolbar:{show:!1}},plotOptions:{bar:{barHeight:"60%",columnWidth:"60%",startingShape:"rounded",endingShape:"rounded",borderRadius:4,distributed:!0}},grid:{show:!1,padding:{top:-20,bottom:0,left:-10,right:-10}},colors:[config.colors_label.primary,config.colors_label.primary,config.colors_label.primary,config.colors_label.primary,config.colors.primary,config.colors_label.primary,config.colors_label.primary],dataLabels:{enabled:!1},series:[{data:[40,95,60,45,90,50,75]}],legend:{show:!1},xaxis:{categories:["Mo","Tu","We","Th","Fr","Sa","Su"],axisBorder:{show:!1},axisTicks:{show:!1},labels:{style:{colors:a,fontSize:"13px"}}},yaxis:{labels:{show:!1}}};if(void 0!==typeof i&&null!==i){new ApexCharts(i,t).render()}const s=document.querySelector("#swiper-with-pagination-cards");s&&new Swiper(s,{loop:!0,autoplay:{delay:2500,disableOnInteraction:!1},pagination:{clickable:!0,el:".swiper-pagination"}})}();
