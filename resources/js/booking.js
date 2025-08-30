function updateTotal() {
  const form = document.getElementById('booking-form');
  if (!form) return;
  const packageSelect = document.getElementById('package');
  const qtyInput = document.getElementById('quantity');
  const totalEl = document.getElementById('total');
  const price = parseFloat(packageSelect?.selectedOptions[0]?.dataset?.price || '0');
  const qty = Math.max(1, parseInt(qtyInput?.value || '1', 10));
  const total = (price * qty).toFixed(2);
  totalEl.textContent = total;
}

window.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('booking-form');
  if (!form) return;
  const packageSelect = document.getElementById('package');
  const qtyInput = document.getElementById('quantity');
  packageSelect?.addEventListener('change', updateTotal);
  qtyInput?.addEventListener('input', updateTotal);
  updateTotal();
});

