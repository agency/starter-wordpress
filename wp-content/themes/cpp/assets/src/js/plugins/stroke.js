(function ($) {
    $.fn.stroke = function (opts) {
        
        let s = $.extend({
            'lineClass' : 'g-line'
        },opts);

        return this.each(function () {

            let $line, $prev;
            
            $(this).find('.' + s.lineClass).contents().unwrap();

            $(this).html(function (i, h) {
                return h.replace(/(\b[\w']+\b)/g, '<span class="' + s.lineClass + '">$1</span>');
            });
            
           	$(this).find('.g-line + .g-line').each((i, el) =>{
                $line = $(el),
                $prev = $line.prev('.g-line');
                
                if ($line.offset().top === $prev.offset().top) {
                    $prev.append(el.previousSibling, $line.contents());
                    $line.remove();
                }
            });
        });
    };
})(jQuery);
