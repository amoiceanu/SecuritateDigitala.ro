document.querySelectorAll('[data-exposure-tabs]').forEach(workspace => {
 const tabs = [...workspace.querySelectorAll('[data-exposure-tab]')];
 const panels = [...workspace.querySelectorAll('[data-exposure-panel]')];
 const tablist = workspace.querySelector('.exposure-tabs');
 if (!tablist || !tabs.length) return;
 const activate = (tab, focus = false) => {
  tabs.forEach(t => { const active = t === tab; t.setAttribute('aria-selected', String(active)); t.tabIndex = active ? 0 : -1; });
  panels.forEach(p => p.hidden = p.dataset.exposurePanel !== tab.dataset.exposureTab);
  if (focus) tab.focus();
 };
 tablist.setAttribute('role', 'tablist');
 tabs.forEach((tab, i) => {
  tab.id = 'exposure-tab-' + tab.dataset.exposureTab;
  tab.setAttribute('role','tab'); tab.setAttribute('aria-controls', tab.dataset.exposureTab);
  const panel = panels.find(p => p.dataset.exposurePanel === tab.dataset.exposureTab);
  panel?.setAttribute('role','tabpanel'); panel?.setAttribute('aria-labelledby',tab.id);
  tab.addEventListener('click', e => { e.preventDefault(); activate(tab); });
  tab.addEventListener('keydown', e => {
   let next;
   if (e.key === 'ArrowRight') next = (i + 1) % tabs.length;
   if (e.key === 'ArrowLeft') next = (i + tabs.length - 1) % tabs.length;
   if (e.key === 'Home') next = 0;
   if (e.key === 'End') next = tabs.length - 1;
   if (next !== undefined) { e.preventDefault(); activate(tabs[next], true); }
  });
 });
 if (tabs.length) activate(tabs.find(t => '#' + t.dataset.exposureTab === window.location.hash) || tabs[0]);
});
document.querySelectorAll('[data-email-check]').forEach(form => {
 const email = form.querySelector('input[type=email][name=subject]');
 const consent = form.querySelector('input[name=own_data]');
 const button = form.querySelector('button[type=submit]');
 const error = form.querySelector('#breach-email-error');
 if (!email || !consent || !button) return;
 const providerReady = form.dataset.providerReady === 'true';
 const update = () => {
  if (error && !email.validity.valid && email.value) {
   error.textContent = email.validity.typeMismatch ? form.dataset.invalidEmail : form.dataset.invalidAddress;
   error.hidden = false;
  } else if (error && (!email.value || email.validity.valid)) {
   error.textContent = '';
   error.hidden = true;
  }
  button.disabled = !providerReady || !email.validity.valid || !consent.checked;
 };
 email.addEventListener('input', update);
 email.addEventListener('blur', update);
 consent.addEventListener('change', update);
 update();
});
document.querySelectorAll('[data-public-search]').forEach(form => {
 const submitButton = form.querySelector('button[type=submit]');
 const updateButton = () => {
  if (!submitButton) return;
  const subject = form.querySelector('[name=subject]');
  const consent = form.querySelector('[name=own_data]');
  submitButton.disabled = form.dataset.providerReady !== 'true' || !subject?.checkValidity() || !consent?.checked;
 };
 form.querySelectorAll('[name=subject], [name=own_data]').forEach(input => input.addEventListener('input', updateButton));
 form.querySelector('[name=own_data]')?.addEventListener('change', updateButton);
 form.querySelectorAll('[name=match_type]').forEach(radio => radio.addEventListener('change', () => {
  const email = form.querySelector('[name=match_type]:checked').value === 'email';
  const input = form.querySelector('[name=subject]');
  input.type = email ? 'email' : 'text'; input.value = ''; input.placeholder = email ? 'nume@exemplu.ro' : form.dataset.namePlaceholder;
  form.querySelector('[data-subject-label]').textContent = email ? form.dataset.emailLabel : form.dataset.nameLabel;
  form.querySelector('[data-own-label]').textContent = email ? form.dataset.ownEmailLabel : form.dataset.ownNameLabel;
  form.querySelector('[data-location]').hidden = email;
  form.querySelector('[name=location]').disabled = email;
  updateButton();
 }));
 updateButton();
});
document.querySelectorAll('[data-processing-form]').forEach(form => form.addEventListener('submit', () => {
 const button = form.querySelector('button[type=submit],button:not([type])');
 if (button) { button.disabled = true; button.innerHTML = `<span class="exposure-spinner" aria-hidden="true"></span><span>${form.dataset.processingText}</span>`; button.setAttribute('aria-live', 'polite'); }
}));
const status = document.querySelector('[data-exposure-status]');
if (status) {
 let stopped = false;
 const poll = async () => {
  if (stopped) return;
  try {
   const response = await fetch(status.dataset.exposureStatus, {headers:{Accept:'application/json'}, cache:'no-store', credentials:'same-origin'});
   if (!response.ok) { stopped = true; status.textContent = status.dataset.unavailableText; return; }
   const data = await response.json();
   if (data.state !== status.dataset.initialState) { stopped = true; window.location.reload(); return; }
  } catch { status.textContent = status.dataset.connectionErrorText; }
  window.setTimeout(poll, 5000);
 };
 window.setTimeout(poll, 5000);
}
