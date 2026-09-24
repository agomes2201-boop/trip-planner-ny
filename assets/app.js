const tripDate = new Date('2027-05-01T00:00:00-03:00');
const now = new Date();
const days = Math.max(0, Math.ceil((tripDate - now) / 86400000));
const output = document.querySelector('#days');
if (output) output.textContent = new Intl.NumberFormat('pt-BR').format(days);
