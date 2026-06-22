<?php

use App\Auth\JwtService;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Database;
use App\Middleware\AuthMiddleware;
use App\Middleware\RateLimit;
use App\Repositories\AuditLogRepository;
use App\Repositories\BookRepository;
use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app): void {
    $pdo = Database::get();
    $jwt = new JwtService();
    $auth = new AuthMiddleware($jwt);
    $audit = new AuditLogRepository($pdo);

    $bookController = new BookController(new BookRepository($pdo), $audit);
    $authController = new AuthController(new UserRepository($pdo), $jwt, $audit);

    $loginMw = new RateLimit(
        (int) ($_ENV['LOGIN_RATE_LIMIT'] ?? 5),
        (int) ($_ENV['LOGIN_WINDOW_SECONDS'] ?? 60),
        'login'
    );

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode([
            'name' => 'Books REST API with JWT + Chapter 12 Security',
            'version' => '1.0.0',
            'security_features' => [
                'Validator helper',
                'XSS-safe JSON encoding',
                'Security headers',
                'Rate limit on /auth/login',
                'CORS allow-list',
                'IDOR owner-or-admin check',
                'audit_log recording',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));

        return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    });

    // Public authentication routes
    $app->post('/auth/register', [$authController, 'register']);
    $app->post('/auth/login', [$authController, 'login'])->add($loginMw);

    // Public read-only book routes
    $app->get('/api/books', [$bookController, 'index']);
    $app->get('/api/books/{id}', [$bookController, 'show']);

    // Protected auth route
    $app->get('/auth/me', [$authController, 'me'])->add($auth);

    // Protected book write routes
    $app->group('/api/books', function ($group) use ($bookController) {
        $group->post('', [$bookController, 'create']);
        $group->put('/{id}', [$bookController, 'update']);
        $group->delete('/{id}', [$bookController, 'delete']);
    })->add($auth);
};
