document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('filterCat').addEventListener('change', applyFilters);

let currentTabFilter = '';
function filterTab(btn){
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  currentTabFilter = btn.dataset.filter;
  applyFilters();
}

function applyFilters(){
  const kw  = document.getElementById('searchInput').value.toLowerCase();
  const cat = document.getElementById('filterCat').value;
  document.querySelectorAll('#invTable tbody tr').forEach(r=>{
    const matchKw  = r.textContent.toLowerCase().includes(kw);
    const matchCat = !cat || r.dataset.cat === cat;
    const matchTab = !currentTabFilter
                  || (currentTabFilter==='low' && r.dataset.low==='1')
                  || (currentTabFilter==='ok'  && r.dataset.low==='0');
    r.style.display = (matchKw && matchCat && matchTab) ? '' : 'none';
  });
}

function exportReport(){
  showToast('Đang xuất báo cáo tồn kho...','info');
  setTimeout(()=>showToast('Xuất báo cáo thành công!','success'),1200);
}