<?php namespace CoZCrashes;

// CrashSender submit endpoint
$app->post('/submit', Actions\SubmitAction::class);

// Static index page for public report retrieval/deletion
$app->get('/gdpr', Actions\GdprIndexAction::class);
$app->redirect('/', '/gdpr', 308);

//
// admin area
//

$app->get('/admin/view/{id}', function ($request, $response, $args) {
    // dummy
})->setName('adminView');