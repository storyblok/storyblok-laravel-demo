<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/{slug?}', function ($slug = 'home') {
  $storyblok = new \Storyblok\Client('I7GGVjvJVC1tjeqcYvSm5Att');
  $storyblok->editMode(); // always enable draft mode
  $data = $storyblok->getStoryBySlug($slug)->getBody();
  return view('index', ['story' => (object)$data['story']]);
});