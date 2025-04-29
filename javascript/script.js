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