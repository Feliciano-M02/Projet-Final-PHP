const user = JSON.parse(sessionStorage.getItem('user'));
if (!user) {
  window.location.href = "login.html";
}

function logout() {
  sessionStorage.clear();
  window.location.href = "login.html";
}

let activeConversation = null;

function loadSidebar() {
  fetch(../../api/chat.php?action=list_friends&user_id=${user.id})
    .then(r => r.json())
    .then(data => {
      let html = '';
      data.forEach(friend => {
        html += `
          <div onclick="selectConversation(${friend.id})" style="cursor:pointer;">
            ${friend.firstname} ${friend.lastname}
          </div>`;
      });
      document.getElementById('sidebar').innerHTML = html;
    });
}

function selectConversation(friendId) {
  fetch(../../api/chat.php?action=start_conversation&user_id=${user.id}&friend_id=${friendId})
    .then(r => r.json())
    .then(data => {
      activeConversation = data.conversation_id;
      loadMessages();
    });
}

function loadMessages() {
  if (!activeConversation) return;
  fetch(../../api/chat.php?action=get_messages&conversation_id=${activeConversation})
    .then(r => r.json())
    .then(data => {
      let html = '';
      data.forEach(msg => {
        html += `<div>
          <strong>${msg.firstname} ${msg.lastname}</strong>: ${msg.message}
          ${msg.image ? <br><img src="../../assets/images/${msg.image}" width="100"> : ""}
          </div><hr>`;
      });
      document.getElementById('messages').innerHTML = html;
      document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
    });
}

// envoyer message
document.getElementById('chatForm').addEventListener('submit', function(e){
  e.preventDefault();
  if (!activeConversation) return alert("Choisissez un ami d'abord");
  const formData = new FormData(this);
  formData.append('conversation_id', activeConversation);
  formData.append('sender_id', user.id);

  fetch('../../api/chat.php?action=send', {
    method: 'POST',
    body: formData
  })
  .then(() => {
    document.getElementById('message').value = "";
    document.getElementById('image').value = "";
    loadMessages();
  });
});

// refresh messages every 3s
setInterval(() => {
  if (activeConversation) {
    loadMessages();
  }
}, 3000);

loadSidebar();