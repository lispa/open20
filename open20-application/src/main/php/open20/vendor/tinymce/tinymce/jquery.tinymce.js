!function(){var e,t,n,i,r,a=[];r="undefined"!=typeof global?global:window,i=r.jQuery;var c=function(){return r.tinymce};i.fn.tinymce=function(e){var l,u,s,f=this,p="";if(!f.length)return f;if(!e)return c()?c().get(f[0].id):null;f.css("visibility","hidden");var d=function(){var t=[],r=0;n||(o(),n=!0),f.each(function(n,i){var a,o=i.id,l=e.oninit;o||(i.id=o=c().DOM.uniqueId()),c().get(o)||(a=c().createEditor(o,e),t.push(a),a.on("init",function(){var e,n=l;f.css("visibility",""),l&&++r==t.length&&("string"==typeof n&&(e=-1===n.indexOf(".")?null:c().resolve(n.replace(/\.\w+$/,"")),n=c().resolve(n)),n.apply(e||c(),t))}))}),i.each(t,function(e,t){t.render()})};if(r.tinymce||t||!(l=e.script_url))1===t?a.push(d):d();else{t=1,u=l.substring(0,l.lastIndexOf("/")),-1!=l.indexOf(".min")&&(p=".min"),r.tinymce=r.tinyMCEPreInit||{base:u,suffix:p},-1!=l.indexOf("gzip")&&(s=e.language||"en",l=l+(/\?/.test(l)?"&":"?")+"js=true&core=true&suffix="+escape(p)+"&themes="+escape(e.theme||"modern")+"&plugins="+escape(e.plugins||"")+"&languages="+(s||""),r.tinyMCE_GZ||(r.tinyMCE_GZ={start:function(){var t=function(e){c().ScriptLoader.markDone(c().baseURI.toAbsolute(e))};t("langs/"+s+".js"),t("themes/"+e.theme+"/theme"+p+".js"),t("themes/"+e.theme+"/langs/"+s+".js"),i.each(e.plugins.split(","),function(e,n){n&&(t("plugins/"+n+"/plugin"+p+".js"),t("plugins/"+n+"/langs/"+s+".js"))})},end:function(){}}));var v=document.createElement("script");v.type="text/javascript",v.onload=v.onreadystatechange=function(n){n=n||window.event,2===t||"load"!=n.type&&!/complete|loaded/.test(v.readyState)||(c().dom.Event.domLoaded=1,t=2,e.script_loaded&&e.script_loaded(),d(),i.each(a,function(e,t){t()}))},v.src=l,document.body.appendChild(v)}return f},i.extend(i.expr[":"],{tinymce:function(e){var t;return!!(e.id&&"tinymce"in r&&(t=c().get(e.id))&&t.editorManager===c())}});var o=function(){var t=function(e){"remove"===e&&this.each(function(e,t){var n=a(t);n&&n.remove()}),this.find("span.mceEditor,div.mceEditor").each(function(e,t){var n=c().get(t.id.replace(/_parent$/,""));n&&n.remove()})},n=function(e){var n,i=this;if(null!=e)t.call(i),i.each(function(t,n){var i;(i=c().get(n.id))&&i.setContent(e)});else if(i.length>0&&(n=c().get(i[0].id)))return n.getContent()},a=function(e){var t=null;return e&&e.id&&r.tinymce&&(t=c().get(e.id)),t},o=function(e){return!!(e&&e.length&&r.tinymce&&e.is(":tinymce"))},l={};i.each(["text","html","val"],function(t,r){var c=l[r]=i.fn[r],u="text"===r;i.fn[r]=function(t){var r=this;if(!o(r))return c.apply(r,arguments);if(t!==e)return n.call(r.filter(":tinymce"),t),c.apply(r.not(":tinymce"),arguments),r;var l="",s=arguments;return(u?r:r.eq(0)).each(function(e,t){var n=a(t);l+=n?u?n.getContent().replace(/<(?:"[^"]*"|'[^']*'|[^'">])*>/g,""):n.getContent({save:!0}):c.apply(i(t),s)}),l}}),i.each(["append","prepend"],function(t,n){var r=l[n]=i.fn[n],c="prepend"===n;i.fn[n]=function(t){var n=this;return o(n)?t!==e?("string"==typeof t&&n.filter(":tinymce").each(function(e,n){var i=a(n);i&&i.setContent(c?t+i.getContent():i.getContent()+t)}),r.apply(n.not(":tinymce"),arguments),n):void 0:r.apply(n,arguments)}}),i.each(["remove","replaceWith","replaceAll","empty"],function(e,n){var r=l[n]=i.fn[n];i.fn[n]=function(){return t.call(this,n),r.apply(this,arguments)}}),l.attr=i.fn.attr,i.fn.attr=function(t,r){var c=this,u=arguments;if(!t||"value"!==t||!o(l))return l.attr.apply(c,u);if(r!==e)return n.call(c.filter(":tinymce"),r),l.attr.apply(c.not(":tinymce"),u),c;var s=c[0],f=a(s);return f?f.getContent({save:!0}):l.attr.apply(i(s),u)}}}();