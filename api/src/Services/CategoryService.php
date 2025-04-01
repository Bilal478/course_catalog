<?php

class CategoryService {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // public function getAllCategories() {
    //     // Get all categories
    //     $stmt = $this->db->query("SELECT * FROM categories");
    //     $categories = [];
        
    //     while ($row = $stmt->fetch()) {
    //         $category = new Category($row);
    //         $category->count_of_courses = $this->getCourseCount($category->id);
    //         $categories[] = $category;
    //     }
        
    //     return $categories;
    // }
    public function getAllCategories() {
        // First get all categories
        $stmt = $this->db->query("SELECT * FROM categories");
        $allCategories = [];
        
        while ($row = $stmt->fetch()) {
            $category = new Category($row);
            $category->count_of_courses = $this->getCourseCount($category->id);
            $allCategories[$category->id] = $category;
        }
        
        // Build hierarchy
        $hierarchicalCategories = [];
        foreach ($allCategories as $category) {
            if ($category->parent_id === null) {
                // This is a top-level category
                $category->subcategories = $this->getSubcategories($category->id, $allCategories);
                $hierarchicalCategories[] = $category;
            }
        }
        
        return $hierarchicalCategories;
    }

    private function getSubcategories($parentId, $allCategories) {
        $subcategories = [];
        
        foreach ($allCategories as $category) {
            if ($category->parent_id === $parentId) {
                // Recursively get subcategories
                $category->subcategories = $this->getSubcategories($category->id, $allCategories);
                $subcategories[] = $category;
            }
        }
        
        return $subcategories;
    }

    public function getCategoryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($row = $stmt->fetch()) {
            $category = new Category($row);
            $category->count_of_courses = $this->getCourseCount($category->id);
            return $category;
        }
        
        return null;
    }

    private function getCourseCount($categoryId) {
        // First get all child category IDs (including self)
        $childIds = $this->getAllChildCategoryIds($categoryId);
        
        // Count courses in these categories
        $placeholders = implode(',', array_fill(0, count($childIds), '?'));
        $query = "SELECT COUNT(*) as count FROM courses WHERE category_id IN ($placeholders)";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($childIds);
        $result = $stmt->fetch();
        
        return (int)($result['count'] ?? 0);
    }

    private function getAllChildCategoryIds($parentId) {
        $ids = [$parentId];
        $stmt = $this->db->prepare("SELECT id FROM categories WHERE parent_id = ?");
        $stmt->execute([$parentId]);
        
        while ($row = $stmt->fetch()) {
            // Recursively get children's IDs
            $ids = array_merge($ids, $this->getAllChildCategoryIds($row['id']));
        }
        
        return $ids;
    }

    public function getParentCategoryName($categoryId) {
        // Get immediate parent
        $stmt = $this->db->prepare("
            SELECT c.name 
            FROM categories c
            JOIN categories child ON child.parent_id = c.id
            WHERE child.id = ?
        ");
        $stmt->execute([$categoryId]);
        
        if ($result = $stmt->fetch()) {
            return $result['name'];
        }
        
        // If no parent found, it's already a top-level category
        $stmt = $this->db->prepare("SELECT name FROM categories WHERE id = ? AND parent_id IS NULL");
        $stmt->execute([$categoryId]);
        
        return $stmt->fetch()['name'] ?? null;
    }

    public function getAllCategoryIdsInHierarchy($categoryId) {
        $ids = [$categoryId]; // Include the parent category itself
        
        // Get all direct children (1 level down)
        $stmt = $this->db->prepare("SELECT id FROM categories WHERE parent_id = ?");
        $stmt->execute([$categoryId]);
        
        while ($row = $stmt->fetch()) {
            // Recursively get children's IDs
            $ids = array_merge($ids, $this->getAllCategoryIdsInHierarchy($row['id']));
        }
        
        return $ids;
    }
}