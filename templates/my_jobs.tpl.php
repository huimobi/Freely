<?php
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';

function drawMyJobsTable(array $orders): void { ?>
  <section class="table-wrapper">
    <h2>Active Jobs</h2>
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Service</th>
            <th>Buyer</th>
            <th>Status</th>
            <th>Ordered On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order):
            $buyer   = User::getUser($order->buyerId);
            $service = Service::getById($order->serviceId);
          ?>
            <tr>
              <td>
                <a href="/pages/service.php?id=<?= $service->id ?>" class="btn--link">
                  <?= htmlspecialchars($service->title) ?>
                </a>
              </td>
              <td><?= htmlspecialchars($buyer->name()) ?></td>
              <td><?= htmlspecialchars($order->status) ?></td>
              <td><?= htmlspecialchars($order->orderDate) ?></td>
              <td>
                <?php if ($order->status === 'InProgress'): ?>
                 <form action="/api/update_order_status.php" method="post" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                    <input type="hidden" name="order_id"   value="<?= $order->id ?>">
                    <input type="hidden" name="new_status" value="Completed">
                    <button type="submit" class="btn btn--primary edit"> Complete </button>
                  </form>
                  <form action="/api/update_order_status.php" method="post" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                    <input type="hidden" name="order_id"   value="<?= $order->id ?>">
                    <input type="hidden" name="new_status" value="Revision">
                    <button type="submit" class="btn btn--primary edit">Revision</button>
                  </form>
                <?php else: ?>
                  &mdash;
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
<?php } ?>
