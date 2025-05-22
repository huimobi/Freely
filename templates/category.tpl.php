<?php
declare(strict_types=1);

require_once __DIR__ . '/../database/scripts/category.class.php';

function drawCategoryList(): void {  
    $session = Session::getInstance();
    $cats = Category::getAllWithStats();

    echo '<section class="category-browse">',
         '<h2>Browse talent by category</h2>',
         '<ul class="category-grid">';

    foreach ($cats as $cat) {
        printf(
          '<li>
             <a href="/pages/browse_cat.php?cat=%d" class="category-card">
               <h3>%s</h3>
               <ul class="stats">
                 <li class="rating"><i class="star fa fa-star"></i> %.2f/5</li>
                 <li class="count">%d skills</li>
               </ul>
             </a>
           </li>',
          $cat->id,
          htmlspecialchars($cat->name, ENT_QUOTES),
          $cat->avgRating,
          $cat->serviceCount
        );
    }

    if ($session->isAdmin()) {
      echo '<li>
            <button type="button"
                    class="category-card add-category-card"
                    data-modal-open="addCategoryDialog">
              <div class="add-icon">＋</div>
              <h3>Add Category</h3>
            </button>
          </li>';
    }

    echo   '</ul>';
    echo '</section>';

    if ($session->isAdmin()) {
        echo '<dialog id="addCategoryDialog">
                <form method="post"action="/actions/action_create_category.php" class="modal-form">
                  <h2>Add New Category</h2>
                  <label>
                    <span>Title</span>
                    <input type="text" name="name" required>
                  </label>
                  <label>
                    <span>Description</span>
                    <textarea name="description" rows="4"></textarea>
                  </label>
                  <button type="submit">Create Category</button>
                </form>
                <menu>
                  <button data-modal-close type="button">Close</button>
                </menu>
              </dialog>';
    }
}
