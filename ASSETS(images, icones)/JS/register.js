document.getElementById('registerForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('../../api/auth.php?action=register', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      document.getElementById('registerMessage').innerHTML = "Inscription réussie ! Vérifiez votre email.";
    } else {
      document.getElementById('registerMessage').innerHTML = data.message;
    }
  });
});