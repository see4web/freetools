<?php
/**
 * Template Name: PDF Compressor
 * Template Post Type: page
 *
 * A fully client-side PDF compression tool with Low/Medium/High/Extreme levels.
 * Uses PDF.js to render pages and jsPDF to re-encode them as a smaller PDF.
 * Install: Upload this file to your active theme folder (e.g. wp-content/themes/your-theme/).
 * Usage:   In WordPress admin, edit any Page → set "Page Attributes > Template" to "PDF Compressor".
 *
 * Requires: PDF.js (cdnjs), jsPDF (cdnjs)
 *
 * @package Webzinger
 */

get_header(); ?>

<style>
.wz-pdfcomp *,
.wz-pdfcomp *::before,
.wz-pdfcomp *::after { box-sizing: border-box; }

.wz-pdfcomp {
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
  --wz-green:   #22c55e;
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

/* Controls */
.wz-controls { display:none; margin-top:1.5rem; }
.wz-controls.visible { display:block; }
.wz-card { background:var(--wz-white); border:1px solid var(--wz-border); border-radius:var(--wz-radius); padding:1.5rem; box-shadow:var(--wz-shadow); }
.wz-card-title { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--wz-muted); margin:0 0 1.25rem; }

/* Compression level cards */
.wz-level-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
.wz-level-card {
  border:2px solid var(--wz-border);
  border-radius:var(--wz-radius);
  padding:1.25rem 1rem;
  text-align:center;
  cursor:pointer;
  transition:all .18s;
  position:relative;
  background:var(--wz-white);
}
.wz-level-card:hover { border-color:var(--wz-blue); background:var(--wz-blue-lt); }
.wz-level-card.active { border-color:var(--wz-blue); background:var(--wz-blue-lt); }
.wz-level-card.active .wz-level-check { display:flex; }
.wz-level-check {
  display:none;
  position:absolute;
  top:8px; right:8px;
  width:20px; height:20px;
  background:var(--wz-blue);
  border-radius:50%;
  align-items:center;
  justify-content:center;
}
.wz-level-check svg { width:10px; height:10px; color:#fff; }
.wz-level-icon { font-size:1.6rem; margin-bottom:.5rem; line-height:1; }
.wz-level-name { font-size:.85rem; font-weight:700; color:var(--wz-navy); margin-bottom:.3rem; }
.wz-level-desc { font-size:.72rem; color:var(--wz-muted); line-height:1.5; }
.wz-level-badge { font-size:.65rem; font-weight:700; padding:2px 8px; border-radius:10px; margin-top:.5rem; display:inline-block; }
.wz-level-card[data-level="low"]     .wz-level-badge { background:#dcfce7; color:#15803d; }
.wz-level-card[data-level="medium"]  .wz-level-badge { background:#fef9c3; color:#854d0e; }
.wz-level-card[data-level="high"]    .wz-level-badge { background:#ffedd5; color:#9a3412; }
.wz-level-card[data-level="extreme"] .wz-level-badge { background:#fee2e2; color:#991b1b; }

/* File info strip */
.wz-file-info { display:flex; align-items:center; gap:1rem; margin-top:1.25rem; padding:1rem 1.25rem; background:var(--wz-blue-lt); border-radius:var(--wz-radius); flex-wrap:wrap; }
.wz-file-icon { width:40px; height:40px; background:var(--wz-blue); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.wz-file-icon svg { width:20px; height:20px; color:#fff; }
.wz-file-name { font-weight:600; font-size:.9rem; color:var(--wz-navy); }
.wz-file-meta { font-size:.75rem; color:var(--wz-muted); }

/* Progress bar */
.wz-progress-wrap { display:none; margin-top:1.25rem; }
.wz-progress-wrap.visible { display:block; }
.wz-progress-label { font-size:.8rem; color:var(--wz-muted); margin-bottom:.5rem; display:flex; justify-content:space-between; }
.wz-progress-bar { height:8px; background:var(--wz-border); border-radius:4px; overflow:hidden; }
.wz-progress-fill { height:100%; background:linear-gradient(90deg,var(--wz-blue),#6497f5); border-radius:4px; transition:width .3s; width:0%; }
.wz-progress-status { font-size:.75rem; color:var(--wz-muted); margin-top:.4rem; }

/* Stats */
.wz-stats { display:none; margin-top:1.25rem; background:var(--wz-navy); border-radius:var(--wz-radius); padding:1.5rem 2rem; align-items:center; justify-content:space-around; gap:1rem; flex-wrap:wrap; }
.wz-stats.visible { display:flex; }
.wz-stat { text-align:center; }
.wz-stat-val { font-size:1.5rem; font-weight:700; color:#fff; display:block; line-height:1; }
.wz-stat-val.accent { color:#7fb3ff; }
.wz-stat-val.green  { color:#4ade80; }
.wz-stat-label { font-size:.68rem; color:rgba(255,255,255,.5); margin-top:4px; display:block; text-transform:uppercase; letter-spacing:.08em; }
.wz-stat-div { width:1px; height:42px; background:rgba(255,255,255,.12); }

.wz-actions { display:none; margin-top:1.25rem; gap:.875rem; flex-wrap:wrap; }
.wz-actions.visible { display:flex; }
.wz-btn { font-family:inherit; font-size:.85rem; font-weight:600; padding:13px 28px; border:none; border-radius:6px; cursor:pointer; transition:all .18s; display:inline-flex; align-items:center; gap:7px; text-decoration:none; }
.wz-btn-primary { background:var(--wz-blue); color:#fff; box-shadow:0 4px 16px rgba(58,111,232,.35); }
.wz-btn-primary:hover { background:#2d5cd4; box-shadow:0 6px 22px rgba(58,111,232,.45); transform:translateY(-1px); color:#fff; }
.wz-btn-outline { background:var(--wz-white); color:var(--wz-navy); border:1.5px solid var(--wz-border); }
.wz-btn-outline:hover { border-color:var(--wz-blue); color:var(--wz-blue); }

.wz-compress-btn-wrap { margin-top:1.25rem; }

@media (max-width:660px) {
  .wz-level-grid { grid-template-columns:1fr 1fr; }
  .wz-stat-div { display:none; }
  .wz-hero { padding:2.5rem 1.25rem; }
}
</style>

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="wz-pdfcomp">

  <div class="wz-hero">
    <p class="wz-hero-eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> &mdash; Free Tools</p>
    <h1><?php the_title(); ?></h1>
    <p><?php
      $desc = get_the_excerpt();
      echo $desc ? esc_html( $desc ) : 'Reduce PDF file size with selectable compression levels &mdash; Low, Medium, High, or Extreme. 100% in-browser.';
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
          <path d="M9 13h6M12 10v6"/>
        </svg>
      </div>
      <p class="wz-drop-title">Drag &amp; Drop your PDF here</p>
      <p class="wz-drop-sub">or <a onclick="event.stopPropagation();document.getElementById('wz-file').click()">browse to upload</a></p>
      <div class="wz-drop-formats"><span class="wz-pill">PDF</span></div>
      <input type="file" id="wz-file" accept="application/pdf,.pdf" style="display:none">
    </div>

    <div class="wz-privacy">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
      </svg>
      Your PDF is processed entirely in your browser. Nothing is uploaded to any server &mdash; completely private.
    </div>

    <!-- Controls -->
    <div class="wz-controls" id="wz-controls">
      <!-- File info -->
      <div class="wz-file-info" id="wz-file-info">
        <div class="wz-file-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
        </div>
        <div>
          <div class="wz-file-name" id="wz-fname">—</div>
          <div class="wz-file-meta" id="wz-fmeta">—</div>
        </div>
      </div>

      <!-- Compression level selector -->
      <div class="wz-card" style="margin-top:1.25rem;">
        <p class="wz-card-title">Compression Level</p>
        <div class="wz-level-grid">
          <div class="wz-level-card" data-level="low">
            <div class="wz-level-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="wz-level-icon">🟢</div>
            <div class="wz-level-name">Low</div>
            <div class="wz-level-desc">High quality, smaller size reduction</div>
            <span class="wz-level-badge">~20–35% smaller</span>
          </div>
          <div class="wz-level-card active" data-level="medium">
            <div class="wz-level-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="wz-level-icon">🟡</div>
            <div class="wz-level-name">Medium</div>
            <div class="wz-level-desc">Good quality, balanced compression</div>
            <span class="wz-level-badge">~40–60% smaller</span>
          </div>
          <div class="wz-level-card" data-level="high">
            <div class="wz-level-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="wz-level-icon">🟠</div>
            <div class="wz-level-name">High</div>
            <div class="wz-level-desc">Visible quality loss, significant savings</div>
            <span class="wz-level-badge">~60–75% smaller</span>
          </div>
          <div class="wz-level-card" data-level="extreme">
            <div class="wz-level-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="wz-level-icon">🔴</div>
            <div class="wz-level-name">Extreme</div>
            <div class="wz-level-desc">Maximum savings, low quality</div>
            <span class="wz-level-badge">~75–90% smaller</span>
          </div>
        </div>
      </div>

      <div class="wz-compress-btn-wrap">
        <button class="wz-btn wz-btn-primary" id="wz-compress" style="padding:15px 36px;font-size:.95rem;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="17 11 12 6 7 11"/><polyline points="17 18 12 13 7 18"/>
          </svg>
          Compress PDF
        </button>
      </div>
    </div>

    <!-- Progress -->
    <div class="wz-progress-wrap" id="wz-progress-wrap">
      <div class="wz-progress-label">
        <span id="wz-prog-label">Processing PDF&hellip;</span>
        <span id="wz-prog-pct">0%</span>
      </div>
      <div class="wz-progress-bar"><div class="wz-progress-fill" id="wz-prog-fill"></div></div>
      <div class="wz-progress-status" id="wz-prog-status">Preparing&hellip;</div>
    </div>

    <!-- Stats -->
    <div class="wz-stats" id="wz-stats">
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-orig">—</span><span class="wz-stat-label">Original Size</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val accent" id="wz-s-comp">—</span><span class="wz-stat-label">Compressed</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val green" id="wz-s-saved">—</span><span class="wz-stat-label">Space Saved</span></div>
      <div class="wz-stat-div"></div>
      <div class="wz-stat"><span class="wz-stat-val" id="wz-s-pages">—</span><span class="wz-stat-label">Pages</span></div>
    </div>

    <!-- Actions -->
    <div class="wz-actions" id="wz-actions">
      <button class="wz-btn wz-btn-primary" id="wz-download">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Download Compressed PDF
      </button>
      <button class="wz-btn wz-btn-outline" id="wz-reset">Compress Another PDF</button>
    </div>

  </div>
</div>

<script>
(function(){
  'use strict';

  // Compression level → render scale & JPEG quality
  var levelConfig = {
    low:     { scale: 2.0, quality: 0.88 },
    medium:  { scale: 1.5, quality: 0.72 },
    high:    { scale: 1.0, quality: 0.52 },
    extreme: { scale: 0.75, quality: 0.30 }
  };

  var dropzone    = document.getElementById('wz-dropzone');
  var fileInput   = document.getElementById('wz-file');
  var controlsEl  = document.getElementById('wz-controls');
  var progressWrap= document.getElementById('wz-progress-wrap');
  var statsEl     = document.getElementById('wz-stats');
  var actionsEl   = document.getElementById('wz-actions');

  var origFile    = null;
  var origBytes   = null;
  var pdfBlob     = null;
  var selectedLevel = 'medium';
  var totalPages  = 0;

  function show(el){ el.classList.add('visible'); }
  function hide(el){ el.classList.remove('visible'); }
  function fmtBytes(b){ if(b<1024) return b+' B'; if(b<1048576) return (b/1024).toFixed(1)+' KB'; return (b/1048576).toFixed(2)+' MB'; }
  function setProgress(pct, status){
    document.getElementById('wz-prog-fill').style.width = pct+'%';
    document.getElementById('wz-prog-pct').textContent  = Math.round(pct)+'%';
    document.getElementById('wz-prog-status').textContent = status;
  }

  // Level selector
  document.querySelectorAll('.wz-level-card').forEach(function(card){
    card.addEventListener('click', function(){
      document.querySelectorAll('.wz-level-card').forEach(function(c){ c.classList.remove('active'); });
      card.classList.add('active');
      selectedLevel = card.dataset.level;
    });
  });

  // Drag & drop
  ['dragover','dragenter'].forEach(function(e){
    dropzone.addEventListener(e, function(ev){ ev.preventDefault(); dropzone.classList.add('drag-over'); });
  });
  ['dragleave','drop'].forEach(function(e){
    dropzone.addEventListener(e, function(ev){ ev.preventDefault(); dropzone.classList.remove('drag-over'); });
  });
  dropzone.addEventListener('drop', function(ev){
    var f = ev.dataTransfer.files[0];
    if (f && f.type === 'application/pdf') loadFile(f);
  });
  fileInput.addEventListener('change', function(){
    if (fileInput.files[0]) loadFile(fileInput.files[0]);
    fileInput.value='';
  });

  function loadFile(file){
    origFile = file;
    var reader = new FileReader();
    reader.onload = function(e){
      origBytes = e.target.result.slice(0); // keep a clean copy — PDF.js detaches the buffer it receives
      // Get page count
      var pdfjs = window['pdfjs-dist/build/pdf'] || pdfjsLib;
      pdfjs.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
      pdfjs.getDocument({ data: e.target.result }).promise.then(function(doc){
        totalPages = doc.numPages;
        document.getElementById('wz-fname').textContent = file.name;
        document.getElementById('wz-fmeta').textContent = fmtBytes(file.size) + ' \u00b7 ' + totalPages + ' page' + (totalPages!==1?'s':'');
        show(controlsEl);
        hide(statsEl); hide(actionsEl);
        progressWrap.classList.remove('visible');
      });
    };
    reader.readAsArrayBuffer(file);
  }

  document.getElementById('wz-compress').addEventListener('click', function(){
    if (!origBytes) return;
    var cfg = levelConfig[selectedLevel];
    progressWrap.classList.add('visible');
    hide(statsEl); hide(actionsEl);
    setProgress(0, 'Loading PDF…');

    var pdfjs = window['pdfjs-dist/build/pdf'] || pdfjsLib;
    pdfjs.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    pdfjs.getDocument({ data: origBytes.slice(0) }).promise.then(function(pdfDoc){
      totalPages = pdfDoc.numPages;
      var jsPDF  = window.jspdf.jsPDF;
      var outPdf = null;
      var canvas = document.createElement('canvas');
      var ctx    = canvas.getContext('2d');

      function processPage(pageNum){
        setProgress((pageNum-1)/totalPages*90, 'Rendering page '+pageNum+' of '+totalPages+'…');
        pdfDoc.getPage(pageNum).then(function(page){
          var vp  = page.getViewport({ scale: cfg.scale });
          canvas.width  = Math.round(vp.width);
          canvas.height = Math.round(vp.height);
          ctx.clearRect(0,0,canvas.width,canvas.height);
          ctx.fillStyle = '#ffffff';
          ctx.fillRect(0,0,canvas.width,canvas.height);
          page.render({ canvasContext: ctx, viewport: vp }).promise.then(function(){
            var imgData = canvas.toDataURL('image/jpeg', cfg.quality);
            var pw = canvas.width  * 25.4 / (cfg.scale * 96); // px → mm at 96dpi
            var ph = canvas.height * 25.4 / (cfg.scale * 96);
            var isLand = pw > ph;
            if (!outPdf){
              outPdf = new jsPDF({ orientation: isLand ? 'l' : 'p', unit: 'mm', format: [pw, ph] });
            } else {
              outPdf.addPage([pw, ph], isLand ? 'l' : 'p');
            }
            outPdf.addImage(imgData, 'JPEG', 0, 0, pw, ph);
            if (pageNum < totalPages){
              processPage(pageNum + 1);
            } else {
              finalize();
            }
          });
        });
      }

      function finalize(){
        setProgress(95, 'Finalising PDF…');
        setTimeout(function(){
          pdfBlob = outPdf.output('blob');
          setProgress(100, 'Done!');
          var saved = origFile.size - pdfBlob.size;
          var pct   = ((saved / origFile.size) * 100).toFixed(1);
          document.getElementById('wz-s-orig').textContent  = fmtBytes(origFile.size);
          document.getElementById('wz-s-comp').textContent  = fmtBytes(pdfBlob.size);
          document.getElementById('wz-s-saved').textContent = Math.abs(pct) + '% ' + (saved >= 0 ? 'saved' : 'larger');
          document.getElementById('wz-s-pages').textContent = totalPages;
          show(statsEl); show(actionsEl);
        }, 200);
      }

      processPage(1);
    }).catch(function(err){
      setProgress(0, 'Error: ' + err.message);
    });
  });

  document.getElementById('wz-download').addEventListener('click', function(){
    if (!pdfBlob) return;
    var name = (origFile.name.replace(/\.pdf$/i, '') || 'document') + '_compressed.pdf';
    var url  = URL.createObjectURL(pdfBlob);
    var a    = document.createElement('a');
    a.href=url; a.download=name; a.click();
    setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
  });

  document.getElementById('wz-reset').addEventListener('click', function(){
    origFile=null; origBytes=null; pdfBlob=null; totalPages=0;
    hide(controlsEl); progressWrap.classList.remove('visible');
    hide(statsEl); hide(actionsEl);
    fileInput.value='';
  });

}());
</script>

<?php get_footer(); ?>
