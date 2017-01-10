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
    $tweet = DB::select('select * from tweets where id = :id', ['id' => $id])[0];
	return json_encode($tweet);
})
->where('id', '[0-9]+');

Route::get('users', function() {
    $users = DB::select('select * from users');
	return json_encode($users);
});

Route::get('users/{username}', function($username) {
    $user = DB::select('select * from users where name = :username', ['username' => $username])[0];
	return json_encode($user);
});

Route::get('users/{username}/tweets', function($username) {
    $user = DB::select('select * from users where name = :username', ['username' => $username])[0];
    $tweets = DB::select('SELECT t.id as tweetID, u.id as userID, t.content, t.timestamp, u.name, u.location, u.website, u.bio, u.avatar FROM tweets AS t, users AS u where authorID = :authorID', ['authorID' => $user->id]);
	return json_encode($tweets);
});

Route::post('users/follow/{id}', function($id) {

})
->where('id', '[0-9]+');

Route::post('users/unfollow/{id}', function($id) {

})
->where('id', '[0-9]+');
