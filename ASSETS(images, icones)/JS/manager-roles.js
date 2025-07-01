const admin = JSON.parse(sessionStorage.getItem('admin'));
if (!admin || admin.role_id != 3) {
  window.location.href = "login.html";
}

function logout(){
  sessionStorage.clear();
  window.location.href="login.html";
}

function loadRoles(){
  fetch('../../api/admin.php?action=list_roles')
    .then(r => r.json())
    .then(data => {
      let html = '';
      data.forEach(role => {
        html += `
          <div>
            ${role.role_name}
            ${role.id > 3 ? <button onclick="deleteRole(${role.id})">Supprimer</button> : ''}
          </div>
        `;
      });
      document.getElementById('roleList').innerHTML = html;
    });
}

loadRoles();

// ajouter un rôle
document.getElementById('addRoleForm').addEventListener('submit', function(e){
  e.preventDefault();
  const formData = new FormData(this);

  fetch('../../api/admin.php?action=add_role', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById('message').innerHTML = data.message;
    loadRoles();
    this.reset();
  });
});

function deleteRole(roleId){
  if (confirm("Supprimer ce rôle ?")) {
    fetch('../../api/admin.php?action=delete_role', {
      method: 'POST',
      headers: {"Content-Type":"application/json"},
      body: JSON.stringify({role_id: roleId})
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('message').innerHTML = data.message;
      loadRoles();
    });
  }
}