<?php

Route::get('/', ['uses' => 'Common\LandingController@index']);

Route::get('login', 'Auth\AuthController@getLogin');
Route::post('login', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

Route::group([
    'middleware' => 'auth',
    'namespace' => 'Privy'
], function () {

    get('dashboard', [
        'uses' => 'DashboardController@index',
        'as' => 'dashboard'
    ]);

    get('user/data', ['uses'  => 'UserController@data', 'as'    => 'user.data']);
    get('user/password/{id}/edit', ['uses' => 'UserController@getPassword', 'as' => 'user.password.edit']);
    put('user/password/{id}', ['uses' => 'UserController@putPassword', 'as' => 'user.password.update']);
    put('user/role/{id}', ['uses' => 'UserController@putRole', 'as' => 'user.role.update']);
    resource('user', 'UserController');

    get('position/data', [
        'uses'  => 'PositionController@data',
        'as'    => 'position.data'
    ]);
    get('position/user/not:{year}', ['uses' => 'PositionController@getSelectUser', 'as' => 'position.user.year']);
    resource('position', 'PositionController');

    resource('media', 'MediaController');

    get('page/data', [
        'uses'  => 'PageController@data',
        'as'    => 'page.data'
    ]);
    resource('page', 'PageController');

    resource('periode', 'PeriodController');

    get('renstra/data', [
        'uses'  => 'PlanController@data',
        'as'    => 'renstra.data'
    ]);
    resource('renstra', 'PlanController');

    get('program/select2', ['uses' => 'ProgramController@getSelect2', 'as' => 'program.select2']);
    get('renstra/program/data', ['uses'  => 'ProgramController@data', 'as'    => 'renstra.program.data']);
    resource('renstra.program', 'ProgramController', ['except'    => ['create', 'show']]);

    // Dirjen
    get('renstra/program/sasaran/data', [
        'uses'  => 'Dirjen\TargetController@data',
        'as'    => 'renstra.program.sasaran.data'
    ]);
    resource('renstra.program.sasaran', 'Dirjen\TargetController');
    get('renstra/program/sasaran/indikator/data', [
        'uses'  => 'Dirjen\IndicatorController@data',
        'as'    => 'renstra.program.sasaran.indikator.data'
    ]);
    resource('renstra.program.sasaran.indikator', 'Dirjen\IndicatorController');

    // Direktorat
    get('kegiatan/select2', ['uses' => 'ActivityController@getSelect2', 'as' => 'kegiatan.select2']);
    get('renstra/program/kegiatan/data', [
        'uses'  => 'ActivityController@data',
        'as'    => 'renstra.program.kegiatan.data'
    ]);
    resource('renstra.program.kegiatan', 'ActivityController');

    get('sasaran/select2', ['uses' => 'TargetController@getSelect2', 'as' => 'sasaran.select2']);
    get('renstra/program/kegiatan/sasaran/data', [
        'uses'  => 'TargetController@data',
        'as'    => 'renstra.program.kegiatan.sasaran.data'
    ]);
    resource('renstra.program.kegiatan.sasaran', 'TargetController');
    get('renstra/program/kegiatan/sasaran/indikator/data', [
        'uses'  => 'IndicatorController@data',
        'as'    => 'renstra.program.kegiatan.sasaran.indikator.data'
    ]);
    resource('renstra.program.kegiatan.sasaran.indikator', 'IndicatorController');

    get('pk/data', [
        'uses'  => 'AgreementController@data',
        'as'    => 'pk.data'
    ]);
    get('pk/select2', ['uses' => 'AgreementController@getSelect2', 'as' => 'pk.select2']);
    resource('pk', 'AgreementController');
    get('pk/program/data', 'ProgramAgreementController@data');
    get('pk/{pk}/program', ['uses' => 'ProgramAgreementController@index', 'as' => 'pk.program.index']);

    // Dirjen
    get('pk/program/sasaran/data', ['uses' => 'Dirjen\TargetAgreementController@data', 'as' => 'pk.program.sasaran.data']);
    get('pk/{pk}/program/{program}/sasaran', ['uses' => 'Dirjen\TargetAgreementController@index', 'as' => 'pk.program.sasaran.index']);
    get('pk/program/sasaran/indikator/data', ['uses' => 'Dirjen\IndicatorAgreementController@data', 'as' => 'pk.program.sasaran.indikator.data']);
    resource('pk.program.sasaran.indikator', 'Dirjen\IndicatorAgreementController', ['only' => ['index', 'edit', 'update']]);

    // Direktorat
    get('pk/program/kegiatan/data', ['uses' => 'ActivityAgreementController@data', 'as' => 'pk.program.kegiatan.data']);
    // get('pk/{pk}/program/{program}/kegiatan', ['uses' => 'ActivityAgreementController@index', 'as' => 'pk.program.kegiatan.index']);
    resource('pk.program.kegiatan', 'ActivityAgreementController', ['only' => ['index','edit','update']]);
    get('pk/program/kegiatan/sasaran/data', ['uses' => 'TargetAgreementController@data', 'as' => 'pk.program.kegiatan.sasaran.data']);
    get('pk/{pk}/program/{program}/kegiatan/{kegiatan}/sasaran', ['uses' => 'TargetAgreementController@index', 'as' => 'pk.program.kegiatan.sasaran.index']);
    get('pk/program/kegiatan/sasaran/indikator/data', ['uses' => 'IndicatorAgreementController@data', 'as' => 'pk.program.kegiatan.sasaran.indikator.data']);
    resource('pk.program.kegiatan.sasaran.indikator', 'IndicatorAgreementController', ['only' => ['index', 'edit', 'update']]);

    get('capaian/media/data', ['uses' => 'PhysicAchievementController@getMediaData', 'as' => 'capaian.media.data']);
    delete('capaian/{achievementId}/media/{mediaId}/destroy', ['uses' => 'PhysicAchievementController@deleteMedia', 'as' => 'capaian.media.destroy']);
    get('goal/{goalId}/capaian/{achievementId}', ['uses' => 'PhysicAchievementController@getDocument', 'as' => 'goal.capaian.doc.create']);
    post('goal/{goalId}/capaian/{achievementId}', ['uses' => 'PhysicAchievementController@postDocument', 'as' => 'goal.capaian.doc.store']);
    get('capaian/fisik/filter', ['uses' => 'PhysicAchievementController@getFilter', 'as' => 'capaian.fisik.filter']);
    get('capaian/fisik/filter/indicator', ['uses' => 'PhysicAchievementController@getIndicator', 'as' => 'capaian.fisik.indicator']);
    get('capaian/fisik/indicator/data', ['uses' => 'PhysicAchievementController@getIndicatorData', 'as' => 'capaian.fisik.indicator.data']);
    Route::group([
        'prefix'    => 'capaian/fisik'
    ], function () {
        resource('goal.achievement', 'PhysicAchievementController', ['only' => ['index','store']]);
    });

    get('capaian/anggaran/filter', ['uses' => 'BudgetAchievementController@getFilter', 'as' => 'capaian.anggaran.filter']);
    get('capaian/anggaran/filter/kegiatan', ['uses' => 'BudgetAchievementController@getActivity', 'as' => 'capaian.anggaran.kegiatan']);
    put('capaian/anggaran/kegiatan/{budget}', ['uses' => 'BudgetAchievementController@update', 'as' => 'capaian.anggaran.kegiatan.update']);
    Route::group([
        'prefix'    => 'capaian/anggaran'
    ], function () {
        resource('goal.achievement', 'BudgetAchievementController', ['only' => ['index','store']]);
    });

    get('capaian/renstra/fisik', [
        'uses'  => 'Period\PhysicAchievementController@index',
        'as'    => 'capaian.renstra.fisik.index'
    ]);
    get('capaian/renstra/anggaran', [
        'uses'  => 'Period\BudgetAchievementController@index',
        'as'    => 'capaian.renstra.anggaran.index'
    ]);

    resource('kegiatan.evaluasi', 'EvaluationController');
});

Route::get('/{slug}', ['uses' => 'Common\LandingController@page', 'as' => 'public.page']);
