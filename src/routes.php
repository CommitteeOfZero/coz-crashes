<?php namespace CoZCrashes;

$redirectBack = new Middleware\RedirectBackMiddleware($container);

// CrashSender submit endpoint
$app->post('/submit', Actions\SubmitAction::class);

// Static index page for public report retrieval/deletion
$app->get('/gdpr', Actions\GdprIndexAction::class);
$app->redirect('/', '/gdpr', 308);

// Destroy report from either public or admin
$app->post('/destroy', Actions\DestroyAction::class)
    ->setName('destroy')
    ->add(new Middleware\WithReportMiddleware($container, 'param', 'guid'))
    ->add($redirectBack);

//
// admin area
//

$forAuthed = new Middleware\AuthMiddleware($container);
$forUnauthed = new Middleware\UnauthMiddleware($container);

// Static login page
$app->get('/admin/login', Actions\AdminLoginAction::class)->setName('adminLogin')->add($forUnauthed);

// OAuth process
$app->any('/admin/authenticate/{provider}', Actions\AdminAuthenticateAction::class)->setName('adminAuthenticate')->add($forUnauthed);
// Admin index (list all reports)
$app->get('/admin', Actions\AdminHomeAction::class)->setName('adminHome')->add($forAuthed);

// View report details
$app->get('/admin/view/{id}', function ($request, $response, $args) {
    // dummy
})->setName('adminView')->add($forAuthed);