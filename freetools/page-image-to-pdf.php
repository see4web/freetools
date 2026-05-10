<?php
/**
 * Template Name: Image to PDF Converter
 * Template Post Type: page
 *
 * A fully client-side tool to convert one or multiple images into a single PDF document.
 * Install: Upload this file to your active theme folder (e.g. wp-content/themes/your-theme/).
 * Usage:   In WordPress admin, edit any Page → set "Page Attributes > Template" to "Image to PDF Converter".
 *
 * Requires: jsPDF (loaded from cdnjs)
 *
 * @package Webzinger
 */

get_header(); ?>

<style>
.wz-img2pdf *,
.wz-img2pdf *::before,
.wz-img2pdf *::after { box-sizing: border-box; }

.wz-img2pdf {
  --wz-navy:    #1a1f4b;
  --wz-navy2:   #252b6b;
  --wz-blue:    #3a6fe8;
  --wz-blue-lt: #eef2fd;
  --wz-blue-md: #c5d3f8;
  --wz-white:   #ffffff;
  --wz-light:   #f5f7ff;
  --wz-border:  #dde3f5;
  --wz-text:    #1a1f4b;
  --wz-muted:   #6b72a3;
  --wz-radius:  10px;
  --wz-shadow:  0 4px 24px rgba(58,111,232,0.10);
  --wz-red:     #e84040;
  font-family: 'Poppins', inherit;
  background: var(--wz-light);
  color: var(--wz-text);
  padding-bottom: 3rem;
}

.wz-hero { background:linear-gradient(135deg,var(--wz-navy) 0%,var(--wz-navy2) 100%); color:#fff; padding:3.5rem 2rem; text-align:center; position:relative; overflow:hidden; }
.wz-hero::before { content:''; position:absolute; top:-70px; right:-70px; width:320px; height:320px; border-radius:50%; background:rgba(58,111,232,.15); pointer-events:none; }
.wz-hero::after  { content:''; position:absolute; bottom:-80px; left:-50px; width:260px; height:260px; border-radius:50%; background:rgba(58,111,232,.10); pointer-events:none; }
.wz-hero-eyebrow { font-size:.72rem; font-weight:600; letter-spacing:.15em; text-transform:uppercase; color:#8da8f5; margin:0 0 .75rem; }
.wz-hero h1 { font-size:clamp(1.8rem,4vw,2.8rem); font-weight:700; line-height:1.2; margin:0 0 .75rem; color:#fff; }
.wz-hero h1 span { color:#7fb3ff; }
.wz-hero p { font-size:.95rem; font-weight:300; color:rgba(255,255,255,.7); max-width:500px; margin:0 auto; }

.wz-wrap { max-width:1060px; margin:0 auto; padding:2.5rem 1.5rem; }

.wz-dropzone { background:var(--wz-white); border:2px dashed var(--wz-blue-md); border-radius:var(--wz-radius); padding:3.5rem 2rem; text-align:center; cursor:pointer; transition:border-color .2s,background .2s,box-shadow .2s; box-shadow:var(--wz-shadow); }
.wz-dropzone:hover,.wz-dropzone.drag-over { border-color:var(--wz-blue); background:var(--wz-blue-lt); box-shadow:0 6px 30px rgba(58,111,232,.15); }
.wz-drop-icon { width:72px; height:72px; background:var(--wz-blue-lt); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; }
.wz-drop-icon svg { width:30px; height:30px; color:var(--wz-blue); }
.wz-drop-title { font-size:1.05rem; font-weight:600; color:var(--wz-navy); margin:0 0 .4rem; }
.wz-drop-sub { font-size:.85rem; color:var(--wz-muted); margin:0; }
.wz-drop-sub a { color:var(--wz-blue); cursor:pointer; font-weight:500; text-decoration:none; }
.wz-drop-sub a:hover { text-decoration:underline; }
.wz-drop-formats { display:flex; gap:8px; justify-content:center; margin-top:1.25rem; flex-wrap:wrap; }
.wz-pill { background:var(--wz-blue-lt); color:var(--wz-blue); font-size:.68rem; font-weight:600; padding:4px 12px; border-radius:20px; letter-spacing:.05em; }

.wz-privacy { display:flex; align-items:center; gap:8px; font-size:.78rem; color:var(--wz-muted); margin-top:1rem; padding:10px 14px; background:var(--wz-blue-lt); border-radius:6px; border-left:3px solid var(--wz-blue); }
.wz-privacy svg { width:14px; height:14px; color:var(--wz-blue); flex-shrink:0; }

/* Image queue */
.wz-queue-section { display:none; margin-top:1.5rem; }
.wz-queue-section.visible { display:block; }
.wz-queue-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.75rem; flex-wrap:wrap; gap:.5rem; }
.wz-section-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.12em; color:var(--wz-blue); margin:0; }
.wz-queue-actions { display:flex; gap:.5rem; }

.wz-queue-list { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:1rem; }
.wz-queue-item { background:var(--wz-white); border:1px solid var(--wz-border); border-radius:var(--wz-radius); overflow:hidden; box-shadow:var(--wz-shadow); position:relative; cursor:grab; user-select:none; transition:box-shadow .15s; }
.wz-queue-item:active { cursor:grabbing; }
.wz-queue-item.dragging { opacity:.4; box-shadow:0 8px 32px rgba(58,111,232,.25); }
.wz-queue-item.drag-over-item { border-color:var(--wz-blue); box-shadow:0 0 0 2px var(--wz-blue); }
.wz-queue-thumb { width:100%; height:100px; object-fit:cover; display:block; pointer-events:none; }
.wz-queue-name { padding:6px 10px 4px; font-size:.7rem; font-weight:600; color:var(--wz-navy); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.wz-queue-size { padding:0 10px 8px; font-size:.65rem; color:var(--wz-muted); }
.wz-queue-remove { position:absolute; top:6px; right:6px; width:22px; height:22px; background:rgba(0,0,0,.55); border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; border:none; transition:background .15s; }
.wz-queue-remove:hover { background:var(--wz-red); }
.wz-queue-remove svg { width:10px; height:10px; color:#fff; }
.wz-queue-num { position:absolute; top:6px; left:6px; background:var(--wz-blue); color:#fff; font-size:.6rem; font-weight:700; border-radius:4px; padding:2px 6px; }

/* Add more button inside queue */
.wz-queue-add { background:var(--wz-blue-lt); border:2px dashed var(--wz-blue-md); border-radius:var(--wz-radius); height:168px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.5rem; cursor:pointer; transition:border-color .2s,background .2s; }
.wz-queue-add:hover { border-color:var(--wz-blue); background:var(--wz-blue-lt); }
.wz-queue-add svg { width:24px; height:24px; color:var(--wz-blue); }
.wz-queue-add span { font-size:.78rem; font-weight:600; color:var(--wz-blue); }

/* Settings */
.wz-settings { display:none; margin-top:1.5rem; gap:1.25rem; grid-template-columns:1fr 1fr; }
.wz-settings.visible { display:grid; }
.wz-card { background:var(--wz-white); border:1px solid var(--wz-border); border-radius:var(--wz-radius); padding:1.5rem; box-shadow:var(--wz-shadow); }
.wz-card-title { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--wz-muted); margin:0 0 1rem; }
.wz-radio-group { display:flex; flex-direction:column; gap:.6rem; }
.wz-radio-row { display:flex; align-items:center; gap:.75rem; cursor:pointer; }
.wz-radio-row input[type="radio"] { display:none; }
.wz-radio-dot { width:18px; height:18px; border:2px solid var(--wz-border); border-radius:50%; position:relative; flex-shrink:0; transition:border-color .15s; }
.wz-radio-row input:checked + .wz-radio-dot { border-color:var(--wz-blue); }
.wz-radio-row input:checked + .wz-radio-dot::after { content:''; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:8px; height:8px; background:var(--wz-blue); border-radius:50%; }
.wz-radio-text { font-size:.85rem; font-weight:500; color:var(--wz-text); }
.wz-radio-sub { font-size:.72rem; color:var(--wz-muted); }

/* Processing */
.wz-processing { display:none; align-items:center; gap:.75rem; font-size:.85rem; color:var(--wz-muted); margin-top:1.25rem; padding:1rem 1.25rem; background:var(--wz-white); border-radius:var(--wz-radius); border:1px solid var(--wz-border); }
.wz-processing.visible { display:flex; }
.wz-spinner { width:18px; height:18px; border:2.5px solid var(--wz-blue-md); border-top-color:var(--wz-blue); border-radius:50%; animation:wz-spin .7s linear infinite; flex-shrink:0; }
@keyframes wz-spin { to { transform:rotate(360deg); } }

.wz-stats { display:none; margin-top:1.25rem; background:var(--wz-navy); border-radius:var(--wz-radius); padding:1.5rem 2rem; align-items:center; justify-content:space-around; gap:1rem; flex-wrap:wrap; }
.wz-stats.visible { display:flex; }
.wz-stat { text-align:center; }
.wz-stat-val { font-size:1.5rem; font-weight:700; color:#fff; display:block; line-height:1; }
.wz-stat-val.accent { color:#7fb3ff; }
.wz-stat-label { font-size:.68rem; color:rgba(255,255,255,.5); margin-top:4px; display:block; text-transform:uppercase; letter-spacing:.08em; }
.wz-stat-div { width:1px; height:42px; background:rgba(255,255,255,.12); }

.wz-actions { display:none; margin-top:1.25rem; gap:.875rem; flex-wrap:wrap; }
.wz-actions.visible { display:flex; }
.wz-btn { font-family:inherit; font-size:.85rem; font-weight:600; padding:13px 28px; border:none; border-radius:6px; cursor:pointer; transition:all .18s; display:inline-flex; align-items:center; gap:7px; text-decoration:none; }
.wz-btn-primary { background:var(--wz-blue); color:#fff; box-shadow:0 4px 16px rgba(58,111,232,.35); }
.wz-btn-primary:hover { background:#2d5cd4; box-shadow:0 6px 22px rgba(58,111,232,.45); transform:translateY(-1px); color:#fff; }
.wz-btn-outline { background:var(--wz-white); color:var(--wz-navy); border:1.5px solid var(--wz-border); }
.wz-btn-outline:hover { border-color:var(--wz-blue); color:var(--wz-blue); }

.wz-convert-wrap { margin-top:1.5rem; }

@media (max-width:660px) {
  .wz-settings { grid-template-columns:1fr; }
  .wz-stat-div { display:none; }
  .wz-hero { padding:2.5rem 1.25rem; }
  .wz-queue-list { grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); }
}
</style>

<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="wz-img2pdf">

  <div class="wz-hero">
    <p class="wz-hero-eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> &mdash; Free Tools</p>
    <h1><?php the_title(); ?></h1>
    <p><?php
      $desc = get_the_excerpt();
      echo $desc ? esc_html( $desc ) : 'Convert one or multiple images into a single PDF document &mdash; drag to reorder, configure page size. 100% in-browser.';
    ?></p>
  </div>

  <div class="wz-wrap">

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <?php if ( get_the_content() ) : ?>
        <div class="entry-content" style="margin-bottom:1.5rem;"><?php the_content(); ?></div>
      <?php endif; ?>
    <?php endwhile; endif; ?>

    <div class="wz-dropzone" id="wz-dropzone" onclick="document.getElementById('wz-file').click()">
      <div class="wz-drop-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
        </svg>
      </div>
      <p class="wz-drop-title">Drag &amp; Drop images here</p>
      <p class="wz-drop-sub">or <a onclick="event.stopPropagation();document.getElementById('wz-file').click()">browse to select</a> &mdash; multiple files supported</p>
      <div class="wz-drop-formats">
        <span class="wz-pill">JPG</span><span class="wz-pill">PNG</span>
        <span class="wz-pill">WebP</span><span class="wz-pill">GIF</span>
        <span class="wz-pill">BMP</span><span class="wz-pill">TIFF</span>
      </div>
      <input type="file" id="wz-file" accept="image/*" multiple style="display:none">
    </div>

    <div class="wz-privacy">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
      </svg>
      Your images are processed entirely in your browser. Nothing is uploaded to any server &mdash; completely private.
    </div>

    <!-- Image queue -->
    <div class="wz-queue-section" id="wz-queue-section">
      <div class="wz-queue-header">
        <p class="wz-section-label">Images &mdash; drag to reorder pages</p>
        <div class="wz-queue-actions">
          <button class="wz-btn wz-btn-outline" id="wz-add-more" style="padding:8px 16px;font-size:.78rem;">+ Add More</button>
          <button class="wz-btn wz-btn-outline" id="wz-clear-all" style="padding:8px 16px;font-size:.78rem;color:var(--wz-red);border-color:var(--wz-red);">Clear All</button>
        </div>
      </div>
      <div class="wz-queue-list" id="wz-queue-list"></div>
    </div>

    <!-- PDF Settings -->
    <div class="wz-settings" id="wz-settings">
      <div class="wz-card">
        <p class="wz-card-title">Page Size</p>
        <div class="wz-radio-group">
          <label class="wz-radio-row">
            <input type="radio" name="wz-page-size" value="fit" checked>
            <span class="wz-radio-dot"></span>
            <div><div class="wz-radio-text">Fit to image</div><div class="wz-radio-sub">Each page matches its image dimensions</div></div>
          </label>
          <label class="wz-radio-row">
            <input type="radio" name="wz-page-size" value="a4">
            <span class="wz-radio-dot"></span>
            <div><div class="wz-radio-text">A4</div><div class="wz-radio-sub">210 &times; 297 mm, image scaled to fit</div></div>
          </label>
          <label class="wz-radio-row">
            <input type="radio" name="wz-page-size" value="letter">
            <span class="wz-radio-dot"></span>
            <div><div class="wz-radio-text">US Letter</div><div class="wz-radio-sub">8.5 &times; 11 in, image scaled to fit</div></div>
          </label>
        </div>
      </div>
      <div class="wz-card">
        <p class="wz-card-title">Page Orientation</p>
        <div class="wz-radio-group" id="wz-orient-group">
          <label class="wz-radio-row">
            <input type="radio" name="wz-orient" value="auto" checked>
            <span class="wz-radio-dot"></span>
            <div><div class="wz-radio-text">Auto per image</div><div class="wz-radio-sub">Portrait for tall, landscape for wide</div></div>
          </label>
          <label class="wz-radio-row">
            <input type="radio" name="wz-orient" value="portrait">
            <span class="wz-radio-dot"></span>
            <div><div class="wz-radio-text">Always Portrait</div></div>
          </label>
          <label class="wz-radio-row">
            <input type="radio" name="wz-orient" value="landscape">
            <span class="wz-radio-dot"></span>
            <div><div class="wz-radio-text">Always Landscape</div></div>
          </label>
        </div>
        <p class="wz-card-title" style="margin-top:1.25rem;">Image Margins</p>
        <div class="wz-radio-group">
          <label class="wz-radio-row">
            <input type="radio" name="wz-margin" value="0" checked>
            <span class="wz-radio-dot"></span>
            <div><div class="wz-radio-text">No margin</div></div>
          </label>
          <label class="wz-radio-row">
            <input type="radio" name="wz-margin" value="10">
            <span class="wz-radio-dot"></span>
            <div><div class="wz-radio-text">Small (10 mm)</div></div>
          </label>
          <label class="wz-radio-row">
            <input type="radio" name="wz-margin" value="20">
            <span class="wz-radio-dot"></span>
            <div><div class="wz-radio-text">Normal (20 mm)</div></div>
          </label>
        </div>
      </div>
    </div>

    <!-- Convert button area -->
    <div class="wz-convert-wrap" id="wz-convert-wrap" style="display:none">
      <button class="wz-btn wz-btn-primary" id="wz-convert" style="padding:15px 36px;font-size:.95rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
        </svg>
        Convert to PDF
      </button>
    </div>

    <div class="wz-processing" id="wz-processing">
      <div class="wz-spinner"></div>
      <span id="wz-proc-text">Building your PDF, please wait&hellip;</span>
    </div>

    <div class="wz-stats" id="wz-stats">
      <div class="wz-stat"><span class="wz-stat-val accent" id="wz-s-pages">—</span><span class="wz-stat-label">Pages</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-images">—</span><span class="wz-stat-label">Images</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-size">—</span><span class="wz-stat-label">PDF Size</span></div>
    </div>

    <div class="wz-actions" id="wz-actions">
      <button class="wz-btn wz-btn-primary" id="wz-download">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Download PDF
      </button>
      <button class="wz-btn wz-btn-outline" id="wz-reset">Start Over</button>
    </div>

  </div>
</div>

<script>
(function(){
  'use strict';

  var dropzone    = document.getElementById('wz-dropzone');
  var fileInput   = document.getElementById('wz-file');
  var queueSec    = document.getElementById('wz-queue-section');
  var queueList   = document.getElementById('wz-queue-list');
  var settingsEl  = document.getElementById('wz-settings');
  var convertWrap = document.getElementById('wz-convert-wrap');
  var processingEl= document.getElementById('wz-processing');
  var statsEl     = document.getElementById('wz-stats');
  var actionsEl   = document.getElementById('wz-actions');

  var imageQueue  = []; // { file, dataUrl, name, size, w, h }
  var pdfBlob     = null;
  var dragSrcIdx  = -1;

  function show(el){ el.classList.add('visible'); }
  function hide(el){ el.classList.remove('visible'); }
  function fmtBytes(b){ if(b<1024) return b+' B'; if(b<1048576) return (b/1024).toFixed(1)+' KB'; return (b/1048576).toFixed(2)+' MB'; }

  // Drag & drop files
  ['dragover','dragenter'].forEach(function(e){
    dropzone.addEventListener(e, function(ev){ ev.preventDefault(); dropzone.classList.add('drag-over'); });
  });
  ['dragleave','drop'].forEach(function(e){
    dropzone.addEventListener(e, function(ev){ ev.preventDefault(); dropzone.classList.remove('drag-over'); });
  });
  dropzone.addEventListener('drop', function(ev){
    var files = ev.dataTransfer.files;
    handleFiles(files);
  });
  fileInput.addEventListener('change', function(){ handleFiles(fileInput.files); fileInput.value=''; });

  document.getElementById('wz-add-more').addEventListener('click', function(){ fileInput.click(); });
  document.getElementById('wz-clear-all').addEventListener('click', function(){
    imageQueue = [];
    renderQueue();
    pdfBlob = null;
    [queueSec, settingsEl, convertWrap, processingEl, statsEl, actionsEl].forEach(hide);
  });

  function handleFiles(files){
    var arr = Array.prototype.slice.call(files).filter(function(f){ return f.type.startsWith('image/'); });
    if (!arr.length) return;
    var loaded = 0;
    arr.forEach(function(file){
      var reader = new FileReader();
      reader.onload = function(e){
        var img = new Image();
        img.onload = function(){
          imageQueue.push({ file:file, dataUrl:e.target.result, name:file.name, size:file.size, w:img.naturalWidth, h:img.naturalHeight });
          loaded++;
          if (loaded === arr.length){
            renderQueue();
            show(queueSec);
            show(settingsEl);
            convertWrap.style.display = 'block';
            hide(statsEl); hide(actionsEl);
          }
        };
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    });
  }

  function renderQueue(){
    queueList.innerHTML = '';
    imageQueue.forEach(function(item, idx){
      var el = document.createElement('div');
      el.className = 'wz-queue-item';
      el.draggable = true;
      el.dataset.idx = idx;
      el.innerHTML = '<span class="wz-queue-num">'+(idx+1)+'</span>'
        +'<img class="wz-queue-thumb" src="'+item.dataUrl+'" alt="">'
        +'<div class="wz-queue-name">'+escHtml(item.name)+'</div>'
        +'<div class="wz-queue-size">'+item.w+' \u00d7 '+item.h+' &middot; '+fmtBytes(item.size)+'</div>'
        +'<button class="wz-queue-remove" data-idx="'+idx+'" title="Remove">'
          +'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'
        +'</button>';
      // Drag-to-reorder
      el.addEventListener('dragstart', function(){ dragSrcIdx = idx; el.classList.add('dragging'); });
      el.addEventListener('dragend', function(){ el.classList.remove('dragging'); queueList.querySelectorAll('.wz-queue-item').forEach(function(x){ x.classList.remove('drag-over-item'); }); });
      el.addEventListener('dragover', function(ev){ ev.preventDefault(); el.classList.add('drag-over-item'); });
      el.addEventListener('dragleave', function(){ el.classList.remove('drag-over-item'); });
      el.addEventListener('drop', function(ev){
        ev.preventDefault();
        el.classList.remove('drag-over-item');
        var targetIdx = parseInt(el.dataset.idx, 10);
        if (dragSrcIdx !== -1 && dragSrcIdx !== targetIdx){
          var moved = imageQueue.splice(dragSrcIdx, 1)[0];
          imageQueue.splice(targetIdx, 0, moved);
          dragSrcIdx = -1;
          renderQueue();
        }
      });
      // Remove button
      el.querySelector('.wz-queue-remove').addEventListener('click', function(e){
        e.stopPropagation();
        imageQueue.splice(idx, 1);
        renderQueue();
        if (!imageQueue.length){ [queueSec,settingsEl,statsEl,actionsEl].forEach(hide); convertWrap.style.display='none'; }
      });
      queueList.appendChild(el);
    });
    // Add more tile
    var addTile = document.createElement('div');
    addTile.className = 'wz-queue-add';
    addTile.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg><span>Add more</span>';
    addTile.addEventListener('click', function(){ fileInput.click(); });
    queueList.appendChild(addTile);
  }

  function escHtml(s){ var d=document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; }

  // Convert
  document.getElementById('wz-convert').addEventListener('click', buildPdf);

  function getRadio(name){ return document.querySelector('input[name="'+name+'"]:checked').value; }

  function buildPdf(){
    if (!imageQueue.length) return;
    processingEl.style.display='flex'; hide(statsEl); hide(actionsEl);
    document.getElementById('wz-proc-text').textContent = 'Building your PDF (0/'+imageQueue.length+')…';

    setTimeout(function(){
      try {
        var jsPDF = window.jspdf.jsPDF;
        var pageSize  = getRadio('wz-page-size');
        var orient    = getRadio('wz-orient');
        var marginMM  = parseFloat(getRadio('wz-margin'));
        var pdf = null;

        imageQueue.forEach(function(item, idx){
          document.getElementById('wz-proc-text').textContent = 'Adding page '+(idx+1)+' of '+imageQueue.length+'…';
          var iw = item.w, ih = item.h;
          var isLandscape;
          if (orient === 'auto')       isLandscape = iw > ih;
          else if (orient === 'landscape') isLandscape = true;
          else                             isLandscape = false;

          var format, pw, ph;
          if (pageSize === 'a4'){
            format = 'a4';
            pw = isLandscape ? 297 : 210;
            ph = isLandscape ? 210 : 297;
          } else if (pageSize === 'letter'){
            format = 'letter';
            pw = isLandscape ? 279.4 : 215.9;
            ph = isLandscape ? 215.9 : 279.4;
          } else {
            // fit to image: 72dpi px→mm
            pw = iw * 25.4 / 72;
            ph = ih * 25.4 / 72;
            if (isLandscape && pw < ph){ var t=pw; pw=ph; ph=t; }
            format = [pw, ph];
          }

          var o = isLandscape ? 'l' : 'p';
          if (!pdf){
            pdf = new jsPDF({ orientation:o, unit:'mm', format:format });
          } else {
            pdf.addPage(format, o);
          }

          // Draw image within margins
          var drawW = pw - marginMM*2;
          var drawH = ph - marginMM*2;
          var scale = Math.min(drawW/iw, drawH/ih);
          var finalW = iw * scale;
          var finalH = ih * scale;
          var ox = marginMM + (drawW - finalW)/2;
          var oy = marginMM + (drawH - finalH)/2;

          var fmt = item.file.type === 'image/png' ? 'PNG' : 'JPEG';
          pdf.addImage(item.dataUrl, fmt, ox, oy, finalW, finalH);
        });

        var output = pdf.output('blob');
        pdfBlob = output;
        processingEl.style.display='none';
        document.getElementById('wz-s-pages').textContent  = imageQueue.length;
        document.getElementById('wz-s-images').textContent = imageQueue.length;
        document.getElementById('wz-s-size').textContent   = fmtBytes(output.size);
        show(statsEl); show(actionsEl);
      } catch(err){
        processingEl.style.display='none';
        alert('PDF generation failed: '+err.message);
      }
    }, 60);
  }

  document.getElementById('wz-download').addEventListener('click', function(){
    if (!pdfBlob) return;
    var url  = URL.createObjectURL(pdfBlob);
    var name = (imageQueue.length===1 ? imageQueue[0].name.replace(/\.[^.]+$/,'') : 'images') + '.pdf';
    var a    = document.createElement('a');
    a.href=url; a.download=name; a.click();
    setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
  });

  document.getElementById('wz-reset').addEventListener('click', function(){
    imageQueue=[]; pdfBlob=null;
    renderQueue();
    [queueSec,settingsEl,processingEl,statsEl,actionsEl].forEach(hide);
    convertWrap.style.display='none';
    fileInput.value='';
  });

}());
</script>

<?php get_footer(); ?>
