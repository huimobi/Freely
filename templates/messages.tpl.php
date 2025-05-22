<?php function drawMessagesPage(array $allUsers, ?int $preselectedUserId, array $myServices): void {
  $session = Session::getInstance();
?>

<section id="messages" class="chat-layout">
  <aside class="chat-sidebar">
    <h2 class="sidebar-title">Previous Messages</h2>
    <ul class="chat-contacts-list">
      <?php foreach ($allUsers as $user): ?>
        <li class="chat-contact" onclick="selectUser(<?= $user->id ?>)">
          <?= htmlspecialchars($user->name()) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </aside>

  <div class="chat-main">
    <header id="chat-header" class="chat-header"></header>
    <div id="message-list" class="messages-box"></div>

    <form id="message-form" class="message-form">
      <button type="button" data-modal-open="offer-modal" class="btn btn--link">Offer Service</button>  
      <input type="text" id="message-input" placeholder="Type a message..." required>
      <button type="submit" class="btn btn--primary">Send</button>
    </form>

    <dialog id="offer-modal">
        <form id="offer-form" method="dialog">
          <h3>Custom Offer</h3>

          <label for="offer-service">Service</label>
          <select id="offer-service" required>
            <option value="" disabled selected>Select a service</option>
            <?php foreach($myServices as $s): ?>
              <option value="<?= $s->id ?>">
                <?= htmlspecialchars($s->title) ?> (<?= $s->currency ?> <?= number_format($s->basePrice,2) ?>)
              </option>
            <?php endforeach; ?>
          </select>

          <label for="offer-requirements">Requirements</label>
          <textarea id="offer-requirements" rows="3"></textarea>

          <label for="offer-price">Price</label>
          <input type="number" id="offer-price" step="0.01" required>

          <label for="offer-currency">Currency</label>
          <select id="offer-currency">
            <option>EUR</option>
            <option>USD</option>
          </select>

          <menu>
            <button type="submit" class="btn btn--primary">Send Offer</button>
            <button type="button" class="btn btn--primary" data-modal-close>Cancel</button>
          </menu>
        </form>
      </dialog>

  </div>
</section>

<script>
  const myServices = <?= json_encode(array_map(
    fn($s)=>[
      'id' => $s->id,
      'title'  => $s->title,
      'basePrice' => $s->basePrice,
      'currency' => $s->currency
    ],
    $myServices
  )) ?>;

  const currentUserId = <?= $session->getUser()->id ?>;
  let selectedUserId = null;

  function selectUser(userId) {
    selectedUserId = userId;
    loadConversation(userId);
    clearInterval(window.refreshInterval);
    window.refreshInterval = setInterval(() => loadConversation(userId), 5000);

    const user = <?= json_encode(array_map(fn($u)=>['id'=>$u->id,'name'=>$u->name()], $allUsers)) ?>
      .find(u => u.id === userId);
    if (user) {
      document.getElementById('chat-header').innerHTML =
        `<a href="profile.php?id=${user.id}">${user.name}</a>`;
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    // pre-select via URL if given
    const pre = <?= json_encode($preselectedUserId) ?>;
    if (pre) selectUser(pre);

    // send text message
    document.getElementById('message-form')
      .addEventListener('submit', async e => {
        e.preventDefault();
        const content = document.getElementById('message-input').value.trim();
        if (!selectedUserId) {
          return alert("Please select a user to chat with.");
        }
        await sendMessage(selectedUserId, content);
        document.getElementById('message-input').value = '';
        loadConversation(selectedUserId);
      });

    // open/close your dialogs
    document.querySelectorAll('[data-modal-open]').forEach(btn =>
      btn.addEventListener('click', () => {
        document.querySelectorAll('dialog').forEach(d => d.close());
        document.getElementById(btn.dataset.modalOpen).showModal();
      })
    );
    document.querySelectorAll('dialog [data-modal-close]').forEach(btn =>
      btn.addEventListener('click', () => btn.closest('dialog').close())
    );

    // **offer‐form submit** (stops bubbling into message-form!)
    document.getElementById('offer-form')
      .addEventListener('submit', async e => {
        e.preventDefault();
        e.stopPropagation();

        if (!selectedUserId) {
          alert("Please select someone to send the offer to.");
          return;
        }

        const payload = {
          buyerId:      selectedUserId,
          serviceId:    +document.getElementById('offer-service').value,
          requirements: document.getElementById('offer-requirements').value.trim(),
          price:        +document.getElementById('offer-price').value,
          currency:     document.getElementById('offer-currency').value
        };

        const res  = await fetch('/api/offer.php?action=create', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.status === 'success') {
          document.getElementById('offer-modal').close();
          loadConversation(selectedUserId);
        } else {
          alert('Error sending offer: ' + data.message);
        }
      });
  });
</script>


<?php } ?>