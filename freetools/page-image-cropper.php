<?php
/**
 * Template Name: Image Cropper
 * Template Post Type: page
 *
 * A fully client-side image cropping tool with draggable crop box and aspect ratio lock.
 * Install: Upload this file to your active theme folder (e.g. wp-content/themes/your-theme/).
 * Usage:   In WordPress admin, edit any Page → set "Page Attributes > Template" to "Image Cropper".
 *
 * @package Webzinger
 */

get_header(); ?>

<style>
.wz-cropper *,
.wz-cropper *::before,
.wz-cropper *::after {
  box-sizing: border-box;
}

.wz-cropper {
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

/* Controls row */
.wz-controls-row {
  display: none;
  margin-top: 1.5rem;
  gap: 1.25rem;
  grid-template-columns: 1fr 1fr;
}
.wz-controls-row.visible { display: grid; }
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

/* Crop dimensions inputs */
.wz-dim-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}
.wz-dim-group { display: flex; flex-direction: column; gap: 4px; }
.wz-dim-label {
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--wz-muted);
  text-transform: uppercase;
  letter-spacing: 0.08em;
}
.wz-dim-input {
  font-family: inherit;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--wz-navy);
  border: 1.5px solid var(--wz-border);
  border-radius: 6px;
  padding: 9px 12px;
  width: 100%;
  outline: none;
  transition: border-color 0.2s;
  background: var(--wz-light);
}
.wz-dim-input:focus { border-color: var(--wz-blue); background: var(--wz-white); }

/* Checkbox toggle */
.wz-toggle-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 1rem;
  cursor: pointer;
  user-select: none;
}
.wz-toggle-row input[type="checkbox"] { display: none; }
.wz-toggle-track {
  width: 40px; height: 22px;
  background: var(--wz-border);
  border-radius: 11px;
  position: relative;
  transition: background 0.2s;
  flex-shrink: 0;
}
.wz-toggle-row input:checked + .wz-toggle-track { background: var(--wz-blue); }
.wz-toggle-thumb {
  position: absolute;
  top: 3px; left: 3px;
  width: 16px; height: 16px;
  background: #fff;
  border-radius: 50%;
  box-shadow: 0 1px 4px rgba(0,0,0,0.18);
  transition: left 0.2s;
}
.wz-toggle-row input:checked ~ .wz-toggle-track .wz-toggle-thumb { left: 21px; }
.wz-toggle-label { font-size: 0.85rem; font-weight: 500; color: var(--wz-text); }
.wz-toggle-sub { font-size: 0.75rem; color: var(--wz-muted); margin-top: 2px; }

/* Crop presets */
.wz-preset-btns { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.wz-preset-btn {
  font-family: inherit;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 7px 14px;
  border: 1.5px solid var(--wz-border);
  background: var(--wz-white);
  color: var(--wz-muted);
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.15s;
}
.wz-preset-btn:hover { border-color: var(--wz-blue); color: var(--wz-blue); background: var(--wz-blue-lt); }
.wz-preset-btn.active { border-color: var(--wz-blue); background: var(--wz-blue); color: #fff; }

/* Crop canvas area */
.wz-crop-area {
  display: none;
  margin-top: 1.5rem;
}
.wz-crop-area.visible { display: block; }
.wz-section-label {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--wz-blue);
  margin: 0 0 0.6rem;
}
.wz-crop-stage {
  background: var(--wz-white);
  border: 1px solid var(--wz-border);
  border-radius: var(--wz-radius);
  box-shadow: var(--wz-shadow);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
}
.wz-crop-container {
  position: relative;
  display: inline-block;
  line-height: 0;
  cursor: crosshair;
}
.wz-crop-img {
  display: block;
  max-width: 100%;
  max-height: 480px;
  user-select: none;
  pointer-events: none;
}
.wz-crop-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.45);
  pointer-events: none;
}
.wz-crop-box {
  position: absolute;
  border: 2px solid var(--wz-blue);
  cursor: move;
  box-shadow: 0 0 0 9999px rgba(0,0,0,0.45);
}
.wz-crop-box-inner {
  position: absolute;
  inset: 0;
  background: transparent;
}
/* Rule-of-thirds grid lines */
.wz-crop-box-inner::before,
.wz-crop-box-inner::after {
  content: '';
  position: absolute;
  background: rgba(255,255,255,0.25);
}
.wz-crop-box-inner::before {
  top: 0; bottom: 0;
  left: 33.33%; width: 1px;
  box-shadow: calc(33.34%) 0 0 rgba(255,255,255,0.25);
}
.wz-crop-box-inner::after {
  left: 0; right: 0;
  top: 33.33%; height: 1px;
  box-shadow: 0 calc(33.34%) 0 rgba(255,255,255,0.25);
}
/* Corner handles */
.wz-handle {
  position: absolute;
  width: 12px; height: 12px;
  background: #fff;
  border: 2px solid var(--wz-blue);
  border-radius: 2px;
}
.wz-handle-nw { top: -6px; left: -6px; cursor: nw-resize; }
.wz-handle-ne { top: -6px; right: -6px; cursor: ne-resize; }
.wz-handle-sw { bottom: -6px; left: -6px; cursor: sw-resize; }
.wz-handle-se { bottom: -6px; right: -6px; cursor: se-resize; }
/* Edge midpoint handles */
.wz-handle-n { top: -5px; left: calc(50% - 5px); cursor: n-resize; }
.wz-handle-s { bottom: -5px; left: calc(50% - 5px); cursor: s-resize; }
.wz-handle-w { top: calc(50% - 5px); left: -5px; cursor: w-resize; }
.wz-handle-e { top: calc(50% - 5px); right: -5px; cursor: e-resize; }
.wz-handle-n, .wz-handle-s, .wz-handle-w, .wz-handle-e {
  width: 10px; height: 10px;
}

/* Crop info bar */
.wz-crop-info {
  display: flex;
  gap: 1.5rem;
  align-items: center;
  margin-top: 0.75rem;
  flex-wrap: wrap;
}
.wz-crop-info-item { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; }
.wz-crop-info-badge {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--wz-muted);
}
.wz-crop-info-val { font-weight: 600; color: var(--wz-navy); }

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

@media (max-width: 660px) {
  .wz-controls-row { grid-template-columns: 1fr; }
  .wz-stat-div { display: none; }
  .wz-hero { padding: 2.5rem 1.25rem; }
  .wz-dim-grid { grid-template-columns: 1fr 1fr; }
}
</style>

<div class="wz-cropper">

  <div class="wz-hero">
    <p class="wz-hero-eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> &mdash; Free Tools</p>
    <h1><?php the_title(); ?></h1>
    <p><?php
      $desc = get_the_excerpt();
      echo $desc
        ? esc_html( $desc )
        : 'Crop images to exact pixel dimensions with a draggable crop box &mdash; 100% in your browser, no uploads.';
    ?></p>
  </div>

  <div class="wz-wrap">

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
          <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
          <polyline points="21 15 16 10 5 21"/>
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
      <!-- Crop dimensions -->
      <div class="wz-card">
        <p class="wz-card-title">Crop Size (pixels)</p>
        <div class="wz-dim-grid">
          <div class="wz-dim-group">
            <span class="wz-dim-label">Width</span>
            <input type="number" class="wz-dim-input" id="wz-crop-w" min="1" placeholder="px">
          </div>
          <div class="wz-dim-group">
            <span class="wz-dim-label">Height</span>
            <input type="number" class="wz-dim-input" id="wz-crop-h" min="1" placeholder="px">
          </div>
        </div>
        <label class="wz-toggle-row" for="wz-lock-ratio">
          <input type="checkbox" id="wz-lock-ratio">
          <span class="wz-toggle-track"><span class="wz-toggle-thumb"></span></span>
          <div>
            <div class="wz-toggle-label">Lock aspect ratio</div>
            <div class="wz-toggle-sub">Resize maintains width&thinsp;:&thinsp;height proportion</div>
          </div>
        </label>
      </div>
      <!-- Presets -->
      <div class="wz-card">
        <p class="wz-card-title">Ratio Presets</p>
        <div class="wz-preset-btns">
          <button class="wz-preset-btn" data-w="1" data-h="1">1:1</button>
          <button class="wz-preset-btn" data-w="4" data-h="3">4:3</button>
          <button class="wz-preset-btn" data-w="16" data-h="9">16:9</button>
          <button class="wz-preset-btn" data-w="3" data-h="2">3:2</button>
          <button class="wz-preset-btn" data-w="2" data-h="3">2:3 Portrait</button>
          <button class="wz-preset-btn" data-w="9" data-h="16">9:16 Story</button>
          <button class="wz-preset-btn" data-w="0" data-h="0">Free</button>
        </div>
      </div>
    </div>

    <!-- Crop stage -->
    <div class="wz-crop-area" id="wz-crop-area">
      <p class="wz-section-label">Drag the crop box &mdash; drag edges or corners to resize</p>
      <div class="wz-crop-stage">
        <div class="wz-crop-container" id="wz-crop-container">
          <img id="wz-source-img" class="wz-crop-img" src="" alt="Source image">
          <div class="wz-crop-box" id="wz-crop-box">
            <div class="wz-crop-box-inner"></div>
            <div class="wz-handle wz-handle-nw" data-dir="nw"></div>
            <div class="wz-handle wz-handle-ne" data-dir="ne"></div>
            <div class="wz-handle wz-handle-sw" data-dir="sw"></div>
            <div class="wz-handle wz-handle-se" data-dir="se"></div>
            <div class="wz-handle wz-handle-n"  data-dir="n"></div>
            <div class="wz-handle wz-handle-s"  data-dir="s"></div>
            <div class="wz-handle wz-handle-w"  data-dir="w"></div>
            <div class="wz-handle wz-handle-e"  data-dir="e"></div>
          </div>
        </div>
      </div>
      <div class="wz-crop-info" id="wz-crop-info">
        <div class="wz-crop-info-item"><span class="wz-crop-info-badge">Crop</span><span class="wz-crop-info-val" id="wz-info-crop">—</span></div>
        <div class="wz-crop-info-item"><span class="wz-crop-info-badge">Position</span><span class="wz-crop-info-val" id="wz-info-pos">—</span></div>
        <div class="wz-crop-info-item"><span class="wz-crop-info-badge">Original</span><span class="wz-crop-info-val" id="wz-info-orig">—</span></div>
      </div>
    </div>

    <!-- Stats -->
    <div class="wz-stats" id="wz-stats">
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-orig">—</span><span class="wz-stat-label">Original Size</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val accent" id="wz-s-crop">—</span><span class="wz-stat-label">Crop Region</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-ratio">—</span><span class="wz-stat-label">Aspect Ratio</span></div>
    </div>

    <!-- Actions -->
    <div class="wz-actions" id="wz-actions">
      <button class="wz-btn wz-btn-primary" id="wz-download">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Download Cropped Image
      </button>
      <button class="wz-btn wz-btn-outline" id="wz-reset">Crop Another Image</button>
    </div>

  </div>
</div>

<canvas id="wz-canvas" style="display:none"></canvas>

<script>
(function () {
  'use strict';

  var dropzone   = document.getElementById('wz-dropzone');
  var fileInput  = document.getElementById('wz-file');
  var controlsEl = document.getElementById('wz-controls');
  var cropArea   = document.getElementById('wz-crop-area');
  var container  = document.getElementById('wz-crop-container');
  var sourceImg  = document.getElementById('wz-source-img');
  var cropBox    = document.getElementById('wz-crop-box');
  var statsEl    = document.getElementById('wz-stats');
  var actionsEl  = document.getElementById('wz-actions');
  var canvas     = document.getElementById('wz-canvas');
  var ctx        = canvas.getContext('2d');
  var inputW     = document.getElementById('wz-crop-w');
  var inputH     = document.getElementById('wz-crop-h');
  var lockCb     = document.getElementById('wz-lock-ratio');
  var presetBtns = document.querySelectorAll('.wz-preset-btn');

  var origFile   = null;
  var naturalW   = 0;
  var naturalH   = 0;
  var scaleX     = 1; // naturalPx / displayPx
  var scaleY     = 1;
  var imgObj     = new Image();

  // Crop box state in display pixels
  var box = { x: 0, y: 0, w: 0, h: 0 };
  var ratioW = 0, ratioH = 0; // 0 = free
  var dragging = false;
  var resizeDir = null;
  var dragStart = {};

  function show(el) { el.classList.add('visible'); }
  function hide(el) { el.classList.remove('visible'); }

  function gcd(a, b) { return b === 0 ? a : gcd(b, a % b); }

  function updateInfoBar() {
    var cw = Math.round(box.w * scaleX);
    var ch = Math.round(box.h * scaleY);
    var cx = Math.round(box.x * scaleX);
    var cy = Math.round(box.y * scaleY);
    document.getElementById('wz-info-crop').textContent = cw + ' \u00d7 ' + ch + ' px';
    document.getElementById('wz-info-pos').textContent  = cx + ', ' + cy + ' px';
    document.getElementById('wz-info-orig').textContent = naturalW + ' \u00d7 ' + naturalH + ' px';
    // update inputs
    inputW.value = cw;
    inputH.value = ch;
    // stats
    var g = gcd(Math.round(cw), Math.round(ch));
    document.getElementById('wz-s-orig').textContent  = naturalW + ' \u00d7 ' + naturalH;
    document.getElementById('wz-s-crop').textContent  = cw + ' \u00d7 ' + ch;
    document.getElementById('wz-s-ratio').textContent = (g > 0 ? (cw/g) + ':' + (ch/g) : '—');
  }

  function clamp(v, mn, mx) { return Math.min(Math.max(v, mn), mx); }

  function applyBox() {
    cropBox.style.left   = box.x + 'px';
    cropBox.style.top    = box.y + 'px';
    cropBox.style.width  = box.w + 'px';
    cropBox.style.height = box.h + 'px';
    updateInfoBar();
  }

  function setBoxFromPixels(pw, ph) {
    // pw/ph in natural image pixels
    var dw = pw / scaleX;
    var dh = ph / scaleY;
    var cw = sourceImg.clientWidth;
    var ch = sourceImg.clientHeight;
    box.w = clamp(dw, 10, cw - box.x);
    box.h = clamp(dh, 10, ch - box.y);
    applyBox();
  }

  function initBox() {
    var cw = sourceImg.clientWidth;
    var ch = sourceImg.clientHeight;
    var bw = cw * 0.7, bh = ch * 0.7;
    if (ratioW && ratioH) {
      var r = ratioW / ratioH;
      if (bw / r <= bh) bh = bw / r; else bw = bh * r;
    }
    box.x = (cw - bw) / 2;
    box.y = (ch - bh) / 2;
    box.w = bw;
    box.h = bh;
    applyBox();
    show(statsEl);
    show(actionsEl);
  }

  // Drag & drop file
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

  function loadFile(file) {
    origFile = file;
    var reader = new FileReader();
    reader.onload = function(e){
      imgObj.onload = function(){
        naturalW = imgObj.naturalWidth;
        naturalH = imgObj.naturalHeight;
        sourceImg.src = e.target.result;
        sourceImg.onload = function(){
          show(controlsEl);
          show(cropArea);
          // Double rAF defers until after browser layout, so clientWidth/clientHeight are non-zero
          requestAnimationFrame(function(){
            requestAnimationFrame(function(){
              var dw = sourceImg.clientWidth;
              var dh = sourceImg.clientHeight;
              if (!dw || !dh) return; // safety guard against zero dimensions
              scaleX = naturalW / dw;
              scaleY = naturalH / dh;
              initBox();
            });
          });
        };
      };
      imgObj.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }

  // Preset buttons
  presetBtns.forEach(function(btn){
    btn.addEventListener('click', function(){
      presetBtns.forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      ratioW = parseFloat(btn.dataset.w);
      ratioH = parseFloat(btn.dataset.h);
      if (ratioW && ratioH) {
        lockCb.checked = true;
      } else {
        lockCb.checked = false;
        ratioW = 0; ratioH = 0;
      }
      if (sourceImg.src) initBox();
    });
  });

  // Lock ratio toggle
  lockCb.addEventListener('change', function(){
    if (!lockCb.checked) { ratioW = 0; ratioH = 0; }
    else {
      // derive ratio from current box
      ratioW = box.w; ratioH = box.h;
      var g = gcd(Math.round(ratioW), Math.round(ratioH));
      ratioW = ratioW / g; ratioH = ratioH / g;
    }
    presetBtns.forEach(function(b){ b.classList.remove('active'); });
  });

  // Manual dimension inputs
  function onDimInput(changedAxis) {
    if (!sourceImg.src) return;
    var cw = sourceImg.clientWidth;
    var ch = sourceImg.clientHeight;
    var pw = parseFloat(inputW.value) || 0;
    var ph = parseFloat(inputH.value) || 0;
    if (lockCb.checked && ratioW && ratioH) {
      if (changedAxis === 'w') ph = pw * (ratioH / ratioW);
      else pw = ph * (ratioW / ratioH);
    }
    box.w = clamp(pw / scaleX, 10, cw - box.x);
    box.h = clamp(ph / scaleY, 10, ch - box.y);
    applyBox();
  }
  inputW.addEventListener('input', function(){ onDimInput('w'); });
  inputH.addEventListener('input', function(){ onDimInput('h'); });

  // --- Drag logic ---
  function getPos(ev) {
    var touch = ev.touches ? ev.touches[0] : ev;
    var rect = container.getBoundingClientRect();
    return { x: touch.clientX - rect.left, y: touch.clientY - rect.top };
  }

  // Move crop box
  cropBox.addEventListener('mousedown', startMove);
  cropBox.addEventListener('touchstart', startMove, { passive: false });
  function startMove(ev) {
    if (ev.target.dataset.dir) return; // handled by handle
    ev.preventDefault();
    dragging = true; resizeDir = null;
    var pos = getPos(ev);
    dragStart = { mx: pos.x, my: pos.y, bx: box.x, by: box.y };
  }

  // Resize handles
  cropBox.querySelectorAll('.wz-handle').forEach(function(h){
    h.addEventListener('mousedown', startResize);
    h.addEventListener('touchstart', startResize, { passive: false });
  });
  function startResize(ev) {
    ev.preventDefault();
    ev.stopPropagation();
    dragging = true;
    resizeDir = ev.target.dataset.dir;
    var pos = getPos(ev);
    dragStart = { mx: pos.x, my: pos.y, bx: box.x, by: box.y, bw: box.w, bh: box.h };
  }

  document.addEventListener('mousemove', onMove);
  document.addEventListener('touchmove', onMove, { passive: false });
  function onMove(ev) {
    if (!dragging) return;
    ev.preventDefault();
    var pos = getPos(ev);
    var dx = pos.x - dragStart.mx;
    var dy = pos.y - dragStart.my;
    var cw = sourceImg.clientWidth;
    var ch = sourceImg.clientHeight;

    if (!resizeDir) {
      // Move
      box.x = clamp(dragStart.bx + dx, 0, cw - box.w);
      box.y = clamp(dragStart.by + dy, 0, ch - box.h);
    } else {
      var nx = dragStart.bx, ny = dragStart.by, nw = dragStart.bw, nh = dragStart.bh;
      var r = (lockCb.checked && ratioW && ratioH) ? ratioW / ratioH : 0;

      if (resizeDir.indexOf('e') > -1) {
        nw = clamp(dragStart.bw + dx, 10, cw - nx);
        if (r) nh = nw / r;
      }
      if (resizeDir.indexOf('s') > -1) {
        nh = clamp(dragStart.bh + dy, 10, ch - ny);
        if (r) nw = nh * r;
      }
      if (resizeDir.indexOf('w') > -1) {
        var newW = clamp(dragStart.bw - dx, 10, dragStart.bx + dragStart.bw);
        nx = dragStart.bx + dragStart.bw - newW;
        nw = newW;
        if (r) nh = nw / r;
      }
      if (resizeDir.indexOf('n') > -1) {
        var newH = clamp(dragStart.bh - dy, 10, dragStart.by + dragStart.bh);
        ny = dragStart.by + dragStart.bh - newH;
        nh = newH;
        if (r) nw = nh * r;
      }
      // Clamp
      nw = clamp(nw, 10, cw - nx);
      nh = clamp(nh, 10, ch - ny);
      box.x = nx; box.y = ny; box.w = nw; box.h = nh;
    }
    applyBox();
  }

  document.addEventListener('mouseup', stopDrag);
  document.addEventListener('touchend', stopDrag);
  function stopDrag() { dragging = false; resizeDir = null; }

  // Draw new crop box by clicking on image background
  container.addEventListener('mousedown', function(ev){
    if (ev.target !== sourceImg && ev.target !== container) return;
    ev.preventDefault();
    var pos = getPos(ev);
    dragStart = { mx: pos.x, my: pos.y };
    dragging = true;
    resizeDir = 'se';
    box.x = pos.x; box.y = pos.y; box.w = 1; box.h = 1;
    dragStart.bx = pos.x; dragStart.by = pos.y; dragStart.bw = 0; dragStart.bh = 0;
    applyBox();
  });

  // Download
  document.getElementById('wz-download').addEventListener('click', function(){
    if (!imgObj.src) return;
    var cw = Math.round(box.w * scaleX);
    var ch = Math.round(box.h * scaleY);
    var cx = Math.round(box.x * scaleX);
    var cy = Math.round(box.y * scaleY);
    canvas.width = cw; canvas.height = ch;
    ctx.drawImage(imgObj, cx, cy, cw, ch, 0, 0, cw, ch);
    canvas.toBlob(function(blob){
      var ext  = (origFile.type === 'image/png') ? 'png' : 'jpg';
      var mime = (origFile.type === 'image/png') ? 'image/png' : 'image/jpeg';
      var name = (origFile.name.replace(/\.[^.]+$/, '') || 'image') + '_cropped.' + ext;
      var url  = URL.createObjectURL(blob);
      var a    = document.createElement('a');
      a.href = url; a.download = name; a.click();
      setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
    }, (origFile.type === 'image/png') ? 'image/png' : 'image/jpeg', 0.95);
  });

  document.getElementById('wz-reset').addEventListener('click', function(){
    origFile = null;
    [controlsEl, cropArea, statsEl, actionsEl].forEach(hide);
    fileInput.value = '';
    sourceImg.src = '';
  });

}());
</script>

<?php get_footer(); ?>
