<!doctype html>
<html lang="nl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Scroll Achtergrond Foto</title>
<style>
  :root{
    --max-scale: 2.4;
  }

  html, body {
    margin: 0;
    padding: 0;
    font-family: Arial, system-ui;
    background: #f4f4f4;
    color: #222;
  }

  header {
    padding: 40px 20px;
    text-align: center;
  }

  header h1 { margin:0 0 8px; }
  header p { margin:0; color:#555; }

  .grow-section {
    height: 180vh;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    background-image: url('https://picsum.photos/id/1015/1200/800');
    background-size: 60vw 40vh; /* begingrootte */
    background-position: center center;
    background-repeat: no-repeat;
    border-radius: 12px;
  }

  .after {
    max-width: 900px;
    margin: 0 auto;
    padding: 60px 20px;
    color: #555;
  }
</style>
</head>
<body>

<header>
  <h1>Scroll om de achtergrondfoto groter te maken</h1>
  <p>De achtergrondfoto begint op 60vw × 40vh en groeit tijdens scroll.</p>
</header>

<section class="grow-section" id="growSection"></section>

<section class="after">
  <h2>Verder omlaag</h2>
  <p>Hier is extra content zodat je ziet dat de achtergrondfoto groter wordt terwijl je scrollt.</p>
</section>

<script>
(function(){
  const section = document.getElementById('growSection');
  const maxScale = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--max-scale')) || 2.4;

  let ticking = false;

  function easeInOut(t){
    return t < 0.5 ? 4*t*t*t : 1 - Math.pow(-2*t + 2, 3)/2;
  }

  function update(){
    const rect = section.getBoundingClientRect();
    const vh = window.innerHeight;
    const total = rect.height + vh;
    let progress = (vh - rect.top) / total;
    progress = Math.max(0, Math.min(1, progress));

    const scale = 1 + (maxScale - 1) * easeInOut(progress);
    section.style.backgroundSize = `${60 * scale}vw ${40 * scale}vh`;

    ticking = false;
  }

  function onScroll(){
    if(!ticking){
      requestAnimationFrame(update);
      ticking = true;
    }
  }

  window.addEventListener('scroll', onScroll, {passive:true});
  window.addEventListener('resize', onScroll);
  onScroll();
})();
</script>

</body>
</html>