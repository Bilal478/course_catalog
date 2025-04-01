document.addEventListener('DOMContentLoaded', function() {
    const categoriesList = document.getElementById('categories-list');
    const coursesList = document.getElementById('courses-list');
    const coursesTitle = document.getElementById('courses-title');
    
    let currentCategoryId = null;
    
    // Fetch and display categories
    function loadCategories() {
        fetch('http://api.cc.localhost/categories')
            .then(response => response.json())
            .then(categories => {
                categoriesList.innerHTML = '';
                
                // Add "All Courses" option
                const allItem = document.createElement('li');
                allItem.textContent = 'All Courses';
                allItem.addEventListener('click', () => {
                    currentCategoryId = null;
                    loadCourses();
                    document.querySelectorAll('#categories-list li').forEach(li => {
                        li.classList.remove('active');
                    });
                    allItem.classList.add('active');
                    coursesTitle.textContent = 'All Courses';
                });
                
                if (!currentCategoryId) {
                    allItem.classList.add('active');
                }
                
                categoriesList.appendChild(allItem);
                
                // Add categories with hierarchy
                categories.forEach(category => {
                    renderCategory(category, categoriesList);
                });
            })
            .catch(error => console.error('Error loading categories:', error));
    }
    
    // Recursive function to render categories and subcategories
    function renderCategory(category, parentElement, level = 0) {
        const item = document.createElement('li');
        item.style.paddingLeft = `${level * 20}px`;
        item.innerHTML = `
            <span class="category-name">${category.name}</span>
            <span class="count">${category.count_of_courses}</span>
        `;
        
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            currentCategoryId = category.id;
            loadCourses(category.id);
            setActiveCategory(item);
            coursesTitle.textContent = `Courses in ${category.name}`;
        });
        
        parentElement.appendChild(item);
        
        // Render subcategories if they exist
        if (category.subcategories && category.subcategories.length > 0) {
            const subList = document.createElement('ul');
            subList.className = 'subcategories';
            parentElement.appendChild(subList);
            
            category.subcategories.forEach(subcategory => {
                renderCategory(subcategory, subList, level + 1);
            });
        }
    }
    
    // Set active category and clear others
    function setActiveCategory(activeItem) {
        document.querySelectorAll('#categories-list li').forEach(li => {
            li.classList.remove('active');
        });
        activeItem.classList.add('active');
    }
    
    // Fetch and display courses
    function loadCourses(categoryId = null) {
        let url = 'http://api.cc.localhost/courses';
        if (categoryId) {
            url += `?category_id=${categoryId}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(courses => {
                coursesList.innerHTML = '';
                
                if (courses.length === 0) {
                    coursesList.innerHTML = '<p>No courses found in this category.</p>';
                    return;
                }
                
                courses.forEach(course => {
                    const courseCard = document.createElement('div');
                    courseCard.className = 'course-card';
                    
                    const imageUrl = course.image_preview || 'https://via.placeholder.com/300x160?text=No+Preview';
                    
                    courseCard.innerHTML = `
                        <div class="course-image" style="background-image: url('${imageUrl}')"></div>
                        <div class="course-info">
                            <h3>${course.title}</h3>
                            <p class="description">${course.description || 'No description available'}</p>
                            <span class="course-category">${course.parent_category_name}</span>
                        </div>
                    `;
                    
                    coursesList.appendChild(courseCard);
                });
            })
            .catch(error => console.error('Error loading courses:', error));
    }
    
    // Initial load
    loadCategories();
    loadCourses();
});