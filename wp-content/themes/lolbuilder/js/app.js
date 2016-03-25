 jQuery(document).on('ready', function($) {
     $ = jQuery;

     function filterItems() {
         var filters = $('#statsFilters').serializeArray().reduce(function(obj, item) {
             if (item.value != '') {
                 obj[item.name] = item.value;
             }
             return obj;
         }, {});
         if (Object.keys(filters).length > 0) {
             $('.list-items .item-list').hide();
         } else {
             $('.list-items .item-list').show();
         }
         $('.list-items .item-list').filter(function() {
             var show = true;
             var $item = $(this);
             $.each(filters, function(key, value) {
                 var compare = $('.' + key + '-compare').val();
                 switch (compare) {
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

     function initChart(championInfos) {
         var ctx = $("#championChart").get(0).getContext("2d");
         var data = {
             labels: ["Difficulty", "Magic", "Defense", "Attack"],
             datasets: [{
                 fillColor: "rgba(27, 162, 185, 0.5)",
                 strokeColor: "#1BA2B9",
                 pointColor: "rgba(0,0,0,0)",
                 pointStrokeColor: "rgba(0,0,0,0)",
                 pointHighlightFill: "#F4C870",
                 pointHighlightStroke: "#F4C870",
                 data: championInfos
             }]
         };
         var championChart = new Chart(ctx).Radar(data, {
             responsive: true
         });
     }

     function setBarCharts(championStatsBase) {
         var maxStatsValue = {
         'data-base-armor': 300, 
         'data-base-attackdamage' : 300, 
         'data-base-attackspeedoffset' : 2.5,
         'data-base-hp' : 5000,
         'data-base-hpregen' : 150,
         'data-base-movespeed' : 500,
         'data-base-mp' : 5000,
         'data-base-mpregenperlevel' : 100,
         'data-base-spellblock' : 300,
         };
         console.log(championStatsBase);
         for (var i = championStatsBase.length - 1; i >= 0; i--) {
             if(typeof maxStatsValue[championStatsBase[i].name] !== 'undefined'){
                var maxValue = maxStatsValue[championStatsBase[i].name];
                 var currentValue = parseFloat(championStatsBase[i].value);
                if(championStatsBase[i].name=='data-base-attackspeedoffset'){
                    championStatsBase[i].label = 'Attack per second';
                }
                var currentValuePercent = Math.round(currentValue*100/maxValue);
                if(currentValue < 10){
                    if(championStatsBase[i].name=='data-base-attackspeedoffset'){
                        currentValue = Math.round(currentValue*1000)/1000;
                    }else{
                        currentValue = Math.round(currentValue*100)/100;
                    }
                }else{
                    currentValue = Math.round(currentValue);
                }
                var barChart = '<div class="champion-bar-chart"><div class="champion-bar-chart-wrapper"><div class="champion-bar-chart-total"  style="height:'+currentValuePercent+'%"><div class="champion-bar-chart-base" style="height:100%"><span>'+currentValue+'</span></div></div></div><div class="champion-bar-chart-value">'+currentValue+'</div><div class="champion-bar-chart-label">'+championStatsBase[i].label+'</div></div>';
                $('.chosen-champion .chosen-champion-tpl .champion-bar-charts').append(barChart);
             }             
         };
         setTimeout(function(){ 
            $('.champion-bar-charts').addClass('ready');
        }, 200);
         
     }

     function changeChampion($champion) {
         if ($('.chosen-champion .chosen-champion-tpl').length > 0) {
             $('.chosen-champion .chosen-champion-tpl').remove();
         }
         var $chosenTpl = $($('#chosenChampionTpl').text());
         $chosenTpl.find('.chosen-champion-thumbnail-img').attr('src', $champion.find('.champion-list-thumbnail img').attr('src'));
         var championAttributes = $champion[0].attributes;
         var championInfos = [];
         var championStatsBase = [];
         var j = 0;
         for (var i = championAttributes.length - 1; i >= 0; i--) {
             var attributeName = championAttributes[i]['name'];
             var attributeValue = championAttributes[i]['value'];
             var attributeLabel = championsStats[attributeName];
             if (attributeName.indexOf('base') > -1) {
                if(attributeName=='data-base-attackspeedoffset'){
                    attributeValue = 0.625/( parseFloat(attributeValue)+1);
                    attributeValue =  Math.round(attributeValue*1000)/1000;
                    attributeLabel = 'Attack per second';
                }
                 var stat = '<li class="column stat-' + attributeName + '"><strong>' + attributeLabel + ': </strong><span>' + attributeValue + '</span></li>';
                 $chosenTpl.find('.chosen-champion-stats').append(stat);
                 championStatsBase[j] = {
                     name : attributeName,
                     value: attributeValue,
                     label: attributeLabel
                 };
                 j++;
             } else {
                 if (attributeName.indexOf('infos') > -1) {
                     championInfos.push(attributeValue);
                 }
             }
         };
         $(".chosen-champion").append($chosenTpl);
         setBarCharts(championStatsBase);
         initChart(championInfos);
     }

     function removeUselessFilters() {
         $('.select-stats-filters option').show();
         $('.select-stats-filters option').filter(function() {
             var key = $(this).attr('value');
             if ($('.list-items .item-list:not([style*=none])[' + key + ']').length == 0) {
                 return true;
             } else {
                 return false;
             }
         }).hide();
     }

     function addFilter(key, label) {
         if ($(".list-stats-filters .filter-select-compare." + key + "-compare").length == 0) {
             var $tpl = $($('#statFilterTpl').text());
             $tpl.find('.column-field-filter-name').text(label);
             $tpl.find('.filter-select-compare').addClass(key + '-compare');
             $tpl.find('.filter-input-name').attr('name', key);
             $(".list-stats-filters").append($tpl);
         }
     }

     function addItemtoBuild($item) {
         if ($('.items-builder-list .item-list').length < 6) {
             $item.addClass('selected-item');
             $('.items-builder-list').append($item.clone().removeClass('selected-item'));
         }
     }

     function removeItemFromBuild($item) {
         var itemName = $item.data('name');
         $item.remove();
         if ($('.items-builder-list .item-list[data-name="' + itemName + '"]').length == 0) {
             $('.list-items .item-list[data-name="' + itemName + '"]').removeClass('selected-item');
         }
     }

     function sortItemsByAttribute(sortby) {
         var $items = $('.list-items .item-list');
         var $itemsSorted = $('<div class="items-sorted"></div>');
         for (var i = $items.length - 1; i >= 0; i--) {
             var isSorted = false;
             var j = 0;
             var $currentItem = $($items[i]);
             var currentItemValue = $currentItem.data(sortby);
             if (sortby == 'gold') {
                 currentItemValue = parseInt(currentItemValue);
             }
             while (!isSorted) {
                 var $itemToCompare = $itemsSorted.find('.item-list:eq(' + j + ')');
                 if ($itemToCompare.length == 0) {
                     $itemsSorted.append($currentItem);
                     isSorted = true;
                 } else {
                     var itemToCompareValue = $itemToCompare.data(sortby);
                     if (sortby == 'gold') {
                         itemToCompareValue = parseInt(itemToCompareValue);
                     }
                     if (currentItemValue <= itemToCompareValue) {
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
     $('.list-stats-filters').on('change keyup', '.filter-input-name', function() {
         filterItems();
         removeUselessFilters();
     })
     $('.list-stats-filters').on('change', '.filter-select-compare', function() {
         filterItems();
         removeUselessFilters();
     })
     $('.list-stats-filters').on('click', '.remove-filter', function() {
         $(this).parents('.field-filter').remove();
         filterItems();
         removeUselessFilters();
     })
     $('.list-items').on('click', '.item-list', function() {
         addItemtoBuild($(this));
     })
     $('.items-builder-list').on('click', '.item-list', function() {
         removeItemFromBuild($(this));
     })
     $('.sort-by-select').on('change', function() {
         sortItemsByAttribute($(this).val());
     })
     $('.items-selector-toggle').on('click', function() {
         $('.items-selector').slideToggle();
     })
     $('.champions-selector-toggle').on('click', function() {
         $('.champions-selector').slideToggle();
     })
     $('.add-stat-filter').on('click', function(e) {
         e.preventDefault();
         var key = $('.select-stats-filters').val();
         var label = $('.select-stats-filters option[value=' + key + ']').text();
         addFilter(key, label);
     })
     $('.champion-list').on('click', function() {
         changeChampion($(this));
     })
 })