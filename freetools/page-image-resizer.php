<?php
/**
 * Template Name: Image Resizer
 * Template Post Type: page
 *
 * A fully client-side image resizing tool with draggable resize handles,
 * locked/free dimensions, and optional background fill for expanded canvases.
 * Install: Upload this file to your active theme folder (e.g. wp-content/themes/your-theme/).
 * Usage:   In WordPress admin, edit any Page → set "Page Attributes > Template" to "Image Resizer".
 *
 * @package Webzinger
 */

get_header(); ?>

<style>
.wz-resizer *,
.wz-resizer *::before,
.wz-resizer *::after { box-sizing: border-box; }

.wz-resizer {
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
  font-family: 'Poppins', inherit;
  background: var(--wz-light);
  color: var(--wz-text);
  padding-bottom: 3rem;
}

.wz-hero {
  background: linear-gradient(135deg, var(--wz-navy) 0%, var(--wz-navy2) 100%);
  color: #fff; padding: 3.5rem 2rem; text-align: center;
  position: relative; overflow: hidden;
}
.wz-hero::before {
  content: ''; position: absolute; top: -70px; right: -70px;
  width: 320px; height: 320px; border-radius: 50%;
  background: rgba(58,111,232,0.15); pointer-events: none;
}
.wz-hero::after {
  content: ''; position: absolute; bottom: -80px; left: -50px;
  width: 260px; height: 260px; border-radius: 50%;
  background: rgba(58,111,232,0.10); pointer-events: none;
}
.wz-hero-eyebrow { font-size:.72rem; font-weight:600; letter-spacing:.15em; text-transform:uppercase; color:#8da8f5; margin:0 0 .75rem; }
.wz-hero h1 { font-size:clamp(1.8rem,4vw,2.8rem); font-weight:700; line-height:1.2; margin:0 0 .75rem; color:#fff; }
.wz-hero h1 span { color:#7fb3ff; }
.wz-hero p { font-size:.95rem; font-weight:300; color:rgba(255,255,255,0.7); max-width:500px; margin:0 auto; }

.wz-wrap { max-width:1060px; margin:0 auto; padding:2.5rem 1.5rem; }

.wz-dropzone {
  background:var(--wz-white); border:2px dashed var(--wz-blue-md);
  border-radius:var(--wz-radius); padding:3.5rem 2rem; text-align:center;
  cursor:pointer; transition:border-color .2s,background .2s,box-shadow .2s; box-shadow:var(--wz-shadow);
}
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

.wz-controls-row { display:none; margin-top:1.5rem; gap:1.25rem; grid-template-columns:1fr 1fr; }
.wz-controls-row.visible { display:grid; }
.wz-card { background:var(--wz-white); border:1px solid var(--wz-border); border-radius:var(--wz-radius); padding:1.5rem; box-shadow:var(--wz-shadow); }
.wz-card-title { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--wz-muted); margin:0 0 1rem; }

.wz-dim-grid { display:grid; grid-template-columns:1fr auto 1fr; gap:.5rem; align-items:end; }
.wz-dim-group { display:flex; flex-direction:column; gap:4px; }
.wz-dim-label { font-size:.72rem; font-weight:600; color:var(--wz-muted); text-transform:uppercase; letter-spacing:.08em; }
.wz-dim-input {
  font-family:inherit; font-size:.9rem; font-weight:600; color:var(--wz-navy);
  border:1.5px solid var(--wz-border); border-radius:6px; padding:9px 12px;
  width:100%; outline:none; transition:border-color .2s; background:var(--wz-light);
}
.wz-dim-input:focus { border-color:var(--wz-blue); background:var(--wz-white); }
.wz-link-icon { display:flex; align-items:center; justify-content:center; padding-bottom:2px; }
.wz-link-icon svg { width:20px; height:20px; color:var(--wz-blue-md); transition:color .2s; }
.wz-link-icon.locked svg { color:var(--wz-blue); }

.wz-toggle-row { display:flex; align-items:center; gap:10px; margin-top:1rem; cursor:pointer; user-select:none; }
.wz-toggle-row input[type="checkbox"] { display:none; }
.wz-toggle-track { width:40px; height:22px; background:var(--wz-border); border-radius:11px; position:relative; transition:background .2s; flex-shrink:0; }
.wz-toggle-row input:checked + .wz-toggle-track { background:var(--wz-blue); }
.wz-toggle-thumb { position:absolute; top:3px; left:3px; width:16px; height:16px; background:#fff; border-radius:50%; box-shadow:0 1px 4px rgba(0,0,0,.18); transition:left .2s; }
.wz-toggle-row input:checked ~ .wz-toggle-track .wz-toggle-thumb { left:21px; }
.wz-toggle-label { font-size:.85rem; font-weight:500; color:var(--wz-text); }
.wz-toggle-sub { font-size:.75rem; color:var(--wz-muted); margin-top:2px; }

/* Fill section – shown only when canvas will expand */
.wz-fill-section { display:none; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--wz-border); }
.wz-fill-section.visible { display:block; }
.wz-fill-label { font-size:.78rem; font-weight:600; color:var(--wz-muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:.5rem; }
.wz-fill-row { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }
.wz-color-swatch {
  width:36px; height:36px; border-radius:6px; border:2px solid var(--wz-border);
  cursor:pointer; overflow:hidden; position:relative; flex-shrink:0;
}
.wz-color-swatch input[type="color"] { position:absolute; inset:-4px; width:calc(100% + 8px); height:calc(100% + 8px); opacity:0; cursor:pointer; }
.wz-color-hex {
  font-family:monospace; font-size:.82rem; font-weight:600; color:var(--wz-navy);
  border:1.5px solid var(--wz-border); border-radius:6px; padding:7px 10px;
  width:100px; outline:none; background:var(--wz-light);
}
.wz-color-hex:focus { border-color:var(--wz-blue); background:var(--wz-white); }
.wz-fill-presets { display:flex; gap:6px; flex-wrap:wrap; }
.wz-fill-preset {
  width:28px; height:28px; border-radius:6px; border:2px solid var(--wz-border);
  cursor:pointer; transition:border-color .15s; flex-shrink:0;
}
.wz-fill-preset:hover { border-color:var(--wz-blue); }
.wz-fill-preset.active { border-color:var(--wz-blue); box-shadow:0 0 0 2px rgba(58,111,232,.35); }

/* Resize preview */
.wz-preview-area { display:none; margin-top:1.5rem; }
.wz-preview-area.visible { display:block; }
.wz-section-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.12em; color:var(--wz-blue); margin:0 0 .6rem; }
.wz-preview-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.wz-preview-card { background:var(--wz-white); border:1px solid var(--wz-border); border-radius:var(--wz-radius); overflow:hidden; box-shadow:var(--wz-shadow); }
.wz-preview-hd { padding:11px 15px; border-bottom:1px solid var(--wz-border); display:flex; align-items:center; justify-content:space-between; background:var(--wz-light); }
.wz-preview-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--wz-muted); }
.wz-preview-size { font-size:.8rem; font-weight:600; color:var(--wz-navy); }
.wz-img-wrap { height:200px; display:flex; align-items:center; justify-content:center; background:#edf0fb; overflow:hidden; }
.wz-img-wrap img { max-width:100%; max-height:100%; object-fit:contain; }

/* Resize handle overlay */
.wz-resize-container { position:relative; display:inline-block; line-height:0; }
.wz-resize-handle {
  position:absolute; bottom:0; right:0; width:20px; height:20px;
  background:var(--wz-blue); border-radius:3px 0 var(--wz-radius) 0;
  cursor:se-resize; display:flex; align-items:center; justify-content:center;
}
.wz-resize-handle svg { width:10px; height:10px; color:#fff; }

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

.wz-expand-notice { display:none; font-size:.78rem; color:var(--wz-blue); font-weight:600; margin-top:.5rem; padding:6px 10px; background:var(--wz-blue-lt); border-radius:6px; }
.wz-expand-notice.visible { display:block; }

@media (max-width:660px) {
  .wz-controls-row,.wz-preview-grid { grid-template-columns:1fr; }
  .wz-stat-div { display:none; }
  .wz-hero { padding:2.5rem 1.25rem; }
}
</style>

<div class="wz-resizer">

  <div class="wz-hero">
    <p class="wz-hero-eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> &mdash; Free Tools</p>
    <h1><?php the_title(); ?></h1>
    <p><?php
      $desc = get_the_excerpt();
      echo $desc ? esc_html( $desc ) : 'Resize images to any dimensions &mdash; lock proportions or resize freely with optional background fill. 100% in-browser.';
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
          <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
        </svg>
      </div>
      <p class="wz-drop-title">Drag &amp; Drop your image here</p>
      <p class="wz-drop-sub">or <a onclick="event.stopPropagation();document.getElementById('wz-file').click()">browse to upload</a></p>
      <div class="wz-drop-formats">
        <span class="wz-pill">JPG</span><span class="wz-pill">PNG</span>
        <span class="wz-pill">WebP</span><span class="wz-pill">GIF</span>
      </div>
      <input type="file" id="wz-file" accept="image/*" style="display:none">
    </div>

    <div class="wz-privacy">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
      </svg>
      Your images are processed entirely in your browser. Nothing is uploaded to any server &mdash; completely private.
    </div>

    <!-- Controls -->
    <div class="wz-controls-row" id="wz-controls">
      <!-- Dimensions -->
      <div class="wz-card">
        <p class="wz-card-title">Output Dimensions</p>
        <div class="wz-dim-grid">
          <div class="wz-dim-group">
            <span class="wz-dim-label">Width (px)</span>
            <input type="number" class="wz-dim-input" id="wz-w" min="1" placeholder="px">
          </div>
          <div class="wz-link-icon" id="wz-link-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
              <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
          </div>
          <div class="wz-dim-group">
            <span class="wz-dim-label">Height (px)</span>
            <input type="number" class="wz-dim-input" id="wz-h" min="1" placeholder="px">
          </div>
        </div>
        <label class="wz-toggle-row" for="wz-lock-ratio">
          <input type="checkbox" id="wz-lock-ratio" checked>
          <span class="wz-toggle-track"><span class="wz-toggle-thumb"></span></span>
          <div>
            <div class="wz-toggle-label">Lock aspect ratio</div>
            <div class="wz-toggle-sub">Width and height scale proportionally</div>
          </div>
        </label>
        <div class="wz-expand-notice" id="wz-expand-notice">
          ↕ Output is larger than original &mdash; empty space will be filled with the selected background colour.
        </div>

        <!-- Fill options -->
        <div class="wz-fill-section" id="wz-fill-section">
          <div class="wz-fill-label">Background Fill Colour</div>
          <div class="wz-fill-row">
            <div class="wz-color-swatch" id="wz-swatch">
              <input type="color" id="wz-color-picker" value="#ffffff">
            </div>
            <input type="text" class="wz-color-hex" id="wz-color-hex" value="#ffffff" maxlength="7" spellcheck="false">
            <div class="wz-fill-presets">
              <div class="wz-fill-preset active" style="background:#ffffff" data-color="#ffffff" title="White"></div>
              <div class="wz-fill-preset" style="background:#000000" data-color="#000000" title="Black"></div>
              <div class="wz-fill-preset" style="background:transparent;background-image:linear-gradient(45deg,#ccc 25%,transparent 25%,transparent 75%,#ccc 75%),linear-gradient(45deg,#ccc 25%,transparent 25%,transparent 75%,#ccc 75%);background-size:8px 8px;background-position:0 0,4px 4px" data-color="transparent" title="Transparent"></div>
              <div class="wz-fill-preset" style="background:#3a6fe8" data-color="#3a6fe8" title="Blue"></div>
              <div class="wz-fill-preset" style="background:#1a1f4b" data-color="#1a1f4b" title="Navy"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick presets -->
      <div class="wz-card">
        <p class="wz-card-title">Size Presets</p>
        <div style="display:flex;flex-direction:column;gap:.5rem;">
          <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--wz-muted);margin-bottom:.25rem;">Social Media</div>
          <div style="display:flex;gap:.5rem;flex-wrap:wrap;" id="wz-presets"></div>
        </div>
      </div>
    </div>

    <!-- Preview -->
    <div class="wz-preview-area" id="wz-preview-area">
      <p class="wz-section-label">Preview</p>
      <div class="wz-preview-grid">
        <div class="wz-preview-card">
          <div class="wz-preview-hd">
            <span class="wz-preview-label">Original</span>
            <span class="wz-preview-size" id="wz-orig-dims">—</span>
          </div>
          <div class="wz-img-wrap"><img id="wz-orig-img" src="" alt="Original"></div>
        </div>
        <div class="wz-preview-card">
          <div class="wz-preview-hd">
            <span class="wz-preview-label">Resized</span>
            <span class="wz-preview-size" id="wz-resized-dims">—</span>
          </div>
          <div class="wz-img-wrap"><img id="wz-resized-img" src="" alt="Resized"></div>
        </div>
      </div>
    </div>

    <div class="wz-stats" id="wz-stats">
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-orig">—</span><span class="wz-stat-label">Original</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val accent" id="wz-s-new">—</span><span class="wz-stat-label">New Size</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-scale">—</span><span class="wz-stat-label">Scale</span></div>
    </div>

    <div class="wz-actions" id="wz-actions">
      <button class="wz-btn wz-btn-primary" id="wz-download">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Download Resized Image
      </button>
      <button class="wz-btn wz-btn-outline" id="wz-reset">Resize Another Image</button>
    </div>

  </div>
</div>

<canvas id="wz-canvas" style="display:none"></canvas>

<script>
(function(){
  'use strict';

  var presets = [
    { label:'FB Cover',  w:820,  h:312  },
    { label:'Instagram', w:1080, h:1080 },
    { label:'Twitter',   w:1200, h:675  },
    { label:'LinkedIn',  w:1200, h:627  },
    { label:'YouTube',   w:1280, h:720  },
    { label:'TikTok',    w:1080, h:1920 },
  ];

  var dropzone    = document.getElementById('wz-dropzone');
  var fileInput   = document.getElementById('wz-file');
  var controlsEl  = document.getElementById('wz-controls');
  var previewArea = document.getElementById('wz-preview-area');
  var statsEl     = document.getElementById('wz-stats');
  var actionsEl   = document.getElementById('wz-actions');
  var canvas      = document.getElementById('wz-canvas');
  var ctx         = canvas.getContext('2d');
  var inputW      = document.getElementById('wz-w');
  var inputH      = document.getElementById('wz-h');
  var lockCb      = document.getElementById('wz-lock-ratio');
  var linkIcon    = document.getElementById('wz-link-icon');
  var fillSection = document.getElementById('wz-fill-section');
  var expandNote  = document.getElementById('wz-expand-notice');
  var colorPicker = document.getElementById('wz-color-picker');
  var colorHex    = document.getElementById('wz-color-hex');
  var fillPresets = document.querySelectorAll('.wz-fill-preset');
  var presetsEl   = document.getElementById('wz-presets');

  var origFile = null;
  var imgObj   = new Image();
  var natW = 0, natH = 0;
  var fillColor = '#ffffff';
  var debounce = null;

  function show(el){ el.classList.add('visible'); }
  function hide(el){ el.classList.remove('visible'); }

  // Build preset buttons
  presets.forEach(function(p){
    var btn = document.createElement('button');
    btn.className = 'wz-preset-btn';
    btn.style.cssText = 'font-family:inherit;font-size:.75rem;font-weight:600;padding:6px 12px;border:1.5px solid var(--wz-border);background:var(--wz-white);color:var(--wz-muted);cursor:pointer;border-radius:6px;transition:all .15s;';
    btn.textContent = p.label + ' ' + p.w + '\u00d7' + p.h;
    btn.addEventListener('mouseover', function(){ this.style.borderColor='var(--wz-blue)'; this.style.color='var(--wz-blue)'; this.style.background='var(--wz-blue-lt)'; });
    btn.addEventListener('mouseout', function(){ this.style.borderColor='var(--wz-border)'; this.style.color='var(--wz-muted)'; this.style.background='var(--wz-white)'; });
    btn.addEventListener('click', function(){
      lockCb.checked = false;
      updateLockUI();
      inputW.value = p.w;
      inputH.value = p.h;
      triggerPreview();
    });
    presetsEl.appendChild(btn);
  });

  function updateLockUI(){
    linkIcon.classList.toggle('locked', lockCb.checked);
  }
  lockCb.addEventListener('change', updateLockUI);
  updateLockUI();

  // Drag & drop
  ['dragover','dragenter'].forEach(function(e){
    dropzone.addEventListener(e, function(ev){ ev.preventDefault(); dropzone.classList.add('drag-over'); });
  });
  ['dragleave','drop'].forEach(function(e){
    dropzone.addEventListener(e, function(ev){ ev.preventDefault(); dropzone.classList.remove('drag-over'); });
  });
  dropzone.addEventListener('drop', function(ev){
    var f = ev.dataTransfer.files[0];
    if (f && f.type.startsWith('image/')) loadFile(f);
  });
  fileInput.addEventListener('change', function(){
    if (fileInput.files[0]) loadFile(fileInput.files[0]);
  });

  function loadFile(file){
    origFile = file;
    var reader = new FileReader();
    reader.onload = function(e){
      imgObj.onload = function(){
        natW = imgObj.naturalWidth;
        natH = imgObj.naturalHeight;
        inputW.value = natW;
        inputH.value = natH;
        document.getElementById('wz-orig-img').src = e.target.result;
        document.getElementById('wz-orig-dims').textContent = natW + ' \u00d7 ' + natH;
        show(controlsEl);
        triggerPreview();
      };
      imgObj.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }

  inputW.addEventListener('input', function(){
    if (lockCb.checked && natW && natH){
      inputH.value = Math.round(parseFloat(inputW.value) * natH / natW) || '';
    }
    clearTimeout(debounce); debounce = setTimeout(checkExpand, 200);
    triggerPreview();
  });
  inputH.addEventListener('input', function(){
    if (lockCb.checked && natW && natH){
      inputW.value = Math.round(parseFloat(inputH.value) * natW / natH) || '';
    }
    clearTimeout(debounce); debounce = setTimeout(checkExpand, 200);
    triggerPreview();
  });

  function checkExpand(){
    var w = parseFloat(inputW.value)||0;
    var h = parseFloat(inputH.value)||0;
    var isExpand = !lockCb.checked && (w > natW || h > natH);
    if (isExpand){ show(fillSection); show(expandNote); } else { hide(fillSection); hide(expandNote); }
  }

  // Color
  colorPicker.addEventListener('input', function(){
    fillColor = colorPicker.value;
    colorHex.value = fillColor;
    updateSwatchBg();
    fillPresets.forEach(function(p){ p.classList.remove('active'); });
    triggerPreview();
  });
  colorHex.addEventListener('input', function(){
    var v = colorHex.value;
    if (/^#[0-9a-fA-F]{6}$/.test(v)){ fillColor = v; colorPicker.value = v; updateSwatchBg(); triggerPreview(); }
  });
  fillPresets.forEach(function(p){
    p.addEventListener('click', function(){
      fillPresets.forEach(function(x){ x.classList.remove('active'); });
      p.classList.add('active');
      fillColor = p.dataset.color;
      if (fillColor !== 'transparent'){ colorPicker.value = fillColor; colorHex.value = fillColor; }
      updateSwatchBg();
      triggerPreview();
    });
  });
  function updateSwatchBg(){
    document.getElementById('wz-swatch').style.background = fillColor === 'transparent' ? 'repeating-conic-gradient(#ccc 0% 25%,transparent 0% 50%) 0 0/8px 8px' : fillColor;
  }

  function triggerPreview(){
    if (!imgObj.src) return;
    clearTimeout(debounce);
    debounce = setTimeout(renderPreview, 200);
  }

  function renderPreview(){
    var tw = Math.round(parseFloat(inputW.value)) || natW;
    var th = Math.round(parseFloat(inputH.value)) || natH;
    tw = Math.max(1, Math.min(tw, 8000));
    th = Math.max(1, Math.min(th, 8000));
    canvas.width = tw; canvas.height = th;
    ctx.clearRect(0,0,tw,th);

    if (fillColor === 'transparent'){
      // leave transparent
    } else {
      ctx.fillStyle = fillColor;
      ctx.fillRect(0,0,tw,th);
    }

    // Draw image centered
    var drawW, drawH, ox, oy;
    if (lockCb.checked){
      drawW = tw; drawH = th; ox = 0; oy = 0;
    } else {
      // Fit image inside, fill rest with background
      var scale = Math.min(tw / natW, th / natH);
      drawW = Math.round(natW * scale);
      drawH = Math.round(natH * scale);
      ox = Math.round((tw - drawW) / 2);
      oy = Math.round((th - drawH) / 2);
    }
    ctx.drawImage(imgObj, 0, 0, natW, natH, ox, oy, drawW, drawH);

    canvas.toBlob(function(blob){
      var url = URL.createObjectURL(blob);
      document.getElementById('wz-resized-img').src = url;
      document.getElementById('wz-resized-dims').textContent = tw + ' \u00d7 ' + th;
      document.getElementById('wz-s-orig').textContent  = natW + ' \u00d7 ' + natH;
      document.getElementById('wz-s-new').textContent   = tw + ' \u00d7 ' + th;
      var sc = ((tw / natW) * 100).toFixed(0);
      document.getElementById('wz-s-scale').textContent = sc + '%';
      show(previewArea); show(statsEl); show(actionsEl);
    }, 'image/jpeg', 0.92);
  }

  document.getElementById('wz-download').addEventListener('click', function(){
    if (!imgObj.src) return;
    var tw = Math.round(parseFloat(inputW.value)) || natW;
    var th = Math.round(parseFloat(inputH.value)) || natH;
    canvas.width = tw; canvas.height = th;
    ctx.clearRect(0,0,tw,th);
    if (fillColor !== 'transparent'){ ctx.fillStyle = fillColor; ctx.fillRect(0,0,tw,th); }
    var drawW, drawH, ox, oy;
    if (lockCb.checked){ drawW=tw; drawH=th; ox=0; oy=0; }
    else {
      var scale = Math.min(tw/natW, th/natH);
      drawW=Math.round(natW*scale); drawH=Math.round(natH*scale);
      ox=Math.round((tw-drawW)/2); oy=Math.round((th-drawH)/2);
    }
    ctx.drawImage(imgObj,0,0,natW,natH,ox,oy,drawW,drawH);
    var mime = (origFile.type==='image/png'||fillColor==='transparent') ? 'image/png' : 'image/jpeg';
    var ext  = (mime==='image/png') ? 'png' : 'jpg';
    canvas.toBlob(function(blob){
      var name = (origFile.name.replace(/\.[^.]+$/,'')||'image')+'_resized.'+ext;
      var url  = URL.createObjectURL(blob);
      var a    = document.createElement('a');
      a.href=url; a.download=name; a.click();
      setTimeout(function(){ URL.revokeObjectURL(url); },1000);
    }, mime, 0.95);
  });

  document.getElementById('wz-reset').addEventListener('click', function(){
    origFile=null;
    [controlsEl,previewArea,statsEl,actionsEl,fillSection,expandNote].forEach(hide);
    fileInput.value='';
  });

}());
</script>

<?php get_footer(); ?>
