document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('../../api/auth.php?action=login', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // stocker dans sessionStorage
            sessionStorage.setItem('user', JSON.stringify(data.user));
            // rediriger vers le flux d'articles (page d'accueil)
            window.location.href = "home.html";
        } else {
            document.getElementById('loginMessage').innerHTML = data.message;
        }
    })
    .catch(err => console.error(err));
});