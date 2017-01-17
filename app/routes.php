<?php

Route::post('tweets/create', function() {
	$authorID = Input::get('authorID');
	$content = Input::get('content');
	return DB::table('tweets')->insertGetId(
    	['authorID' => $authorID, 'content' => $content]
	);
});

Route::get('tweets', function() {
	$tweets = DB::select('SELECT t.id AS tweetID, t.authorID AS userID, t.content, t.timestamp, u.name, u.location, u.website, u.bio, u.avatar FROM tweets AS t, users AS u WHERE t.authorID = u.id');
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
    $user = DB::select('select u.avatar, u.bio, u.id AS userID, u.location, u.name, u.role, u.website from users as u where u.name = :username', ['username' => $username])[0];
	return json_encode($user);
});

Route::get('users/{username}/timeline', function($username) {
    $tweets = DB::select('SELECT t.id as tweetID, t.authorID as userID, t.content, t.timestamp, u.name, u.location, u.website, u.bio, u.avatar FROM tweets AS t, following AS f, users AS u WHERE u.name = :username AND u.id = f.followedID AND t.authorID != u.id', ['username' => $username]);
	return json_encode($tweets);
});

Route::get('users/{username}/tweets', function($username) {
    $user = DB::select('select * from users where name = :username', ['username' => $username])[0];
    $tweets = DB::select('SELECT t.id as tweetID, t.authorID as userID, t.content, t.timestamp, u.name, u.location, u.website, u.bio, u.avatar FROM tweets AS t, users AS u where t.authorID = :authorID AND t.authorID = u.id', ['authorID' => $user->id]);
	return json_encode($tweets);
});

Route::get('users/{username}/tweets/latest', function($username) {
    $user = DB::select('select * from users where name = :username', ['username' => $username])[0];
    $tweet = DB::select('SELECT t.id as tweetID, t.authorID as userID, t.content, t.timestamp, u.name, u.location, u.website, u.bio, u.avatar FROM tweets AS t, users AS u where t.authorID = :authorID AND t.authorID = u.id order by timestamp desc', ['authorID' => $user->id])[0];
	return json_encode($tweet);
});

Route::get('users/{username}/followers', function($username) {
	$user = DB::select('select * from users where name = :username', ['username' => $username])[0];
    $followers = DB::select('SELECT u.avatar, u.bio, u.id AS userID, u.location, u.name, u.role, u.website FROM users AS u, following AS f WHERE f.followedID = :userID AND u.id = f.followerID', ['userID' => $user->id]);
	return json_encode($followers);
});

Route::get('users/{username}/following', function($username) {
	$user = DB::select('select * from users where name = :username', ['username' => $username])[0];
    $following = DB::select('SELECT u.avatar, u.bio, u.id AS userID, u.location, u.name, u.role, u.website FROM users AS u, following AS f WHERE f.followerID = :userID AND u.id = f.followedID', ['userID' => $user->id]);
	return json_encode($following);
});

Route::post('users/{username}/follow/{id}', function($username, $id) {
	$me = DB::select('select * from users where name = :username', ['username' => $username])[0]->id;
	DB::insert('INSERT INTO following VALUES (:me, :them)', ['me' => $me, 'them' => $id]);

	return 'ok';
})
->where('id', '[0-9]+');

Route::post('users/{username}/unfollow/{id}', function($username, $id) {
	$me = DB::select('select * from users where name = :username', ['username' => $username])[0]->id;
	$res = DB::delete('DELETE FROM following WHERE followerID = :me AND followedID = :them', ['me' => $me, 'them' => $id]);

	if ($res != 1) {
		DB::select('select * from users where id = -3');
	}
})
->where('id', '[0-9]+');

Route::delete('tweets/{id}', function($id) {
	$userID = Input::get('userID');
	$user = DB::select('SELECT * FROM users WHERE id = :userID AND role = 1 OR role = 2', ['userID' => $userID])[0];

	$res = DB::delete('DELETE FROM tweets where id = :id', ['id' => $id]);
	if ($res == 1) {
		return 'ok';
	}
})
->where('id', '[0-9]+');
