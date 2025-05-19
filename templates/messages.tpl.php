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
      <input type="text" id="message-input" placeholder="Type a message..." required>
      <button type="submit" class="btn btn--primary">Send</button>
    </form>
  </div>
</section>

<script>
  const currentUserId = <?= $session->getUser()->id ?>;
  let selectedUserId = null;

  function selectUser(userId) {
    selectedUserId = userId;
    loadConversation(selectedUserId);
    clearInterval(window.refreshInterval);
    window.refreshInterval = setInterval(() => loadConversation(selectedUserId), 5000);

    const user = <?= json_encode(array_map(fn($u) => ['id' => $u->id, 'name' => $u->name()], $allUsers)) ?>.find(u => u.id === userId);
    if (user) {
      document.getElementById('chat-header').innerHTML =
        `<a href="profile.php?id=${user.id}">${user.name}</a>`;
    }
  }

  document.getElementById('message-form').addEventListener('submit', async e => {
    e.preventDefault();
    const content = document.getElementById('message-input').value;
    if (!selectedUserId) {
      alert("Please select a user to chat with.");
      return;
    }
    await sendMessage(selectedUserId, content);
    document.getElementById('message-input').value = '';
    loadConversation(selectedUserId);
  });
</script> 