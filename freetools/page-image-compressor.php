<?php
/**
 * Template Name: Image Compressor
 * Template Post Type: page
 *
 * A fully client-side image compression tool.
 * Install: Upload this file to your active theme folder (e.g. wp-content/themes/your-theme/).
 * Usage:   In WordPress admin, edit any Page → set "Page Attributes > Template" to "Image Compressor".
 *
 * @package Webzinger
 */

get_header(); ?>

<style>
/* ── Scoped to .wz-compressor so it doesn't bleed into theme styles ── */
.wz-compressor *,
.wz-compressor *::before,
.wz-compressor *::after {
  box-sizing: border-box;
}

.wz-compressor {
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

/* Hero */
.wz-hero {
  background: linear-gradient(135deg, var(--wz-navy) 0%, var(--wz-navy2) 100%);
  color: #fff;
  padding: 3.5rem 2rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.wz-hero::before {
  content: '';
  position: absolute;
  top: -70px; right: -70px;
  width: 320px; height: 320px;
  border-radius: 50%;
  background: rgba(58,111,232,0.15);
  pointer-events: none;
}
.wz-hero::after {
  content: '';
  position: absolute;
  bottom: -80px; left: -50px;
  width: 260px; height: 260px;
  border-radius: 50%;
  background: rgba(58,111,232,0.10);
  pointer-events: none;
}
.wz-hero-eyebrow {
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: #8da8f5;
  margin: 0 0 0.75rem;
}
.wz-hero h1 {
  font-size: clamp(1.8rem, 4vw, 2.8rem);
  font-weight: 700;
  line-height: 1.2;
  margin: 0 0 0.75rem;
  color: #fff;
}
.wz-hero h1 span { color: #7fb3ff; }
.wz-hero p {
  font-size: 0.95rem;
  font-weight: 300;
  color: rgba(255,255,255,0.7);
  max-width: 500px;
  margin: 0 auto;
}

/* Wrap */
.wz-wrap {
  max-width: 1060px;
  margin: 0 auto;
  padding: 2.5rem 1.5rem;
}

/* Drop zone */
.wz-dropzone {
  background: var(--wz-white);
  border: 2px dashed var(--wz-blue-md);
  border-radius: var(--wz-radius);
  padding: 3.5rem 2rem;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
  box-shadow: var(--wz-shadow);
}
.wz-dropzone:hover,
.wz-dropzone.drag-over {
  border-color: var(--wz-blue);
  background: var(--wz-blue-lt);
  box-shadow: 0 6px 30px rgba(58,111,232,0.15);
}
.wz-drop-icon {
  width: 72px; height: 72px;
  background: var(--wz-blue-lt);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.25rem;
}
.wz-drop-icon svg { width: 30px; height: 30px; color: var(--wz-blue); }
.wz-drop-title {
  font-size: 1.05rem;
  font-weight: 600;
  color: var(--wz-navy);
  margin: 0 0 0.4rem;
}
.wz-drop-sub {
  font-size: 0.85rem;
  color: var(--wz-muted);
  margin: 0;
}
.wz-drop-sub a {
  color: var(--wz-blue);
  cursor: pointer;
  font-weight: 500;
  text-decoration: none;
}
.wz-drop-sub a:hover { text-decoration: underline; }
.wz-drop-formats {
  display: flex;
  gap: 8px;
  justify-content: center;
  margin-top: 1.25rem;
  flex-wrap: wrap;
}
.wz-pill {
  background: var(--wz-blue-lt);
  color: var(--wz-blue);
  font-size: 0.68rem;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 20px;
  letter-spacing: 0.05em;
}

/* Privacy note */
.wz-privacy {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.78rem;
  color: var(--wz-muted);
  margin-top: 1rem;
  padding: 10px 14px;
  background: var(--wz-blue-lt);
  border-radius: 6px;
  border-left: 3px solid var(--wz-blue);
}
.wz-privacy svg { width: 14px; height: 14px; color: var(--wz-blue); flex-shrink: 0; }

/* Controls */
.wz-controls {
  display: none;
  margin-top: 1.5rem;
  gap: 1.25rem;
  grid-template-columns: 1fr 1fr;
}
.wz-controls.visible { display: grid; }
.wz-card {
  background: var(--wz-white);
  border: 1px solid var(--wz-border);
  border-radius: var(--wz-radius);
  padding: 1.5rem;
  box-shadow: var(--wz-shadow);
}
.wz-card-title {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--wz-muted);
  margin: 0 0 1rem;
}
.wz-quality-row {
  display: flex;
  align-items: baseline;
  gap: 0.75rem;
  margin-bottom: 1rem;
}
.wz-quality-val {
  font-size: 2.2rem;
  font-weight: 700;
  color: var(--wz-blue);
  line-height: 1;
}
.wz-quality-desc { font-size: 0.78rem; color: var(--wz-muted); }

/* Range slider */
.wz-compressor input[type="range"] {
  -webkit-appearance: none;
  width: 100%;
  height: 4px;
  background: var(--wz-border);
  outline: none;
  cursor: pointer;
  border-radius: 4px;
  margin: 0;
}
.wz-compressor input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 20px;
  height: 20px;
  background: var(--wz-blue);
  cursor: pointer;
  border-radius: 50%;
  box-shadow: 0 2px 8px rgba(58,111,232,0.35);
  transition: transform 0.15s;
}
.wz-compressor input[type="range"]::-webkit-slider-thumb:hover { transform: scale(1.15); }
.wz-compressor input[type="range"]::-webkit-slider-runnable-track {
  background: linear-gradient(to right, var(--wz-blue) 0%, var(--wz-blue) var(--pct, 80%), var(--wz-border) var(--pct, 80%));
  height: 4px;
  border-radius: 4px;
}

/* Format buttons */
.wz-fmt-btns { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.wz-fmt-btn {
  font-family: inherit;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 9px 20px;
  border: 1.5px solid var(--wz-border);
  background: var(--wz-white);
  color: var(--wz-muted);
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.15s;
}
.wz-fmt-btn:hover { border-color: var(--wz-blue); color: var(--wz-blue); background: var(--wz-blue-lt); }
.wz-fmt-btn.active { border-color: var(--wz-blue); background: var(--wz-blue); color: #fff; }
.wz-fmt-info {
  font-size: 0.74rem;
  color: var(--wz-muted);
  margin: 0.875rem 0 0;
  line-height: 1.7;
}
.wz-fmt-info strong { color: var(--wz-navy); }

/* Processing */
.wz-processing {
  display: none;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.85rem;
  color: var(--wz-muted);
  margin-top: 1.25rem;
  padding: 1rem 1.25rem;
  background: var(--wz-white);
  border-radius: var(--wz-radius);
  border: 1px solid var(--wz-border);
}
.wz-processing.visible { display: flex; }
.wz-spinner {
  width: 18px; height: 18px;
  border: 2.5px solid var(--wz-blue-md);
  border-top-color: var(--wz-blue);
  border-radius: 50%;
  animation: wz-spin 0.7s linear infinite;
  flex-shrink: 0;
}
@keyframes wz-spin { to { transform: rotate(360deg); } }

/* Preview */
.wz-preview { display: none; margin-top: 1.5rem; }
.wz-preview.visible { display: block; }
.wz-section-label {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--wz-blue);
  margin: 0 0 0.6rem;
}
.wz-preview-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
}
.wz-preview-card {
  background: var(--wz-white);
  border: 1px solid var(--wz-border);
  border-radius: var(--wz-radius);
  overflow: hidden;
  box-shadow: var(--wz-shadow);
}
.wz-preview-hd {
  padding: 11px 15px;
  border-bottom: 1px solid var(--wz-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--wz-light);
}
.wz-preview-label {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--wz-muted);
}
.wz-preview-size { font-size: 0.8rem; font-weight: 600; color: var(--wz-navy); }
.wz-img-wrap {
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #edf0fb;
  overflow: hidden;
}
.wz-img-wrap img { max-width: 100%; max-height: 100%; object-fit: contain; }

/* Stats */
.wz-stats {
  display: none;
  margin-top: 1.25rem;
  background: var(--wz-navy);
  border-radius: var(--wz-radius);
  padding: 1.5rem 2rem;
  align-items: center;
  justify-content: space-around;
  gap: 1rem;
  flex-wrap: wrap;
}
.wz-stats.visible { display: flex; }
.wz-stat { text-align: center; }
.wz-stat-val {
  font-size: 1.5rem;
  font-weight: 700;
  color: #fff;
  display: block;
  line-height: 1;
}
.wz-stat-val.accent { color: #7fb3ff; }
.wz-stat-label {
  font-size: 0.68rem;
  color: rgba(255,255,255,0.5);
  margin-top: 4px;
  display: block;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}
.wz-stat-div { width: 1px; height: 42px; background: rgba(255,255,255,0.12); }

/* Action buttons */
.wz-actions { display: none; margin-top: 1.25rem; gap: 0.875rem; flex-wrap: wrap; }
.wz-actions.visible { display: flex; }
.wz-btn {
  font-family: inherit;
  font-size: 0.85rem;
  font-weight: 600;
  padding: 13px 28px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.18s;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  text-decoration: none;
}
.wz-btn-primary {
  background: var(--wz-blue);
  color: #fff;
  box-shadow: 0 4px 16px rgba(58,111,232,0.35);
}
.wz-btn-primary:hover {
  background: #2d5cd4;
  box-shadow: 0 6px 22px rgba(58,111,232,0.45);
  transform: translateY(-1px);
  color: #fff;
}
.wz-btn-outline {
  background: var(--wz-white);
  color: var(--wz-navy);
  border: 1.5px solid var(--wz-border);
}
.wz-btn-outline:hover { border-color: var(--wz-blue); color: var(--wz-blue); }

/* Responsive */
@media (max-width: 660px) {
  .wz-controls,
  .wz-preview-grid { grid-template-columns: 1fr; }
  .wz-stat-div { display: none; }
  .wz-hero { padding: 2.5rem 1.25rem; }
}
</style>

<div class="wz-compressor">

  <!-- Hero banner -->
  <div class="wz-hero">
    <p class="wz-hero-eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> &mdash; Free Tools</p>
    <h1><?php the_title(); ?></h1>
    <p><?php
      $desc = get_the_excerpt();
      echo $desc
        ? esc_html( $desc )
        : 'Reduce image file sizes instantly &mdash; no uploads, no servers, 100% processed in your browser.';
    ?></p>
  </div>

  <div class="wz-wrap">

    <!-- Optional: render page content if editor content was added -->
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <?php if ( get_the_content() ) : ?>
        <div class="entry-content" style="margin-bottom:1.5rem;">
          <?php the_content(); ?>
        </div>
      <?php endif; ?>
    <?php endwhile; endif; ?>

    <!-- Drop zone -->
    <div class="wz-dropzone" id="wz-dropzone" onclick="document.getElementById('wz-file').click()">
      <div class="wz-drop-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="17 8 12 3 7 8"/>
          <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
      </div>
      <p class="wz-drop-title">Drag &amp; Drop your image here</p>
      <p class="wz-drop-sub">or <a onclick="event.stopPropagation();document.getElementById('wz-file').click()">browse to upload</a></p>
      <div class="wz-drop-formats">
        <span class="wz-pill">JPG</span>
        <span class="wz-pill">PNG</span>
        <span class="wz-pill">WebP</span>
        <span class="wz-pill">GIF</span>
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
    <div class="wz-controls" id="wz-controls">
      <div class="wz-card">
        <p class="wz-card-title">Compression Quality</p>
        <div class="wz-quality-row">
          <span class="wz-quality-val" id="wz-q-val">80%</span>
          <span class="wz-quality-desc" id="wz-q-desc">Good balance</span>
        </div>
        <input type="range" id="wz-quality" min="1" max="100" value="80">
      </div>
      <div class="wz-card">
        <p class="wz-card-title">Output Format</p>
        <div class="wz-fmt-btns">
          <button class="wz-fmt-btn active" data-fmt="image/jpeg">JPEG</button>
          <button class="wz-fmt-btn" data-fmt="image/png">PNG</button>
          <button class="wz-fmt-btn" data-fmt="image/webp">WebP</button>
        </div>
        <p class="wz-fmt-info">
          <strong>JPEG</strong> &mdash; best for photos &amp; large images.<br>
          <strong>PNG</strong> &mdash; lossless, ideal for graphics &amp; logos.<br>
          <strong>WebP</strong> &mdash; modern format, smallest file sizes.
        </p>
      </div>
    </div>

    <!-- Processing indicator -->
    <div class="wz-processing" id="wz-processing">
      <div class="wz-spinner"></div>
      <span>Compressing your image, please wait&hellip;</span>
    </div>

    <!-- Preview -->
    <div class="wz-preview" id="wz-preview">
      <p class="wz-section-label">Preview</p>
      <div class="wz-preview-grid">
        <div class="wz-preview-card">
          <div class="wz-preview-hd">
            <span class="wz-preview-label">Original</span>
            <span class="wz-preview-size" id="wz-orig-size">—</span>
          </div>
          <div class="wz-img-wrap"><img id="wz-orig-img" src="" alt="Original image"></div>
        </div>
        <div class="wz-preview-card">
          <div class="wz-preview-hd">
            <span class="wz-preview-label">Compressed</span>
            <span class="wz-preview-size" id="wz-comp-size">—</span>
          </div>
          <div class="wz-img-wrap"><img id="wz-comp-img" src="" alt="Compressed image"></div>
        </div>
      </div>
    </div>

    <!-- Stats bar -->
    <div class="wz-stats" id="wz-stats">
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-orig">—</span><span class="wz-stat-label">Original Size</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-comp">—</span><span class="wz-stat-label">Compressed</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val accent" id="wz-s-saved">—</span><span class="wz-stat-label">Space Saved</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-dims">—</span><span class="wz-stat-label">Dimensions</span></div>
    </div>

    <!-- Action buttons -->
    <div class="wz-actions" id="wz-actions">
      <button class="wz-btn wz-btn-primary" id="wz-download">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Download Compressed Image
      </button>
      <button class="wz-btn wz-btn-outline" id="wz-reset">Compress Another Image</button>
    </div>

  </div><!-- /.wz-wrap -->
</div><!-- /.wz-compressor -->

<canvas id="wz-canvas" style="display:none"></canvas>

<script>
(function () {
  'use strict';

  var dropzone   = document.getElementById('wz-dropzone');
  var fileInput  = document.getElementById('wz-file');
  var controls   = document.getElementById('wz-controls');
  var qSlider    = document.getElementById('wz-quality');
  var qVal       = document.getElementById('wz-q-val');
  var qDesc      = document.getElementById('wz-q-desc');
  var fmtBtns    = document.querySelectorAll('.wz-fmt-btn');
  var processing = document.getElementById('wz-processing');
  var preview    = document.getElementById('wz-preview');
  var stats      = document.getElementById('wz-stats');
  var actions    = document.getElementById('wz-actions');
  var canvas     = document.getElementById('wz-canvas');
  var ctx        = canvas.getContext('2d');

  var origFile   = null;
  var origSize   = 0;
  var compBlob   = null;
  var format     = 'image/jpeg';
  var debounce   = null;
  var img        = new Image();

  function qualityLabel(v) {
    if (v >= 90) return 'Maximum quality';
    if (v >= 75) return 'Good balance';
    if (v >= 50) return 'Moderate compression';
    if (v >= 25) return 'High compression';
    return 'Maximum compression';
  }

  function fmtBytes(b) {
    if (b < 1024) return b + ' B';
    if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
    return (b / 1048576).toFixed(2) + ' MB';
  }

  function show(el)  { el.classList.add('visible'); }
  function hide(el)  { el.classList.remove('visible'); }

  // Drag & drop
  ['dragover', 'dragenter'].forEach(function (e) {
    dropzone.addEventListener(e, function (ev) { ev.preventDefault(); dropzone.classList.add('drag-over'); });
  });
  ['dragleave', 'drop'].forEach(function (e) {
    dropzone.addEventListener(e, function (ev) { ev.preventDefault(); dropzone.classList.remove('drag-over'); });
  });
  dropzone.addEventListener('drop', function (ev) {
    var f = ev.dataTransfer.files[0];
    if (f && f.type.startsWith('image/')) loadFile(f);
  });

  fileInput.addEventListener('change', function () {
    if (fileInput.files[0]) loadFile(fileInput.files[0]);
  });

  qSlider.addEventListener('input', function () {
    var v = parseInt(qSlider.value, 10);
    qVal.textContent  = v + '%';
    qDesc.textContent = qualityLabel(v);
    qSlider.style.setProperty('--pct', v + '%');
    clearTimeout(debounce);
    debounce = setTimeout(compress, 320);
  });

  fmtBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      fmtBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      format = btn.dataset.fmt;
      if (origFile) compress();
    });
  });

  document.getElementById('wz-download').addEventListener('click', function () {
    if (!compBlob) return;
    var ext  = format.split('/')[1].replace('jpeg', 'jpg');
    var name = (origFile.name.replace(/\.[^.]+$/, '') || 'image') + '_compressed.' + ext;
    var url  = URL.createObjectURL(compBlob);
    var a    = document.createElement('a');
    a.href = url; a.download = name; a.click();
    setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
  });

  document.getElementById('wz-reset').addEventListener('click', function () {
    origFile = null; compBlob = null;
    [controls, preview, stats, actions, processing].forEach(hide);
    fileInput.value = '';
  });

  function loadFile(file) {
    origFile = file; origSize = file.size;
    var reader = new FileReader();
    reader.onload = function (e) {
      img.onload = function () {
        canvas.width  = img.naturalWidth;
        canvas.height = img.naturalHeight;
        document.getElementById('wz-orig-img').src  = e.target.result;
        document.getElementById('wz-orig-size').textContent = fmtBytes(origSize);
        document.getElementById('wz-s-dims').textContent    = img.naturalWidth + ' \u00d7 ' + img.naturalHeight;
        qSlider.style.setProperty('--pct', qSlider.value + '%');
        show(controls);
        compress();
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }

  function compress() {
    if (!img.src || !origFile) return;
    show(processing); hide(preview); hide(stats); hide(actions);

    setTimeout(function () {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(img, 0, 0);
      canvas.toBlob(function (blob) {
        if (!blob) { hide(processing); return; }
        compBlob = blob;
        var url = URL.createObjectURL(blob);
        document.getElementById('wz-comp-img').src               = url;
        document.getElementById('wz-comp-size').textContent      = fmtBytes(blob.size);
        document.getElementById('wz-s-orig').textContent         = fmtBytes(origSize);
        document.getElementById('wz-s-comp').textContent         = fmtBytes(blob.size);
        var saved = origSize - blob.size;
        var pct   = ((saved / origSize) * 100).toFixed(1);
        document.getElementById('wz-s-saved').textContent = Math.abs(pct) + '% ' + (saved >= 0 ? 'saved' : 'larger');
        hide(processing); show(preview); show(stats); show(actions);
      }, format, parseFloat(qSlider.value) / 100);
    }, 50);
  }

  // Init slider track colour
  qSlider.style.setProperty('--pct', '80%');
}());
</script>

<?php get_footer(); ?>
