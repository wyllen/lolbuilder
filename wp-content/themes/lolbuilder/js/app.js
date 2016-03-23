 jQuery(document).on('ready',function($){ 	
    $ = jQuery;
    function filterItems(){
    	var filters = $('#statsFilters').serializeArray().reduce(function(obj, item) {
		if(item.value!=''){
		    obj[item.name] = item.value;
		}
		    return obj;
		}, {});
		if(Object.keys(filters).length>0){
    		$('.item-list').hide();
		}else{
    		$('.item-list').show();
		}
		
		    $('.item-list').filter(function() {
		    	var show = true;
		    	var $item = $(this);
		    	$.each( filters, function(key, value) {
					var compare = $('.'+key+'-compare').val();
					switch(compare) {
					    case 'low':
					    	show = show && parseInt($item.attr(key)) < parseInt(value);
					        break;
					    case 'sup':
					    	show = show && parseInt($item.attr(key)) > parseInt(value);
					        break;
					    case 'equal':
					    	show = show && parseInt($item.attr(key)) == parseInt(value);
					        break;
					}
				});
				return show;		    	
			}).show();
    }

    function removeUselessFilters(){
    	$('.select-stats-filters option').show();
    	$('.select-stats-filters option').filter(function() {
    			var key = $(this).attr('value');
		    	if($('.item-list:not([style*=none])['+key+']').length==0){
		    		return true;
		    	}else{
		    		return false;
		    	}
			}).hide();
    }

    function addFilter(key, label){
    	if($(".list-stats-filters .filter-select-compare."+key+"-compare").length==0){
	    	var $tpl = $($('#statFilterTpl').text());
			$tpl.find('.column-field-filter-name').text(label);
			$tpl.find('.filter-select-compare').addClass(key+'-compare');
			$tpl.find('.filter-input-name').attr('name', key);
			$(".list-stats-filters").append($tpl);
    	}
    }

    removeUselessFilters();

    $('.list-stats-filters').on('change keyup', '.filter-input-name', function(){
    	 filterItems();
    	removeUselessFilters();
    })
    $('.list-stats-filters').on('change', '.filter-select-compare', function(){
    	 filterItems();
    	removeUselessFilters();
    })

    $('.list-stats-filters').on('click', '.remove-filter', function(){
    	$(this).parents('.field-filter').remove();
    	 filterItems();
    	removeUselessFilters();
    })



    $('.add-stat-filter').on('click',function(e){
    	e.preventDefault();
    	var key = $('.select-stats-filters').val();
    	var label = $('.select-stats-filters option[value='+key+']').text();
    	addFilter(key, label);
    })

 })