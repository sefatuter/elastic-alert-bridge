<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ElastAlertRulesController;
use App\Http\Controllers\ElasticsearchController;
use App\Http\Controllers\TestApiController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\IntegrationsController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\DeployController;

Route::get('/', function () {
    return view('welcome');
});

// Elasticsearch Controller
Route::get('/elasticsearch', [ElasticsearchController::class, 'index'])->name('elasticsearch.index');
Route::get('/elasticsearch/create-alert', [ElasticsearchController::class, 'createAlert'])->name('elasticsearch.create-alert');

// AI Controller
Route::post('/elasticsearch/generate-rule', [AIController::class, 'generateRule'])->name('elasticsearch.generate-rule');


// Index Controller
Route::get('/api/elasticsearch/indexes', [IndexController::class, 'getIndexes'])->name('elasticsearch.indexes');
Route::get('/api/elasticsearch/data', [IndexController::class, 'getIndexData'])->name('elasticsearch.data');

// Rule Controller
Route::get('/elasticsearch/rules', [RuleController::class, 'showRulesPage'])->name('elasticsearch.rules');
Route::get('/api/elasticsearch/rules/list', [RuleController::class, 'listRuleFiles'])->name('api.elasticsearch.rules.list');
Route::get('/api/elasticsearch/rules/content', [RuleController::class, 'getRuleFileContent'])->name('api.elasticsearch.rules.content');

// Deploy Controller for ElastAlert control
Route::get('/api/elastalert/status', [DeployController::class, 'getStatus'])->name('api.elastalert.status');
Route::get('/api/elastalert/logs', [DeployController::class, 'getLogs'])->name('api.elastalert.logs');
Route::post('/elastalert/start', [DeployController::class, 'start'])->name('elastalert.start');
Route::post('/elastalert/stop', [DeployController::class, 'stop'])->name('elastalert.stop');
Route::post('/elastalert/restart', [DeployController::class, 'restart'])->name('elastalert.restart');

// Integrations routes
Route::get('/elasticsearch/integrations', [IntegrationsController::class, 'index'])->name('elasticsearch.integrations');
Route::get('/api/elasticsearch/integrations', [IntegrationsController::class, 'getIntegrations'])->name('api.elasticsearch.integrations.list');
Route::post('/api/elasticsearch/integrations/email', [IntegrationsController::class, 'saveEmailIntegration'])->name('api.elasticsearch.integrations.email.save');
Route::delete('/api/elasticsearch/integrations/email', [IntegrationsController::class, 'deleteEmailIntegration'])->name('api.elasticsearch.integrations.email.delete');
Route::post('/api/elasticsearch/integrations/email/test', [IntegrationsController::class, 'testEmailIntegration'])->name('api.elasticsearch.integrations.email.test');
Route::get('/api/elasticsearch/integrations/email/list', [IntegrationsController::class, 'getEmailIntegrations'])->name('api.elasticsearch.integrations.email.list');

// Slack integration routes
Route::post('/api/elasticsearch/integrations/slack', [IntegrationsController::class, 'saveSlackIntegration'])->name('api.elasticsearch.integrations.slack.save');
Route::delete('/api/elasticsearch/integrations/slack', [IntegrationsController::class, 'deleteSlackIntegration'])->name('api.elasticsearch.integrations.slack.delete');
Route::post('/api/elasticsearch/integrations/slack/test', [IntegrationsController::class, 'testSlackIntegration'])->name('api.elasticsearch.integrations.slack.test');
Route::get('/api/elasticsearch/integrations/slack/list', [IntegrationsController::class, 'getSlackIntegrations'])->name('api.elasticsearch.integrations.slack.list');

// Review Configuration routes, both GET and POST
Route::get('/elasticsearch/print-rule', [IntegrationsController::class, 'reviewConf'])->name('elasticsearch.print-rule');
Route::post('/elasticsearch/print-rule', [IntegrationsController::class, 'reviewConf'])->name('elasticsearch.print-rule.post');

// Gemini Test API Page
Route::get('/test-gemini-api', [TestApiController::class, 'showTestPage'])->name('test.gemini.show');
Route::post('/api/test-gemini-api/prompt', [TestApiController::class, 'handlePrompt'])->name('api.test.gemini.prompt');
