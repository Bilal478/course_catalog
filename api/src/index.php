<?php

// Simple autoloader
// spl_autoload_register(function ($class) {
//     $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
//     if (file_exists($file)) {
//         require $file;
//     }
// });
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
// Manual class loading
require_once __DIR__ . '/utils/Database.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Course.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/CourseService.php';
require_once __DIR__ . '/controllers/ApiController.php';

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// try {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    $api = new ApiController();

    switch ($requestMethod) {
        case 'GET':
            if ($requestUri === '/categories') {
                $api->getCategories();
            } elseif (preg_match('/^\/categories\/([a-f0-9-]{36})$/', $requestUri, $matches)) {
                $api->getCategoryById($matches[1]);
            } elseif ($requestUri === '/courses') {
                $categoryId = $_GET['category_id'] ?? null;
                $api->getCourses($categoryId);
            } elseif (preg_match('/^\/courses\/([a-f0-9-]{36})$/', $requestUri, $matches)) {
                $api->getCourseById($matches[1]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Not Found']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
    }
// } catch (Throwable $e) {
//     http_response_code(500);
//     echo json_encode([
//         'error' => 'Internal Server Error',
//         'message' => $e->getMessage()
//     ]);
// }