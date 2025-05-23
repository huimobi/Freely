<?php
declare(strict_types=1);

require_once __DIR__ . '/../database/scripts/category.class.php';

function drawCategoryList(): void {
  $session = Session::getInstance();
  $cats    = Category::getAllWithStats();
  ?>
  <section class="category-browse">
      <h2>Browse talent by category</h2>
      <ul class="category-grid">
          <?php foreach ($cats as $cat): ?>
              <li>
                  <a href="/pages/browse_cat.php?cat=<?= $cat->id ?>" class="category-card">
                      <h3><?= htmlspecialchars($cat->name, ENT_QUOTES) ?></h3>
                      <ul class="stats">
                          <li class="rating"><i class="star fa fa-star"></i> <?= number_format($cat->avgRating, 2) ?>/5</li>
                          <li class="count"><?= $cat->serviceCount ?> skills</li>
                      </ul>
                  </a>
              </li>
          <?php endforeach; ?>

          <?php if ($session->isAdmin()): ?>
              <li>
                  <button type="button"
                          class="category-card add-category-card"
                          data-modal-open="addCategoryDialog">
                      <div class="add-icon">＋</div>
                      <h3>Add Category</h3>
                  </button>
              </li>
          <?php endif; ?>
      </ul>
  </section>

  <?php if ($session->isAdmin()): ?>
      <dialog id="addCategoryDialog">
          <form method="post" action="/actions/action_create_category.php" class="modal-form">
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
      </dialog>
  <?php endif;
}
