<?php

require __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Controllers\UserController;
use App\Controllers\ItemController;
use App\Controllers\DashboardController;

Auth::start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri !== '/' && file_exists(__DIR__ . $uri) && is_file(__DIR__ . $uri)) {
    return false;
}

$segments = array_values(array_filter(explode('/', $uri), fn($s) => $s !== ''));

if (!empty($segments) && $segments[0] === 'api') {
    handleApi($method, array_slice($segments, 1));
    exit;
}

handlePages($method, $segments);

function handleApi(string $method, array $segs): void
{
    try {
        if ($segs[0] === 'login' && $method === 'POST') {
            apiLogin();
        }
        if ($segs[0] === 'logout' && $method === 'POST') {
            Auth::logout();
            jsonResponse(['message' => 'Logged out']);
        }
        if ($segs[0] === 'me' && $method === 'GET') {
            $user = Auth::current();
            $user ? jsonResponse(['user' => $user]) : jsonResponse(['error' => 'Unauthorized'], 401);
        }
        if ($segs[0] === 'stats' && $method === 'GET') {
            Auth::requireLogin() or jsonResponse(['error' => 'Unauthorized'], 401);
            jsonResponse(['stats' => (new DashboardController())->stats()]);
        }

        $resource = $segs[0];
        $id = isset($segs[1]) ? (int) $segs[1] : null;

        if ($resource === 'users') {
            $ctrl = new UserController();
        } elseif ($resource === 'items') {
            $ctrl = new ItemController();
        } else {
            jsonResponse(['error' => 'Not found'], 404);
        }

        Auth::requireLogin() or jsonResponse(['error' => 'Unauthorized'], 401);

        if ($id === null) {
            if ($method === 'GET') jsonResponse([$resource => $ctrl->index()]);
            if ($method === 'POST') jsonResponse([substr($resource, 0, -1) => $ctrl->store(getInput())], 201);
        } else {
            if ($method === 'GET') {
                $row = $ctrl->show($id);
                $row ? jsonResponse([substr($resource, 0, -1) => $row]) : jsonResponse(['error' => 'Not found'], 404);
            }
            if ($method === 'PUT') jsonResponse([substr($resource, 0, -1) => $ctrl->update($id, getInput())]);
            if ($method === 'DELETE') {
                $ctrl->destroy($id);
                jsonResponse(['message' => 'Deleted']);
            }
        }
        jsonResponse(['error' => 'Method not allowed'], 405);
    } catch (\Exception $e) {
        $code = $e->getCode() >= 400 ? $e->getCode() : 500;
        jsonResponse(['error' => $e->getMessage()], $code);
    }
}

function apiLogin(): void
{
    $input = getInput();
    $user = Auth::attempt($input['email'] ?? '', $input['password'] ?? '');
    if (!$user) {
        jsonResponse(['error' => 'Invalid credentials'], 401);
    }
    Auth::login($user);
    jsonResponse(['message' => 'Logged in', 'user' => Auth::current()]);
}

function handlePages(string $method, array $segs): void
{
    $page = $segs[0] ?? 'dashboard';

    if ($page === 'login') {
        if ($method === 'POST') {
            $input = $_POST;
            $user = Auth::attempt($input['email'] ?? '', $input['password'] ?? '');
            if ($user) {
                Auth::login($user);
                redirect('/dashboard');
            }
            view('login', ['error' => 'Invalid email or password']);
        }
        if (Auth::current()) redirect('/dashboard');
        view('login', []);
    }

    if ($page === 'logout' && $method === 'POST') {
        Auth::logout();
        redirect('/login');
    }

    $user = Auth::requireLogin();
    if (!$user) redirect('/login');

    if ($page === '' || $page === 'dashboard') {
        view('dashboard', ['user' => $user, 'stats' => (new DashboardController())->stats()]);
    }

    if ($page === 'users') {
        $ctrl = new UserController();
        if ($method === 'POST') {
            try {
                if (!empty($_POST['id'])) {
                    $ctrl->update((int) $_POST['id'], $_POST);
                } else {
                    $ctrl->store($_POST);
                }
            } catch (\Exception $e) {
                $editUser = !empty($_POST['id']) ? $ctrl->show((int) $_POST['id']) : null;
                view('users', ['user' => $user, 'users' => $ctrl->index(), 'editUser' => $editUser, 'error' => $e->getMessage()]);
            }
            redirect('/users');
        }
        if (isset($_GET['delete'])) {
            $ctrl->destroy((int) $_GET['delete']);
            redirect('/users');
        }
        $editUser = isset($_GET['edit']) ? $ctrl->show((int) $_GET['edit']) : null;
        view('users', ['user' => $user, 'users' => $ctrl->index(), 'editUser' => $editUser, 'error' => null]);
    }

    if ($page === 'items') {
        $ctrl = new ItemController();
        if ($method === 'POST') {
            try {
                if (!empty($_POST['id'])) {
                    $ctrl->update((int) $_POST['id'], $_POST);
                } else {
                    $ctrl->store($_POST);
                }
            } catch (\Exception $e) {
                $editItem = !empty($_POST['id']) ? $ctrl->show((int) $_POST['id']) : null;
                view('items', ['user' => $user, 'items' => $ctrl->index(), 'editItem' => $editItem, 'error' => $e->getMessage()]);
            }
            redirect('/items');
        }
        if (isset($_GET['delete'])) {
            $ctrl->destroy((int) $_GET['delete']);
            redirect('/items');
        }
        $editItem = isset($_GET['edit']) ? $ctrl->show((int) $_GET['edit']) : null;
        view('items', ['user' => $user, 'items' => $ctrl->index(), 'editItem' => $editItem, 'error' => null]);
    }

    http_response_code(404);
    echo 'Page not found';
}

function getInput(): array
{
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) return $decoded;
    return $_POST;
}

function jsonResponse($data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function redirect(string $to): void
{
    header('Location: ' . $to);
    exit;
}

function view(string $name, array $data = []): void
{
    $viewFile = __DIR__ . '/../views/' . $name . '.php';
    $render = function () use ($viewFile, $data) {
        extract($data);
        require $viewFile;
    };
    ob_start();
    $render();
    $content = ob_get_clean();
    $currentUser = $data['user'] ?? null;
    $pageError = $data['error'] ?? null;
    require __DIR__ . '/../views/layout.php';
    exit;
}
