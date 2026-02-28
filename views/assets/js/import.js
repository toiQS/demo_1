const productList = <?=json_encode($products)?>;
let rowCount = 0;

document.getElementById('searchInput').addEventListener('input', function(){
  const kw = this.value.toLowerCase();
  document.querySelectorAll('#importTable tbody tr').forEach(r=>{
    r.style.display = r.textContent.toLowerCase().includes(kw) ? '' : 'none';
  });
});

function addItemRow(prod='', qty=1, cost=0){
  rowCount++;
  const id = `row${rowCount}`;
  const opts = productList.map(p=>`<option${prod===p?' selected':''}>${p}</option>`).join('');
  const div = document.createElement('div');
  div.className = 'import-item-row';
  div.id = id;
  div.innerHTML = `
    <select class="form-control" onchange="calcTotal()"><option value="">-- Chọn sản phẩm --</option>${opts}</select>
    <input type="number" class="form-control" value="${qty}" min="1" placeholder="SL" oninput="calcTotal()">
    <input type="number" class="form-control" value="${cost}" min="0" placeholder="Giá nhập (₫)" oninput="calcTotal()">
    <div class="item-total" id="total_${rowCount}">0₫</div>
    <button class="btn-remove-item" onclick="removeRow('${id}')"><i class="fa-solid fa-xmark"></i></button>
  `;
  document.getElementById('itemRows').appendChild(div);
  calcTotal();
}

function removeRow(id){ document.getElementById(id).remove(); calcTotal(); }

function calcTotal(){
  let grand = 0;
  document.querySelectorAll('.import-item-row').forEach((row, idx) => {
    const inputs = row.querySelectorAll('input[type=number]');
    const qty  = parseFloat(inputs[0].value) || 0;
    const cost = parseFloat(inputs[1].value) || 0;
    const total = qty * cost;
    grand += total;
    const tEl = row.querySelector('.item-total');
    if(tEl) tEl.textContent = total.toLocaleString('vi') + '₫';
  });
  document.getElementById('grandTotal').textContent = grand.toLocaleString('vi') + '₫';
}

function openModal(r=null){
  document.getElementById('iNote').value  = r ? r.note : '';
  document.getElementById('modalTitle').textContent = r ? 'Sửa phiếu nhập ' + r.id : 'Tạo phiếu nhập';
  document.getElementById('itemRows').innerHTML = '';
  rowCount = 0;
  if(r){ addItemRow('',r.items,r.total/r.items); }
  else  { addItemRow(); }
  document.getElementById('importModal').classList.add('show');
}
function closeModal(){ document.getElementById('importModal').classList.remove('show'); }

function saveImport(status){
  showToast(status==='done' ? 'Phiếu nhập đã được xác nhận!' : 'Đã lưu phiếu nháp', 'success');
  closeModal();
}
function confirmImport(id){ showConfirm(`Xác nhận nhập kho phiếu ${id}?`, ()=>showToast('Đã xác nhận nhập kho!','success'), 'Xác nhận'); }
function confirmDelete(id){ showConfirm(`Xoá phiếu nhập ${id}?`, ()=>showToast('Đã xoá phiếu nhập','success'), 'Xoá'); }

document.getElementById('importModal').addEventListener('click', function(e){ if(e.target===this) closeModal(); });
// Add 1 default row on load
window.addEventListener('load', ()=>{ if(document.getElementById('itemRows').children.length===0) addItemRow(); });