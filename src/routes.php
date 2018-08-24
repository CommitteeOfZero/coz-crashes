<?php namespace CoZCrashes;

$redirectBack = new Middleware\RedirectBackMiddleware($container);
$redirectHome = new Middleware\RedirectHomeMiddleware($container);

// CrashSender submit endpoint
$app->post('/submit', Actions\SubmitAction::class);

// Static index page for public report retrieval/deletion
$app->get('/gdpr', Actions\GdprIndexAction::class)->setName('home');
$app->redirect('/', '/gdpr', 308);

// View report details from public
$app->get('/view/{id}', Actions\ViewAction::class)
    ->setName('view')
    ->add(new Middleware\WithReportMiddleware($container, 'arg', 'guid'));

// Destroy report from public
$app->post('/destroy', Actions\DestroyAction::class)
    ->setName('destroy')
    ->add(new Middleware\WithReportMiddleware($container, 'param', 'guid'))
    ->add($redirectHome);

//
// admin area
//

$app->group('', function () use ($container, $redirectBack, $redirectHome) {
    $forAuthed = new Middleware\AuthMiddleware($container);
    $forUnauthed = new Middleware\UnauthMiddleware($container);

    // Static login page
    $this->get('/admin/login', Actions\AdminLoginAction::class)->setName('adminLogin')->add($forUnauthed);

    // OAuth process
    $this->any('/admin/authenticate/{provider}', Actions\AdminAuthenticateAction::class)->setName('adminAuthenticate')->add($forUnauthed);
    // Admin index (list all reports)
    $this->get('/admin', Actions\AdminHomeAction::class)->setName('adminHome')->add($forAuthed);

    // View report details
    $this->get('/admin/view/{id}', Actions\ViewAction::class)
        ->setName('adminView')
        ->add(new Middleware\WithReportMiddleware($container, 'arg'))
        ->add($forAuthed);

    // Update admin comment
    $this->post('/admin/update/{id}', Actions\AdminUpdateAction::class)->setName('adminUpdate')
        ->add(new Middleware\WithReportMiddleware($container, 'arg'))
        ->add($redirectBack)
        ->add($forAuthed);

    // Destroy report from admin
    $this->post('/admin/destroy', Actions\DestroyAction::class)
        ->setName('adminDestroy')
        ->add(new Middleware\WithReportMiddleware($container, 'param'))
        ->add($redirectHome)
        ->add($forAuthed);
})->add(new Middleware\AdminAreaMiddleware($container));