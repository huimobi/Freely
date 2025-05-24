<?php
declare(strict_types=1);

function drawEditServiceForm($service, array $cats, array $errors): void { ?>
<main class="form-page">
  <h2>Edit Service</h2>

  <?php if ($errors): ?>
    <ul class="form-error-list">
        <?php foreach ($errors as $e): ?>
        <li class="form-error"><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form action="/actions/action_edit_service.php" method="post" class="edit-service__form" enctype="multipart/form-data">
    <input type="hidden" name="serviceId" value="<?= $service->id ?>">

    <label> Category
        <select name="categoryId" required>
            <option value="">— choose —</option>
            <?php foreach ($cats as $cat): ?>
            <option value="<?= $cat->id ?>" <?= $cat->id === $service->categoryId ? 'selected' : '' ?>> <?= htmlspecialchars($cat->name) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Tags (comma-separated) <input type="text" name="tags" value="<?= htmlspecialchars(implode(', ', $service->getTags())) ?>"> </label>
    <label> Title<input type="text" name="title" value="<?= htmlspecialchars($service->title) ?>"maxlength="150" required> </label>
    <label> Description<textarea name="description" maxlength="2000" required><?= htmlspecialchars($service->description) ?></textarea> </label>
    <label> Base Price<input type="number" name="basePrice" step="0.01" min="0" value="<?= number_format($service->basePrice, 2, '.', '') ?>" required> </label>
    <label> Currency<input type="text" name="currency" maxlength="3" value="<?= htmlspecialchars($service->currency) ?>" required> </label>
    <label> Delivery Days<input type="number" name="deliveryDays" min="1" value="<?= number_format($service->deliveryDays) ?>" required> </label>
    <label> Revisions<input type="number" name="revisions" min="0" value="<?= number_format($service->revisions) ?>" required> </label>
    <label> Change Images(optional)<input type="file" accept="image/jpeg,image/png" class="image-input" name="photos[]" multiple></label>

    <div class="image-preview"></div>    

    <button type="submit" class="btn btn--primary">Save Changes</button>
  </form>
</main>
<?php } ?>