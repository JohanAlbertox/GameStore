// Confirmação em ações destrutivas
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('submit', e => { if (!confirm(el.dataset.confirm)) e.preventDefault(); });
});
// Envia o form do carrinho ao alterar a quantidade
document.querySelectorAll('.qty-input').forEach(i => i.addEventListener('change', () => i.form.submit()));
