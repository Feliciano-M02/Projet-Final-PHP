document.getElementById('adminLoginForm').addEventListener('submit', function(e){
  e.preventDefault();

  const formData = new FormData(this);

  fetch('../../api/admin.php?action=login', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if(data.success) {
      sessionStorage.setItem('admin', JSON.stringify(data.admin));
      window.location.href = "dashboard.html";
    } else {
      document.getElementById('adminLoginMessage').innerHTML = data.message;
    }
  });
});