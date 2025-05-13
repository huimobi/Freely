<?php
declare(strict_types=1);

function drawCreateServiceForm(array $cats, array $errors): void { 
?>
<main class="form-page">
  <h2>Create New Service</h2>

  <?php if ($errors): ?>
    <ul class="form-error-list">
      <?php foreach ($errors as $e): ?>
        <li class="form-error"><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form action="/actions/action_create_service.php" method="post" class="create-service__form">

    <label> Category
      <select name="category_id" required>
        <option value="">— choose —</option>
        <?php foreach ($cats as $cat): ?>
          <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label> Title <input type="text" name="title" maxlength="150" required> </label>
    <label> Description <textarea name="description" maxlength="2000" required></textarea> </label>
    <label> Base Price <input type="number" name="base_price" step="0.01" min="0" required> </label>
    <label> Currency <input type="text" name="currency" maxlength="3" value="EUR" required> </label>
    <label> Delivery Days <input type="number" name="delivery_days" min="1" required> </label>
    <label> Revisions <input type="number" name="revisions" min="0" value="1" required> </label>

    <button type="submit" class="btn btn--primary">Create Service</button>

  </form>
</main>
<?php
}
?>
