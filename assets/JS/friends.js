// vérifier la session
const user = JSON.parse(sessionStorage.getItem('user'));
if (!user) {
  window.location.href = "login.html";
}

function logout() {
  sessionStorage.clear();
  window.location.href = "login.html";
};

// charger les utilisateurs pour envoyer des invitations
function loadUsers() {
  fetch('../../api/friends.php?action=list_users&user_id=' + user.id)
    .then(r => r.json())
    .then(data => {
      let html = '';
      data.forEach(u => {
        html += `
          <div>
            ${u.firstname} ${u.lastname}
            <button onclick="sendInvite(${u.id})">Ajouter en ami</button>
          </div>
        `;
      });
      document.getElementById('users').innerHTML = html;
    });
}

// charger les invitations reçues
function loadInvitations() {
  fetch('../../api/friends.php?action=list_invites&user_id=' + user.id)
    .then(r => r.json())
    .then(data => {
      let html = '';
      data.forEach(invite => {
        html += `
          <div>
            ${invite.firstname} ${invite.lastname}
            <button onclick="acceptInvite(${invite.id})">Accepter</button>
            <button onclick="refuseInvite(${invite.id})">Refuser</button>
          </div>
        `;
      });
      document.getElementById('invitations').innerHTML = html;
    });
}

function sendInvite(friend_id) {
  fetch('../../api/friends.php?action=send', {
    method: 'POST',
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({user_id: user.id, friend_id: friend_id})
  })
  .then(() => {
    loadUsers();
    alert("Invitation envoyée !");
  });
}

function acceptInvite(invite_id) {
  fetch('../../api/friends.php?action=accept', {
    method: 'POST',
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({invite_id: invite_id})
  })
  .then(() => loadInvitations());
}

function refuseInvite(invite_id) {
  fetch('../../api/friends.php?action=refuse', {
    method: 'POST',
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({invite_id: invite_id})
  })
  .then(() => loadInvitations());
}

// initial load
loadUsers();
loadInvitations();