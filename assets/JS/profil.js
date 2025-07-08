const user = JSON.parse(sessionStorage.getItem('user'));
if (!user) {
  window.location.href = "login.html";
}

function logout() {
  sessionStorage.clear();
  window.location.href = "login.html";
}

// charger les infos
function loadProfile() {
  fetch(`../../api/user.php?action=profile&user_id=${user.id}`)
    .then(r => r.json())
    .then(data => {
      document.getElementById('profileInfos').innerHTML = `
        <img src="../../assets/images/${data.avatar}" width="100"><br>
        ${data.firstname} ${data.lastname}<br>
        ${data.email}
      `;
      document.getElementById('firstname').value = data.firstname;
      document.getElementById('lastname').value = data.lastname;
      document.getElementById('email').value = data.email;
    });
}

loadProfile();

// update infos
document.getElementById('updateForm').addEventListener('submit', function(e){
  e.preventDefault();
  const formData = new FormData(this);
  formData.append('user_id', user.id);

  fetch('../../api/user.php?action=update', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById('message').innerHTML = data.message;
    loadProfile();
  });
});

// update password
document.getElementById('passwordForm').addEventListener('submit', function(e){
  e.preventDefault();
  const formData = new FormData(this);
  formData.append('user_id', user.id);

  fetch('../../api/user.php?action=update_password', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById('message').innerHTML = data.message;
  });
});

// update avatar
document.getElementById('avatarForm').addEventListener('submit', function(e){
  e.preventDefault();
  const formData = new FormData(this);
  formData.append('user_id', user.id);

  fetch('../../api/user.php?action=update_avatar', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById('message').innerHTML = data.message;
    loadProfile();
  });
});