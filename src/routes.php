<?php namespace CoZCrashes;

// CrashSender submit endpoint
$app->post('/submit', Actions\SubmitAction::class);

// Static index page for public report retrieval/deletion
$app->get('/gdpr', Actions\GdprIndexAction::class);

//
// admin area
//

$app->get('/view/{id}', function ($request, $response, $args) {
    // dummy
})->setName('adminView');