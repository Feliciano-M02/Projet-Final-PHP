document.getElementById('registerForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('../../api/register.php', {
    method: 'POST',
    body: formData
  })
  .then(res => {
    console.log("Réponse du serveur :", res);
    return res.json()})
  .then(data => {
    if (data.success) {
      // Stocker l'utilisateur dans sessionStorage
      sessionStorage.setItem('user', JSON.stringify(data.user));

      // Rediriger vers home.html
      window.location.href = "home.html";
    } else {
      document.getElementById('registerMessage').innerText = data.message;
    }
  })
  .catch(err => {
    console.error("Erreur lors de l'inscription :", err);
    document.getElementById('registerMessage').innerText = "Erreur réseau.";
  });
});