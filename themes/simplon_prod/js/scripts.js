(function ($, root, undefined) {
	
	$(function () {
		
		'use strict';
		
    // -- SMOOTH SCROLL
		$(document).on('click', 'a[href^="#"]', function (event)
    {
      event.preventDefault();

      $('html, body').animate(
      {
        scrollTop: $($.attr(this, 'href')).offset().top
      }, 500);

    });

    // -- NAVIGATION MENU OPEN/CLOSE
    $('a.menu-hamburger').click(function()
    {
      var icn = $(this).find('i');
      if(icn.is('.fa-bars'))
      {
        icn.removeClass('fa-bars');
        icn.addClass('fa-times');
      }
      else
      {
        icn.addClass('fa-bars');
        icn.removeClass('fa-times');
      }
      $(this.parentNode).attr('data-visible', function(_, attr){ return attr == "0" ? "1" : "0"; });
    });

    // -- SLIDESHOW
    $('.slideshow').each(function()
    {

      // -- COMMON
      var current = 0;
      var img = $(this).find('img');
      var li  = $(this).find('li');
      var count = img.length;

      // -- INIT
      $(li[current]).addClass('active');
      $(img[current]).addClass('active');

      // -- BUBBLE NAVIGATION
      $(li).each(function(i)
      {

        $(this).click(function()
        {
          current = i;

          $(li).each(function(){ $(this).removeClass('active'); });
          $(img).each(function(){ $(this).removeClass('active'); });
          $(li[current]).addClass('active');
          $(img[current]).addClass('active');

        });

      });

      // -- ARROW NAVIGATION
      $(this).find('a').each(function()
      {

        $(this).click(function()
        {
          if(this.className=="left")
            current = current-1 >= 0 ? current-1 : count-1;

          if(this.className=="right")
            current = current+1 < count ? current+1 : 0;

          $(li).each(function(){ $(this).removeClass('active'); });
          $(img).each(function(){ $(this).removeClass('active'); });
          $(li[current]).addClass('active');
          $(img[current]).addClass('active');

        });

      });

      // -- AUTO SLIDE
      setInterval(
        function()
        { 
          current = current+1 < count ? current+1 : 0;
          $(li).each(function(){ $(this).removeClass('active'); });
          $(img).each(function(){ $(this).removeClass('active'); });
          $(li[current]).addClass('active');
          $(img[current]).addClass('active');
        }, 
        10000
      );

    });

  });

})(jQuery, this);
