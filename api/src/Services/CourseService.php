<?php

class CourseService {
    private $db;
    private $categoryService;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->categoryService = new CategoryService();
    }

    public function getAllCourses($categoryId = null) {
        // Base query to get all courses
        $query = "SELECT c.* FROM courses c";
        $params = [];
        
        // If filtering by category (including subcategories)
        if ($categoryId) {
            // Get all category IDs in the hierarchy (parent + children)
            $categoryIds = $this->categoryService->getAllCategoryIdsInHierarchy($categoryId);
            
            // Add WHERE clause with placeholders for each category ID
            $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $query .= " WHERE c.category_id IN ($placeholders)";
            $params = $categoryIds;
        }
        
        // Execute query
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        // Process results
        $courses = [];
        while ($row = $stmt->fetch()) {
            $course = new Course($row);
            $course->parent_category_name = $this->categoryService->getParentCategoryName($course->category_id);
            $courses[] = $course;
        }
        
        return $courses;
    }

    public function getCourseById($id) {
        // Simple query to get single course by ID
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($row = $stmt->fetch()) {
            $course = new Course($row);
            $course->parent_category_name = $this->categoryService->getParentCategoryName($course->category_id);
            return $course;
        }
        
        return null;
    }

}