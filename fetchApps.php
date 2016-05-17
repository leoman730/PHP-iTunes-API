<?php
include('classes/itunes.php');
// $results = iTunes::search('Maroon 5', array(
//     'country' => 'NL'
// ))->results;


	//$results = iTunes::fetchContentFromURL('https://itunes.apple.com/us/rss/topfreeapplications/limit=10/genre=6013/json')->feed->entry;
	
	date_default_timezone_set('America/New_York');
	
	iTunes::showOutput("Date: ". date('Y-m-d H:i:s')."\n");
	iTunes::showOutput("Start fetching top 10 applications\n");
	
	$results = iTunes::fetchContentFromURL('https://itunes.apple.com/us/rss/toppaidapplications/limit=10/genre=6013/json')->feed->entry;

	$apps = array();
	$num_reviews = 0;
	
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
		
		// $summary .= substr($data['title'], 0, 10)."...({$data['id']})\t\t";

		iTunes::showOutput("Application: ". $data['title']. " (id: {$data['id']})\n");

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
	

		$review_counter = 0;
		foreach($reviews as $review) {
			// the first entry usually just content app info, and not include any review content
			if (isset($review->content)) {
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
				
				$review_counter++;
				
				iTunes::writeToCVS($u_data, $data['title'].'('.$data['id'].').csv');
				iTunes::writeToCVS($u_data, 'master_reviews.csv');				
			}
		}
				
		iTunes::showOutput($review_counter. " reviews\n");
		$num_reviews = $num_reviews + $review_counter;
		// print_r($u_data);
		// break;
	}

	iTunes::showOutput("Fetching completed at: ". date('Y-m-d H:i:s').".");
	iTunes::showOutput(" Total number of reviews fetched: ". $num_reviews."\n");

	//echo $summary;

?>