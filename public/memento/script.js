const months=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
const events={ '2025-7-8':{t:'Reunión de proyecto',c:'trabajo'}, '2025-7-9':{t:'Llamar a la clínica',c:'personal'},
  '2025-7-11':{t:'Enviar propuesta',c:'trabajo2'}, '2025-7-12':{t:'Cena con familia',c:'familia'} };
const reminders=[
 {cat:'Prioritario',dot:'#d9564a',title:'Reunión de proyecto',date:'Hoy, 8 de julio',time:'10:00',pill:'Trabajo',cls:''},
 {cat:'Personal',dot:'#6d4fe0',title:'Llamar a la clínica',date:'Mañana, 9 de julio',time:'09:30',pill:'Personal',cls:''},
 {cat:'Trabajo',dot:'#4a90d9',title:'Enviar propuesta',date:'Viernes, 11 de julio',time:'12:00',pill:'Trabajo',cls:'blue'},
 {cat:'Personal',dot:'#e0a23a',title:'Cena con familia',date:'Sábado, 12 de julio',time:'20:30',pill:'Personal',cls:'amber'}];
const TODAY={y:2025,m:7,d:8};
let view={y:2025,m:7};
const grid=document.getElementById('grid');
function render(){
  document.getElementById('month-title').textContent=`${months[view.m-1]} ${view.y}`;
  grid.innerHTML='';
  const first=(new Date(view.y,view.m-1,1).getDay()+6)%7;
  const days=new Date(view.y,view.m,0).getDate();
  for(let i=0;i<42;i++){
    const d=i-first+1, cell=document.createElement('div');
    cell.className='cell';
    if(d<1||d>days){cell.classList.add('muted');grid.appendChild(cell);continue;}
    const ev=events[`${view.y}-${view.m}-${d}`];
    const isToday=view.y===TODAY.y&&view.m===TODAY.m&&d===TODAY.d;
    if(isToday)cell.classList.add('today');
    if(ev)cell.classList.add('has-event');
    cell.innerHTML=`<span class="num">${d}</span>`+(ev?`<span class="ev ${ev.c}">${ev.t}</span>`:'');
    grid.appendChild(cell);
  }
}
document.getElementById('prev').onclick=()=>{view.m--;if(view.m<1){view.m=12;view.y--}render()};
document.getElementById('next').onclick=()=>{view.m++;if(view.m>12){view.m=1;view.y++}render()};
document.getElementById('today-btn').onclick=()=>{view={y:TODAY.y,m:TODAY.m};render()};
document.getElementById('today').textContent='Martes, 8 de julio';
document.getElementById('cards').innerHTML=reminders.map(r=>`
 <article class="card">
  <div class="top"><span class="dot" style="background:${r.dot}"></span>${r.cat}<span class="dots">•••</span></div>
  <h3>${r.title}</h3><p class="date">${r.date}</p>
  <div class="bottom"><span class="time"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>${r.time}</span>
  <span class="pill ${r.pill} ${r.cls}">${r.pill}</span></div></article>`).join('');
render();
