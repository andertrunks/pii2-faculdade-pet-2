const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const themeToggle = document.getElementById('trilho');
const pageBody = document.body;

registerBtn?.addEventListener('click', () => {
  container?.classList.add('active');
});

loginBtn?.addEventListener('click', () => {
  container?.classList.remove('active');
});

function applyTheme(isLight) {
  themeToggle?.classList.toggle('light', isLight);
  pageBody?.classList.toggle('light', isLight);
  themeToggle?.setAttribute('aria-pressed', String(isLight));
}

function getSavedTheme() {
  try {
    return window.localStorage?.getItem('adota-pet-theme') ?? null;
  } catch {
    return null;
  }
}

function saveTheme(value) {
  try {
    window.localStorage?.setItem('adota-pet-theme', value);
  } catch {
    // O tema continua funcionando mesmo quando o navegador bloqueia o armazenamento.
  }
}

applyTheme(getSavedTheme() === 'light');

themeToggle?.addEventListener('click', () => {
  const isLight = !pageBody.classList.contains('light');
  applyTheme(isLight);
  saveTheme(isLight ? 'light' : 'dark');
});

const cepInput = document.getElementById('cep');
const streetInput = document.getElementById('rua');
const cityInput = document.getElementById('cidade');
const stateInput = document.getElementById('uf');
const cepStatus = document.getElementById('cep-status');

function clearAddress() {
  if (streetInput) streetInput.value = '';
  if (cityInput) cityInput.value = '';
  if (stateInput) stateInput.value = '';
}

cepInput?.addEventListener('blur', async () => {
  const cep = cepInput.value.replace(/\D/g, '');
  if (cep.length !== 8) {
    clearAddress();
    if (cepStatus) cepStatus.textContent = 'Informe um CEP com 8 dígitos.';
    return;
  }

  if (cepStatus) cepStatus.textContent = 'Consultando CEP…';
  try {
    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`, { headers: { Accept: 'application/json' } });
    if (!response.ok) throw new Error('Falha na consulta');
    const address = await response.json();
    if (address.erro) throw new Error('CEP não encontrado');
    if (streetInput) streetInput.value = address.logradouro ?? '';
    if (cityInput) cityInput.value = address.localidade ?? '';
    if (stateInput) stateInput.value = address.uf ?? '';
    if (cepStatus) cepStatus.textContent = 'Endereço localizado. Confira os dados.';
  } catch {
    clearAddress();
    if (cepStatus) cepStatus.textContent = 'Não foi possível localizar o CEP. Preencha o endereço manualmente.';
  }
});
