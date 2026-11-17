window.initAdminProductList = function initAdminProductList() {
  const list = document.querySelector('[data-admin-product-list]');
  if (!list) return;

  const search = document.querySelector('[data-admin-search]');
  const sort = document.querySelector('[data-admin-sort]');
  const previous = document.querySelector('[data-admin-prev]');
  const next = document.querySelector('[data-admin-next]');
  const pageLabel = document.querySelector('[data-admin-page]');
  const countLabel = document.querySelector('[data-admin-results-count]');
  const products = [...list.querySelectorAll('[data-admin-product]')];
  const pageSize = 10;
  let page = 1;
  const empty = document.createElement('p');
  empty.className = 'admin-list-empty';
  empty.textContent = 'No encontramos productos con esa búsqueda.';

  list.querySelectorAll('[data-admin-image]').forEach(image => {
    const useFallback = () => {
      if (image.dataset.fallbackUsed !== '1' && image.dataset.fallback) {
        image.dataset.fallbackUsed = '1';
        image.src = image.dataset.fallback;
      } else {
        image.dataset.fallbackUsed = '1';
        image.src = 'assets/images/product-placeholder.svg';
      }
    };
    image.addEventListener('error', useFallback);
    if (image.complete && image.naturalWidth === 0) useFallback();
  });

  const render = () => {
    const term = (search?.value || '').trim().toLocaleLowerCase('es');
    const filtered = products.filter(product => (product.dataset.name || '').toLocaleLowerCase('es').includes(term));
    const mode = sort?.value || 'recent';
    filtered.sort((a, b) => {
      if (mode === 'name') return (a.dataset.name || '').localeCompare(b.dataset.name || '', 'es', { sensitivity: 'base' });
      if (mode === 'price-asc') return Number(a.dataset.price) - Number(b.dataset.price);
      if (mode === 'price-desc') return Number(b.dataset.price) - Number(a.dataset.price);
      if (mode === 'stock') return Number(b.dataset.stock) - Number(a.dataset.stock);
      return (b.dataset.updated || '').localeCompare(a.dataset.updated || '');
    });

    const pageCount = Math.max(1, Math.ceil(filtered.length / pageSize));
    page = Math.min(page, pageCount);
    const start = (page - 1) * pageSize;
    const current = filtered.slice(start, start + pageSize);
    list.replaceChildren(...current);
    if (!filtered.length) list.append(empty);

    if (countLabel) countLabel.textContent = filtered.length
      ? `Mostrando ${start + 1}–${Math.min(start + pageSize, filtered.length)} de ${filtered.length} productos`
      : 'Mostrando 0 productos';
    if (pageLabel) pageLabel.textContent = `${page} / ${pageCount}`;
    if (previous) previous.disabled = page <= 1;
    if (next) next.disabled = page >= pageCount;
    const pagination = document.querySelector('.admin-product-pagination');
    if (pagination) pagination.hidden = pageCount <= 1;
  };

  search?.addEventListener('input', () => { page = 1; render(); });
  sort?.addEventListener('change', () => { page = 1; render(); });
  previous?.addEventListener('click', () => { page -= 1; render(); });
  next?.addEventListener('click', () => { page += 1; render(); });
  render();
};
