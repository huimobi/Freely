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
  document.body.appendChild(suggestionBox); // append to body

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