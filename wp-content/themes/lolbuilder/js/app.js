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
    		$('.list-items .item-list').hide();
		}else{
    		$('.list-items .item-list').show();
		}
		
		    $('.list-items .item-list').filter(function() {
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
		    	if($('.list-items .item-list:not([style*=none])['+key+']').length==0){
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

    
    function addItemtoBuild($item){
        if($('.items-builder-list .item-list').length<6){
            $item.addClass('selected-item');
            $('.items-builder-list').append($item.clone().removeClass('selected-item')); 
        }
    }

    function removeItemFromBuild($item){
        var itemName = $item.data('name');
        $item.remove();
        if($('.items-builder-list .item-list[data-name="'+itemName+'"]').length==0){
                $('.list-items .item-list[data-name="'+itemName+'"]').removeClass('selected-item');
        }
    }

    function sortItemsByAttribute(sortby){
        var $items = $('.list-items .item-list');
        var $itemsSorted = $('<div class="items-sorted"></div>');
        for (var i = $items.length - 1; i >= 0; i--) {
            var isSorted = false;
            var j = 0;
            var $currentItem = $($items[i]);
            var currentItemValue = $currentItem.data(sortby);
            if(sortby=='gold'){
                currentItemValue = parseInt(currentItemValue);
            }
            while(!isSorted){
                var $itemToCompare = $itemsSorted.find('.item-list:eq('+j+')');
                if($itemToCompare.length==0){
                    $itemsSorted.append($currentItem);
                    isSorted = true;
                }else{
                    var itemToCompareValue = $itemToCompare.data(sortby);
                    if(sortby=='gold'){
                        itemToCompareValue = parseInt(itemToCompareValue);
                    }
                    if(currentItemValue <= itemToCompareValue){
                        $currentItem.insertBefore($itemToCompare);
                        isSorted = true;
                    } 
                }
                j++;                
            } 
            
        };
        $('.list-items').html('');
        $('.list-items').append($itemsSorted);
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

    $('.list-items').on('click', '.item-list', function(){
       addItemtoBuild($(this));
    })
    $('.items-builder-list').on('click', '.item-list', function(){
       removeItemFromBuild($(this));
    })


    $('.sort-by-select').on('change',function(){
    	sortItemsByAttribute($(this).val());
    })

    $('.items-selector-toggle').on('click',function(){
        $('.items-selector').slideToggle();
    })


    $('.champions-selector-toggle').on('click',function(){
        $('.champions-selector').slideToggle();
    })

    $('.add-stat-filter').on('click',function(e){
    	e.preventDefault();
    	var key = $('.select-stats-filters').val();
    	var label = $('.select-stats-filters option[value='+key+']').text();
    	addFilter(key, label);
    })

 })