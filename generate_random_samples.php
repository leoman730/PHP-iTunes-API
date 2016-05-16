<?php
include('classes/itunes.php');

function readCSV($csvFile){
	$file_handle = fopen($csvFile, 'r');
	while (!feof($file_handle) ) {
		$line_of_text[] = fgetcsv($file_handle, 2048);
	}
	fclose($file_handle);
	return $line_of_text;
}


// Set path to CSV file
$csvFile = 'data/master_reviews.csv';

$csv = readCSV($csvFile);

$num_records = count($csv)-1;

$index = array();
$max_records = 500;

while (count($index) < $max_records) {
	$rand = rand(0,$num_records);

	if(!in_array($rand, $index)) {
		array_push($index, $rand);
		echo $rand ."\n";
	}
}

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

iTunes::writeToCVS($u_header, 'random_samples.csv');

for ($i=0; $i < count($index); $i++) { 	
	iTunes::writeToCVS($csv[$index[$i]], 'random_samples.csv');
}

echo count($index);
