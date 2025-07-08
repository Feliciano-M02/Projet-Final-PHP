document.getElementById('form-inscription').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('../../api/inscription.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    document.getElementById('message').textContent = data.message;
  })
  .catch(error => {
    document.getElementById('message').textContent = 'Erreur réseau';
  });
});

document.getElementById('form-connexion')?.addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('../../api/connexion.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    document.getElementById('message').textContent = data.message;
    if (data.success) {
      sessionStorage.setItem("userId", data.userId); // Stocker l'ID utilisateur
      // Rediriger ou afficher l’accueil
    }
  });
});
