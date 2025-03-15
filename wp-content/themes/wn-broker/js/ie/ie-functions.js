/*------------------------------------------------------------------------*\
    SVG REPLACE FOR IE8
\*------------------------------------------------------------------------*/

if (!Modernizr.svg) {
  jQuery('img[src$=".svg"]').each(function() {
      //Replaces 'logo.svg' with 'logo.png'.
      jQuery(this).attr('src', jQuery(this).attr('src').replace('.svg', '.png'));
  });
}
