<?php $p=$product;$kind=match($p['categoria']){'PC de escritorio'=>'desktop','Componentes informáticos'=>'component','Periféricos'=>'peripheral','Accesorios y movilidad'=>'accessory','Redes y conectividad'=>'network','Monitores y pantallas'=>'monitor','Audio y video'=>'audio','Gaming'=>'gaming','Usados y reacondicionados'=>'used',default=>'portable'};$cartQuantity=(int)($_SESSION['cart'][$p['id_producto']]??0);$availableToAdd=max(0,(int)$p['stock']-$cartQuantity);$inStock=(int)$p['stock']>0;?>
<section class="content">
 <p class="crumb"><a href="?">Inicio</a> &nbsp;›&nbsp; <a href="?action=catalog">Catálogo</a> &nbsp;›&nbsp; <?=h($p['categoria'])?> &nbsp;›&nbsp; <?=h($p['nombre'])?></p>
 <div class="detail">
  <div class="detail-media" data-kind="<?=$kind?>">
   <div class="placeholder product-photo"><?php $needsPhoto=empty($p['imagen'])||str_ends_with(strtolower((string)$p['imagen']),'.svg')||str_contains((string)$p['imagen'],'product-placeholder.svg');?><img data-auto-product-image data-product-id="<?=(int)$p['id_producto']?>" data-image-query="<?=h(trim(($p['marca']??'').' '.($p['nombre']??'').' '.($p['modelo']??'').' '.($p['categoria']??'')))?>" src="<?=h(product_image_fallback($p))?>" data-local-image-fallback="<?=h(product_image_local_fallback($p))?>" data-image-fallback="<?=$needsPhoto?'1':'0'?>" alt="<?=h($p['nombre'])?>"></div>
  </div>
  <div class="detail-info">
   <div class="product-meta"><span class="category-mark"><?=kroma_icon(category_icon_name($p['categoria']))?><?=h($p['categoria'])?></span><span><?=h($p['marca'])?> · <?=h($p['modelo'])?></span></div>
   <h1><?=h($p['nombre'])?></h1>
   <div class="rating"><span class="unrated">Todavía no hay valoraciones de usuarios</span></div>
   <p class="detail-description"><?=kroma_icon('info')?><span><?=h($p['descripcion'])?></span></p>
   <?php if(!empty($p['precio_anterior'])&&(float)$p['precio']<(float)$p['precio_anterior']):?><p><del class="meta"><?=ars($p['precio_anterior'])?></del> <span class="badge">Oferta activa</span></p><?php endif?>
   <div class="price"><?=ars($p['precio'])?></div>
   <p class="detail-stock"><span class="badge"><?=h(product_condition_label($p['condicion']??''))?></span><span class="stock-state <?=$inStock?'is-available':'is-unavailable'?>"><?=kroma_icon($inStock?'check':'info')?><?=$inStock?'Disponible':'Agotado'?></span></p>
   <div class="trust-row"><span><?=kroma_icon('shield')?>Garantía <?= (int)$p['garantia_meses']?> meses</span><span><?=kroma_icon('check')?>Pago simulado seguro</span><span><?=kroma_icon('refresh')?>Precios de demostración</span></div>
   <?php if((Auth::user()["rol"]??"")!=="administrador"):?>
    <form method="post" action="?action=cart_add"><?=csrf_field()?><input type="hidden" name="id" value="<?=(int)$p['id_producto']?>"><label>Cantidad <input type="number" name="cantidad" value="1" min="1" max="<?=$availableToAdd?>" style="width:80px;padding:8px" <?=(!$availableToAdd?'disabled':'')?> required></label> <button class="btn" <?=(!$availableToAdd?'disabled':'')?>><?=$availableToAdd?'Agregar al carrito':'Sin unidades disponibles'?></button><?php if($cartQuantity>0):?><small class="cart-stock-hint">Ya tenés <?= $cartQuantity?> en el carrito; quedan <?= $availableToAdd?> para agregar.</small><?php endif?></form>
    <form method="post" action="?action=favorite"><?=csrf_field()?><input type="hidden" name="id" value="<?=(int)$p['id_producto']?>"><button class="lightbtn"><?=kroma_icon('heart')?> Guardar en favoritos</button></form>
   <?php endif?>
   <h3>Componentes y características</h3>
   <?php if($p['specs']):?><ul class="spec-list"><?php foreach($p['specs'] as $s):?><li><span class="spec-icon"><?=kroma_icon(spec_icon_name($s['atributo']))?></span><span><strong><?=h($s['atributo'])?></strong><br><?=h($s['atributo']==='Condición'?product_condition_label($s['valor']):$s['valor'])?> <?=h($s['unidad'])?></span></li><?php endforeach?></ul><?php else:?><p>Consultá las características del modelo antes de comprar.</p><?php endif?>
  </div>
 </div>
</section>
