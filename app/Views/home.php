<?php
$categoryCovers=[
    'portatiles'=>'assets/images/categoria-portatiles.webp',
    'pc-escritorio'=>'assets/images/categoria-pc-escritorio.webp',
    'componentes'=>'assets/images/categoria-componentes.webp',
    'perifericos'=>'assets/images/categoria-perifericos.webp',
    'accesorios'=>'assets/images/categoria-accesorios.webp',
    'redes'=>'assets/images/categoria-redes.webp',
    'monitores'=>'assets/images/categoria-monitores.webp',
    'gaming'=>'assets/images/categoria-gaming.webp',
    'usados'=>'assets/images/categoria-usados.webp',
];
?>
<section class="hero-carousel" data-hero-carousel aria-label="Productos destacados">
 <div class="hero-slides" aria-live="polite">
  <article class="hero-slide is-active" aria-hidden="false">
   <div class="hero-copy"><span class="hero-badge">EQUIPOS PORTÁTILES</span><h1>Potencia que te acompaña</h1><p>Laptops, notebooks y más. Rendimiento, diseño y la mejor tecnología.</p><a class="btn" href="?action=catalog&categoria=portatiles">Ver equipos <span aria-hidden="true">→</span></a></div>
   <div class="hero-visual"><img src="assets/images/categoria-portatiles.webp" alt="Notebook iluminada con tonos cálidos" fetchpriority="high"></div>
  </article>
  <article class="hero-slide" aria-hidden="true" inert>
   <div class="hero-copy"><span class="hero-badge">GAMING</span><h2>Jugá sin límites</h2><p>Equipos potentes, gráficos envolventes y luces para llevar cada partida al máximo.</p><a class="btn" href="?action=catalog&categoria=gaming">Ver gaming <span aria-hidden="true">→</span></a></div>
   <div class="hero-visual"><img src="assets/images/categoria-gaming.webp" alt="Interior de una PC gamer con iluminación RGB" loading="lazy"></div>
  </article>
  <article class="hero-slide" aria-hidden="true" inert>
   <div class="hero-copy"><span class="hero-badge">PC DE ESCRITORIO</span><h2>Tu espacio, a otro nivel</h2><p>Computadoras y accesorios para trabajar, crear y disfrutar todos los días.</p><a class="btn" href="?action=catalog&categoria=pc-escritorio">Ver computadoras <span aria-hidden="true">→</span></a></div>
   <div class="hero-visual"><img src="assets/images/categoria-pc-escritorio.webp" alt="Computadora de escritorio y periféricos sobre un escritorio" loading="lazy"></div>
  </article>
 </div>
 <button class="hero-control hero-prev" type="button" aria-label="Imagen anterior">‹</button><button class="hero-control hero-next" type="button" aria-label="Imagen siguiente">›</button>
 <div class="hero-dots" role="group" aria-label="Elegir imagen"><button type="button" aria-label="Mostrar imagen 1" aria-current="true"></button><button type="button" aria-label="Mostrar imagen 2" aria-current="false"></button><button type="button" aria-label="Mostrar imagen 3" aria-current="false"></button></div>
</section>
<section class="section">
 <div class="section-heading"><h2>Explorá por categoría</h2><a href="?action=catalog">Ver todas →</a></div>
 <div class="category-grid">
  <?php foreach($cats as $c):if($c['slug']==='audio-video')continue;?>
   <a class="category-tile<?php if(isset($categoryCovers[$c['slug']])):?> has-cover<?php endif?>" href="?action=catalog&categoria=<?=h($c['slug'])?>">
    <?php if(isset($categoryCovers[$c['slug']])):?><img class="category-cover" src="<?=h($categoryCovers[$c['slug']])?>" alt="" aria-hidden="true"><?php endif?>
    <span><?=h($c['nombre'])?></span>
   </a>
  <?php endforeach?>
 </div>
</section>
<section class="section">
 <div class="section-heading"><h2>Productos destacados</h2><a href="?action=catalog">Ver todos →</a></div>
 <div class="grid"><?php foreach(array_slice($products,0,6) as $p)product_card($p)?></div>
</section>
