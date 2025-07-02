const admin = JSON.parse(sessionStorage.getItem('admin'));
if (!admin) {
  window.location.href = "login.html";
}

function logout(){
  sessionStorage.clear();
  window.location.href="login.html";
}

function loadStats(){
  fetch(`/api/admin.php?action=stats`)
    .then(r=>r.json())
    .then(stats=>{
      document.getElementById('stats').innerHTML = `
        <p>Utilisateurs : ${stats.users}</p>
        <p>Articles : ${stats.posts}</p>
        <p>Commentaires : ${stats.comments}</p>
      `;

      document.getElementById('adminActions').innerHTML = `
        <a href="manage-users.html">Gérer les utilisateurs</a><br>
        ${admin.role_id == 3 ? '<a href="manage-roles.html">Gérer les rôles</a>' : ''}
      `;
    });
}

loadStats();