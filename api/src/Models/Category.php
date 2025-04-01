<?php

class Category {
    public $id;
    public $name;
    public $parent_id;
    public $count_of_courses;
    public $children;
    public $created_at;
    public $updated_at;
    public $subcategories = []; // Explicitly declare subcategories array

    public function __construct($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}