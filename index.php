<?php

    // подключаем phpquery
	require_once 'php-scripts/pars/phpQuery-onefile.php';


	// массив в csv и скачать
	function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
	    $f = fopen('php://memory', 'w');
	    fputs($f, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM 
	    foreach ($array as $line) { 
	        fputcsv($f, $line, $delimiter); 
	    }
	    fseek($f, 0);
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachment; filename="'.$filename.'";');
	    fpassthru($f);
	}


	// скачать картинку
	function download_img( $link_to_file, $path_file_to_server ){
		$fp = fopen( $path_file_to_server, 'w' );
		$ch = curl_init( $link_to_file );
		curl_setopt($ch, CURLOPT_FILE, $fp);
		$data = curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}




	$value = "https://yandex.ru/pogoda/sochi";

	$html = file_get_contents( $value );
	$pq = phpQuery::newDocument( $html);
	
	// получаем содержание .fact__temp .temp__value
	$val = $pq->find('.fact__temp .temp__value');
	$text = $val->html();

	echo 'Сегодня в Сочи '.$text.'<br/>';

	$value = "https://yandex.ru/pogoda/sochi/month?via=ms";


	$html = file_get_contents( $value );
	$pq = phpQuery::newDocument( $html);


	$month_need = '';
	$day_need = '';


	foreach ( $pq->find('.climate-calendar__cell') as $val) {

		$link_val = pq($val);

		$day = $link_val->find('.climate-calendar-day__row .climate-calendar-day__day');
		$month = $link_val->find('.climate-calendar-day__row .climate-calendar-day__day-title');

		$temp_day = $link_val->find('.climate-calendar-day__temp .climate-calendar-day__temp-day');
		$temp_night = $link_val->find('.climate-calendar-day__temp .climate-calendar-day__temp-night');


		if( $day->html() == '' ){
			continue;
		}


		if( mb_eregi_replace('[0-9]', '', $day->html()) ){
			$day_need = mb_eregi_replace('[^0-9 ]', '', $day->html());
			$month_need = mb_eregi_replace('[0-9]', '', $day->html());
		}else{
			if( $month->html() != '' ){
				$month_need = $month->html();
			}
			$day_need = $day->html();
		}
		

		echo '<u>'.$day_need.' '.$month_need.'</u><br/> <b>днём</b>:'.$temp_day.'<b>ночью</b>:'.$temp_night.'<br/>';


	}


?>