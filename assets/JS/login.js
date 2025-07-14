document.getElementById('loginForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;

  fetch('../../api/login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email, password })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      sessionStorage.setItem('user', JSON.stringify(data.user));
      window.location.href = 'home.html';
    } else {
      document.getElementById('loginMessage').innerText = data.message;
    }
  })
  .catch(err => {
    console.error("Erreur lors de la connexion :", err);
    document.getElementById('loginMessage').innerText = "Une erreur s'est produite.";
  });
});