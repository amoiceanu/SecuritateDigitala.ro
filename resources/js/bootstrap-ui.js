import { Modal, Dropdown } from 'bootstrap';

document.querySelectorAll('[data-bootstrap-dropdown]').forEach(root => {
    const trigger=root.querySelector('button, [role="button"]');
    if(!trigger)return;
    trigger.setAttribute('data-bs-toggle','dropdown');trigger.setAttribute('aria-expanded','false');trigger.classList.add('dropdown-toggle');
    Dropdown.getOrCreateInstance(trigger);
});
document.querySelectorAll('.modal[data-modal-show="1"]').forEach(element=>Modal.getOrCreateInstance(element).show());
window.addEventListener('open-modal',event=>{const element=document.getElementById(event.detail);if(element)Modal.getOrCreateInstance(element).show();});
window.addEventListener('close-modal',event=>{const element=document.getElementById(event.detail);if(element)Modal.getOrCreateInstance(element).hide();});
