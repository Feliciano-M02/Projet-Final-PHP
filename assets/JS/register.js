document.getElementById('registerForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  fetch('../../api/user.php?action=register', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    console.log(data);
    if (data.success) {
      document.getElementById('registerMessage').style.color = "green";
      document.getElementById('registerMessage').innerHTML = "Inscription réussie ! Vérifiez votre email.";
    } else {
      document.getElementById('registerMessage').style.color = "red";
      document.getElementById('registerMessage').innerHTML = data.message ?? data.error ?? data ?? "Une erreur est survenue.";
    }
  })
  .catch(err => {
    console.error(err);
    document.getElementById('registerMessage').style.color = "red";
    document.getElementById('registerMessage').innerHTML = "Vous aviez deja utilise cet email";
  });
});