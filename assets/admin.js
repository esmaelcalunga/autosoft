(function () {
  'use strict';

  function fmtSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    if (bytes < 1024 * 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    return (bytes / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
  }

  function renderPreviews(input, holder) {
    holder.innerHTML = '';
    if (!input.files || input.files.length === 0) {
      holder.hidden = true;
      return;
    }
    holder.hidden = false;
    Array.prototype.forEach.call(input.files, function (file) {
      var url = URL.createObjectURL(file);
      var item = document.createElement('div');
      item.className = 'upload-preview';
      var isVideo = file.type.indexOf('video/') === 0;
      var media;
      if (isVideo) {
        media = document.createElement('video');
        media.src = url;
        media.muted = true;
        media.playsInline = true;
        media.preload = 'metadata';
        var tag = document.createElement('span');
        tag.className = 'upload-preview-tag';
        tag.textContent = 'VÍDEO';
        item.appendChild(media);
        item.appendChild(tag);
      } else {
        media = document.createElement('img');
        media.src = url;
        item.appendChild(media);
      }
      var meta = document.createElement('div');
      meta.className = 'upload-preview-meta';
      meta.innerHTML = '<span class="upload-preview-name">' + file.name + '</span>' +
                      '<span class="upload-preview-size">' + fmtSize(file.size) + '</span>';
      item.appendChild(meta);
      holder.appendChild(item);

      if (isVideo) {
        media.addEventListener('loadedmetadata', function () { try { media.currentTime = 0.1; } catch (e) {} });
      }
    });
  }

  function totalSize(input) {
    var s = 0;
    if (input.files) {
      for (var i = 0; i < input.files.length; i++) s += input.files[i].size;
    }
    return s;
  }

  function initUpload() {
    var input = document.getElementById('upload-input');
    var previews = document.getElementById('upload-previews');
    var progress = document.getElementById('upload-progress');
    var bar = document.getElementById('upload-progress-bar');
    var txt = document.getElementById('upload-progress-text');
    var size = document.getElementById('upload-progress-size');
    if (!input || !previews) return;

    input.addEventListener('change', function () { renderPreviews(input, previews); });

    var form = input.form;
    if (!form || !window.FormData || !window.XMLHttpRequest) return;

    form.addEventListener('submit', function (ev) {
      if (!input.files || input.files.length === 0) return;
      ev.preventDefault();

      var xhr = new XMLHttpRequest();
      var data = new FormData(form);
      var total = totalSize(input);

      progress.hidden = false;
      bar.style.width = '0%';
      txt.textContent = '0%';
      if (size) size.textContent = '0 / ' + fmtSize(total);

      Array.prototype.forEach.call(form.querySelectorAll('button[type=submit]'), function (b) { b.disabled = true; });

      xhr.upload.addEventListener('progress', function (e) {
        if (!e.lengthComputable) return;
        var pct = Math.round(e.loaded / e.total * 100);
        bar.style.width = pct + '%';
        txt.textContent = pct + '%';
        if (size) size.textContent = fmtSize(e.loaded) + ' / ' + fmtSize(e.total);
      });
      xhr.addEventListener('load', function () {
        if (xhr.status >= 400) {
          bar.style.background = 'var(--red-500)';
          txt.textContent = 'Servidor devolveu erro ' + xhr.status + ' — ' + (xhr.responseText || '').slice(0, 200);
          if (size) size.textContent = '';
          Array.prototype.forEach.call(form.querySelectorAll('button[type=submit]'), function (b) { b.disabled = false; });
          return;
        }
        bar.style.width = '100%';
        txt.textContent = 'Concluído — a recarregar...';
        var dest = xhr.responseURL || form.action;
        try {
          var data = JSON.parse(xhr.responseText);
          if (data && data.redirect) { dest = data.redirect; }
        } catch (e) {}
        window.location.href = dest;
      });
      xhr.addEventListener('error', function () {
        txt.textContent = 'Erro de rede — tente de novo.';
        bar.style.background = 'var(--red-500)';
        Array.prototype.forEach.call(form.querySelectorAll('button[type=submit]'), function (b) { b.disabled = false; });
      });
      xhr.addEventListener('abort', function () {
        txt.textContent = 'Envio cancelado.';
        Array.prototype.forEach.call(form.querySelectorAll('button[type=submit]'), function (b) { b.disabled = false; });
      });

      xhr.open('POST', form.action);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.send(data);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initUpload);
  } else {
    initUpload();
  }
})();
