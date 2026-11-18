<?php
$lines=[];$subtotal=0;
foreach($cart_items??[] as $id=>$qty){$product=$this->store->product((int)$id);if($product){$product['cantidad']=(int)$qty;$product['linea']=(float)$product['precio']*(int)$qty;$lines[]=$product;$subtotal+=$product['linea'];}}
$shipping=(float)($shipping_cost??0);
$itemCount=array_sum(array_column($lines,'cantidad'));
?>
<section class="content checkout-page">
 <?php if(!$lines):?>
  <div class="checkout-empty"><span class="checkout-empty-icon"><?=kroma_icon('bag')?></span><p class="checkout-eyebrow">KROMA · TU COMPRA</p><h1>Tu carrito está vacío</h1><p>Cuando agregues productos, vas a poder revisar tu pedido y elegir cómo recibirlo.</p><a class="btn" href="?action=catalog">Explorar productos</a></div>
 <?php else:?>
  <header class="checkout-heading"><a class="checkout-back" href="?action=catalog">← Seguir comprando</a><div class="checkout-title"><span class="checkout-title-icon"><?=kroma_icon('bag')?></span><div><p class="checkout-eyebrow">KROMA · FINALIZAR PEDIDO</p><h1>Finalizá tu compra</h1><p>Revisá los productos y completá la entrega para confirmar el pedido.</p></div></div><ol class="checkout-steps" aria-label="Pasos de compra"><li class="is-complete"><span>1</span>Carrito</li><li class="is-current"><span>2</span>Entrega</li><li><span>3</span>Confirmación</li></ol></header>
  <div class="checkout-layout">
   <div class="checkout-main-column">
    <section class="checkout-card checkout-cart-card"><div class="checkout-card-heading"><div><span class="checkout-step-tag">PASO 1</span><h2>Productos del carrito</h2></div><span class="checkout-item-count"><?=$itemCount?> <?=($itemCount===1?'producto':'productos')?></span></div>
     <form method="post" action="?action=cart_update" class="checkout-cart-form"><?=csrf_field()?>
      <div class="checkout-edit-list">
       <?php foreach($lines as $product):?><article class="checkout-edit-row"><a class="checkout-thumb" href="?action=product&id=<?=(int)$product['id_producto']?>"><img data-auto-product-image data-product-id="<?=(int)$product['id_producto']?>" data-image-query="<?=h(trim(($product['marca']??'').' '.$product['nombre'].' '.($product['modelo']??'')))?>" src="<?=h(product_image_fallback($product))?>" data-local-image-fallback="<?=h(product_image_local_fallback($product))?>" alt="<?=h($product['nombre'])?>" loading="lazy"></a><div class="checkout-edit-info"><a href="?action=product&id=<?=(int)$product['id_producto']?>"><?=h($product['nombre'])?></a><span><?=h($product['marca']??'')?></span><strong><?=ars($product['precio'])?></strong></div><label class="checkout-quantity">Cantidad<input type="number" min="1" max="<?=max(1,(int)$product['stock'])?>" name="cantidades[<?=(int)$product['id_producto']?>]" value="<?=(int)$product['cantidad']?>" aria-label="Cantidad de <?=h($product['nombre'])?>"></label><button class="checkout-remove" type="submit" name="id" value="<?=(int)$product['id_producto']?>" formaction="?action=cart_remove" aria-label="Quitar <?=h($product['nombre'])?>">Quitar</button></article><?php endforeach?>
      </div>
      <div class="checkout-cart-actions"><a href="?action=catalog">＋ Agregar más productos</a></div>
     </form>
    </section>
    <form class="checkout-card checkout-form" id="checkout-form" method="post" action="?action=checkout" data-shipping-cost="<?=h($shipping)?>"><?=csrf_field()?><input type="hidden" name="_checkout_token" value="<?=h($_SESSION['_checkout_token']??'')?>">
     <div class="checkout-card-heading"><div><span class="checkout-step-tag">PASO 2</span><h2>Entrega y confirmación</h2></div><span class="checkout-security"><?=kroma_icon('shield')?>Compra protegida</span></div>
     <fieldset class="checkout-delivery"><legend>¿Cómo querés recibir tu compra?</legend><label class="delivery-option"><input type="radio" name="modalidad" value="retiro" checked><span class="delivery-option-icon"><?=kroma_icon('package')?></span><span><strong>Retiro en sucursal</strong><small>Sin cargo</small></span><span class="delivery-option-check"></span></label><label class="delivery-option"><input type="radio" name="modalidad" value="envio"><span class="delivery-option-icon"><?=kroma_icon('truck')?></span><span><strong>Envío a domicilio</strong><small><?=ars($shipping)?></small></span><span class="delivery-option-check"></span></label></fieldset>
     <div class="checkout-address" id="direccion-wrap" hidden><label for="direccion">Dirección de entrega</label><textarea id="direccion" name="direccion" rows="3" placeholder="Calle, número, ciudad y provincia" disabled></textarea></div>
     <section class="checkout-payment" aria-labelledby="checkout-payment-title"><div class="checkout-payment-heading"><span class="checkout-payment-icon"><?=kroma_icon('check')?></span><div><h3 id="checkout-payment-title">Datos de pago</h3><p>Completá la información para confirmar tu pedido.</p></div></div>
      <label class="checkout-field-label" for="checkout-email">Correo para el pedido</label><input class="checkout-input" id="checkout-email" type="email" value="<?=h(Auth::user()['email']??'')?>" readonly required autocomplete="email" aria-describedby="checkout-email-help"><small class="checkout-field-help" id="checkout-email-help">Este es el correo de tu cuenta. Podés modificarlo desde Mi cuenta.</small>
      <label class="checkout-field-label" for="checkout-dni">DNI del comprador</label><input class="checkout-input" id="checkout-dni" name="dni" type="text" value="<?=h($identity['dni']??'')?>" inputmode="numeric" maxlength="10" pattern="[0-9]{7,8}|[0-9]{1,2}\.[0-9]{3}\.[0-9]{3}" placeholder="Ej.: 12.345.678" required aria-describedby="checkout-dni-help"><small class="checkout-field-help" id="checkout-dni-help">DNI argentino de 7 u 8 números; podés escribirlo con puntos o sin ellos.</small>
      <label class="checkout-field-label" for="checkout-full-name">Nombre completo del comprador</label><input class="checkout-input" id="checkout-full-name" name="nombre_completo" value="<?=h(trim(($identity['nombre']??'').' '.($identity['apellido']??'')))?>" maxlength="160" autocomplete="name" required>
      <div class="checkout-billing-grid"><div><label class="checkout-field-label" for="checkout-city">Ciudad</label><input class="checkout-input" id="checkout-city" name="ciudad" maxlength="100" autocomplete="address-level2" required></div><div><label class="checkout-field-label" for="checkout-postal">Código postal</label><input class="checkout-input" id="checkout-postal" name="codigo_postal" maxlength="10" pattern="[A-Za-z0-9 -]{3,10}" autocomplete="postal-code" placeholder="Ej.: 1425 o C1425ABC" required></div></div>
      <div class="checkout-demo-notice"><strong>Pago de demostración</strong><span>No ingreses datos reales de tarjeta. Elegí el resultado que querés registrar para esta prueba.</span></div>
      <div class="checkout-demo-result"><label for="checkout-demo-result">Resultado de la simulación</label><select class="checkout-input" id="checkout-demo-result" name="pago" required><option value="aprobado" selected>Compra aprobada</option><option value="rechazado">Pago rechazado</option></select></div>
     </section>
     <button class="checkout-confirm" type="submit"><?=kroma_icon('check')?>Confirmar pedido<span aria-hidden="true">→</span></button>
     <p class="checkout-disclaimer">Revisá los datos antes de confirmar el pedido.</p>
    </form>
   </div>
   <aside class="checkout-summary" aria-label="Resumen de la compra"><div class="checkout-summary-heading"><?=kroma_icon('bag')?><div><h2>Resumen de tu compra</h2><p><?=$itemCount?> <?=($itemCount===1?'producto':'productos')?></p></div></div>
    <div class="checkout-summary-items"><?php foreach($lines as $product):?><article class="checkout-summary-item"><span class="checkout-summary-thumb"><img data-auto-product-image data-product-id="<?=(int)$product['id_producto']?>" data-image-query="<?=h(trim(($product['marca']??'').' '.$product['nombre'].' '.($product['modelo']??'')))?>" src="<?=h(product_image_fallback($product))?>" data-local-image-fallback="<?=h(product_image_local_fallback($product))?>" alt="" loading="lazy"></span><span class="checkout-summary-name"><strong><?=h($product['nombre'])?></strong><small>Cantidad: <?=(int)$product['cantidad']?></small></span><strong class="checkout-line-total"><?=ars($product['linea'])?></strong></article><?php endforeach?></div>
    <div class="checkout-totals"><div><span>Subtotal</span><strong><?=ars($subtotal)?></strong></div><div class="checkout-shipping-line" data-shipping-line><span>Envío a domicilio</span><strong><?=ars(0)?></strong></div><div class="checkout-total"><span>Total a pagar</span><strong data-checkout-total data-subtotal="<?=h($subtotal)?>"><?=ars($subtotal)?></strong></div></div>
    <div class="checkout-assurances"><span><?=kroma_icon('shield')?>Compra segura</span><span><?=kroma_icon('truck')?>Envíos a todo el país</span><span><?=kroma_icon('info')?>Soporte Kroma</span></div>
   </aside>
  </div>
 <?php endif?>
</section>
