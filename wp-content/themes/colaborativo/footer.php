 	</div> <!-- .container -->

 	<footer id="footer">
 		<div class="container" id="footer-inner">
 			<div class="row">
 				<div class="span5">

          <a class="brand ir sprite" href="<?php bloginfo('url'); ?>" id="branding"><?php bloginfo('title'); ?></a>

          <span class="ir sprite" id="beta">
            <?php _e('Estamos en beta', 'colaborativo'); ?>
          </span>

 				</div>
 				<div class="span7 aright navbar">

   				<div class="aright right" id="menu-social">
            <label><?php _e('Síguenos', 'colaborativo'); ?></label>
            <a target="_blank" class="sprite ir facebook" href="https://www.facebook.com/pages/Colaborativoco/385044858220704">Facebook</a>
            <a target="_blank" class="sprite ir twitter" href="http://twitter.com/colaborativo_co">Twitter</a>
            <a target="_blank" class="sprite ir googleplus" href="https://plus.google.com/u/0/103569849344851752659/">Google+</a>
            <a target="_blank" class="sprite ir youtube" href="http://youtube.com/colaborativoco">YouTube</a>       
          </div>

          <div id="about-dropdown" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Acerca de</a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
              <li><a href="http://colaborativo.co/#quote">Que es colaborativo</a></li>
              <li><a href="http://colaborativo.co/#about_us">Como funciona</a></li>
              <li><a href="http://colaborativo.co/#nuestra_plataforma">Plataforma</a></li>
              <li><a href="http://colaborativo.co/#contact">Contáctenos</a></li>
            </ul>
          </div>

          <a href="mailto:founders@colaborativo.co" class="ir sprite right" id="feedback">
            <?php _e('Bienvenido el feedback', 'colaborativo'); ?>
          </a>

 				</div>
 			</div>
 			<div class="row">
 				<div class="span5">
 					<p class="copyright">
 						Colaborativo &copy; <?php echo date('Y'); ?> | <?php _e('Todos los derechos reservados', 'colaborativo'); ?>
 					</p>
 				</div>
 				<div class="span7 aright">
 					<p>
 						<?php bloginfo('title'); ?> fue creado por: <a class="ir sprite" id="activa" href="http://activamc.com" target="_blank">Activa MC</a> <a class="ir sprite" id="manos" href="http://8manos.com" target="_blank">&infin;manos</a>
 						Cubrimiento: <a class="ir sprite" id="rey" href="http://contenidoselrey.com/" target="_blank">Contenidos El rey</a>
 					</p>
 				</div>
 			</div>
 		</div>
 	</footer>

    <!-- Modal -->
    <div class="modal hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body">
        <p>un momento…</p>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
      </div>
    </div>
    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-32985059-1']);
      _gaq.push(['_setDomainName', 'colaborativo.co']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>
    <?php wp_footer(); ?>
    <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4e5a7cc445b73dea"></script>
</body>
</html>