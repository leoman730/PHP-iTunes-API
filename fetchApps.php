<?php
include('classes/itunes.php');
// $results = iTunes::search('Maroon 5', array(
//     'country' => 'NL'
// ))->results;


	//$results = iTunes::fetchContentFromURL('https://itunes.apple.com/us/rss/topfreeapplications/limit=10/genre=6013/json')->feed->entry;
	$results = iTunes::fetchContentFromURL('https://itunes.apple.com/us/rss/toppaidapplications/limit=10/genre=6013/json')->feed->entry;

	$apps = array();

	$fp = fopen('topapps.csv', 'w');

	foreach($results as $result) {
		$data = array();
		$data['id'] = $result->id->attributes->{'im:id'};
		$data['title'] = $result->title->label;
		$data['category'] = $result->category->attributes->term;
		$data['relsease_date'] = $result->{'im:releaseDate'}->attributes->label;

		$appDetails = iTunes::lookup($data['id']);

		$data['userRatingCountForCurrentVersion'] = $appDetails->results[0]->userRatingCountForCurrentVersion;
		$data['averageUserRatingForCurrentVersion'] = $appDetails->results[0]->averageUserRatingForCurrentVersion;
		$data['trackContentRating'] = $appDetails->results[0]->trackContentRating;
		$data['version'] = $appDetails->results[0]->version;
		$data['description'] = $appDetails->results[0]->description;
		$data['price'] = '$'.$appDetails->results[0]->price;
		$data['primaryGenreId'] = $appDetails->results[0]->primaryGenreId;
		$data['currentVersionReleaseDate'] = $appDetails->results[0]->currentVersionReleaseDate;
		$data['averageUserRating'] = $appDetails->results[0]->averageUserRating;
		$data['userRatingCount'] = $appDetails->results[0]->userRatingCount;
		$data['qryURL'] = 'http://itunes.apple.com/lookup?id='.$data['id'];
		
		fputcsv($fp, $data);
		

		// print_r($data);

		// print_r($appDetails);
		// break;
	}
	
	fclose($fp);


?>