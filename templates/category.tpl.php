<?php
declare(strict_types=1);

require_once __DIR__ . '/../database/scripts/category.class.php';

function drawCategoryList(): void {
    $cats = Category::getAllWithStats();

    echo '<section class="category-browse">',
         '<h2>Browse talent by category</h2>',
         '<ul class="category-grid">';

    foreach ($cats as $cat) {
        printf(
          '<li>
             <a href="/browse.php?cat=%d" class="category-card">
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

    echo '</ul></section>';
}
