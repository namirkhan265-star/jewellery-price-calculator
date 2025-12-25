/**
 * Jewellery Price Calculator - Frontend JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Toggle detailed price breakup
        $('.jpc-detailed-breakup summary').on('click', function() {
            $(this).toggleClass('active');
        });
        
        // Animate price changes
        $('.jpc-price-breakup').each(function() {
            $(this).addClass('jpc-fade-in');
        });
        
        // Format currency on load
        $('.jpc-metal-price').each(function() {
            var price = $(this).text();
            if (price && !price.includes('₹')) {
                $(this).text('₹' + price);
            }
        });
        
        // Marquee pause on hover
        $('.jpc-metal-rates-marquee').hover(
            function() {
                $(this).find('.jpc-metal-rates-marquee-content').css('animation-play-state', 'paused');
            },
            function() {
                $(this).find('.jpc-metal-rates-marquee-content').css('animation-play-state', 'running');
            }
        );
        
        // Responsive table handling
        if ($(window).width() < 768) {
            $('.jpc-metal-rates-table').wrap('<div class="jpc-table-responsive"></div>');
        }
        
        // Print price breakup
        $('.jpc-print-breakup').on('click', function(e) {
            e.preventDefault();
            window.print();
        });
        
        // Copy metal rates to clipboard
        $('.jpc-copy-rates').on('click', function(e) {
            e.preventDefault();
            
            var rates = '';
            $('.jpc-metal-rates-list li').each(function() {
                var name = $(this).find('.jpc-metal-name').text();
                var price = $(this).find('.jpc-metal-price').text();
                rates += name + ': ' + price + '\n';
            });
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(rates).then(function() {
                    alert('Metal rates copied to clipboard!');
                });
            }
        });
        
        // Smooth scroll to price breakup
        $('a[href="#jpc-price-breakup"]').on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.jpc-price-breakup').offset().top - 100
            }, 500);
        });
        
        // Highlight price changes
        $('.jpc-price-change').each(function() {
            var change = parseFloat($(this).data('change'));
            if (change > 0) {
                $(this).addClass('price-increase');
            } else if (change < 0) {
                $(this).addClass('price-decrease');
            }
        });
        
        // Mobile menu toggle for price breakup
        $('.jpc-breakup-toggle').on('click', function() {
            $('.jpc-price-breakup-table').slideToggle();
            $(this).toggleClass('active');
        });
        
        // Add loading animation
        $('.jpc-loading').each(function() {
            $(this).html('<span class="spinner"></span>');
        });
        
        // Variation price update (for variable products)
        $('form.variations_form').on('found_variation', function(event, variation) {
            // Update price breakup if available
            if (variation.jpc_breakup) {
                updatePriceBreakup(variation.jpc_breakup);
            }
        });
        
        function updatePriceBreakup(breakup) {
            // Update price breakup display for variations
            if ($('.jpc-price-breakup').length) {
                // Update each row with new values
                $.each(breakup, function(key, value) {
                    $('.jpc-breakup-' + key).text('₹' + parseFloat(value).toFixed(2));
                });
            }
        }
        
        // Tooltip for price components
        $('.jpc-price-component').hover(
            function() {
                var tooltip = $(this).data('tooltip');
                if (tooltip) {
                    $(this).append('<span class="jpc-tooltip">' + tooltip + '</span>');
                }
            },
            function() {
                $(this).find('.jpc-tooltip').remove();
            }
        );
        
        // Add animation class on scroll
        $(window).on('scroll', function() {
            $('.jpc-price-breakup').each(function() {
                var elementTop = $(this).offset().top;
                var viewportBottom = $(window).scrollTop() + $(window).height();
                
                if (elementTop < viewportBottom - 100) {
                    $(this).addClass('jpc-visible');
                }
            });
        });
        
    });
    
})(jQuery);
