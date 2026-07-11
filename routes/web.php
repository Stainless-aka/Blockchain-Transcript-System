<?php

/**
 * Application Routes
 * All routes are registered here with their controllers and middleware
 *
 * @var Router $router
 */

// ─── Public Routes ────────────────────────────────────────────────────────────

// Authentication
$router->get('/auth/login',  'AuthController@login');
$router->post('/auth/login', 'AuthController@processLogin');
$router->get('/auth/logout', 'AuthController@logout');

// Public Transcript Verification
$router->get('/verify',  'VerificationController@index');
$router->post('/verify', 'VerificationController@verify');

// Root redirect
$router->get('/', 'AuthController@login');

// ─── Protected Routes (require AuthMiddleware) ────────────────────────────────

// Dashboard
$router->get('/dashboard', 'DashboardController@index', ['AuthMiddleware']);

// Profile
$router->get('/profile',           'AuthController@profile',        ['AuthMiddleware']);
$router->post('/profile/update',   'AuthController@updateProfile',  ['AuthMiddleware']);
$router->post('/profile/password', 'AuthController@updatePassword', ['AuthMiddleware']);

// Students
$router->get('/students',                 'StudentController@index',  ['AuthMiddleware']);
$router->get('/students/create',          'StudentController@create', ['AuthMiddleware']);
$router->post('/students/store',          'StudentController@store',  ['AuthMiddleware']);
$router->get('/students/edit/{id}',       'StudentController@edit',   ['AuthMiddleware']);
$router->post('/students/update/{id}',    'StudentController@update', ['AuthMiddleware']);
$router->post('/students/delete/{id}',    'StudentController@delete', ['AuthMiddleware']);

// Transcripts
$router->get('/transcripts',              'TranscriptController@index',  ['AuthMiddleware']);
$router->get('/transcripts/create',       'TranscriptController@create', ['AuthMiddleware']);
$router->post('/transcripts/store',       'TranscriptController@store',  ['AuthMiddleware']);
$router->get('/transcripts/view/{id}',    'TranscriptController@detail',  ['AuthMiddleware']);
$router->post('/transcripts/delete/{id}', 'TranscriptController@delete', ['AuthMiddleware']);

// Blockchain Explorer
$router->get('/blockchain',          'BlockchainController@index',    ['AuthMiddleware']);
$router->post('/blockchain/validate','BlockchainController@runValidation', ['AuthMiddleware']);
