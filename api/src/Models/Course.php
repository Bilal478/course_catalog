<?php

class Course {
    public $id;
    public $product_id;
    public $title;
    public $description;
    public $image_preview;
    public $category_id;
    public $parent_category_name; // New field for parent category name
    public $created_at;
    public $updated_at;

    public function __construct($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}