<!DOCTYPE html>

<html>
	<head>
		<title>:: COLABORATIVO.CO ::</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel=stylesheet href="<?php bloginfo('stylesheet_directory'); ?>/css/styles.css" type="text/css">
		<link href='http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Bitter' rel='stylesheet' type='text/css'>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<script>
			$(document).ready(function() {
			 
				$(".scroll").click(function(event){		
					event.preventDefault();
					$('html,body').animate({scrollTop:$(this.hash).offset().top}, 800);
				});
			});
		</script>

	</head>
	<body>
		<div id="header" class="clearfix">
			<div class="container">
				<h1>
					<a href="#header" class="scroll">Colaborativo.co</a>
				</h1>
				<ul id="main_nav">
					<li><a href="#quote" class="scroll">Qué es colaborativo</a></li>
					<li><a href="#about_us" class="scroll">Cómo funciona</a></li>
					<li><a href="#en_linea p" class="scroll">A quién le sirve</a></li>
					<li><a href="#quien_sirve li" class="scroll">Plataforma</a></li>
					<li><a href="#contact" class="scroll">Contáctenos</a></li>
				</ul>
			</div>
		</div>
		<div id="main_pic" class="clearfix">
			<div class="container"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/banner.jpg" alt="Banner principal"></div>
		</div>
		<div id="quote">
			<div class="container">
				<h1>Agrupamos lo que la gente habla en sus redes sociales</h1><span></span>
			</div>
		</div>
		<div id="about_us" class="clearfix">
			<div class="container first">
				<h2>¿Qué es colaborativo?</h2>
				<p>Colaborativo es una plataforma que reúne en un solo lugar los contenidos publicados por los usuarios en sus redes sociales en torno a eventos, marcas o actividades personales.</p>
			</div>
			<div class="container second">
				<h2>¿Cómo funciona?</h2>
				<p>Agrupamos los hashtags usados en twitter, instagram, youtube, flickr y otras redes sociales y publicamos todos los contenidos en un solo lugar para facilitar el acceso a la información.</p>
				<img src="<?php bloginfo('stylesheet_directory'); ?>/images/screen_captures.jpg" alt="Capturas de pantalla">
			</div>
		</div>
		<div id="objectives">
			<div class="container first">
				<img src="<?php bloginfo('stylesheet_directory'); ?>/images/pic1.jpg" alt="fotografia">
				<h2>Colaborativo.co en eventos</h2>
				<p>Ayudamos a agrupar los contenidos de eventos corporativos, congresos, activaciones de marca, conciertos musicales, eventos deportivos y eventos de asistencia masiva</p>
			</div>
			<div class="container second">
				<img src="<?php bloginfo('stylesheet_directory'); ?>/images/pic2.jpg" alt="fotografia">
				<h2>Colaborativo.co para marcas</h2>
				<p>Apoyamos las actividades digitales de las marcas, somos un tablero para presentar la información generada por la marca y lo que los usuarios hablan sobre esta, conozca lo que los usuarios estan conversando sobre su marca, centralice toda la información en un mismo lugar, uste podrá moderar el contenido publicado.</p>
			</div>
			<div class="container third">
				<img src="<?php bloginfo('stylesheet_directory'); ?>/images/pic3.jpg" alt="fotografia">
				<h2>Colaborativo.co para personas</h2>
				<p>Agrupa los momentos más importantes en tu vida, cumpleaños, matrimonios, celebrabaciones con amigos y reuniones familiares. Define un hashtags, usalo en tus redes sociales y comparte el tablero con tus amigos.</p>
			</div>
			<div class="container fourth">
				<a href="#contact" class="scroll">solicitar una prueba</a>
			</div>
		</div>
		<div id="en_linea">
			<div class="container">
				<img src="<?php bloginfo('stylesheet_directory'); ?>/images/pic4.jpg" alt="fotografia">
				<h2>Colaborativo para Cubrimiento en línea</h2>
				<p>El equipo de colaborativo ayuda a cubrir eventos usando dispositivos móviles que facilitan la generación de contenidos y publicación minuto a minuto de las principales actividades de los eventos y actividades.<br>
Nuestros periodístas digitales y community managers que se encargarán de generar contenidos, administrar y monitorear las conversaciones de los usuarios.</p>
			</div>
		</div>
		<div id="quien_sirve">
			<div class="container">
				<h2>A quién le sirve Colaborativo?</h2>
				<p>A todas las empresas, marcas y personas que quieran agrupar los contenidos generados en torno a una actividad en las redes sociales.</p>
				<div class="who_wrapper">
					<div class="first_item">
						<img src="<?php bloginfo('stylesheet_directory'); ?>/images/icon1.jpg" alt="icono empresas">
						<h3>Empresas</h3>
						<ul>
							<li>Eventos Empresariales</li>
							<li>Seminarios y Capacitaciones</li>
							<li>Conciertos y Festivales musicales</li>
							<li>Congresos y seminarios</li>
							<li>Capacitaciones empresariales</li>
						</ul>

					</div>

					<div>
						<img src="<?php bloginfo('stylesheet_directory'); ?>/images/icon2.jpg" alt="icono marcas">
						<h3>Marcas</h3>
						<ul>
							<li>Activaciones digitales</li>
							<li>Campalñas de Redes Sociales</li>
							<li>Conciertos y Festivales musicales</li>
							<li>Promociones</li>
							<li>Pantallas para tiendas</li>
						</ul>

					</div>

					<div>
						<img src="<?php bloginfo('stylesheet_directory'); ?>/images/icon3.jpg" alt="icono personas">
						<h3>Personas</h3>
						<ul>
							<li>Matrimonios</li>
							<li>Celebraciones</li>
							<li>Cumpleaños</li>
							<li>Fiestas</li>
						</ul>

					</div>
				</div>

			</div>
		</div>
		<div id="nuestra_plataforma">
			<div class="container">
				<h2>
					Nuestra plataforma
				</h2>
				<p>Colaborativo se conecta con las principales redes sociales como Twitter, Instagram, Youtube, SoundCloud, Flickr y llama automáticamente los contenidos asociados a un Hashtag y los almacena en nuestra plataforma.</p>
				<img src="<?php bloginfo('stylesheet_directory'); ?>/images/diagrama_redes.png" alt="Diagrama redes">
				<p class="second_paragraph">La plataforma de cubrimiento permite crear categorías y asociar #hashtags para agrupar los contenidos generados por los usuarios y seguir @usuarios específicos para registrar su actividad</p>
			</div>
		</div>
		<div id="contact">
			<div class="container">
				<h2>Contáctenos</h2>
				<p>Actualmente colaborativo se encuentra disponible únicamente por pedidos o requerimientos especiales.<br> Por favor comuniquese con nosotros en Bogotá al 2354008.<br><br>

				<strong>¿Quiere ser de los primeros en usar Colaborativo?</strong><br>Complete el formulario y cuéntenos para que quiere usarlo</p>
				<form>
					<div class="left_wrapper">
							<label>Nombre</label><input name="nombre"></input>
							
							<label>E-mail</label><input name="email"></input>
						
							<label>Teléfono</label><input name="tel"></input>
					</div>
					<div class="right_wrapper">
						<label>Mensaje</label><textarea name="mensaje"></textarea>
						<input type="submit" class="submit" value="ENVIAR"></input>
					</div>

				</form>
			</div>
		</div>
		<div id="footer">
			<div class="container">
				<h1>
					<a href="#header">Colaborativo.co</a>
				</h1>
				<p>Todos los derechos reservados. Colaborativo.co © 2013</p>
			</div>
		</div>


	</body>

</html>