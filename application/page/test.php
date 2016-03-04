<?php


//$test=new \Senseio\Model\Repository('localhost', 'cosmopolitan2');
//die('EXIT '.__FILE__.'@'.__LINE__);



?>

<!doctype html>
<html>
<head>
<script src="https://code.jquery.com/jquery-2.2.1.min.js"></script>
<script src="vendor/echart/build/dist/echarts-all.js"></script>

</head>
<body>


<?php


$crawlerSpeed=new \Senseio\Component\CrawlerSpeed();
echo $crawlerSpeed->render(300,300);


?>

</body>




</html>