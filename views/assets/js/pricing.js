document.getElementById('searchInput').addEventListener('input', function(){
  const kw=this.value.toLowerCase();
  document.querySelectorAll('#pricingTable tbody tr').forEach(r=>{
    r.style.display=r.textContent.toLowerCase().includes(kw)?'':'none';
  });
});

function updateRow(input){
  const row = input.closest('tr');
  const id  = row.dataset.id;
  const cost= parseFloat(row.dataset.cost)||0;
  const pct = parseFloat(input.value)||0;
  const price= Math.round(cost*(1+pct/100));
  const margin=price-cost;
  const priceEl = document.getElementById('price_'+id);
  const marginEl= document.getElementById('margin_'+id);
  const barEl   = document.getElementById('bar_'+id);
  if(priceEl)  priceEl.textContent  = price.toLocaleString('vi')+'₫';
  if(marginEl) marginEl.textContent = margin.toLocaleString('vi')+'₫';
  if(barEl)    barEl.style.width    = Math.min(100,pct/2)+'%';
}

function saveProfit(input){ showToast('Đã cập nhật % lợi nhuận!','success'); }

function applyBulk(){
  const pct=parseFloat(document.getElementById('bulkProfit').value);
  if(!pct){ showToast('Vui lòng nhập % lợi nhuận','warning'); return; }
  const cat=document.getElementById('bulkCat').value;
  const msg=cat?`Áp dụng ${pct}% LN cho danh mục "${cat}"?`:`Áp dụng ${pct}% LN cho tất cả sản phẩm?`;
  showConfirm(msg,()=>{
    document.querySelectorAll('#pricingTable tbody tr').forEach(row=>{
      if(!cat || row.textContent.includes(cat)){
        const inp=row.querySelector('.editable-profit');
        if(inp){inp.value=pct;updateRow(inp);}
      }
    });
    showToast('Đã cập nhật giá hàng loạt!','success');
  },'Áp dụng');
}

let currentP=null;
function openModal(p){
  currentP=p;
  document.getElementById('mProduct').value=p.name;
  document.getElementById('mCost').value=p.cost;
  document.getElementById('mProfit').value=p.profit;
  document.getElementById('modalTitle').textContent='Cập nhật giá: '+p.name;
  modalCalc();
  document.getElementById('priceModal').classList.add('show');
}
function closeModal(){document.getElementById('priceModal').classList.remove('show');}
function modalCalc(){
  const cost=parseFloat(document.getElementById('mCost').value)||0;
  const pct =parseFloat(document.getElementById('mProfit').value)||0;
  const price=Math.round(cost*(1+pct/100));
  const margin=price-cost;
  document.getElementById('prevCost').textContent   =cost.toLocaleString('vi')+'₫';
  document.getElementById('prevMargin').textContent ='+'+margin.toLocaleString('vi')+'₫';
  document.getElementById('prevPrice').textContent  =price.toLocaleString('vi')+'₫';
}
function saveModal(){showToast('Đã cập nhật giá sản phẩm!','success');closeModal();}
document.getElementById('priceModal').addEventListener('click',function(e){if(e.target===this)closeModal();});