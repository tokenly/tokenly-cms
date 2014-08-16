//Usage: $('table').mobilizeTables();
//add data-ignore="1" to a <th> element to ignore the heading when converting to mobile
//add data-use-ignored="1" to a <td> element to have it use the previously ignored heading (where "1" = data-ignore value)

var mobileHeadings = 0;
function checkWindowSize(table){
    var width = $(window).width();
    if(width <= 767 && mobileHeadings == 0){
        mobileHeadings = 1;
        table.find('tr').each(function(){
			var num = 0
            $(this).find('td').each(function(){
                if($(this).data('heading')){
                    var thisHeading = $(this).data('heading');
               
                    $(this).before('<span class="mobile-tableHeading">' + thisHeading + '</span>');
                }
                num++;
            });
        });
    }
    if(width > 767){
        mobileHeadings = 0;
        table.find('.mobile-tableHeading').remove();
    }	
}
(function($){
    $.fn.mobilizeTables = function(){
        this.addClass('table-mobile');
        var thisTable = this;
		var headings = new Array();
        var ignoredHeadings = new Array();
		this.find('th').each(function(){
            if($(this).data('ignore')){
                headings.push('');
                ignoredHeadings[$(this).data('ignore')] = $(this).html();
            }
            else{
                headings.push($(this).html());
            }
		});
	
		this.find('tr').each(function(){
			var num = 0;
			$(this).find('td').each(function(){
                if($(this).data('use-ignored')){
                    $(this).data('heading', ignoredHeadings[$(this).data('use-ignored')]);
                }
                else{
                    $(this).data('heading', headings[num]);
                }
				num++;
			});
		});
		checkWindowSize(thisTable);
        $(window).resize(function(){
            checkWindowSize(thisTable);
        });
    }
}(jQuery));
