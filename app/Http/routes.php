<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function(){
    return redirect('user/lists');
});

// 认证路由...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

Route::get('/sign','SignController@lists');
Route::get('/sign/unbind','SignController@unbind');
Route::post('/sign/bind','SignController@bind');
Route::post('/sign/signed','SignController@signed');

Route::get('admin/lists', 'AdminController@lists');
Route::any('admin/create', 'AdminController@create');
Route::post('admin/update', 'AdminController@update');
Route::post('admin/delete', 'AdminController@delete');
Route::get('/oauth/oauth','OauthController@oauth');
Route::get('/oauth/callback','OauthController@callback');
//用户
Route::get('/user/lists','UserController@lists');
Route::post('/user/batchtag','UserController@batchtag');
Route::post('/user/batchuntag','UserController@batchuntag');
Route::post('/user/remark','UserController@remark');
Route::post('/user/addblack','UserController@addblack');
Route::post('/user/removeblack','UserController@removeblack');

//用户标签
Route::get('/usertag/lists','UsertagController@lists');
Route::post('/usertag/create','UsertagController@create');
Route::post('/usertag/update','UsertagController@update');
Route::post('/usertag/delete','UsertagController@delete');

//菜单
Route::get('/menu/lists','MenuController@lists');
Route::any('/menu/create','MenuController@create');
Route::post('/menu/delete','MenuController@delete');
Route::post('/menu/test','MenuController@test');

//消息
Route::get('/message/lists','MsgController@lists');
Route::post('/message/create','MsgController@create');
Route::post('/message/update','MsgController@update');
Route::post('/message/delete','MsgController@delete');
Route::get('/message/reply','MsgController@reply');
Route::post('/message/get','MsgController@get');
Route::post('/message/getnot','MsgController@getnot');
Route::get('/message/edit','MsgController@edit');

//素材
Route::get('/material/lists','MaterialController@lists');
Route::post('/material/upload','MaterialController@upload');
Route::post('/material/delete','MaterialController@delete');
Route::post('/material/blocked','MaterialController@blocked');
Route::post('/material/unlock','MaterialController@unlock');
Route::post('/material/update','MaterialController@update');
Route::post('/material/getlist','MaterialController@getlist');
Route::post('/material/updatearticle','MaterialController@updateArticle');

//事件
Route::get('/event/lists','EventController@lists');
Route::post('/event/update','EventController@update');
Route::post('/event/delete','EventController@delete');
Route::post('/event/create','EventController@create');

//网页授权
Route::get('/oauth/lists','OauthController@lists');
Route::post('/oauth/update','OauthController@update');
Route::get('/oauth','OauthController@oauth');
Route::get('/oauth/callback','OauthController@callback');
Route::any('/oauth/test','OauthController@test');
Route::any('/oauth/shorturl','OauthController@shorturl');

//红包
Route::get('/lucky/lists','LuckyController@lists');
Route::post('/lucky/create','LuckyController@create');
Route::post('/lucky/query','LuckyController@query');

//模板消息
Route::any('/templatenotice','TemplatenoticeController@notice');

//客服
Route::get('/staff/lists','StaffController@lists');
Route::post('/staff/create','StaffController@create');
Route::post('/staff/update','StaffController@update');
Route::post('/staff/delete','StaffController@delete');
Route::post('/staff/avatar','StaffController@avatar');
Route::post('/staff/records','StaffController@records');
Route::post('/staff/send','StaffController@send');

Route::post('/staff/sessioncreate','StaffController@sessioncreate');
Route::get('/staff/sessionlists','StaffController@sessionlists');
Route::post('/staff/close','StaffController@close');
Route::post('/staff/state','StaffController@get');

//服务器
Route::any('/wechat','WechatController@serve');
Route::any('/wechat/test','WechatController@test');

//同步数据
Route::get('/schedule/taglists','ApiController@taglists');
Route::get('/schedule/update','ScheduleController@update');
//API
Route::get('/api/userlists','ApiController@userlists');
Route::get('/api/userinfo','ApiController@userinfo');
Route::post('/api/usersinfo','ApiController@usersinfo');
Route::get('/api/userinfolists','ApiController@userinfolists');
Route::get('/api/usergroup','ApiController@usergroup');
Route::get('/api/userremark','ApiController@userremark');

Route::get('/api/taglists','ApiController@taglists');
Route::get('/api/tagcreate','ApiController@tagcreate');
Route::get('/api/tagupdate','ApiController@tagupdate');
Route::get('/api/tagdelete','ApiController@tagdelete');
Route::get('/api/usertags','ApiController@usertags');
Route::get('/api/taguserlist','ApiController@taguserlist');
Route::post('/api/batchtagusers','ApiController@batchtagusers');
Route::post('/api/batchuntagusers','ApiController@batchuntagusers');

Route::get('/api/grouplists','ApiController@grouplists');
Route::get('/api/groupcreate','ApiController@groupcreate');
Route::get('/api/groupupdate','ApiController@groupupdate');
Route::get('/api/groupdelete','ApiController@groupdelete');
Route::get('/api/groupmoveuser','ApiController@groupmoveUser');
Route::post('/api/groupmoveusers','ApiController@groupmoveUsers');

Route::get('/api/menulists','ApiController@menulists');
Route::get('/api/mymenulists','ApiController@mymenulists');
Route::post('/api/menuadd','ApiController@menuadd');
Route::post('/api/menuaddmy','ApiController@menuaddmy');
Route::get('/api/menudelete','ApiController@menudelete');
Route::get('/api/menudeleteall','ApiController@menudeleteall');
Route::get('/api/menutest','ApiController@menutest');

Route::post('/api/uploadarticle','ApiController@uploadarticle');
Route::post('/api/updatearticle','ApiController@updatearticle');
Route::get('/api/materialget','ApiController@materialget');
Route::get('/api/materiallists','ApiController@materiallists');
Route::get('/api/materialstats','ApiController@materialstats');
Route::get('/api/materialdelete','ApiController@materialdelete');
Route::get('/api/gettemporary','ApiController@gettemporary');
Route::get('/api/download','ApiController@download');

Route::get('/api/luckyquery','ApiController@luckyquery');
Route::get('/api/reply','ApiController@reply');

Route::get('/api/staffonlines','ApiController@staffonlines');
Route::get('/api/stafflists','ApiController@stafflists');
Route::get('/api/staffdelete','ApiController@staffdelete');
Route::get('/api/sessionlists','ApiController@sessionlists');

Route::get('/api/phpconf','ApiController@phpconf');