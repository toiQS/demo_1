const statusMap = <?=json_encode($status_map)?>;
const orders    = <?=json_encode($orders)?>;
const stepOrder = ['pending','processing','shipped','completed'];
const stepLabels= ['Chờ xác nhận','Đang xử lý','Đang giao','Hoàn thành'];

function applyFilters(){
  const kw=document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('#ordersTable tbody tr').forEach(r=>{
    const text=r.textContent.toLowerCase();
    r.style.display=text.includes(kw)?'':'none';
  });
}
function resetFilters(){
  document.getElementById('searchInput').value='';
  document.getElementById('filterFrom').value='';
  document.getElementById('filterTo').value='';
  applyFilters();
}
document.getElementById('searchInput').addEventListener('input',applyFilters);

function filterByTab(btn){
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  const st=btn.dataset.status;
  document.querySelectorAll('#ordersTable tbody tr').forEach(r=>{
    r.style.display=(!st||r.dataset.status===st)?'':'none';
  });
}

function openDetail(o){
  const s=statusMap[o.status];
  const stepIdx=stepOrder.indexOf(o.status);
  let stepperHtml='<div class="status-stepper">';
  stepOrder.forEach((st,i)=>{
    const done=i<stepIdx;
    const active=i===stepIdx;
    stepperHtml+=`<div class="step ${done?'done':active?'active':''}">
      <div class="step-dot">${done?'<i class="fa-solid fa-check"></i>':i+1}</div>
      <div class="step-label">${stepLabels[i]}</div>
    </div>`;
  });
  stepperHtml+='</div>';

  let itemsHtml='';
  let sub=0;
  o.items.forEach(it=>{
    const total=it.qty*it.price;sub+=total;
    itemsHtml+=`<tr><td>${it.name}</td><td style="text-align:center">${it.qty}</td><td style="text-align:right;font-family:var(--mono)">${it.price.toLocaleString()}₫</td><td style="text-align:right;font-family:var(--mono);color:var(--accent)">${total.toLocaleString()}₫</td></tr>`;
  });

  document.getElementById('orderModalTitle').textContent=`Chi tiết đơn #${o.id}`;
  document.getElementById('orderModalBody').innerHTML=`
    ${stepperHtml}
    <div class="order-detail-grid">
      <div class="detail-block">
        <h4><i class="fa-solid fa-user" style="margin-right:6px;color:var(--accent)"></i>Thông tin khách</h4>
        <div class="detail-row"><span>Tên</span><span>${o.customer}</span></div>
        <div class="detail-row"><span>SĐT</span><span>${o.phone}</span></div>
        <div class="detail-row"><span>Địa chỉ</span><span style="max-width:200px">${o.address}</span></div>
      </div>
      <div class="detail-block">
        <h4><i class="fa-solid fa-receipt" style="margin-right:6px;color:var(--accent)"></i>Thông tin đơn</h4>
        <div class="detail-row"><span>Mã đơn</span><span style="font-family:var(--mono);color:var(--blue)">#${o.id}</span></div>
        <div class="detail-row"><span>Ngày đặt</span><span>${o.date}</span></div>
        <div class="detail-row"><span>Trạng thái</span><span><span class="badge ${s.class}">${s.label}</span></span></div>
        ${o.note?`<div class="detail-row"><span>Ghi chú</span><span>${o.note}</span></div>`:''}
      </div>
    </div>
    <h4 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted);margin-bottom:10px">Sản phẩm đặt mua</h4>
    <div style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
      <table class="items-table">
        <thead><tr><th>Sản phẩm</th><th style="text-align:center">SL</th><th style="text-align:right">Đơn giá</th><th style="text-align:right">Thành tiền</th></tr></thead>
        <tbody>${itemsHtml}</tbody>
      </table>
    </div>
    <div class="order-total-line"><span>Tổng cộng</span><span>${o.total.toLocaleString()}₫</span></div>
  `;

  const nb=document.getElementById('nextStepBtn');
  if(s.next){
    nb.style.display='flex';
    nb.innerHTML=`<i class="fa-solid fa-circle-check" style="margin-right:6px"></i>${s.next_label}`;
    nb.onclick=()=>updateStatus(o.id,o.status,s.next);
  } else { nb.style.display='none'; }

  document.getElementById('orderModal').classList.add('show');
}
function closeModal(){document.getElementById('orderModal').classList.remove('show');}
function updateStatus(id,from,to){
  showConfirm(`Chuyển đơn #${id} sang trạng thái "${statusMap[to].label}"?`,()=>{
    showToast(`Đã cập nhật trạng thái đơn #${id}`,'success');
    closeModal();
  },statusMap[to].label);
}
function cancelOrder(id){
  showConfirm(`Huỷ đơn hàng #${id}?`,()=>showToast(`Đã huỷ đơn #${id}`,'success'),'Huỷ đơn');
}
document.getElementById('orderModal').addEventListener('click',function(e){if(e.target===this)closeModal();});