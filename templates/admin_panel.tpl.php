<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/service_table.tpl.php';

function drawAdminPanel(array $services, array $users, array $categories): void {
?>
<main class="admin-panel">
  <h1>Admin Panel</h1>

  <!-- Services -->
  <section class="table-wrapper">
    <h2>All Services</h2>
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Owner</th>
            <th>Created At</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($services as $s): ?>
          <tr>
            <td><?= $s->id ?></td>
            <td><?= htmlspecialchars($s->title) ?></td>
            <td>
              <?php $owner = User::getUser($s->sellerId); ?>
              <?= htmlspecialchars($owner?->userName ?? 'Unknown') ?>
            </td>
            <td><?= $s->createdAt ?></td>
            <td>
              <form method="post" action="/actions/action_toggle_service.php" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                <input type="hidden" name="serviceId" value="<?= $s->id ?>">
                <button type="submit" class="btn btn--primary <?= $s->isActive ? 'active' : 'inactive' ?>"><?= $s->isActive ? 'Active' : 'Inactive' ?></button>
              </form>
            </td>
            <td>
              <form method="post" action="/actions/action_delete_service.php" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                <input type="hidden" name="serviceId" value="<?= $s->id ?>">
                <button type="submit" class="btn btn--primary delete">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Users -->
  <section class="table-wrapper">
    <h2>All Users</h2>
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Created At</th>
            <th>Status</th>
            <th>Admin</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u->id ?></td>
            <td><?= htmlspecialchars($u->userName ?? 'Unknown') ?></td>
            <td><?= $u->creationDate ?></td>
            <td>
              <form method="post" action="/actions/action_toggle_user.php" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                <input type="hidden" name="id" value="<?= $u->id ?>">
                <button type="submit" class="btn btn--primary <?= $u->isActive ? 'active' : 'inactive' ?>"><?= $u->isActive ? 'Active' : 'Inactive' ?></button>
              </form>
            </td>
             <td>
              <form method="post" action="/actions/action_toggle_admin.php" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                <input type="hidden" name="id" value="<?= $u->id ?>">
                <?php $admin = User::isAdmin($u->id); ?>
                <button type="submit" class="btn btn--primary <?= $admin ? 'active' : 'inactive' ?>"><?= $admin ? 'Admin' : 'User' ?></button>
              </form>
            </td>
            <td>
              <?php if (isset($_SESSION['user_id']) && $u->id !== $_SESSION['user_id']): ?>
                <form method="post" action="/actions/action_delete_user.php" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                  <input type="hidden" name="id" value="<?= $u->id ?>">
                  <button type="submit" class="btn btn--primary delete">Delete</button>
                </form>
              <?php else: ?>
                <span class="text--muted">(You)</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Categories -->
  <section class="table-wrapper">
    <h2>All Categories</h2>
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categories as $c): ?>
          <tr>
            <td><?= $c->id ?></td>
            <td><?= htmlspecialchars($c->name) ?></td>
            <td>
              <?php if ($c->id !== 1): ?>
                <form method="post" action="/actions/action_delete_category.php" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                  <input type="hidden" name="id" value="<?= $c->id ?>">
                  <button type="submit" class="btn btn--primary delete">Delete</button>
                </form>
              <?php else: ?>
                <span class="text--muted">(Protected)</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

</main>
<?php
}
