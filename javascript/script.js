document.querySelectorAll('[data-modal-open]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('dialog').forEach(d => { if (d.open) d.close(); });
      const dlg = document.getElementById(btn.dataset.modalOpen);
      dlg?.showModal();
    });
  });
  

document.querySelectorAll('dialog [data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
        const dlg = btn.closest('dialog');
        if (dlg) dlg?.close();
    });
});

document.querySelectorAll('dialog').forEach(dialog => {
  dialog.addEventListener('close', () => {
    dialog
      .querySelectorAll('.form-error')
      .forEach(el => el.remove());
  });
});


// PROFILE DROPDOWN 
const profileBtn = document.getElementById('profileBtn');
if (profileBtn) {
  const nav = profileBtn.closest('.profile-nav');
  const menu = document.getElementById('profileMenu');

  profileBtn.addEventListener('click', e => {
    e.stopPropagation();
    const isOpen = nav.classList.toggle('open');
    menu.setAttribute('aria-hidden', !isOpen);
  });

  // close when clicking outside
  document.addEventListener('click', () => {
    if (nav.classList.contains('open')) {
      nav.classList.remove('open');
      menu.setAttribute('aria-hidden', 'true');
    }
  });
}


document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.querySelector('#q');
  const suggestionBox = document.createElement('ul');
  suggestionBox.id = 'tag-suggestions';
  suggestionBox.classList.add('tag-suggestions');
  document.body.appendChild(suggestionBox);

  searchInput.addEventListener('input', async () => {
    const query = searchInput.value.trim();
    if (query.length === 0) {
      suggestionBox.innerHTML = '';
      return;
    }

    const rect = searchInput.getBoundingClientRect();
    suggestionBox.style.top = `${rect.bottom + window.scrollY}px`;
    suggestionBox.style.left = `${rect.left + window.scrollX}px`;
    suggestionBox.style.width = `${rect.width}px`;

    const res = await fetch(`../api/search_tags.php?q=${encodeURIComponent(query)}`);
    const tags = await res.json();

    suggestionBox.innerHTML = '';
    for (const tag of tags) {
      const li = document.createElement('li');
      li.textContent = tag;
      li.classList.add('tag-suggestion-item');
      li.addEventListener('click', () => {
        searchInput.value = tag;
        suggestionBox.innerHTML = '';
        searchInput.form.submit();
      });
      suggestionBox.appendChild(li);
    }
  });

  document.addEventListener('click', (e) => {
    if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
      suggestionBox.innerHTML = '';
    }
  });
});


/* -------- Messages -------- */
async function loadConversation(otherUserId) {
  // 1) fetch messages and offers in parallel
  const [msgRes, offRes] = await Promise.all([
    fetch(`/api/get_messages.php?with=${otherUserId}`),
    fetch(`/api/offer.php?action=list&with=${otherUserId}`)
  ]);

  const msgData = await msgRes.json();
  const offData = await offRes.json();

  if (msgData.status !== 'success' || offData.status !== 'success') {
    console.error('Failed to load convo:', msgData, offData);
    return;
  }

  // 2) tag and merge
  const taggedMsgs = msgData.messages.map(m => ({ ...m, type: 'message' }));
  const taggedOffs = offData.offers.map(o => ({ ...o, type: 'offer' }));
  const convo = [...taggedMsgs, ...taggedOffs]
    .sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

  // 3) render
  const container = document.getElementById('message-list');
  container.innerHTML = '';
  for (const item of convo) {
    if (item.type === 'message') {
      // your existing message bubble logic:
      const wrapper = document.createElement('div');
      wrapper.classList.add('message-wrapper', item.senderId === currentUserId ? 'sent' : 'received');
      const bubble = document.createElement('div');
      bubble.classList.add('message', item.senderId === currentUserId ? 'sent' : 'received');
      bubble.textContent = item.content;
      wrapper.appendChild(bubble);
      container.appendChild(wrapper);

    } else if (item.type === 'offer') {
      // NEW: offer bubble
      const wrapper = document.createElement('div');
      wrapper.classList.add('offer-wrapper', item.senderId === currentUserId ? 'sent' : 'received');

      const box = document.createElement('div');
      box.classList.add('offer-bubble');

      // service title
      const svc = myServices.find(s => s.id === item.serviceId);
      const title = document.createElement('h4');
      title.textContent = svc ? svc.title : `Service #${item.serviceId}`;
      box.appendChild(title);

      // requirements
      const req = document.createElement('p');
      req.textContent = `Requirements: ${item.requirements}`;
      box.appendChild(req);

      // price
      const price = document.createElement('p');
      price.textContent = `Price: ${item.currency} ${item.price.toFixed(2)}`;
      box.appendChild(price);

      // status / actions
      const statusWrap = document.createElement('div');
      statusWrap.classList.add('offer-status');

      if (item.status === 'pending' && currentUserId === item.receiverId) {
        const accept = document.createElement('button');
        accept.textContent = 'Accept';
        accept.classList.add('btn', 'btn--small', 'btn--success');
        accept.onclick = async () => {
          await fetch(`/api/offer.php?action=accept`, {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ offerId: item.offerId })
          });
          loadConversation(otherUserId);
        };

        const decline = document.createElement('button');
        decline.textContent = 'Decline';
        decline.classList.add('btn', 'btn--small', 'btn--danger');
        decline.onclick = async () => {
          await fetch(`/api/offer.php?action=decline`, {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ offerId: item.offerId })
          });
          loadConversation(otherUserId);
        };

        statusWrap.append(accept, decline);

      } else {
        const badge = document.createElement('span');
        badge.textContent = item.status.charAt(0).toUpperCase() + item.status.slice(1);
        badge.classList.add('offer-badge', `offer-${item.status}`);
        statusWrap.appendChild(badge);
      }

      box.appendChild(statusWrap);
      wrapper.appendChild(box);
      container.appendChild(wrapper);
    }
  }

  // 4) scroll
  container.scrollTop = container.scrollHeight;
}


async function sendMessage() {
  const content = document.getElementById('message-input').value.trim();
  const response = await fetch('/actions/action_send_message.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ selectedUserId, content })
  });

  const result = await response.json();

  if (result.status === 'success') {
    loadConversation(selectedUserId);
    document.getElementById('message-input').value = '';
  } else {
    alert('Message error: ' + result.message);
  }
}


//select photo on service page
    const mainPhoto = document.getElementById('selected-photo');
    const thumbnailPhotos = document.querySelectorAll('.thumbnail-photos img');
    thumbnailPhotos.forEach(thumbnail => {
      thumbnail.addEventListener('click', () => {
        // Set main photo src to clicked thumbnail src
        mainPhoto.src = thumbnail.src;

        // Highlight selected photo
        document.querySelectorAll('.thumbnail-photos img').forEach(img => {
          img.removeAttribute('data-selected');
        });
        thumbnail.setAttribute('data-selected', 'true');
      });
  });
