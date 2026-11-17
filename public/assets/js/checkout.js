document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('#checkout-form');
  if (!form) return;

  const deliveryOptions = [...form.querySelectorAll('input[name="modalidad"]')];
  const addressWrap = form.querySelector('#direccion-wrap');
  const address = form.querySelector('#direccion');
  const shippingRow = document.querySelector('[data-shipping-line]');
  const totalLabel = document.querySelector('[data-checkout-total]');
  const shippingCost = Number(form.dataset.shippingCost) || 0;
  const subtotal = Number(totalLabel?.dataset.subtotal) || 0;
  const formatMoney = amount => `$ ${Math.round(amount).toLocaleString('es-AR')}`;

  const cartForm = document.querySelector('.checkout-cart-form');
  cartForm?.querySelectorAll('input[name^="cantidades["]').forEach(input => {
    input.addEventListener('change', () => cartForm.requestSubmit());
  });

  const updateDelivery = () => {
    const shipping = form.querySelector('input[name="modalidad"]:checked')?.value === 'envio';
    if (addressWrap) addressWrap.hidden = !shipping;
    if (address) {
      address.disabled = !shipping;
      address.required = shipping;
    }
    if (shippingRow) shippingRow.querySelector('strong').textContent = formatMoney(shipping ? shippingCost : 0);
    if (totalLabel) totalLabel.textContent = formatMoney(subtotal + (shipping ? shippingCost : 0));
  };

  deliveryOptions.forEach(option => option.addEventListener('change', updateDelivery));
  updateDelivery();
});
