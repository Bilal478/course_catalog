<?php

class ApiController {
    private $categoryService;
    private $courseService;

    public function __construct() {
        $this->categoryService = new CategoryService();
        $this->courseService = new CourseService();
        $this->setHeaders();
    }

    private function setHeaders() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
    }

    public function getCategories() {
        echo json_encode($this->categoryService->getAllCategories());
    }

    public function getCategoryById($id) {
        $category = $this->categoryService->getCategoryById($id);
        echo $category ? json_encode($category) : $this->notFound();
    }

    public function getCourses($categoryId = null) {
        echo json_encode($this->courseService->getAllCourses($categoryId));
    }

    public function getCourseById($id) {
        $course = $this->courseService->getCourseById($id);
        echo $course ? json_encode($course) : $this->notFound();
    }

    private function notFound() {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
}