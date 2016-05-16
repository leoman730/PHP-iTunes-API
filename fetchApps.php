<?php
include('classes/itunes.php');
// $results = iTunes::search('Maroon 5', array(
//     'country' => 'NL'
// ))->results;


	//$results = iTunes::fetchContentFromURL('https://itunes.apple.com/us/rss/topfreeapplications/limit=10/genre=6013/json')->feed->entry;
	$results = iTunes::fetchContentFromURL('https://itunes.apple.com/us/rss/toppaidapplications/limit=10/genre=6013/json')->feed->entry;

	date_default_timezone_set('America/New_York');

	$summary = 'Fetched date: '. date('Y-m-d H:i:s')."\n";

	$apps = array();

	$header = ['id', 
	'title', 
	'category', 
	'releaseDate', 
	'userRatingCountForCurrentVersion',
	'averageUserRatingForCurrentVersion',
	'trackContentRating',
	'version',
	'description',
	'price',
	'primaryGenreId',
	'currentVersionReleaseDate',
	'averageUserRating',
	'userRatingCount',
	'qryURL'
	];
	
	iTunes::writeToCVS($header, 'topapps.csv');
	
	foreach($results as $result) {
		$data = array();
		$data['id'] = $result->id->attributes->{'im:id'};
		$data['title'] = trim(explode('-', $result->title->label)[0]);
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
		
		iTunes::writeToCVS($data, 'topapps.csv');
		
		$summary .= substr($data['title'], 0, 10)."...({$data['id']})\t\t";

		$reviews = iTunes::getUserReviewsByAppId($data['id']);

		$u_header = [
		'appID',
		'appTitle',
		'author', 
		'version',
		'rating', 
		'title',
		'content',
		'voteSum',
		'voteCount',
		'link'
		];	
		
		iTunes::writeToCVS($u_header, $data['title'].'('.$data['id'].').csv');
		iTunes::writeToCVS($u_header, 'master_reviews.csv');
	
		foreach($reviews as $review) {

			$u_data = array();
			$u_data['appID'] = $data['id'];
			$u_data['appTitle'] = $data['title'];
			$u_data['author'] = $review->author->name->label;
			$u_data['version'] = $review->{'im:version'}->label;
			$u_data['rating'] = $review->{'im:rating'}->label;
			$u_data['title'] = $review->title->label;
			$u_data['content'] = $review->content->label;
			$u_data['voteSum'] = $review->{'im:voteSum'}->label;
			$u_data['voteCount'] = $review->{'im:voteCount'}->label;
			$u_data['link'] = $review->link->attributes->href;

	
			iTunes::writeToCVS($u_data, $data['title'].'('.$data['id'].').csv');
			iTunes::writeToCVS($u_data, 'master_reviews.csv');
		}
		
		$summary .= ' has '.count($reviews). " reviews\n";
		
		iTunes::writeToFile($summary, 'summary.txt');

		// print_r($u_data);
		// break;
	}

	echo $summary;

?>