<?php if( have_rows('section') ): ?><?php while ( have_rows('section') ) : the_row(); ?>



<?php if( get_row_layout() == 'accordion' ): ?>



    <?php if( have_rows('accordion') ): ?>


    <section class="section">



            <div class="accordion">

        <?php while( have_rows('accordion') ): the_row(); ?>

            
            <div class="accordion__item" id="">
            
                <button class="accordion__button">
                    <?php the_sub_field('item_title'); ?>
                </button>

                <div class="accordion__panel">

                    <div class="accordion__panel__inner">
                        <?php the_sub_field('item_content'); ?>                        
                    </div>

                </div>

            </div>
            

        <?php endwhile; ?>

            </div>

    </section>

    <script>



var acc = document.getElementsByClassName("accordion__button");
var i;

for (i = 0; i < acc.length; i++) {
	acc[i].addEventListener("click", function (event) {
		event.preventDefault()
		this.classList.toggle("active");
		var panel = this.nextElementSibling;
		if (panel.style.maxHeight) {
			panel.style.maxHeight = null;
		} else {
			panel.style.maxHeight = panel.scrollHeight + "px";
		}
	});
}
    </script>

    <?php endif; ?>


<?php endif; ?>

<?php endwhile; ?>
<?php else : ?>
<?php endif; ?>