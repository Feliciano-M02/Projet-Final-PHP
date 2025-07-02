const admin = JSON.parse(sessionStorage.getItem('admin'));
if (!admin) {
  window.location.href = "login.html";
}

function logout(){
  sessionStorage.clear();
  window.location.href="login.html";
}

function loadUsers(){
  fetch('../../api/admin.php?action=list_users')
    .then(r=>r.json())
    .then(data=>{
      let html = '';
      data.forEach(u=>{
        html += `
          <div style="border:1px solid #ccc;margin:5px;padding:5px;">
            ${u.firstname} ${u.lastname} - ${u.email} - Rôle: ${u.role_name}
            <button onclick="deleteUser(${u.id})">Supprimer</button>
            ${admin.role_id == 3 ? `
              <select onchange="changeRole(${u.id}, this.value)">
                ${data.roles.map(r=>`
                  <option value="${r.id}" ${r.id==u.role_id ? 'selected' : ''}>
                    ${r.role_name}
                  </option>`).join("")}
              </select>
            `: ''}
          </div>
        `;
      });
      document.getElementById('userList').innerHTML = html;
    });
}

function deleteUser(userId){
  if(confirm("Supprimer cet utilisateur ?")){
    fetch('../../api/admin.php?action=delete_user', {
      method: 'POST',
      headers: {"Content-Type":"application/json"},
      body: JSON.stringify({user_id:userId})
    })
    .then(r=>r.json())
    .then(data=>{
      document.getElementById('message').innerHTML = data.message;
      loadUsers();
    });
  }
}

function changeRole(userId, roleId){
  fetch('../../api/admin.php?action=change_role', {
    method: 'POST',
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({user_id:userId, role_id:roleId})
  })
  .then(r=>r.json())
  .then(data=>{
    document.getElementById('message').innerHTML = data.message;
    loadUsers();
  });
}

loadUsers(); 