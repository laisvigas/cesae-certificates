// resources/js/participants-list.js

// Toggle de edição (abre/fecha o form vinculado via data-target="#edit-<id>")
function handleToggleEdit(btn) {
  const sel = btn.getAttribute('data-target');
  if (!sel) return;
  const el = document.querySelector(sel);
  if (!el) return;

  const showing = !el.classList.contains('hidden');
  el.classList.toggle('hidden', showing);
  btn.setAttribute('aria-expanded', String(!showing));

  // opcional: scroll até o form quando abrir
  if (!showing) {
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
}

// Fecha o form de edição
function handleCancelEdit(btn) {
  const sel = btn.getAttribute('data-target');
  if (!sel) return;
  const el = document.querySelector(sel);
  if (!el) return;

  el.classList.add('hidden');

  // opcional: focar de volta no botão "Editar" correspondente
  const toggleBtn = document.querySelector(`[data-toggle-edit][data-target="${sel}"]`);
  if (toggleBtn) toggleBtn.focus();
}

// Envia certificado por email (preenche o form oculto e submete)
function handleSendCertEmail(btn) {
  const form = document.getElementById('certificate-email-form');
  if (!form) return console.warn('certificate-email-form não encontrado');

  const participantId = btn.getAttribute('data-participant-id');
  const eventId = btn.getAttribute('data-event-id');

  if (!participantId || !eventId) {
    console.warn('Faltam data-participant-id ou data-event-id');
    return;
  }

  form.querySelector('input[name="participant_id"]').value = participantId;
  form.querySelector('input[name="event_id"]').value = eventId;

  form.submit();
}

// Delegação de eventos (funciona para elementos criados dinamicamente também)
document.addEventListener('click', (e) => {
  const toggleBtn = e.target.closest('[data-toggle-edit]');
  if (toggleBtn) {
    e.preventDefault();
    handleToggleEdit(toggleBtn);
    return;
  }

  const cancelBtn = e.target.closest('[data-cancel-edit]');
  if (cancelBtn) {
    e.preventDefault();
    handleCancelEdit(cancelBtn);
    return;
  }

  const sendBtn = e.target.closest('[data-cert-email]');
  if (sendBtn) {
    e.preventDefault();
    handleSendCertEmail(sendBtn);
    return;
  }
});
