// assets/chat.js
document.addEventListener('DOMContentLoaded', function(){
  const chatBox = document.getElementById('chat-box');
  const form = document.getElementById('chat-form');
  const recipient = form.querySelector('input[name="recipient_id"]').value;
  let lastLen = 0;

  async function fetchMessages(){
    const url = '/ajax/fetch_messages.php?u=' + encodeURIComponent(recipient);
    const res = await fetch(url);
    const data = await res.json();
    if (!data.ok) return;
    // render messages
    chatBox.innerHTML = '';
    data.messages.forEach(m => {
      const div = document.createElement('div');
      div.style.padding = '6px';
      div.style.marginBottom = '6px';
      div.innerHTML = '<strong>' + escapeHtml(m.sender_name) + '</strong> <small>' + m.created_at + '</small><div>' + nl2br(escapeHtml(m.body)) + '</div>';
      chatBox.appendChild(div);
    });
    // scroll to bottom if there are new messages
    if (data.messages.length !== lastLen) {
      chatBox.scrollTop = chatBox.scrollHeight;
    }
    lastLen = data.messages.length;
  }

  form.addEventListener('submit', async function(e){
    e.preventDefault();
    const body = form.querySelector('textarea[name="body"]').value.trim();
    if (!body) return;
    const fd = new FormData();
    fd.append('recipient_id', recipient);
    fd.append('body', body);
    const resp = await fetch('/ajax/send_message.php', { method:'POST', body: fd });
    const result = await resp.json();
    if (result.ok) {
      form.querySelector('textarea[name="body"]').value = '';
      await fetchMessages();
    } else {
      alert('Gönderilemedi: ' + (result.error || 'hata'));
    }
  });

  // polling
  fetchMessages();
  setInterval(fetchMessages, 2000);

  function escapeHtml(s) {
    return s.replace(/[&<>"']/g, function(m) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];
    });
  }
  function nl2br(s) {
    return s.replace(/\n/g, '<br>');
  }
});
