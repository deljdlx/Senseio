<?php


chdir(__DIR__.'/../../..');


require('bootstrap.php');




/**
 * @var \SenseioApplication\Application application
 */
$database=$application::getInstance()->getDatasource('crawl');



$manager=new \SenseioApplication\Model\Collection\TitleWord($database);
$words=$manager->getWordCollection();



$couple=$database->titleWordCouple;
$words=$database->titleWord;

/*
$test=$words->find(
	array(
		'word'=>'chemise'
	),
	array(
		'sort'=>array(
			'count'=>-1
		)
	)
);

foreach ($test as $values) {
	echo $values->word."\t".$values->nextWord;
	echo "\n";
}
*/




function getGraph($string, $database) {

	$coupleCollection=$database->titleWordCouple;
	$wordCollection=$database->titleWord;



	$test=removeAccent(mb_strtolower($string));


	$test=preg_replace('`\b\w{1,2}\b`', ' ', $test);
	$test=preg_replace('`\W+`', ' ', $test);
	$test=trim($test);

	$words=explode(' ', $test);

	$tree=array(
		$words[0]=>array(
			'children'=>array()
		)
	);

	$node=&$tree[$words[0]]['children'];

	$terms=array();

	foreach ($words as $key=>$word) {


		$terms=array($word);

		$nextWords=$coupleCollection->find(
			array(
				'word'=>$word
			),
			array(
				'sort'=>array(
					'count'=>1
				)
			)
		);


		$values=array();


		foreach ($nextWords as $nextWord) {
			$values[$nextWord->nextWord]=$nextWord->count;
		}

		arsort($values);

		foreach ($values as $nextWord=>$count) {
			$node[$nextWord]=array(
				'count'=>$count,
				'children'=>array(),
			);
		}

		if(isset($words[$key+1]) && $key<count($words)-2) {
			if(isset($node[$words[$key+1]]['children'])) {
				$node=&$node[$words[$key+1]]['children'];
			}
		}
	}
	return $tree;

}



function getReverseGraph($string, $database) {

	$coupleCollection=$database->titleWordCouple;
	$wordCollection=$database->titleWord;

	$test=removeAccent(mb_strtolower($string));


	$test=preg_replace('`\b\w{1,2}\b`', ' ', $test);
	$test=preg_replace('`\W+`', ' ', $test);
	$test=trim($test);

	$words=explode(' ', $test);
	$words=array_reverse($words);

	$tree=array(
		$words[0]=>array(
			'children'=>array()
		)
	);

	$node=&$tree[$words[0]]['children'];

	foreach ($words as $key=>$word) {

		$previousWords=$coupleCollection->find(
			array(
				'nextWord'=>$word
			),
			array(
				'sort'=>array(
					'count'=>1
				)
			)
		);


		$values=array();





		foreach ($previousWords as $previousWord) {


			//on recherche le mot et s'il a trop d'association, on considère qu'il n'est pas pertinant

			/*
			$data=$wordCollection->findOne(
				array('word'=>$previousWord->word)
			);
			*/

			//if($data->association<100) {
				$values[$previousWord->word]=$previousWord->count;
			//}

			//$values[$previousWord->word]=$previousWord->count;
		}


		foreach ($values as $nextWord=>$count) {
			$node[$nextWord]=array(
				'count'=>$count,
				'children'=>array(),
			);
		}

		if(isset($words[$key+1])) {
			if(isset($node[$words[$key+1]]['children']) && $key<count($words)-2) {
				$node=&$node[$words[$key+1]]['children'];
			}
		}
	}
	return $tree;

}







/*
$regexp=implode('.*\b', $terms).'\b.*\b'.$nextWord->nextWord.'\b';

echo $regexp;
echo "\n";


foreach ($data as $exists) {
	if($exists->title) {

		break;
	}
}

*/



function buildString($database, $rootNode, &$strings=array(), $reverse=false, $string=false) {


	if(!$string) {
		$string=key($rootNode);
		$rootNode=reset($rootNode);
	}


	$subString=$string;


	foreach ($rootNode['children'] as $word=>$node) {

		if($reverse) {
			$subString=$word.' '.$string;
		}
		else {
			$subString=$string.' '.$word;
		}


		if(!empty($node['children'])) {
			$subString=buildString($database, $node, $strings, $reverse, $subString);
		}
		else {
			$strings[]=$subString;
		}
	}

	return $subString;
}


function filterStrings($originString, $strings, $database) {


	$originString=clean($originString);


	$count=count(explode(' ', $originString));

	$wordCollection=$database->titleWord;

	$outputStrings=array();
	foreach ($strings as $string) {
		$outputString='';
		$words=explode(' ', $string);

		$skip=0;

		foreach ($words as $word) {
			/*
			$relation=$wordCollection->findOne(array(
				'word'=>$word
			));
			*/

			//if($relation->association<100) {
			$outputString.=$word.' ';
			//}
		}

		$outputString=trim($outputString);

		if($outputString) {
			$countCurrent=count(explode(' ', $outputString));
			if($countCurrent>=$count/3 || 1) {
				$outputStrings[]=$outputString;
			}
			else {
			}
		}
	}




	$outputStrings=array_unique($outputStrings);
	return $outputStrings;
}




function clean($string) {

	$test=removeAccent(mb_strtolower($string));
	$test=preg_replace('`\b\w{1,2}\b`', ' ', $test);
	$test=preg_replace('`\W+`', ' ', $test);
	$test=trim($test);

	return $test;
}



$wordCollection=$database->titleWord;

$test="VOICI LE SECRET POUR QUE VOTRE CHAT SOIT PLUS AFFECTUEUX";
$test="Surnoms mignons et petits noms d'amour : ce que les hommes en pensent...";
$test="35 citations pour se dire Je t'aime piochées sur Pinterest";
$test="Saint-Valentin 2016 : nos idées pour fêter l'amour...";
$test="22 plats à ne surtout pas manger avant le sexe";
$test="14 choses à savoir avant de sortir avec un addict au café !";





$test="MA BLANQUETTE DE VEAU POUR 6 PERSONNES";
$test="RISOTTO AUX CÈPES";
$test="TRIPLE CHOC BROWNIE CRUNCH RECETTE";



$test="L'armée peut faire de vous un meilleur chef";
$test="Quatre activités pour souder un groupe";
$test="RENAULT pose les jalons de son retour en Formule 1";
$test="Vodafone annonce la fin des discussions avec Liberty";

$test=clean($test);


$words=explode(' ', $test);


$frequencies=array();
$associations=array();

foreach ($words as $word) {
	$data=$wordCollection->findOne(array(
			'word'=>$word
		)
	);

	$frequencies[$word]=$data->count;
	$association[$word]=$data->association;
}


//on cherche association de terme peu courant

$pageList=array();



asort($frequencies);
asort($association);


print_r($frequencies);

//print_r($association);



$index1=0;
$index2=0;

$frequencies2=$frequencies1=$frequencies;



$currentWord1=key($frequencies);
$currentFrequency1=current($frequencies);

$currentWord2=key($frequencies2);
$currentFrequency2=current($frequencies2);


do {


	if($currentFrequency1>1 && $currentWord1!=$currentWord2) {


		$regexp='\b'.$currentWord1.'\b.*\b'.$currentWord2.'\b';

		$pages=$database->page->find(array(
			'title'=>array(
				'$regex'=>new \MongoDB\BSON\Regex($regexp, 'i')
			)
		));




		echo "=====================================";
		echo "\n";
		echo $currentWord1."\t".$currentWord2;
		echo "\n";

		foreach ($pages as $page) {
			$pageList[$page->url]=$page->title;
			echo $page->title;
			echo "\n";
		}


	}


	next($frequencies2);
	$currentWord2=key($frequencies2);
	$currentFrequency2=current($frequencies2);


	if($currentFrequency2>$currentFrequency1) {



		reset($frequencies2);
		$currentWord2=key($frequencies2);
		$currentFrequency2=current($frequencies2);

		next($frequencies1);
		$currentWord1=key($frequencies1);
		$currentFrequency1=current($frequencies1);
	}



	/*

	else {
		prev($frequencies1);
		$currentWord1=key($frequencies1);
		$currentFrequency1=current($frequencies1);
	}
	*/


	//echo $currentWord."\t".$currentFrequency;
	//echo "\n";

} while($currentWord1 && $currentWord2);

print_r($pageList);


die('EXIT '.__FILE__.'@'.__LINE__);




print_r($pageList);

die('EXIT '.__FILE__.'@'.__LINE__);





//on cherche les mots les plus rares






//$test='35 CITATIONS POUR SE DIRE "JE T\'AIME" PIOCHÉES SUR PINTEREST';







die('EXIT '.__FILE__.'@'.__LINE__);

$strings=array();



$tree=getGraph($test, $database);
buildString($database, $tree, $strings);





$tree=getReverseGraph($test, $database);
buildString($database, $tree, $strings, true);



$strings=filterStrings($test, $strings, $database);


/*
usort($strings, function($item1, $item2) use($test) {
	if(strlen($item1)>strlen($item2)) {
		return 1;
	}
	else {
		return -1;
	}
});
print_r($strings);
die('EXIT '.__FILE__.'@'.__LINE__);
*/


usort($strings, function($item1, $item2) use($test) {

	$similarity1=levenshtein($test, $item1);
	$similarity2=levenshtein($test, $item2);

	if($similarity1>$similarity2) {
		return 1;
	}
	else {
		return -1;
	}
});


foreach ($strings as $string) {

	echo levenshtein($test, $string)."\t".$string;
	echo "\n";

}


die('EXIT '.__FILE__.'@'.__LINE__);





$titles=array();

foreach ($strings as $string) {


	$regexp=preg_replace('`\W+`', ' ', $string);

	$regexp=preg_replace('` `', '\b.*\b', $regexp);

	echo $string."\t".$regexp;
	echo "\n";


	$test=$pages->find(
		array(
			'title'=>array('$regex'=>new \MongoDB\BSON\Regex($regexp, 'i'))
		)
	);


	if(!empty($test)) {
		foreach ($test as $value) {

			$titles[]=$value->title;
			echo $string."\t=>\t".$value->title;
			echo "\n";
		}
	}
};


$titles=array_unique($titles);

print_r($titles);



//die('EXIT '.__FILE__.'@'.__LINE__);

//print_r($tree);
//
//die('EXIT '.__FILE__.'@'.__LINE__);
//print_r(array_keys($tree['comment']));
//die('EXIT '.__FILE__.'@'.__LINE__);
//









/*
print_r($tree);

foreach ($tree as $word=>$data) {
	echo $word;
	echo "\n";
}
*/



//print_r($tree);





exit();






/*
function getWords($collection) {

	$data=$collection->find(array(

	), array(
		'sort'=>array(
			'count'=>-1
		)
	));
	return $data;
}

$data=getWords($words);


$sum=0;

$words=array();

//nettoyage si trop d'association différentes
foreach ($data as $value) {
	$sum+=$value->count;

	if($value->association>500) {
		echo $value->word."\t".$value->count."\t".$value->association;
		echo "\n";
	}
}
*/


/*
foreach ($words as $value) {

	if(($value->count/$sum)>0.005) {
		//echo $value->word."\t".$value->count."\t".$value->association."\t".($value->count/$sum);
		//echo "\n";
	}
}
*/
echo $sum;

echo "\n";






