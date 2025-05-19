<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/service_table.tpl.php';

function drawAdminPanel(array $services, array $users, array $categories): void {
?>
  <main class="admin-panel">
    <h1> Admin Panel </h1>
    <section>
        <h2>All Services</h2>
        <table class="table">
            <thead>
                <tr>
                <th>ID</th>
                <th>Service Title</th>
                <th>Avg Rating</th>
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
                <td><?= number_format($s->avgRating ?? 0, 1) ?></td>
                <td><?= $s->createdAt ?></td>
                <td><?= $s->isActive ? 'Active' : 'Inactive' ?></td>
                <td>
                    <form method="post" action="../actions/action_toggle_service.php"> <input type="hidden" name="serviceId" value="<?= $s->id ?>"> <button type="submit"><?= $s->isActive ? 'Deactivate' : 'Activate' ?></button> </form>
                    <form method="post" action="../actions/action_delete_service.php" onsubmit="return confirm('Are you sure you want to delete this service?')"> <input type="hidden" name="id" value="<?= $s->id ?>"><button>Delete</button> </form>
                </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2>All Freelancers</h2>
        <table class="table">
            <thead>
                <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Created At</th>
                <th>Status</th>
                <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                <td><?= $u->id ?></td>
                <td><?= htmlspecialchars($u->username) ?></td>
                <td><?= $u->creationDate ?></td>
                <td><?= $u->isActive ? 'Active' : 'Inactive' ?></td>
                <td>
                    <form method="post" action="../actions/action_toggle_admin.php"><input type="hidden" name="id" value="<?= $u->id ?>"><button>Toggle Admin</button></form>
                    <form method="post" action="../actions/action_toggle_user.php"><input type="hidden" name="id" value="<?= $u->id ?>"><button>Deactivate</button></form>
                    <form method="post" action="../actions/action_delete_user.php"><input type="hidden" name="id" value="<?= $u->id ?>"><button>Delete</button></form>
                </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2>All Categories</h2>
        <table class="table">
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
                        <form method="post" action="../actions/action_delete_category.php">
                            <input type="hidden" name="id" value="<?= $c->id ?>">
                            <button>Delete</button>
                        </form>
                    <?php else: ?>
                        <span style="color: gray;">(Protected)</span>
                    <?php endif; ?>
                </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>
<?php
}
