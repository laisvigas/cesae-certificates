(function () {
  function initMultiSelect(selectId, rootId, itemAttr) {
    const form = document.getElementById('filters-form');
    const hiddenSelect = document.getElementById(selectId);
    const root = document.getElementById(rootId);
    if (!form || !hiddenSelect || !root) return;

    const btn = root.querySelector('[data-dd-toggle]');
    const menu = root.querySelector('[data-dd-menu]');
    if (!btn || !menu) return;

    function getSelected() {
      return Array.from(hiddenSelect.options)
        .filter(o => o.selected)
        .map(o => String(o.value));
    }
    function setSelected(values) {
      const set = new Set(values.map(String));
      Array.from(hiddenSelect.options).forEach(o => (o.selected = set.has(String(o.value))));
    }

    // open/close
    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });
    document.addEventListener('click', (e) => {
      if (!root.contains(e.target)) menu.classList.add('hidden');
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') menu.classList.add('hidden');
    });

    // item click → toggle select + auto-submit
    menu.querySelectorAll(`[${itemAttr}]`).forEach((item) => {
      item.addEventListener('click', () => {
        const id = String(item.getAttribute(itemAttr));
        let curr = getSelected();
        if (curr.includes(id)) {
          curr = curr.filter((v) => v !== id);
        } else {
          curr.push(id);
        }
        setSelected(curr);

        const cb = item.querySelector('input[type="checkbox"]');
        if (cb) cb.checked = curr.includes(id);

        form.submit();
      });
    });
  }

  // TIPOS
  initMultiSelect('types-hidden', 'types-dd', 'data-type-id');
  // ESTADO
  initMultiSelect('status-hidden', 'status-dd', 'data-status-id');
})();
