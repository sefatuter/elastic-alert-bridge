<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ElastAlertRulesController;
use App\Http\Controllers\ElasticsearchController;
use App\Http\Controllers\TestApiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/elasticsearch', [ElasticsearchController::class, 'index'])->name('elasticsearch.index');
Route::get('/elasticsearch/create-alert', [ElasticsearchController::class, 'createAlert'])->name('elasticsearch.create-alert');
Route::post('/elasticsearch/generate-rule', [ElasticsearchController::class, 'generateRule'])->name('elasticsearch.generate-rule');
Route::get('/api/elasticsearch/indexes', [ElasticsearchController::class, 'getIndexes'])->name('elasticsearch.indexes');
Route::get('/api/elasticsearch/data', [ElasticsearchController::class, 'getIndexData'])->name('elasticsearch.data');
Route::get('/elasticsearch/rules', [ElasticsearchController::class, 'showRulesPage'])->name('elasticsearch.rules');
Route::get('/api/elasticsearch/rules/list', [ElasticsearchController::class, 'listRuleFiles'])->name('api.elasticsearch.rules.list');
Route::get('/api/elasticsearch/rules/content', [ElasticsearchController::class, 'getRuleFileContent'])->name('api.elasticsearch.rules.content');

Route::get('/elasticsearch/print-rule', [ElasticsearchController::class, 'printRule'])->name('elasticsearch.print-rule');


// Gemini Test API Page
Route::get('/test-gemini-api', [TestApiController::class, 'showTestPage'])->name('test.gemini.show');
Route::post('/api/test-gemini-api/prompt', [TestApiController::class, 'handlePrompt'])->name('api.test.gemini.prompt');
