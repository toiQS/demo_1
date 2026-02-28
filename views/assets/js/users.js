// Filters
function applyFilters(){
  const kw=document.getElementById('searchInput').value.toLowerCase();
  const role=document.getElementById('filterRole').value;
  const st=document.getElementById('filterStatus').value;
  document.querySelectorAll('#usersTable tbody tr').forEach(r=>{
    const ok=r.textContent.toLowerCase().includes(kw)&&(!role||r.dataset.role===role)&&(!st||r.dataset.status===st);
    r.style.display=ok?'':'none';
  });
}
['searchInput','filterRole','filterStatus'].forEach(id=>{
  const el=document.getElementById(id);
  el.addEventListener(id==='searchInput'?'input':'change',applyFilters);
});

// Modal helpers
function closeModal(id){document.getElementById(id).classList.remove('show');}
[document.getElementById('userModal'),document.getElementById('resetModal')].forEach(m=>{
  m.addEventListener('click',function(e){if(e.target===this)this.classList.remove('show');});
});

function togglePass(inputId,btn){
  const el=document.getElementById(inputId);
  const isPass=el.type==='password';
  el.type=isPass?'text':'password';
  btn.innerHTML=isPass?'<i class="fa-solid fa-eye-slash"></i>':'<i class="fa-solid fa-eye"></i>';
}

// Add/Edit user
function openAddModal(){
  document.getElementById('uId').value='';
  document.getElementById('uName').value='';
  document.getElementById('uEmail').value='';
  document.getElementById('uPhone').value='';
  document.getElementById('uRole').value='customer';
  document.getElementById('uPass').value='';
  document.getElementById('passGroup').style.display='';
  document.getElementById('userModalTitle').textContent='Thêm tài khoản';
  document.getElementById('userModal').classList.add('show');
}
function openEditModal(u){
  document.getElementById('uId').value=u.id;
  document.getElementById('uName').value=u.name;
  document.getElementById('uEmail').value=u.email;
  document.getElementById('uPhone').value=u.phone;
  document.getElementById('uRole').value=u.role;
  document.getElementById('passGroup').style.display='none';
  document.getElementById('userModalTitle').textContent='Sửa tài khoản: '+u.name;
  document.getElementById('userModal').classList.add('show');
}
function saveUser(){
  const name=document.getElementById('uName').value.trim();
  const email=document.getElementById('uEmail').value.trim();
  if(!name||!email){showToast('Vui lòng nhập đầy đủ thông tin','warning');return;}
  showToast('Đã lưu tài khoản thành công!','success');
  closeModal('userModal');
}

// Reset password
function openResetModal(u){
  document.getElementById('resetUserName').textContent=u.name+' — '+u.email;
  document.getElementById('newPass').value='';
  document.getElementById('resetResult').style.display='none';
  document.getElementById('resetModal').classList.add('show');
}
function generatePass(){
  const chars='ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789@#!';
  let pass='';
  for(let i=0;i<10;i++) pass+=chars[Math.floor(Math.random()*chars.length)];
  document.getElementById('newPass').value=pass;
  document.getElementById('generatedPass').textContent=pass;
  document.getElementById('resetResult').style.display='block';
}
function doReset(){
  let pass=document.getElementById('newPass').value.trim();
  if(!pass){generatePass();pass=document.getElementById('newPass').value;}
  showToast('Đã đặt lại mật khẩu thành công!','success');
  closeModal('resetModal');
}

// Lock/Unlock
function lockUser(id,name){
  showConfirm(`Khoá tài khoản "${name}"?`,()=>showToast('Đã khoá tài khoản!','success'),'Khoá tài khoản');
}
function unlockUser(id,name){
  showConfirm(`Mở khoá tài khoản "${name}"?`,()=>showToast('Đã mở khoá tài khoản!','success'),'Mở khoá');
}