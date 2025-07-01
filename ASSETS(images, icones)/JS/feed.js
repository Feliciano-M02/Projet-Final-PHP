// vérifier si l'utilisateur est en session
const user = JSON.parse(sessionStorage.getItem('user'));
if (!user) {
  window.location.href = "login.html";
}

function logout() {
  sessionStorage.clear();
  window.location.href = "login.html";
}

function loadFeed() {
  fetch('../../api/posts.php?action=list')
    .then(response => response.json())
    .then(posts => {
      let html = '';
      posts.forEach(post => {
        html += `
          <div class="post">
            <img src="../../assets/images/${post.avatar}" alt="avatar" width="50">
            <strong>${post.firstname} ${post.lastname}</strong><br>
            <p>${post.description}</p>
            ${post.image ? <img src="../../assets/images/${post.image}" width="200"> : ''}
            <br>
            <button onclick="toggleLike(${post.id}, ${post.liked_by_user ? 1 : 0})">
              ${post.liked_by_user ? '❤' : '🤍'}
            </button> (${post.like_count})
            <br>
            <button onclick="loadComments(${post.id})">Commentaires</button>
            <div id="comments-${post.id}"></div>
            <div>
              <input type="text" id="commentInput-${post.id}" placeholder="Commenter..." />
              <button onclick="addComment(${post.id})">Envoyer</button>
            </div>
          </div>
          <hr>
        `;
      });
      document.getElementById('feed').innerHTML = html;
    })
    .catch(err => console.error(err));
}

loadFeed();

// gérer le like
function toggleLike(postId, liked) {
  fetch(../../api/posts.php?action=like, {
    method: 'POST',
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ user_id: user.id, post_id: postId, liked: liked })
  })
  .then(() => loadFeed());
}

// charger les commentaires
function loadComments(postId) {
  fetch(../../api/posts.php?action=comments&post_id=${postId})
    .then(response => response.json())
    .then(comments => {
      let html = '';
      comments.forEach(c => {
        html += <strong>${c.firstname} ${c.lastname}</strong>: ${c.comment}<br>;
      });
      document.getElementById(comments-${postId}).innerHTML = html;
    });
}

// ajouter un commentaire
function addComment(postId) {
  const text = document.getElementById(commentInput-${postId}).value;
  fetch(../../api/posts.php?action=add_comment, {
    method: 'POST',
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ user_id: user.id, post_id: postId, comment: text })
  })
  .then(() => {
    loadComments(postId);
    document.getElementById(commentInput-${postId}).value = "";
  });
}

