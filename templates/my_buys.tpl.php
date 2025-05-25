<?php
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';

function drawMyBuysTable(array $orders): void { ?>
  <section class="table-wrapper">
    <h2>My Purchases</h2>
    <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>Service</th>
          <th>Status</th>
          <th>Ordered On</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): 
          $service = Service::getById($order->serviceId); 
          $hasComment=Comment::hasComment($order->id);
          ?>
          <tr>
            <td> <a href="/pages/service.php?id=<?= $service->id ?>" class="btn btn--link"> <?= htmlspecialchars($service->title) ?> </a> </td>
            <td><?= htmlspecialchars($order->status) ?></td>
            <td><?= htmlspecialchars($order->orderDate) ?></td>
            <td>
                <?php if ($order->status === 'Revision'): ?>
                    <form action="/api/update_order_status.php" method="post" style="display:inline;">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                      <input type="hidden" name="order_id" value="<?= $order->id ?>">
                      <input type="hidden" name="new_status" value="Completed">
                    <button type="submit" class="btn btn--primary active">Mark as Completed</button>
                    </form>
                    <form action="/api/update_order_status.php" method="post" style="display:inline;">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                      <input type="hidden" name="order_id" value="<?= $order->id ?>">
                      <input type="hidden" name="new_status" value="InProgress">
                    <button type="submit" class="btn btn--primary inactive">Send Back</button>
                    </form>
                <?php elseif ($order->status === 'InProgress'): ?>
                    <form action="/api/update_order_status.php" method="post" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                        <input type="hidden" name="order_id" value="<?= $order->id ?>">
                        <input type="hidden" name="new_status" value="Cancelled">
                        <button type="submit" class="btn btn--primary delete">Cancel</button>
                    </form>
                  <?php elseif ($order->status === 'Completed' & !$hasComment): ?>
                    <form action="../pages/create_comment.php" method="post" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
                        <input type="hidden" name="order_id" value="<?= $order->id ?>">
                        <input type="hidden" name="service_id" value="<?=$service->id?>">
                        <button type="submit" class="btn btn--primary comment">Comment Service</button>
                    </form>
                <?php else: ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </section>
<?php } ?>
