var win=window.dialogArguments||opener||parent||top;(function(a){win.kcFileMultiple=function(f){var b=win.kcSettings.upload.target,g=b.data("currentFiles"),d=b.children().last(),h=a(),c=null;for(var e in f){if(!f.hasOwnProperty(e)||a.inArray(f[e].id,g)>-1){continue}c=d.clone().removeClass("hidden");c.find("img").attr("src",f[e].img);c.find("input").val(f[e].id).prop("checked",false);c.find(".title").text(f[e].title);h=h.add(c)}b.append(h);if(d.is(".hidden")){h.show();d.remove()}b.show().prev(".info").show()};win.kcFileSingle=function(f){var b=win.kcSettings.upload.target,e=b.find("span").text(f.title),d=b.find("img").attr("src",f.img);b.removeAttr("data-type");b.find("input").val(f.id);b.children("a.up").hide();b.find("p").fadeIn().children("a.up").show().siblings("a.rm").show();if(f.type=="image"){b.attr("data-type",f.type);e.hide();var c=b.data("size");if(c!=="thumbnail"){a.ajax({type:"POST",url:ajaxurl,data:{action:"kc_get_image_url",id:f.id,size:c},success:function(g){if(g){d.attr("src",g)}}})}}else{e.show()}}})(jQuery);jQuery(document).ready(function(g){var e=g("body"),n=g("#kcsb"),h=g("form.kcsb"),c=g("#kc-settings-form");if(c.length){var f=c.find("div.metabox-holder");if(f.length){var b=f.attr("id"),l=c.find(":checkbox");if(l.length){var m=g();l.each(function(){var q=g("#"+b+"-"+this.value);if(!q.length){return}var p=g(this),o=g("#"+b+"-"+this.value+"-hide");p.data("sectHider",o).data("sectBox",q);if(!(this.checked===o[0].checked)){o.prop("checked",this.checked).triggerHandler("click")}m=m.add(p)});if(m.length){m.change(function(){var o=g(this);o.data("sectHider").prop("checked",this.checked).triggerHandler("click");if(this.checked){o.data("sectBox").kcGoto({offset:-40,speed:"slow"})}})}}}}g("ul.kc-rows").sortable({axis:"y",start:function(o,p){p.placeholder.height(p.item.outerHeight())},stop:function(o,p){p.item.parent().kcReorder(p.item.data("mode"),true).children().each(function(){g("> .actions .count",this).text(g(this).index()+1)})}});e.on("click",".row a.del",function(r){r.preventDefault();var o=g(this).closest(".row");if(!o.siblings(".row").length){return false}var p=o.parent(),s=o.data("mode"),q=o.is(":last-child");o.addClass("removing").fadeOut("slow",function(){o.remove();if(!q){p.kcReorder(s,true);if(h.length){p.children().each(function(){g("> .actions .count",this).text(g(this).index()+1)})}}})});e.on("click",".row a.add",function(t){t.preventDefault();var w=g(this),v=w.closest(".row"),p=v.parent(),s=v.data("mode"),q=v.is(":last-child"),r=v.clone(false).addClass("adding"),u=false,o=400;if(s=="sections"){u=true;o=1200}else{if(s=="fields"){u=true;o=800}}if(h.length){r.find(".kc-rows").each(function(){g(this).children(".row").not(":first").remove()});r.find(":input").each(function(){var x=g(this);if(this.type=="text"||this.type=="textarea"){x.removeAttr("style").val("")}else{if(this.type=="checkbox"||this.type=="radio"){x.prop("checked",this.checked)}}if(x.is(".kcsb-ids")){x.kcsbUnique()}})}else{r.find(":input").each(function(){var x=g(this);if(x.data("nocleanup")!==true){x.val("")}})}g(".hasdep",r).kcFormDep();v.after(r);if(u){r.kcGoto({offset:-100,speed:o})}setTimeout(function(){r.removeClass("adding")},o);p.kcReorder(s,true);if(h.length){if(q){g("> .actions .count",r).text(r.index()+1)}else{p.children().each(function(){g("> .actions .count",this).text(g(this).index()+1)})}}});e.on("click",".row a.clear",function(o){o.preventDefault();g(this).closest(".row").find(":input").val("")});var a=g("input[type=date]");if(a.length&&Modernizr.inputtypes.date===false){var i=g("body").is(".admin-color-classic")?"cupertino":"flick";Modernizr.load([{load:win.kcSettings.paths.styles+"/jquery-ui/"+i+"/style.css",complete:function(){a.datepicker({dateFormat:"yy-mm-dd"})}}])}var k=g("input[type=color]");if(k.length&&Modernizr.inputtypes.color===false){Modernizr.load([{load:[win.kcSettings.paths.scripts+"/colorpicker/js/colorpicker.js",win.kcSettings.paths.scripts+"/colorpicker/css/colorpicker.css",win.kcSettings.paths.scripts+"/rgbcolor.js"],complete:function(){k.ColorPicker({onBeforeShow:function(){g(this).ColorPickerSetColor(this.value)},onSubmit:function(o,s,q,r){var p="#"+s;g(r).css({backgroundColor:p,color:invertColor(p)}).val(p).ColorPickerHide()}}).each(function(){var o=g(this);if(o.val()!==""){o.css({backgroundColor:this.value,color:invertColor(this.value)})}})}}])}e.on("click",".kcs-file a.rm",function(p){p.preventDefault();var o=g(this).closest(".row");o.addClass("removing").fadeOut("slow",function(){if(o.siblings().length){o.remove()}else{o.removeClass("removing").addClass("hidden").find(":input").val("").prop("checked",false);g("input.fileID",o).prop("disabled",true);o.parent().hide().prev(".info").hide()}})});e.on("click","a.kcsf-upload",function(s){s.preventDefault();var q=g(this),o=q.siblings(".kc-rows"),p=o.find(".row.hidden"),r=[];if(p.length){g("input.fileID",p).prop("disabled",false)}else{g("input.fileID",o).each(function(){r.push(this.value)})}win.kcSettings.upload.target=o.data("currentFiles",r);tb_show("",q.attr("href"))});e.on("click",".kcs-file-single a.rm",function(o){o.preventDefault();g(this).fadeOut().closest("div").find("p.current").fadeOut(function(){g(this).siblings("a.up").show().siblings("input").val("")})});e.on("click",".kcs-file-single a.up",function(p){p.preventDefault();var o=g(this);win.kcSettings.upload.target=o.closest("div");tb_show("",o.attr("href"))});g("ul.kc-sortable").sortable({axis:"y",start:function(o,p){p.placeholder.height(p.item.outerHeight())}});g("a.kc-help-trigger").on("click",function(o){o.preventDefault();g("#contextual-help-link").click();g("#screen-meta").kcGoto()});g(".kcs-tabs").kcTabs();var j=g("#addtag");if(j.length){var d=g();g("div.kcs-field").each(function(){d=d.add(g(this).clone())});if(d.length){j.ajaxComplete(function(p,q,o){if(o.data.indexOf("action=add-tag")<0){return}g("div.kcs-field").each(function(r){g(this).replaceWith(d.eq(r).clone())});g(".kcs-tabs",j).kcTabs();j.trigger("kcsRefreshed")})}}if(n.length){if(!n.is(".hidden")){n.kcGoto()}g(".hasdep",n).kcFormDep();e.on("blur","input.kcsb-slug",function(){var o=g(this);o.val(kcsbSlug(o.val()))});g("input.kcsb-ids").kcsbUnique();e.on("blur","input.required, input.clone-id",function(){g(this).kcsbCheck()});g("#new-kcsb").on("click",function(o){o.preventDefault();n.kcGoto()});g("a.kcsb-cancel").on("click",function(o){o.preventDefault();g("#kcsb").slideUp("slow")});g("a.clone-open").on("click",function(o){o.preventDefault();g(this).parent().children().hide().filter("div.kcsb-clone").fadeIn()});g("a.clone-do").on("click",function(p){var o=g(this),q=g(this).siblings("input");if(q.kcsbCheck()===false){return false}o.attr("href",o.attr("href")+"&new="+q.val())});g("input.clone-id").on("keypress",function(p){var o=p.keyCode||p.which;if(o===13){p.preventDefault();g(this).blur().siblings("a.clone-do").click()}});g(".kcsb-tools a.close").on("click",function(p){p.preventDefault();var o=g(this);o.siblings("input").val("");o.parent().fadeOut(function(){g(this).siblings().show()})});h.submit(function(p){var o=true;g(this).find("input.required").not(":disabled").each(function(){if(g(this).kcsbCheck()===false){o=false;return false}});if(!o){return false}})}});