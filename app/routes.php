<?php

Route::post('tweets/create', function() {
	$authorID = Input::get('authorID');
	$content = Input::get('content');
	return DB::table('tweets')->insertGetId(
    	['authorID' => $authorID, 'content' => $content]
	);
});

Route::get('tweets', function() {
	$tweets = DB::select('select * from tweets');
	return json_encode($tweets);
});

Route::get('tweets/{id}', function($id) {
    $tweet = DB::select('select * from tweets where id = :id', ['id' => $id]);
	return json_encode($tweet);
})
->where('id', '[0-9]+');;

Route::get('users', function() {
    $users = DB::select('select * from users');
	return json_encode($users);
});

Route::get('users/{id}', function($id) {
    $user = DB::select('select * from users where id = :id', ['id' => $id]);
	return json_encode($user);
})
->where('id', '[0-9]+');;

Route::post('users/follow/{id}', function($id) {

})
->where('id', '[0-9]+');;

Route::post('users/unfollow/{id}', function($id) {

})
->where('id', '[0-9]+');;
