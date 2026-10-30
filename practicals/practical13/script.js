const api = 'practicals/practical13/api.php';
let csrf = '';
const form = document.querySelector('#event-form');
const notice = document.querySelector('#event-notice');
const list = document.querySelector('#event-list');
const loading = document.querySelector('#event-loading');
const idField = document.querySelector('#event-id');
const value = id => document.getElementById(id).value;

function show(message, error = false) {
  notice.textContent = message;
  notice.classList.toggle('error', error);
  notice.hidden = !message;
}
async function send(method = 'GET', formData = null) {
  const options = { method, headers: { Accept: 'application/json' } };
  if (formData) { options.headers['X-CSRF-Token'] = csrf; options.body = formData; }
  const response = await fetch(api, options);
  const data = await response.json();
  if (!response.ok || !data.ok) throw new Error(data.message || `Request failed (${response.status}).`);
  return data;
}
async function loadEvents() {
  loading.hidden = false;
  try {
    const data = await send(); csrf = data.csrf; list.replaceChildren();
    for (const event of data.events) {
      const row = document.createElement('article'); row.className = 'event-row';
      if (event.poster_path) { const image = document.createElement('img'); image.className = 'poster-preview'; image.src = event.poster_path; image.alt = `Poster for ${event.title}`; row.append(image); }
      else { const placeholder = document.createElement('span'); placeholder.textContent = 'No poster'; row.append(placeholder); }
      const info = document.createElement('div');
      const title = document.createElement('strong'); title.textContent = event.title;
      const details = document.createElement('p'); details.textContent = `${event.category} · ${event.event_date} · ${String(event.start_time).slice(0,5)} · ${event.location}`;
      const status = document.createElement('small'); status.textContent = event.status === 'open' ? 'Active' : 'Cancelled';
      info.append(title, details, status); row.append(info);
      const actions = document.createElement('div'); actions.className = 'event-actions';
      const edit = document.createElement('button'); edit.type='button'; edit.className='event-action'; edit.textContent='Edit';
      edit.onclick=()=>{
        idField.value=event.event_id; document.querySelector('#form-title').textContent='Update event';
        document.querySelector('#save-event').textContent='Save event changes';
        document.querySelector('#title').value=event.title; document.querySelector('#category').value=event.category;
        document.querySelector('#description').value=event.description; document.querySelector('#event-date').value=event.event_date;
        document.querySelector('#start-time').value=String(event.start_time).slice(0,5);
        document.querySelector('#end-time').value=event.end_time ? String(event.end_time).slice(0,5) : '';
        document.querySelector('#location').value=event.location; document.querySelector('#capacity').value=event.capacity ?? '';
        document.querySelector('#cancel-edit').hidden=false; window.scrollTo({top:0,behavior:'smooth'});
      };
      actions.append(edit);
      if (event.status==='open') { const del=document.createElement('button'); del.type='button'; del.className='event-action'; del.textContent='Delete'; del.onclick=async()=>{
        if(!confirm('Delete this event from the active list?')) return;
        const fd=new FormData(); fd.set('action','delete'); fd.set('event_id',event.event_id);
        try { const result=await send('POST',fd); show(result.message); await loadEvents(); } catch(e) { show(e.message,true); }
      }; actions.append(del); }
      row.append(actions); list.append(row);
    }
    if(!data.events.length) list.textContent='No events have been created yet.';
  } catch(e) { show(e.message,true); }
  finally { loading.hidden=true; }
}
function resetForm() { form.reset(); idField.value=''; document.querySelector('#form-title').textContent='Create an event'; document.querySelector('#save-event').textContent='Create event'; document.querySelector('#cancel-edit').hidden=true; }
form.addEventListener('submit',async event=>{
  event.preventDefault(); const fd=new FormData(form); fd.set('action',idField.value?'update':'create');
  try { const result=await send('POST',fd); show(result.message); resetForm(); await loadEvents(); }
  catch(e) { show(e.message,true); }
});
document.querySelector('#cancel-edit').addEventListener('click',resetForm);
document.querySelector('#reload-events').addEventListener('click',loadEvents);
loadEvents();

