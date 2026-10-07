/*!
 * __audioBackground вЂ” shared primitives that keep WebAudio tools alive in
 * background tabs (user report 2026-07-10: endless-piano died on tab switch;
 * the whole ambient family вЂ” white-noise, binaural-beats, isochronic-tones,
 * ocean-waves, metronome вЂ” has the same disease).
 *
 * Two independent legs, use BOTH for etalon background behavior:
 *
 * 1) createClock(fn, intervalMs) вЂ” scheduler tick source running in a
 *    dedicated Worker. Main-thread setInterval is throttled to once/minute in
 *    background Chromium (and worse in power-saver browsers); Worker timers
 *    are not visibility-throttled. Falls back to setInterval when Workers are
 *    unavailable вЂ” callers should ALSO grow their look-ahead when
 *    document.hidden as a second belt.
 *
 * 2) routeToElement(ctx, sourceNode) вЂ” reroutes a WebAudio graph's output
 *    into a hidden <audio> element via MediaStreamDestination. A tab playing
 *    a media element gets the browser's full "playing media" treatment:
 *    audible-tab exemption from throttling, MediaSession/lock-screen
 *    integration, iOS screen-lock survival and ring-switch bypass. Must be
 *    called from a user gesture (element.play() needs activation).
 *    IMPORTANT: caller must ensure sourceNode is NOT also connected to
 *    ctx.destination, or the user hears doubled audio.
 *
 * 3) holdScheduled(ctx, name) вЂ” РѕР±СЉСЏРІР»СЏРµС‚ В«Сѓ СЌС‚РѕРіРѕ РєРѕРЅС‚РµРєСЃС‚Р° Р—РђР‘Р РћРќРР РћР’РђРќ Р·РІСѓРє
 *    РІ Р±СѓРґСѓС‰РµРј, РЅРµ СѓСЃС‹РїР»СЏС‚СЊВ». Р‘РµСЂС‘С‚ РѕР±Рµ РґРІРµСЂРё СЃСЂР°Р·Сѓ: РїРѕРјРµС‡Р°РµС‚ РєРѕРЅС‚РµРєСЃС‚ РґР»СЏ
 *    РЅР°С€РµРіРѕ Р¶Рµ С…СѓРєР° РІ timbrica-core.js Р РґРµСЂР¶РёС‚ РќР• РїРѕСЃС‚Р°РІР»РµРЅРЅС‹Р№ РЅР° РїР°СѓР·Сѓ
 *    <audio> Р’ Р”РћРљРЈРњР•РќРўР• (РїСЂРµРґРёРєР°С‚ anyMediaPlaying С‚РѕРіРѕ Р¶Рµ С…СѓРєР° + В«СЃС‚СЂР°РЅРёС†Р°
 *    РёРіСЂР°РµС‚ РјРµРґРёР°В» РґР»СЏ СЃР°РјРѕРіРѕ Р±СЂР°СѓР·РµСЂР°). РЎС‡РёС‚Р°РµС‚СЃСЏ РїРѕ РёРјРµРЅР°Рј, СЃРЅРёРјР°РµС‚СЃСЏ
 *    release(). вљ пёЏ Р—РІР°С‚СЊ РёР· РѕР±СЂР°Р±РѕС‚С‡РёРєР° Р¶РµСЃС‚Р°: element.play() С‚СЂРµР±СѓРµС‚
 *    Р°РєС‚РёРІР°С†РёРё. РџРѕРґСЂРѕР±РЅРѕСЃС‚Рё Рё Р·Р°РјРµСЂ вЂ” РІ РєРѕРјРјРµРЅС‚Р°СЂРёРё Сѓ suspendAllIfIdle.
 *
 * ES5-safe, no build step.
 */(function(){"use strict";var o=[];function v(n){for(var e=0;e<o.length;e++)if(o[e].ctx===n)return o[e];return null}function i(n){try{return!!(n&&n.el&&n.el.isConnected&&!n.el.paused&&!n.el.ended)}catch{return!1}}var l=null;function p(){if(l)return l;var n=8e3,e=n/2,r=new Uint8Array(44+e),t=new DataView(r.buffer),a,c=function(u,d){for(var f=0;f<d.length;f++)r[u+f]=d.charCodeAt(f)};for(c(0,"RIFF"),t.setUint32(4,36+e,!0),c(8,"WAVEfmt "),t.setUint32(16,16,!0),t.setUint16(20,1,!0),t.setUint16(22,1,!0),t.setUint32(24,n,!0),t.setUint32(28,n,!0),t.setUint16(32,1,!0),t.setUint16(34,8,!0),c(36,"data"),t.setUint32(40,e,!0),a=44;a<r.length;a++)r[a]=128;return l=URL.createObjectURL(new Blob([r],{type:"audio/wav"})),l}function y(n){var e=document.createElement("audio");return e.setAttribute("aria-hidden","true"),e.style.cssText="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;pointer-events:none",e.loop=!0,e.src=p(),e.muted=!0,e.volume=0,e.setAttribute("playsinline",""),document.body.appendChild(e),{ctx:n,el:e,names:{}}}function h(n){try{var e=n.el.play();e&&e.catch&&e.catch(function(){})}catch{}}function m(n,e){try{if(!window.__aeFunnel)return;var r={f:"audio_hold",act:n};if(e)for(var t in e)Object.prototype.hasOwnProperty.call(e,t)&&(r[t]=e[t]);window.__aeFunnel("audio_hold",r)}catch{}}var s=null;function w(){s||(s=setInterval(function(){for(var n=!1,e=o.length-1;e>=0;e--){var r=o[e];if(Object.keys(r.names).length&&(n=!0,!i(r))){if(m("repair",{why:r.el&&!r.el.isConnected?"detached":"paused"}),!r.el.isConnected)try{document.body.appendChild(r.el)}catch{}h(r)}}n||(clearInterval(s),s=null)},5e3))}["pointerdown","keydown","touchstart"].forEach(function(n){document.addEventListener(n,function(){for(var e=0;e<o.length;e++){var r=o[e];Object.keys(r.names).length&&!i(r)&&h(r)}},{capture:!0,passive:!0})}),window.__audioBackground={holdScheduled:function(n,e){var r=e||"anon",t={release:function(){},held:function(){return!1}};if(!n||!document.body)return t;var a=v(n);if(!a){try{a=y(n)}catch(u){return m("attach_failed",{err:String(u&&u.name||u).slice(0,40)}),t}o.push(a)}a.names[r]=(a.names[r]||0)+1;try{n.__tmbScheduled=(n.__tmbScheduled||0)+1}catch{}h(a),w(),a.reported||(a.reported=1,setTimeout(function(){m(i(a)?"held":"not_playing",{tool:r})},1200));var c=!1;return{held:function(){return i(a)},release:function(){if(!c){c=!0,a.names[r]=(a.names[r]||1)-1,a.names[r]<=0&&delete a.names[r];try{n.__tmbScheduled=Math.max(0,(n.__tmbScheduled||1)-1)}catch{}if(!Object.keys(a.names).length){try{a.el.pause(),a.el.removeAttribute("src"),a.el.load(),a.el.remove()}catch{}var u=o.indexOf(a);u>=0&&o.splice(u,1)}}}}},createClock:function(n,e){var r=!1,t=null,a=null;try{t=new Worker(typeof window!="undefined"&&window.__jsSrc?window.__jsSrc("/js/audio-clock-worker.js"):"/js/audio-clock-worker.js"),t.onmessage=function(){r||n()},t.onerror=function(){try{t.terminate()}catch{}t=null,!r&&a===null&&(a=setInterval(n,e||250))},t.postMessage({cmd:"start",interval:e||250})}catch{a=setInterval(n,e||250)}return{usingWorker:!!t,stop:function(){if(r=!0,t){try{t.postMessage({cmd:"stop"}),t.terminate()}catch{}t=null}a!==null&&(clearInterval(a),a=null)}}},routeToElement:function(n,e){try{if(!n||!e||!n.createMediaStreamDestination)return{ok:!1};var r=n.createMediaStreamDestination();e.connect(r);var t=document.createElement("audio");t.setAttribute("aria-hidden","true"),t.style.display="none",t.srcObject=r.stream,t.volume=1,document.body.appendChild(t);var a=t.play();return a&&a.catch&&a.catch(function(){}),{ok:!0,element:t,resume:function(){try{var c=t.play();c&&c.catch&&c.catch(function(){})}catch{}},pause:function(){try{t.pause()}catch{}},stop:function(){try{t.pause(),t.srcObject=null,t.remove()}catch{}try{e.disconnect(r)}catch{}}}}catch{return{ok:!1}}}}})();
//# sourceMappingURL=audio-background.js.map