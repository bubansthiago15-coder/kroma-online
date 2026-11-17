<?php $selectedCategory=$filters['categoria']??'';?>
<section class="content catalog-layout">
 <aside class="catalog-side">
  <h2>Categorías</h2>
  <a class="side-link <?=!$selectedCategory?'active':''?>" href="?action=catalog"><?=kroma_icon('grid','side-icon-svg')?> <span>Todos los productos</span></a>
  <?php foreach($cats as $c):if($c['slug']==='audio-video')continue;?>
   <a class="side-link <?=$selectedCategory===$c['slug']?'active':''?>" href="?action=catalog&categoria=<?=h($c['slug'])?>"><span class="side-icon"><?=kroma_icon(category_icon_name($c['slug']))?></span><span><?=h($c['nombre'])?></span></a>
  <?php endforeach?>
 </aside>
 <div class="catalog-main">
  <div class="catalog-title"><div><p class="eyebrow">KROMA / CATÁLOGO</p><h1><?=!empty($filters['tipo'])?($filters['tipo']==='ofertas'?'Ofertas':'Novedades'):($selectedCategory?htmlspecialchars($cats[array_search($selectedCategory,array_column($cats,'slug'))]['nombre']??'Catálogo'):'Todos los productos')?></h1><p class="muted"><?=$total?> productos para explorar</p></div></div>
  <form class="filter" method="get"><input type="hidden" name="action" value="catalog"><input name="q" placeholder="Buscar por producto, marca o modelo" value="<?=h($filters['q']??'')?>"><select name="orden" aria-label="Ordenar por"><option value="novedad">Ordenar por: Más recientes</option><option value="precio" <?=($filters['orden']??'')==='precio'?'selected':''?>>Menor precio</option><option value="precio_desc" <?=($filters['orden']??'')==='precio_desc'?'selected':''?>>Mayor precio</option></select><?php foreach(['categoria','condicion','tipo'] as $key):if(!empty($filters[$key])):?><input type="hidden" name="<?=$key?>" value="<?=h($filters[$key])?>"><?php endif;endforeach?><button class="btn">Aplicar filtros</button></form>
  <?php if($products):?>
   <div class="grid"><?php foreach($products as $p)product_card($p)?></div><p class="muted catalog-count">Mostrando <?=count($products)?> de <?=$total?> productos</p>
   <?php $pages=(int)ceil($total/12);if($pages>1):?><nav aria-label="Paginación del catálogo" class="filter pagination"><?php for($n=1;$n<=$pages;$n++):$params=$_GET;$params['pagina']=$n;?><a class="pill" <?=($n===$page_num)?'aria-current="page"':''?> href="?<?=h(http_build_query($params))?>"> <?=$n?> </a><?php endfor?></nav><?php endif?>
  <?php else:?>
   <div class="empty"><h2>No encontramos productos</h2><p>Probá con otros filtros o términos.</p><a class="btn" href="?action=catalog">Ver todo el catálogo</a></div>
  <?php endif?>
 </div>
</section>
