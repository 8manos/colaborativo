 	</div> <!-- .container -->

 	<footer id="footer">
 		<div class="container" id="footer-inner">
 			<div class="row">
 				<div class="span11">
 					<a class="ir sprite" href="<?php bloginfo('url'); ?>" id="footer-brand" title="<?php bloginfo('title'); ?>"><?php bloginfo('title'); ?></a>
 					<ul class="nav">
						<?php wp_list_categories('title_li=&hide_empty=0'); ?>
					</ul>
 				</div>
 				<div class="span1 aright">
 					<!-- menu social -->
 				</div>
 			</div>
 			<div class="row">
 				<div class="span5">
 					<p class="copyright">
 						<?php bloginfo('title'); ?> &copy; <?php echo date('Y'); ?> | <?php _e('TODOS LOS DERECHOS RESERVADOS', 'colaborativo'); ?>
 					</p>
 				</div>
 				<div class="span7 aright">
 					<p>
 						<?php bloginfo('title'); ?> fue creado por: <a class="ir sprite" id="activa" href="http://activamc.com" target="_blank">Activa MC</a> <a class="ir sprite" id="manos" href="http://8manos.com" target="_blank">&infin;manos</a>
 						Cubrimiento: <a class="ir sprite" id="rey" href="#">Contenidos El rey</a>
 					</p>
 				</div>
 			</div>
 		</div>
 	</footer>

    <!-- Modal -->
    <div class="modal hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Modal header</h3>
      </div>
      <div class="modal-body">
        <p>One fine body…</p>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary">Save changes</button>
      </div>
    </div>

    <script>
        var _gaq=[['_setAccount','UA-32985059-1'],['_trackPageview']];
        (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
        g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g,s)}(document,'script'));
    </script>
    <?php wp_footer(); ?>
    <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4e5a7cc445b73dea"></script>
</body>
</html>