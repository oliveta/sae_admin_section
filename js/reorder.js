window.addEvent('domready', function(){
									 
									new Asset.css(dir+'textpattern/js/reorder.css', { });
									 if (document.id('sortable'))
									 { 
										
									 sorta=new Sortables(document.id('sortable'), {
													 constrain: false,
    clone: true,
    revert: true
});
									 
									 sorta.addEvent('complete',function() {
	data=this.serialize(function(el) {return el.id.replace("item_","");});
rec=new Request({method: 'get', url: dir+'textpattern/js/reorder.php'});
rec.send("sectionorder="+data+"&tab="+tab);
rec.addEvent('success',function(responseText,responseXML) {
								
								
								});
});
									 }
									 })