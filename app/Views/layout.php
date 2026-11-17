<?php
$headerUser=Auth::user();
$avatarInitials=strtoupper(mb_substr((string)($headerUser['nombre']??'K'),0,1,'UTF-8').mb_substr((string)($headerUser['apellido']??''),0,1,'UTF-8'));
$navHome=$name==='home';$navCategories=in_array($name,['catalog','product'],true);$navSupport=$name==='contact'||($name==='admin'&&($_GET['section']??'')==='soporte');
$title=['home'=>'Inicio','catalog'=>'Catálogo','product'=>'Producto','login'=>'Ingresar','register'=>'Crear cuenta','checkout'=>'Carrito y compra','account'=>'Mi cuenta','orders'=>'Mis pedidos','order'=>'Detalle del pedido','admin'=>'Administración','stock'=>'Inventario','contact'=>'Soporte','notfound'=>'No encontrado'][$name]??'Kroma';
$cats=$this->store->categories();$flash=$_SESSION['flash']??null;unset($_SESSION['flash']);
function h(mixed $v): string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function ars(mixed $n): string{return '$ '.number_format((float)$n,0,',','.');}
function kroma_icon(string $name,string $class=''): string {
    $paths=[
        'laptop'=>'<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M2 20h20l-2-3H4l-2 3Z"/>',
        'desktop'=>'<rect x="3" y="4" width="13" height="12" rx="2"/><path d="M9.5 16v4m-3 0h6M19 8h2v12h-4V8h2Z"/>',
        'chip'=>'<rect x="6" y="6" width="12" height="12" rx="2"/><path d="M9 9h6v6H9zM9 2v4m6-4v4M9 18v4m6-4v4M2 9h4m-4 6h4m12-6h4m-4 6h4"/>',
        'keyboard'=>'<rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M6 9h.01M9 9h.01M12 9h.01M15 9h.01M18 9h.01M6 12h.01M9 12h.01M12 12h.01M15 12h.01M18 12h.01M8 15h8"/>',
        'plug'=>'<path d="M8 3v5m8-5v5M6 8h12v3a6 6 0 0 1-6 6v4m-4-13v3a6 6 0 0 0 4 5.7"/>',
        'router'=>'<rect x="3" y="11" width="18" height="9" rx="2"/><path d="M7 11V7m10 4V7M6 15h.01M10 15h.01M14 15h.01M18 15h.01M8 5a6 6 0 0 1 8 0"/>',
        'monitor'=>'<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M12 17v4m-4 0h8"/>',
        'gamepad'=>'<path d="M7 9h10a4 4 0 0 1 3.9 3.1l1 4.2a2.5 2.5 0 0 1-4.1 2.4L15 16H9l-2.8 2.7a2.5 2.5 0 0 1-4.1-2.4l1-4.2A4 4 0 0 1 7 9Z"/><path d="M7 12v4m-2-2h4m7-1h.01M18 15h.01"/>',
        'refresh'=>'<path d="M20 7v5h-5M4 17v-5h5"/><path d="M5.6 9A7 7 0 0 1 18 6l2 2M4 16l2 2a7 7 0 0 0 12.4-3"/>',
        'grid'=>'<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'info'=>'<circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/>',
        'check'=>'<path d="m5 12 4 4L19 6"/>',
        'shield'=>'<path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/>',
        'truck'=>'<path d="M3 6h11v12H3zM14 10h4l3 3v5h-7z"/><circle cx="7.5" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/>',
        'drive'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 15h.01M11 15h.01M15 15h2"/>',
        'heart'=>'<path d="M20.8 8.8c0 5.2-8.8 10-8.8 10s-8.8-4.8-8.8-10A4.7 4.7 0 0 1 12 6.1a4.7 4.7 0 0 1 8.8 2.7Z"/>',
        'tag'=>'<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V4h9l8.6 6.6a2 2 0 0 1 0 2.8Z"/><circle cx="7.5" cy="8" r="1"/>',
        'star'=>'<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>',
        'money'=>'<path d="M12 2v20M17 6.5C17 5.1 15 4 12 4S7 5 7 7s1.6 3 5 4 5 2 5 4-2 4-5 4-5-1.1-5-2.5"/>',
        'calendar'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18m-13 4h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01"/>',
        'package'=>'<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="M3 8v8l9 5 9-5V8M12 13v8m-4-15 9 5"/>',
        'settings'=>'<path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/><path d="m19.4 15 .1.1 1.4 1.1-1.4 2.4-1.8-.6a8 8 0 0 1-1.6.9l-.3 1.9h-2.8l-.3-1.9a8 8 0 0 1-1.6-.9l-1.8.6-1.4-2.4 1.4-1.1a7 7 0 0 1 0-1.9l-1.4-1.1 1.4-2.4 1.8.6a8 8 0 0 1 1.6-.9l.3-1.9h2.8l.3 1.9a8 8 0 0 1 1.6.9l1.8-.6 1.4 2.4-1.4 1.1a7 7 0 0 1 0 1.8Z"/>',
        'bag'=>'<path d="M5 8h14l1 13H4L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/>',
        'search'=>'<circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/>',
        'edit'=>'<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>',
        'chevron'=>'<path d="m7 10 5 5 5-5"/>',
        'user'=>'<circle cx="12" cy="8" r="3.5"/><path d="M4.5 21a7.5 7.5 0 0 1 15 0"/>',
        'mail'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>',
        'message'=>'<path d="M5 5.5h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H10l-5 3v-13a2 2 0 0 1 2-2Z"/><path d="M9 10h6m-6 3h4"/>',
        'send'=>'<path d="m21 3-7.4 18-3.8-7.2L3 10.1 21 3Z"/><path d="m9.8 13.8 4.5-4.5"/>',
    ];
    return '<svg class="kroma-icon'.($class!==''?' '.h($class):'').'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'.($paths[$name]??$paths['info']).'</svg>';
}
function category_icon_name(string $category): string {
    return match($category){'portatiles','Equipos portátiles'=>'laptop','pc-escritorio','PC de escritorio'=>'desktop','componentes','Componentes informáticos'=>'chip','perifericos','Periféricos'=>'keyboard','accesorios','Accesorios y movilidad'=>'plug','redes','Redes y conectividad'=>'router','monitores','Monitores y pantallas'=>'monitor','gaming','Gaming'=>'gamepad','usados','Usados y reacondicionados'=>'refresh',default=>'grid'};
}
function spec_icon_name(string $attribute): string {
    $attribute=mb_strtolower($attribute,'UTF-8');
    if(str_contains($attribute,'memoria')||str_contains($attribute,'procesador')||str_contains($attribute,'ram'))return 'chip';
    if(str_contains($attribute,'almacen')||str_contains($attribute,'disco')||str_contains($attribute,'ssd'))return 'drive';
    if(str_contains($attribute,'garant'))return 'shield';
    if(str_contains($attribute,'pantalla')||str_contains($attribute,'resolución'))return 'monitor';
    return 'info';
}
function order_status_label(mixed $status): string {
    return match((string)$status) {
        'confirmado' => 'Confirmado',
        'cancelado' => 'Cancelado',
        'preparando' => 'En preparación',
        'listo_retiro' => 'Listo para retirar',
        'despachado' => 'Despachado',
        'entregado' => 'Entregado',
        default => ucfirst(str_replace('_', ' ', (string)$status)),
    };
}
function delivery_mode_label(mixed $mode): string {
    return match((string)$mode) {
        'retiro' => 'Retiro en sucursal',
        'envio' => 'Envío a domicilio',
        default => ucfirst((string)$mode),
    };
}
function product_condition_label(mixed $condition): string {
    return match((string)$condition) {
        'nuevo' => 'Nuevo',
        'usado' => 'Usado',
        'reacondicionado' => 'Reacondicionado',
        default => ucfirst((string)$condition),
    };
}
function csrf_field(): string{return '<input type="hidden" name="_csrf" value="'.h($_SESSION['_csrf']??'').'">';}
function product_image_fallback(array $p): string {
    if (!empty($p['imagen']) && !str_contains((string)$p['imagen'], 'product-placeholder.svg')) {$image=(string)$p['imagen'];return preg_match('~^https?://~i',$image)?$image:implode('/',array_map('rawurlencode',explode('/',$image)));}
    if (!empty($p['slug'])) return 'assets/images/products/'.rawurlencode((string)$p['slug']).'.svg';
    $images = ['Equipos portátiles'=>'Equipos portatiles.webp','PC de escritorio'=>'PC de escritorio.webp','Componentes informáticos'=>'Tarjeta Grafica.jpg','Periféricos'=>'Periféricos.jpg','Accesorios y movilidad'=>'Accesorios.jpg','Redes y conectividad'=>'Redes.jpg','Monitores y pantallas'=>'Monitores.jpg','Audio y video'=>'Accesorios.jpg','Gaming'=>'Gaming.jpg','Usados y reacondicionados'=>'Recondicionados.jpg'];
    return 'assets/images/'.implode('/',array_map('rawurlencode',explode('/',($images[$p['categoria']??'']??'Equipos portatiles.webp'))));
}
function product_image_local_fallback(array $p): string {
    return 'assets/images/products/'.rawurlencode((string)($p['slug']??'producto')).'.svg';
}
function product_card(array $p): void { $kindMap=['Equipos portátiles'=>'portable','PC de escritorio'=>'desktop','Componentes informáticos'=>'component','Periféricos'=>'peripheral','Accesorios y movilidad'=>'accessory','Redes y conectividad'=>'network','Monitores y pantallas'=>'monitor','Audio y video'=>'audio','Gaming'=>'gaming','Usados y reacondicionados'=>'used'];$category=(string)($p['categoria']??'Tecnología');$kind=$kindMap[$category]??'portable';$available=(int)($p['stock']??0)>0;$needsPhoto=empty($p['imagen'])||str_ends_with(strtolower((string)$p['imagen']),'.svg')||str_contains((string)$p['imagen'],'product-placeholder.svg'); ?><article class="product" data-kind="<?=$kind?>"><a href="?action=product&id=<?= (int)$p['id_producto'] ?>" aria-label="Ver <?=h($p['nombre'])?>"><div class="placeholder product-photo" aria-hidden="true"><img data-auto-product-image data-product-id="<?=(int)$p['id_producto']?>" data-image-query="<?=h(trim(($p['marca']??'').' '.($p['nombre']??'').' '.($p['modelo']??'').' '.$category))?>" src="<?=h(product_image_fallback($p))?>" data-local-image-fallback="<?=h(product_image_local_fallback($p))?>" data-image-fallback="<?=$needsPhoto?'1':'0'?>" alt=""></div></a><div class="product-meta"><span class="category-mark"><?=kroma_icon(category_icon_name($category))?><?=h($category)?></span><span><?=h($p['marca']??'Kroma')?></span></div><h3><?=h($p['nombre'])?></h3><?php if(!empty($p['precio_anterior'])&&(float)$p['precio']<(float)$p['precio_anterior']):?><span class="meta"><del><?=ars($p['precio_anterior'])?></del> <span class="badge">Oferta</span></span><?php endif?><p class="product-summary"><?=kroma_icon('info','description-icon')?><span><?=h($p['descripcion']??'')?></span></p><div class="price"><?=ars($p['precio'])?></div><div class="product-stock <?=$available?'is-available':'is-unavailable'?>"><?=kroma_icon($available?'check':'info')?><?=$available?'Disponible':'Agotado'?> <span>·</span> <?=h(product_condition_label($p['condicion']??''))?></div><?php if((Auth::user()["rol"]??"")!=="administrador"):?><div class="product-actions"><a class="btn" href="?action=product&id=<?=(int)$p['id_producto']?>">Ver producto</a><form method="post" action="?action=cart_add"><?=csrf_field()?><input type="hidden" name="id" value="<?=(int)$p['id_producto']?>"><button class="lightbtn" <?=((int)($p['stock']??0)<=Cart::quantity((int)$p['id_producto']))?'disabled':''?>>＋ Carrito</button></form></div><?php endif?></article><?php }
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="description" content="Kroma, tecnología que te conecta."><title><?=h($title)?> | Kroma</title><link rel="stylesheet" href="assets/css/app.css"><link rel="stylesheet" href="assets/css/theme.css"><link rel="stylesheet" href="assets/css/surfaces.css?v=20261113-auth-logo-v1"><link rel="stylesheet" href="assets/css/brand.css?v=20261117-checkout-simulation-v1"></head><body><header class="top"><div class="wrap"><div class="bar"><a class="logo" href="?" aria-label="Kroma, inicio"><img src="assets/images/kroma-logo-actual.svg?v=20261112b" alt="Kroma"></a><form class="search" action="" method="get"><input type="hidden" name="action" value="catalog"><input name="q" aria-label="Buscar" placeholder="Buscar productos, marcas y modelos…" value="<?=h($_GET['q']??'')?>"><button>Buscar</button></form><div class="quick"><?php if($headerUser):?><div class="account-menu"><button class="account-menu-trigger" type="button" aria-expanded="false" aria-controls="header-account-menu"><span class="account-avatar" aria-hidden="true"><?=h($avatarInitials)?></span><span>Mi cuenta</span><?=kroma_icon('chevron','account-chevron')?></button><div class="account-menu-panel" id="header-account-menu" hidden><div class="account-menu-profile"><strong><?=h(trim(($headerUser['nombre']??'').' '.($headerUser['apellido']??'')))?></strong><span><?=h($headerUser['email']??'')?></span></div><a href="?action=account">Mi información</a><form method="post" action="?action=logout"><?=csrf_field()?><button type="submit">Cerrar sesión</button></form></div></div><?php if(($headerUser['rol']??'')!=='administrador'):?><a class="cart-link" href="?action=checkout" aria-label="Carrito, <?=isset($this->store)?$this->store->cartCount():0?> productos">🛒 <?=isset($this->store)?$this->store->cartCount():0?></a><?php endif?><?php else:?><a href="?action=login">Ingresar</a><a class="signup-link" href="?action=register">Crear cuenta</a><?php endif?></div></div><nav aria-label="Navegación principal" class="<?=($headerUser['rol']??'')==='administrador'?'admin-navigation':'store-navigation'?>"><?php if(($headerUser['rol']??'')==='administrador'):?><a href="?" <?php if($navHome):?>class="active" aria-current="page"<?php endif?>>Inicio</a><a href="?action=admin#categorias-admin">Categorías</a><a href="?action=admin#panel-general">Panel de administración</a><a href="?action=admin#productos-existentes">Productos existentes</a><a href="?action=admin#pedidos">Pedidos recientes</a><a href="?action=admin#soporte">Soporte</a><?php else:?><a href="?" <?php if($navHome):?>class="active" aria-current="page"<?php endif?>>Inicio</a><a href="?action=catalog" <?php if($navCategories):?>class="active" aria-current="page"<?php endif?>>Categorías</a><a href="?action=contact" <?php if($navSupport):?>class="active" aria-current="page"<?php endif?>>Soporte</a><?php endif?></nav></div></header><main class="<?php echo $name === 'home' ? 'home-main' : ($name === 'contact' ? 'support-main' : 'wrap'); ?>"><?php if($flash&&!($name==='admin'&&!empty($createProductError))):?><div class="flash" role="status"><?=h($flash)?></div><?php endif?><?php if(is_file($view))require $view; ?></main><footer class="footer"><div class="wrap"><a class="footer-logo" href="?" aria-label="Kroma, inicio"><img src="assets/images/kroma-logo-actual.svg?v=20261112b" alt="Kroma"></a><strong>Kroma · Tecnología que te conecta</strong><p>Catálogo, pedidos y soporte en un mismo lugar.</p><div class="footer-socials" aria-label="Redes sociales y contacto"><span>Instagram · @kroma.tecnologia</span><span aria-hidden="true">·</span><span>Facebook · Kroma Tecnología</span><span aria-hidden="true">·</span><span>contacto@kroma.com.ar</span></div></div></footer><script src="assets/js/app.js?v=20261116-async-toasts-v1" defer></script><script src="assets/js/hero-carousel.js?v=20261028-hero-carousel-v3" defer></script><script src="assets/js/admin-products.js?v=20261116-admin-products-v1" defer></script><script src="assets/js/checkout.js?v=20261117-demo-payment-v1" defer></script></body></html>
