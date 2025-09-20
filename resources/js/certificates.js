// resources/js/certificates.js

document.addEventListener('DOMContentLoaded', () => {
  /* =======================
   * 1) Botão "enviar por e-mail" (em outra view)
   * ======================= */
  (() => {
    const emailButtons = document.querySelectorAll('[data-cert-email]');
    if (!emailButtons.length) return;

    emailButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const participantId = btn.dataset.participantId;
        const eventId = btn.dataset.eventId;

        const form = document.getElementById('certificate-email-form');
        if (!form) return;

        form.querySelector("[name='participant_id']").value = participantId;
        form.querySelector("[name='event_id']").value = eventId;
        form.submit();
      });
    });
  })();

  /* =======================
   * 2) Dropdown dinâmico de participantes (view custom.blade.php)
   * ======================= */
  const eventSelect = document.getElementById('event_id');
  const participantSelect = document.getElementById('participant_id');
  const participantsByEvent = eventSelect
    ? JSON.parse(eventSelect.dataset.participants || '{}')
    : {};

  function populateParticipants(eventId) {
    if (!participantSelect) return;
    participantSelect.innerHTML = '<option value="">Selecione um participante</option>';
    if (!eventId || !participantsByEvent[eventId]) return;

    participantsByEvent[eventId].forEach(p => {
      const option = document.createElement('option');
      option.value = p.id;
      option.textContent = p.name;
      participantSelect.appendChild(option);
    });
  }

  if (eventSelect && participantSelect) {
    eventSelect.addEventListener('change', () => populateParticipants(eventSelect.value));

    // Se já houver evento selecionado (ex.: após voltar de validação), popula na carga
    if (eventSelect.value) populateParticipants(eventSelect.value);
  }

  /* =======================
   * 3) Preview proporcional + auto-preview (view custom.blade.php)
   * ======================= */
  const form = document.getElementById('certificateForm');
  const frame = document.getElementById('previewFrame');
  const btn = document.getElementById('btnPreview');
  const auto = document.getElementById('chkAutoPreview');

  const previewUrl = form?.getAttribute('data-preview-url') || '';
  const BASE_W = 1123;
  const BASE_H = 794;

  let t;
  const debounce = (fn, ms = 500) => (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), ms);
  };

  function applyScale() {
    if (!frame) return;
    const host = frame.parentElement;
    if (!host) return;

    const hostWidth = host.clientWidth;
    const hostHeight = host.clientHeight;

    const scale = Math.min(hostWidth / BASE_W, hostHeight / BASE_H, 1);
    const scaledWidth = BASE_W * scale;
    const scaledHeight = BASE_H * scale;

    frame.style.width = `${scaledWidth}px`;
    frame.style.height = `${scaledHeight}px`;
    frame.style.transformOrigin = 'top center';
    frame.style.border = '1px solid #e5e7eb';
    frame.style.borderRadius = '.5rem';
    frame.style.background = '#fff';
    frame.style.boxShadow = '0 1px 6px rgba(0,0,0,.08)';
  }

  async function updatePreview() {
    if (!form || !frame) return;
    if (!form.event_id?.value || !form.participant_id?.value) {
      frame.srcdoc = '';
      return;
    }

    try {
      const fd = new FormData(form); // inclui o _token
      const url = previewUrl ? previewUrl + '?preview=1' : '';

      const res = await fetch(url, { method: 'POST', body: fd });
      const html = await res.text();
      frame.srcdoc = html;
      frame.onload = () => applyScale();
    } catch (e) {
      console.error('Falha ao atualizar preview', e);
    }
  }

  const autoUpdate = debounce(() => {
    if (auto?.checked) updatePreview();
  }, 500);

  if (form && frame) {
    form.addEventListener('input', autoUpdate);
    form.addEventListener('change', autoUpdate);
    btn?.addEventListener('click', updatePreview);

    window.addEventListener('resize', debounce(applyScale, 150));

    const hostForRO = frame.parentElement;
    if (hostForRO && 'ResizeObserver' in window) {
      const ro = new ResizeObserver(() => applyScale());
      ro.observe(hostForRO);
    }
  }

  /* =======================
   * 4) Cores: swatches + color picker + HEX (sincronizados)
   * ======================= */
  const hidden = document.getElementById('primary_color');       // <input type="hidden" name="primary_color">
  const textHex = document.getElementById('primary_color_text'); // input texto
  const picker = document.getElementById('colorPicker');         // <input type="color">
  const presets = document.querySelectorAll('#colorPresets [data-color]');

  function normalizeHex(v) {
    const m = String(v || '').trim().match(/^#?[0-9a-fA-F]{6}$/);
    return m ? ('#' + m[0].replace('#', '')).toLowerCase() : null;
  }

  function highlight(value) {
    const v = (value || '').toLowerCase();
    presets.forEach(b => {
      const active = b.dataset.color.toLowerCase() === v;
      b.classList.toggle('ring-2', active);
      b.classList.toggle('ring-gray-900', active);
    });
  }

  function setColor(v, { triggerPreview = true } = {}) {
    const hex = normalizeHex(v);
    if (!hex) return;
    if (hidden) hidden.value = hex;
    if (textHex) textHex.value = hex;
    if (picker) picker.value = hex;
    highlight(hex);

    if (triggerPreview && hidden) {
      // dispara listeners de 'input' no form (auto-preview)
      hidden.dispatchEvent(new Event('input', { bubbles: true }));
    }
  }

  if (presets.length || picker || textHex || hidden) {
    presets.forEach(btn =>
      btn.addEventListener('click', () => setColor(btn.dataset.color))
    );
    picker?.addEventListener('input', e => setColor(e.target.value));
    textHex?.addEventListener('input', e => {
      const hex = normalizeHex(e.target.value);
      if (hex) setColor(hex);
    });

    // estado inicial
    setColor(hidden?.value || picker?.value || textHex?.value, { triggerPreview: false });
  }

  // Primeira renderização do preview (se os selects já estiverem preenchidos)
  updatePreview();
});


  /* =======================
   * 5) Mostrar e Salvar os templates
   * ======================= */
document.addEventListener('DOMContentLoaded', () => {

  const templateSelect = document.getElementById('template_id');
  const btnSaveTemplate = document.getElementById('btnSaveTemplate');
  const form = document.getElementById('certificateForm');

  const storeUrl = form.dataset.storeUrl; // rota para salvar o template na base de dados
  const showBaseUrl = form.dataset.showUrl; // rota para buscar os templates da base de daods

  // Save template via AJAX
  btnSaveTemplate?.addEventListener('click', async () => {
    const templateName = prompt('Digite um nome para o template:');
    if (!templateName) return;

    const fd = new FormData(form);
    fd.append('name', templateName);

    // OBS: In JavaScript's fetch() API, body: fd is the part
    // of the request configuration that sends the data to the server.
    // body is a property within the fetch() request options. It holds the data you want to send
    // fd is a variable that represents a FormData object.
    // the name res for the variable in "const res = await fetch(...)" is a short, easy-to-type abbreviation
    // for response. The variable holds the Response object that the fetch API returns after a network request is completed.
    try {
    const res = await fetch(storeUrl, {
        method: 'POST',
        body: fd
    });

      const data = await res.json();
      if (data.success) {
        // Add new template to dropdown
        const opt = document.createElement('option');
        opt.value = data.template.id;
        opt.textContent = data.template.name;
        opt.selected = true;
        templateSelect.appendChild(opt);
        alert('Template salvo com sucesso!');
    } else {
        alert(data.message || 'Erro ao salvar o template.');
    }
    } catch (err) {
      console.error(err);
      alert('Erro ao salvar o template.');
    }
  });

  // Load template options when a template is selected
  templateSelect?.addEventListener('change', async () => {
    const templateId = templateSelect.value;
    if (!templateId) return;

    try {
      const res = await fetch(`${showBaseUrl}/${templateId}`);
      const data = await res.json();

      // Populate form with saved template values
      if (data.primary_color) document.getElementById('primary_color').value = data.primary_color;
      if (data.watermark) document.getElementById('watermark').value = data.watermark;
      if (data.course_line_prefix) document.getElementById('course_line_prefix').value = data.course_line_prefix;

      //  FALTAM O Logo e a assinatura (podem ser tratados à parte)

      // Trigger color update logic
      const event = new Event('input', { bubbles: true });
      document.getElementById('primary_color').dispatchEvent(event);

    } catch (err) {
      console.error(err);
      alert('Erro ao carregar o template.');
    }
  });

});


