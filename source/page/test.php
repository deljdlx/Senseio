<?php


//$test=new \Senseio\Model\Repository('localhost', 'cosmopolitan2');
//die('EXIT '.__FILE__.'@'.__LINE__);



?>

<!doctype html>
<html>
<head>
<meta charset="utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">


<script src="https://code.jquery.com/jquery-2.2.1.min.js"></script>
<script src="vendor/echart/build/dist/echarts-all.js"></script>


<!-- Compiled and minified CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>


<link rel="stylesheet" href="senseio/style/main.css"/>

	<script src="senseio/source/bootstrap.js"></script>

	<script src="senseio/source/class/Application.js"></script>



	<style>


	</style>


</head>
<body>


<nav class="head">
	<div class="nav-wrapper">

		<a href="#!" class="brand-logo">Cosmopolitan.fr</a>


		<a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
		<ul class="right hide-on-med-and-down">
			<li><a href="sass.html">Gérer mes sites</a></li>
			<li><a href="badges.html">Aide</a></li>
			<li><a href="mobile.html">Mon compte</a></li>
		</ul>


		<ul class="side-nav" id="mobile-demo">
			<li><a href="sass.html">Gérer mes sites</a></li>
			<li><a href="badges.html">Aide</a></li>
			<li><a href="mobile.html">Mon compte</a></li>
		</ul>
	</div>
</nav>

<main>

	<div class="leftPanel">
		<ul>
			<li>Statistiques générales</li>
			<li>Crawl</li>
			<li>Analyse contenus</li>
			<li>Analyse linking</li>
			<li>Synthèse</li>
			<li>Recommendations</li>
			<li>Raw data</li>
		</ul>

	</div>

	<div class="rightPanel">



		<?php
		//$crawlerSpeed=new \SenseioApplication\Component\CrawlerSpeed();
		//echo $crawlerSpeed->render(300,300);
		?>



        <!--
        <div style="float: left; width:600px;">
            <?php
            $statistique=new \SenseioApplication\Component\GeneralStatistique();
            echo $statistique->render(600, 500);
            ?>
        </div>

        //-->


		<div style="float: left; width:600px;">
			<?php
			$pageDepth=new \SenseioApplication\Component\PageDepth();
			echo $pageDepth->render(600, 500);
			?>
		</div>



		<div style="float: left; width:600px;">
		<?php
			$pageStatus=new \SenseioApplication\Component\PageStatus();
			echo $pageStatus->render(600, 500);
			?>
		</div>

	</div>


</main>




<!--
<footer class="page-footer">
	<div class="container">
		<div class="row">
			<div class="col l6 s12">
				<h5 class="white-text">Footer Content</h5>
				<p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer content.</p>
			</div>
			<div class="col l4 offset-l2 s12">
				<h5 class="white-text">Links</h5>
				<ul>
					<li><a class="grey-text text-lighten-3" href="#!">Link 1</a></li>
					<li><a class="grey-text text-lighten-3" href="#!">Link 2</a></li>
					<li><a class="grey-text text-lighten-3" href="#!">Link 3</a></li>
					<li><a class="grey-text text-lighten-3" href="#!">Link 4</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="footer-copyright">
		<div class="container">
			© 2014 Copyright Text
			<a class="grey-text text-lighten-4 right" href="#!">More Links</a>
		</div>
	</div>
</footer>
//-->





</body>

<script>
	$(".button-collapse").sideNav();
</script>



</html>