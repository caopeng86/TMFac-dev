<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//Route::group('newsmapa01', function () {
//    Route::rule('map', 'newsmapa01/api/map');
//    Route::rule('sections', 'newsmapa01/api/sections');
//    Route::rule('section', 'newsmapa01/api/section');
//    Route::rule('detail', 'newsmapa01/api/detail');
//    Route::rule('getComments', 'newsmapa01/api/getComments');
//    Route::rule('comments', 'newsmapa01/api/comments');
//    Route::rule('modulecomments', 'newsmapa01/api/modulecomments');
//    Route::rule('like', 'newsmapa01/api/like');
//})->allowCrossDomain();
//
//Route::group('subjecta01', function () {
//    Route::rule('banners', 'subjecta01/api/banners');
//    Route::rule('section', 'subjecta01/api/section');
//})->allowCrossDomain();
//
//Route::group('videoa01', function () {
//    Route::rule('createlive', 'videoa01/api/createlive');
//    Route::rule('livenews', 'videoa01/api/livenews');
//    Route::rule('livecategory', 'videoa01/api/livecategory');
//    Route::rule('livenewsdetail', 'videoa01/api/livenewsdetail');
//    Route::rule('getComments', 'videoa01/api/getComments');
//    Route::rule('comments', 'videoa01/api/comments');
//    Route::rule('selflive', 'videoa01/api/selflive');
//    Route::rule('liveonoff', 'videoa01/api/liveonoff');
//    Route::rule('getlivepermit', 'videoa01/api/getlivepermit');
//})->allowCrossDomain();
//
//Route::group('querya01', function () {
//
//})->allowCrossDomain();
//
////Route::group('matrixa01', function () {
////    Route::rule('station', 'matrixa01/api/station');
////    Route::rule('stations', 'matrixa01/api/stations');
////    Route::rule('sections', 'matrixa01/api/sections');
////})->allowCrossDomain();
//
//
//Route::group('map', function () {
//    Route::rule('corners', 'newsmapa01/section/countCorners');
//    Route::rule('sections/:id', 'newsmapa01/section/read');
//    Route::rule('sections', 'newsmapa01/section/index');
//})->allowCrossDomain();
//
//Route::group('sections', function () {
//    Route::rule(':id/comments', 'newsmapa01/section/getComments', 'GET');
//    Route::rule(':id/comments', 'newsmapa01/section/update', 'POST');
//    Route::rule(':id/like', 'newsmapa01/section/save', 'POST');
//})->allowCrossDomain();
//
//Route::group('stations', function () {
//    Route::rule(':id/sections', 'matrixa01/station/getSections');
//    Route::rule('', 'matrixa01/station/index');
//})->allowCrossDomain();
//
//Route::group('topics', function () {
//    Route::rule(':id/sections', 'subjecta01/topic/index');
//    Route::rule(':id', 'subjecta01/topic/read');
//})->allowCrossDomain();
//
///*****************************************************************************************************/
//
//Route::get('think', function () {
//    return 'hello,ThinkPHP5!';
//});
//
//Route::get('hello/:name', 'index/hello');

//return [
//
//];



Route::get('hello/:name', 'index/hello');
Route::rule('hlhj_news/user_api/postComment', 'hlhj_news/Userapi/postComment');
Route::rule('hlhj_news/user_api/getUserMenu', 'hlhj_news/Userapi/getUserMenu');
Route::rule('hlhj_news/user_api/get_comment', 'hlhj_news/Userapi/get_comment');
Route::rule('hlhj_news/user_api/commonarticle', 'hlhj_news/Userapi/commonarticle');
Route::rule('hlhj_news/user_api/addforward', 'hlhj_news/Userapi/addforward');

// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[user_api/postComment]'     => [
//         ':id'   => ['userapi/postComment', ['method' => 'get'], ['id' => '\d+']]
//     ],

// ];



