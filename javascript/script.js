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