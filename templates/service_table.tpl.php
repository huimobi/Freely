<?php
function drawServiceTable(array $services, bool $editable): void { ?>
  <section class="table-wrapper">
    <h2>My Services</h2>
    <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>Service Title</th>
          <th>Avg Rating</th>
          <th>Created At</th>
          <?php if ($editable): ?>
            <th>Edit</th>
            <th>Status</th>
            <th>Delete</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($services as $service): ?>
          <tr>
            <td> <a href="/pages/service.php?id=<?= $service->id ?>" class="btn btn--link"> <?= htmlspecialchars($service->title) ?> </a> </td>
            <td><?= number_format($service->getAverageRating(), 1) ?> ★</td>
            <td><?= htmlspecialchars($service->createdAt) ?></td>
            <?php if ($editable): ?>
              <td> <a href="/pages/edit_service.php?id=<?= $service->id ?>" class="btn btn--primary edit">Edit</a> </td>
              <td>
                <form method="post" action="/actions/action_toggle_service.php" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                  <input type="hidden" name="serviceId" value="<?= $service->id ?>">
                  <button type="submit" class="btn btn--primary <?= $service->isActive ? 'active' : 'inactive' ?>"> <?= $service->isActive ? 'Active' : 'Inactive' ?> </button>
                </form>
              </td>
              <td>
                <form method="post" action="/actions/action_delete_service.php" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                  <input type="hidden" name="serviceId" value="<?= $service->id ?>">
                  <button type="submit" class="btn btn--primary delete">Delete</button>
                </form>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </section>
<?php } ?>
