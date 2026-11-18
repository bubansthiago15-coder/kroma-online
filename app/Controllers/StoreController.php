<?php
declare(strict_types=1);
final class StoreController {
 private Store $store;
 public function __construct(){ $this->store=new Store(); }
 public function handle(string $action):void {
  if($_SERVER['REQUEST_METHOD']==='POST'&&!in_array($action,['login','register'],true))$this->csrf();
  if((Auth::user()['rol']??'')==='administrador'&&in_array($action,['login','register'],true)){header('Location: ?action=admin');return;}
  if((Auth::user()['rol']??'')==='administrador'&&in_array($action,['cart_add','cart_update','cart_remove','checkout','addresses','address_add','favorites','orders','order','favorite'],true)){http_response_code(403);exit('Las cuentas de administrador no pueden usar las funciones de compra o cliente.');}
  $db=Database::connection();
  switch($action){
   case 'login':$this->login();return;
   case 'register':$this->register();return;
   case 'logout':$this->csrf();Auth::logout();header('Location: ?');return;
   case 'cart_add':
    Auth::requireCustomer();
    $id=(int)($_POST['id']??0);$quantity=filter_var($_POST['cantidad']??1,FILTER_VALIDATE_INT);
    try{if($quantity===false||$quantity<1)throw new RuntimeException('Ingresá una cantidad válida.');Cart::add($id,(int)$quantity);$_SESSION['flash']='Producto agregado al carrito.';}catch(PDOException $error){error_log((string)$error);$_SESSION['flash']='No se pudo actualizar el carrito. Intentá nuevamente.';}catch(RuntimeException $error){$_SESSION['flash']=$error->getMessage();}
    header('Location: '.($_SERVER['HTTP_REFERER']??'?action=catalog'));return;
   case 'cart_update':
    Auth::requireCustomer();
    $quantities=$_POST['cantidades']??[];$changed=is_array($quantities)?Cart::updateMany($quantities):false;
    $_SESSION['flash']=$changed?'Se ajustó el carrito para respetar el stock disponible.':'Cantidades actualizadas.';header('Location: ?action=checkout');return;
   case 'cart_remove':Auth::requireCustomer();Cart::remove((int)($_POST['id']??0));header('Location: ?action=checkout');return;
   case 'checkout':
    Auth::requireLogin();if($_SERVER['REQUEST_METHOD']==='POST'){$this->checkout();return;}
    $cartAdjusted=Cart::normalize();if($cartAdjusted)$_SESSION['flash']='Actualizamos el carrito para que ninguna cantidad supere el stock disponible.';
    $cartItems=Cart::items();$fee=$db->prepare("SELECT costo FROM configuracion_envio WHERE modalidad='envio' AND activo=1 ORDER BY id_configuracion DESC LIMIT 1");$fee->execute();$identity=$db->prepare('SELECT nombre,apellido,dni FROM usuarios WHERE id_usuario=?');$identity->execute([Auth::id()]);
    $_SESSION['_checkout_token']=bin2hex(random_bytes(32));$this->page('checkout',['shipping_cost'=>$fee->fetchColumn()?:0,'identity'=>$identity->fetch(),'cart_items'=>$cartItems]);return;
   case 'account':Auth::requireLogin();$s=$db->prepare('SELECT nombre,apellido,email,telefono,dni FROM usuarios WHERE id_usuario=?');$s->execute([Auth::id()]);$this->page('account',['profile'=>$s->fetch()]);return;
   case 'profile_save':Auth::requireLogin();$name=trim($_POST['nombre']??'');$last=trim($_POST['apellido']??'');$email=strtolower(trim($_POST['email']??''));$phone=trim($_POST['telefono']??'');$dni=preg_replace('/[. -]/','',trim($_POST['dni']??''));if($name===''||strlen($name)>100||strlen($last)>100||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($phone)>30||($dni!==''&&!preg_match('/^\\d{7,8}$/',$dni))){$_SESSION['flash']='Revisá el nombre, el correo, el teléfono y el DNI (7 u 8 números).';}else{try{$s=$db->prepare('UPDATE usuarios SET nombre=?,apellido=?,email=?,telefono=?,dni=? WHERE id_usuario=?');$s->execute([$name,$last,$email,$phone?:null,$dni?:null,Auth::id()]);$_SESSION['user']['nombre']=$name;$_SESSION['user']['apellido']=$last;$_SESSION['user']['email']=$email;$_SESSION['flash']='Perfil actualizado.';}catch(PDOException $e){$_SESSION['flash']=in_array($e->getCode(),['23000','23505'],true)?'Ese correo o DNI ya está asociado a otra cuenta.':'No se pudo actualizar el perfil.';}}header('Location: ?action=account');return;
   case 'addresses':Auth::requireLogin();$s=$db->prepare('SELECT * FROM direcciones WHERE id_usuario=? ORDER BY es_principal DESC,fecha_registro DESC');$s->execute([Auth::id()]);$this->page('addresses',['addresses'=>$s->fetchAll()]);return;
   case 'address_add':Auth::requireLogin();$required=['calle','numero','ciudad','provincia','codigo_postal'];foreach($required as $field){if(trim($_POST[$field]??'')===''){$_SESSION['flash']='Completá los campos obligatorios de la dirección.';header('Location: ?action=addresses');return;}}$s=$db->prepare('INSERT INTO direcciones(id_usuario,alias,calle,numero,piso_departamento,ciudad,provincia,codigo_postal,referencia) VALUES(?,?,?,?,?,?,?,?,?)');$s->execute([Auth::id(),trim($_POST['alias']??'Casa'),trim($_POST['calle']),trim($_POST['numero']),trim($_POST['piso_departamento']??''),trim($_POST['ciudad']),trim($_POST['provincia']),trim($_POST['codigo_postal']),trim($_POST['referencia']??'')]);$_SESSION['flash']='Dirección guardada.';header('Location: ?action=addresses');return;
   case 'favorites':Auth::requireLogin();$s=$db->prepare('SELECT p.*,c.nombre categoria,m.nombre marca,i.cantidad_disponible stock FROM favoritos f JOIN productos p USING(id_producto) JOIN categorias c USING(id_categoria) JOIN marcas m USING(id_marca) LEFT JOIN inventario i USING(id_producto) WHERE f.id_usuario=? ORDER BY f.fecha_agregado DESC');$s->execute([Auth::id()]);$this->page('favorites',['products'=>$s->fetchAll()]);return;
   case 'orders':Auth::requireLogin();$s=$db->prepare('SELECT * FROM pedidos WHERE id_usuario=? ORDER BY fecha_pedido DESC');$s->execute([Auth::id()]);$this->page('orders',['orders'=>$s->fetchAll()]);return;
   case 'order':Auth::requireLogin();$s=$db->prepare('SELECT * FROM pedidos WHERE numero_pedido=? AND id_usuario=?');$s->execute([$_GET['n']??'',Auth::id()]);$order=$s->fetch();if(!$order){http_response_code(404);$this->page('notfound');return;}$s=$db->prepare('SELECT dp.*,p.slug,p.modelo,m.nombre marca,c.nombre categoria,(SELECT ip.ruta_imagen FROM imagenes_producto ip WHERE ip.id_producto=dp.id_producto ORDER BY ip.es_principal DESC,ip.orden,ip.id_imagen LIMIT 1) imagen FROM detalle_pedido dp LEFT JOIN productos p USING(id_producto) LEFT JOIN marcas m USING(id_marca) LEFT JOIN categorias c USING(id_categoria) WHERE dp.id_pedido=?');$s->execute([$order['id_pedido']]);$this->page('order',['order'=>$order,'lines'=>$s->fetchAll()]);return;
   case 'favorite':Auth::requireLogin();$favoriteSql=Database::driver()==='pgsql'?'INSERT INTO favoritos(id_usuario,id_producto) VALUES(?,?) ON CONFLICT (id_usuario,id_producto) DO NOTHING':'INSERT IGNORE INTO favoritos(id_usuario,id_producto) VALUES(?,?)';$s=$db->prepare($favoriteSql);$s->execute([Auth::id(),(int)($_POST['id']??0)]);$_SESSION['flash']='Guardado en favoritos.';header('Location: '.($_SERVER['HTTP_REFERER']??'?'));return;
   case 'admin':Auth::requireAdmin();$this->adminPage($db);return;
   case 'admin_notification_read':Auth::requireAdmin();$noticeId=(int)($_POST['id']??0);if($noticeId>0){$s=$db->prepare('UPDATE notificaciones_administrador SET leida=1 WHERE id_notificacion=?');$s->execute([$noticeId]);}header('Location: ?action=admin#notificaciones');return;
   case 'admin_category_create':Auth::requireAdmin();$this->createCategory($db);return;
   case 'admin_category_save':Auth::requireAdmin();$this->saveCategory($db);return;
   case 'admin_create_product':Auth::requireAdmin();$this->createProduct($db);return;
   case 'admin_save_product':Auth::requireAdmin();$this->saveProduct($db);return;
   case 'admin_delete_product':Auth::requireAdmin();$this->deleteProduct($db,(int)($_POST['id']??0));return;
   case 'admin_stock_adjust':Auth::requireAdmin();$targetStock=filter_var($_POST['stock']??null,FILTER_VALIDATE_INT);if($targetStock===false){$_SESSION['flash']='Ingresá una cantidad de stock válida.';header('Location: ?action=admin#productos-existentes');return;}$this->adjustStock($db,(int)($_POST['id']??0),$targetStock,trim($_POST['motivo']??''));return;
   case 'admin_order_update':Auth::requireAdmin();$this->updateOrder($db,(int)($_POST['id']??0),(string)($_POST['estado']??''));return;
   case 'admin_support_status':Auth::requireAdmin();$this->updateSupportStatus($db,(int)($_POST['id']??0),(string)($_POST['estado']??''));return;
   case 'stock':Auth::requireAdmin();$this->page('stock',['inventory'=>$db->query('SELECT p.id_producto,p.nombre,p.condicion,i.cantidad_disponible,i.fecha_actualizacion FROM inventario i JOIN productos p USING(id_producto) ORDER BY i.cantidad_disponible,p.nombre')->fetchAll()]);return;
   case 'contact':if($_SERVER['REQUEST_METHOD']==='POST'){$this->submitSupport($db);return;}$this->page('contact');return;
   case 'notfound':http_response_code(404);$this->page('notfound');return;
   case 'product':$p=$this->store->product((int)($_GET['id']??0));if(!$p){http_response_code(404);$this->page('notfound');return;}$this->page('product',['product'=>$p]);return;
   case 'catalog':$f=['q'=>trim($_GET['q']??''),'categoria'=>$_GET['categoria']??'','condicion'=>$_GET['condicion']??'','orden'=>$_GET['orden']??'','solo_ofertas'=>($_GET['tipo']??'')==='ofertas','solo_novedades'=>($_GET['tipo']??'')==='novedades','pagina'=>max(1,(int)($_GET['pagina']??1))];$this->page('catalog',['products'=>$this->store->products($f),'total'=>$this->store->productCount($f),'page_num'=>$f['pagina'],'filters'=>$_GET]);return;
   default:$this->page('home',['products'=>$this->store->products([])]);
  }
 }
 private function adminPage(PDO $db):void{
  $stats=$db->query("SELECT COUNT(*) productos,(SELECT COUNT(*) FROM pedidos) pedidos,(SELECT COALESCE(SUM(total),0) FROM pedidos WHERE estado='confirmado') ventas FROM productos WHERE estado=1")->fetch();
  $orders=$db->query('SELECT p.*,u.nombre,u.apellido FROM pedidos p JOIN usuarios u USING(id_usuario) ORDER BY p.fecha_pedido DESC LIMIT 25')->fetchAll();
  $categories=$db->query('SELECT id_categoria,nombre,slug,descripcion FROM categorias WHERE estado=1 ORDER BY orden_visualizacion,nombre')->fetchAll();
  $brands=$db->query('SELECT id_marca,nombre FROM marcas WHERE estado=1 ORDER BY nombre')->fetchAll();
  $products=$db->query('SELECT p.*,c.nombre categoria,m.nombre marca,COALESCE(i.cantidad_disponible,0) stock,(SELECT ip.ruta_imagen FROM imagenes_producto ip WHERE ip.id_producto=p.id_producto ORDER BY ip.es_principal DESC,ip.id_imagen LIMIT 1) imagen FROM productos p JOIN categorias c USING(id_categoria) JOIN marcas m USING(id_marca) LEFT JOIN inventario i USING(id_producto) WHERE p.estado=1 ORDER BY p.fecha_actualizacion DESC')->fetchAll();
  $specQuery=$db->prepare('SELECT atributo,valor FROM especificaciones_producto WHERE id_producto=? ORDER BY id_especificacion');
  foreach($products as &$product){$specQuery->execute([(int)$product['id_producto']]);$product['specs_text']=implode("\n",array_map(static fn($row)=>$row['atributo'].': '.$row['valor'],$specQuery->fetchAll()));}
  unset($product);
  $supportMessages=$db->query("SELECT id_consulta,nombre,email,tipo,asunto,mensaje,estado,fecha_creacion FROM consultas_soporte ORDER BY CASE estado WHEN 'nuevo' THEN 1 WHEN 'en_revision' THEN 2 WHEN 'resuelto' THEN 3 ELSE 4 END,fecha_creacion DESC LIMIT 100")->fetchAll();
  $newSupportCount=(int)$db->query("SELECT COUNT(*) FROM consultas_soporte WHERE estado='nuevo'")->fetchColumn();
  $stockNotifications=$db->query('SELECT n.id_notificacion,n.id_producto,n.id_pedido,n.titulo,n.mensaje,n.fecha_creacion,p.nombre producto,p.modelo,o.numero_pedido FROM notificaciones_administrador n LEFT JOIN productos p USING(id_producto) LEFT JOIN pedidos o USING(id_pedido) WHERE n.leida=0 ORDER BY n.fecha_creacion DESC LIMIT 20')->fetchAll();
  $stockNotificationCount=(int)$db->query('SELECT COUNT(*) FROM notificaciones_administrador WHERE leida=0')->fetchColumn();
  $createProductDraft=$_SESSION['admin_create_draft']??[];unset($_SESSION['admin_create_draft']);$createProductError=$_SESSION['admin_create_error']??'';unset($_SESSION['admin_create_error']);
  $this->page('admin',['products'=>$products,'stats'=>$stats,'orders'=>$orders,'categories'=>$categories,'brands'=>$brands,'supportMessages'=>$supportMessages,'newSupportCount'=>$newSupportCount,'stockNotifications'=>$stockNotifications,'stockNotificationCount'=>$stockNotificationCount,'createProductDraft'=>$createProductDraft,'createProductError'=>$createProductError]);
 }
 private function categorySlug(string $name):string{$source=function_exists('iconv')?iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$name):$name;return trim(preg_replace('/[^a-z0-9]+/','-',strtolower((string)$source)),'-');}
 private function createCategory(PDO $db):void{$name=trim($_POST['nombre']??'');$description=trim($_POST['descripcion']??'');if($name===''||mb_strlen($name)>100||mb_strlen($description)>1000){$_SESSION['flash']='Ingresá un nombre de categoría (hasta 100 caracteres) y una descripción breve.';header('Location: ?action=admin#categorias-admin');return;}$slug=$this->categorySlug($name);if($slug==='')$slug='categoria-'.bin2hex(random_bytes(3));try{$s=$db->prepare('INSERT INTO categorias(nombre,slug,descripcion,estado) VALUES(?,?,?,1)');$s->execute([$name,$slug,$description?:null]);$_SESSION['flash']='Categoría creada correctamente.';}catch(PDOException){$_SESSION['flash']='Ya existe una categoría con ese nombre o no se pudo guardar.';}header('Location: ?action=admin#categorias-admin');}
 private function saveCategory(PDO $db):void{$id=(int)($_POST['id']??0);$name=trim($_POST['nombre']??'');$description=trim($_POST['descripcion']??'');if($id<1||$name===''||mb_strlen($name)>100||mb_strlen($description)>1000){$_SESSION['flash']='Revisá el nombre y la descripción de la categoría.';header('Location: ?action=admin#categorias-admin');return;}$slug=$this->categorySlug($name);if($slug==='')$slug='categoria-'.$id;try{$s=$db->prepare('UPDATE categorias SET nombre=?,slug=?,descripcion=? WHERE id_categoria=? AND estado=1');$s->execute([$name,$slug,$description?:null,$id]);$_SESSION['flash']=$s->rowCount()?'Categoría actualizada.':'No hubo cambios en la categoría.';}catch(PDOException){$_SESSION['flash']='Ya existe otra categoría con ese nombre.';}header('Location: ?action=admin#categorias-admin');}
 private function submitSupport(PDO $db):void{
  $name=trim($_POST['nombre']??'');$email=strtolower(trim($_POST['email']??''));$message=trim($_POST['mensaje']??'');$type='Consulta general';$subject=mb_substr($message,0,180);
  if($name===''||mb_strlen($name)>120||!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($email)>190||$message===''||mb_strlen($message)>5000){$_SESSION['flash']='Completá un nombre, un correo válido y tu mensaje (hasta 5000 caracteres).';header('Location: ?action=contact');return;}
  $statement=$db->prepare('INSERT INTO consultas_soporte(id_usuario,nombre,email,tipo,asunto,mensaje) VALUES(?,?,?,?,?,?)');
  $statement->execute([Auth::id(),$name,$email,$type,$subject,$message]);
  $_SESSION['flash']='Tu consulta quedó registrada. El equipo de Kroma podrá revisarla desde el panel de administración.';header('Location: ?action=contact');
 }
 private function updateSupportStatus(PDO $db,int $id,string $status):void{
  if($id<1||!in_array($status,['nuevo','en_revision','resuelto'],true)){$_SESSION['flash']='No se pudo actualizar el estado de la consulta.';header('Location: ?action=admin&section=soporte#soporte');return;}
  $statement=$db->prepare('UPDATE consultas_soporte SET estado=? WHERE id_consulta=?');$statement->execute([$status,$id]);
  $_SESSION['flash']='Estado de la consulta actualizado.';header('Location: ?action=admin&section=soporte#soporte');
 }
 private function csrf():void{if(!hash_equals($_SESSION['_csrf']??'',$_POST['_csrf']??'')){http_response_code(419);exit('La sesión venció. Actualizá la página e intentá nuevamente.');}}
 private function preserveProductDraft(string $message):void{$keys=['nombre','modelo','id_categoria','id_marca','precio','stock','condicion','garantia_meses','descripcion','especificaciones','publicado'];$draft=[];foreach($keys as $key){$value=$_POST[$key]??'';if(is_scalar($value))$draft[$key]=mb_substr((string)$value,0,5000);}$upload=$_FILES['imagen_archivo']??null;if($upload&&!empty($upload['name']))$draft['imagen_archivo_nombre']=mb_substr((string)$upload['name'],0,180);$_SESSION['admin_create_draft']=$draft;$_SESSION['admin_create_error']=$message;$_SESSION['flash']=$message;header('Location: ?action=admin#productos');}
 private function createProduct(PDO $db):void{
  $name=trim($_POST['nombre']??'');$model=trim($_POST['modelo']??'');$description=trim($_POST['descripcion']??'');$category=(int)($_POST['id_categoria']??0);$brand=(int)($_POST['id_marca']??0);$condition=(string)($_POST['condicion']??'nuevo');$price=filter_var($_POST['precio']??null,FILTER_VALIDATE_FLOAT);$stock=filter_var($_POST['stock']??null,FILTER_VALIDATE_INT);$warranty=filter_var($_POST['garantia_meses']??0,FILTER_VALIDATE_INT);$image='';$published=isset($_POST['publicado'])?1:0;$upload=$_FILES['imagen_archivo']??null;$uploadTemp=null;$uploadPath=null;$createdUpload=null;
  if($upload&&$upload['error']!==UPLOAD_ERR_NO_FILE){if($upload['error']!==UPLOAD_ERR_OK||!is_uploaded_file($upload['tmp_name'])||(int)$upload['size']>8*1024*1024){$this->preserveProductDraft('No se pudo leer la imagen. Usá un archivo de hasta 8 MB.');return;}$mime=(new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);$extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];$dimensions=@getimagesize($upload['tmp_name']);if(!isset($extensions[$mime])||!$dimensions||$dimensions[0]>8000||$dimensions[1]>8000){$this->preserveProductDraft('La imagen debe ser JPG, PNG o WebP y medir como máximo 8000 × 8000 píxeles.');return;}$uploadTemp=$upload['tmp_name'];$uploadPath='assets/images/uploads/'.hash_file('sha256',$uploadTemp).'.'.$extensions[$mime];$image=$uploadPath;}
  if($name===''||$model===''||$description===''||!in_array($condition,['nuevo','usado','reacondicionado'],true)||$price===false||$price<0||$stock===false||$stock<0||$warranty===false||$warranty<0||strlen($name)>180||strlen($model)>120){$this->preserveProductDraft('Revisá nombre, modelo, descripción, precio, stock, categoría y marca.');return;}
  // Las imágenes remotas no se aceptan: se sube un archivo o se genera una ilustración.
  try{$db->beginTransaction();$s=$db->prepare('SELECT c.id_categoria,c.slug categoria_slug,m.id_marca FROM categorias c CROSS JOIN marcas m WHERE c.id_categoria=? AND c.estado=1 AND m.id_marca=? AND m.estado=1');$s->execute([$category,$brand]);$selected=$s->fetch();if(!$selected)throw new RuntimeException('Elegí una categoría y una marca activas.');if($image!==''){$duplicate=$db->prepare('SELECT 1 FROM imagenes_producto WHERE ruta_imagen=? LIMIT 1');$duplicate->execute([$image]);if($duplicate->fetchColumn())throw new RuntimeException('Ese archivo ya está asignado a otro producto.');}
   $slugSource=function_exists('iconv')?iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$name):$name;$base=trim(preg_replace('/[^a-z0-9]+/','-',strtolower((string)$slugSource)),'-');$base=substr($base?:'producto',0,180);$slug=$base;$suffix=2;$check=$db->prepare('SELECT 1 FROM productos WHERE slug=?');while(true){$check->execute([$slug]);if(!$check->fetchColumn())break;$ending='-'.$suffix++;$slug=substr($base,0,200-strlen($ending)).$ending;}
   if($uploadTemp!==null){$createdUpload=ROOT.'/public/'.$uploadPath;$uploadDir=dirname($createdUpload);if(!is_dir($uploadDir)&&!mkdir($uploadDir,0775,true)&&!is_dir($uploadDir))throw new RuntimeException('No se pudo preparar la carpeta para guardar la imagen.');if(!move_uploaded_file($uploadTemp,$createdUpload))throw new RuntimeException('No se pudo guardar la imagen subida.');}elseif($image==='')$image=ProductArtwork::ensure($slug,$name,$model,$selected['categoria_slug']);$s=$db->prepare(Database::insertReturning('INSERT INTO productos(id_categoria,id_marca,nombre,slug,modelo,descripcion,tipo_producto,condicion,precio,garantia_meses,estado,publicado,fecha_publicacion) VALUES(?,?,?,?,?,?,?,?,?,?,1,?,NOW())','id_producto'));$s->execute([$category,$brand,$name,$slug,$model,$description,$condition,$condition,$price,$warranty,$published]);$id=Database::insertedId($db,$s);$s=$db->prepare('INSERT INTO producto_categoria(id_producto,id_categoria) VALUES(?,?)');$s->execute([$id,$category]);$s=$db->prepare('INSERT INTO inventario(id_producto,cantidad_disponible) VALUES(?,?)');$s->execute([$id,$stock]);$s=$db->prepare('INSERT INTO imagenes_producto(id_producto,ruta_imagen,texto_alternativo,es_principal) VALUES(?,?,?,1)');$s->execute([$id,$image,$name]);
   $specs=$db->prepare('INSERT INTO especificaciones_producto(id_producto,atributo,valor) VALUES(?,?,?)');foreach(preg_split('/\r?\n/',trim($_POST['especificaciones']??'')) as $line){if(str_contains($line,':')){[$key,$value]=array_map('trim',explode(':',$line,2));if($key!==''&&$value!=='')$specs->execute([$id,mb_substr($key,0,100),mb_substr($value,0,255)]);}}
   if($stock>0&&$condition==='nuevo'){$s=$db->prepare("INSERT INTO movimientos_inventario(id_producto,tipo_movimiento,cantidad,motivo,id_usuario_responsable) VALUES(?,'entrada',?,'Alta inicial de producto',?)");$s->execute([$id,$stock,Auth::id()]);}elseif($stock>0){$unit=$db->prepare(Database::insertReturning('INSERT INTO unidades_usadas(id_producto,codigo_unidad,estado_conservacion,descripcion_estado,precio_especifico,garantia_meses) VALUES(?,?,?,?,?,?)','id_unidad_usada'));$movement=$db->prepare("INSERT INTO movimientos_inventario(id_producto,id_unidad_usada,tipo_movimiento,cantidad,motivo,id_usuario_responsable) VALUES(?,?,'entrada',1,'Alta inicial de producto',?)");for($n=1;$n<=$stock;$n++){$unit->execute([$id,sprintf('KRU-%05d-%04d',$id,$n),$condition,$description,$price,$warranty]);$unitId=Database::insertedId($db,$unit);$movement->execute([$id,$unitId,Auth::id()]);}}$s=$db->prepare("INSERT INTO auditoria(id_usuario,accion,entidad,id_registro_afectado,descripcion) VALUES(?,'crear','productos',?,?)");$s->execute([Auth::id(),$id,'Producto creado: '.$name]);$db->commit();$_SESSION['flash']='Producto creado correctamente.';
  }catch(Throwable $e){if($db->inTransaction())$db->rollBack();if($createdUpload&&is_file($createdUpload))@unlink($createdUpload);@file_put_contents(ROOT.'/storage/app.log','['.date('c').'] Product creation failed: '.$e.PHP_EOL,FILE_APPEND|LOCK_EX);$message=$e instanceof RuntimeException?$e->getMessage():'No se pudo crear el producto. El error quedó registrado para poder corregirlo.';$this->preserveProductDraft($message);return;}header('Location: ?action=admin');
 }
 private function saveProduct(PDO $db):void{
  $id=(int)($_POST['id']??0);$name=trim($_POST['nombre']??'');$model=trim($_POST['modelo']??'');$description=trim($_POST['descripcion']??'');$category=(int)($_POST['id_categoria']??0);$brand=(int)($_POST['id_marca']??0);$condition=(string)($_POST['condicion']??'nuevo');$price=filter_var($_POST['precio']??null,FILTER_VALIDATE_FLOAT);$warranty=filter_var($_POST['garantia_meses']??0,FILTER_VALIDATE_INT);$image='';$upload=$_FILES['imagen_archivo']??null;$temp=null;$path=null;$created=null;
  if($upload&&$upload['error']!==UPLOAD_ERR_NO_FILE){if($upload['error']!==UPLOAD_ERR_OK||!is_uploaded_file($upload['tmp_name'])||(int)$upload['size']>8*1024*1024){$_SESSION['flash']='La imagen debe pesar hasta 8 MB.';header('Location: ?action=admin#productos-existentes');return;}$mime=(new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);$extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];$dimensions=@getimagesize($upload['tmp_name']);if(!isset($extensions[$mime])||!$dimensions||$dimensions[0]>8000||$dimensions[1]>8000){$_SESSION['flash']='Usá una imagen JPG, PNG o WebP de hasta 8000 × 8000 píxeles.';header('Location: ?action=admin#productos-existentes');return;}$temp=$upload['tmp_name'];$path='assets/images/uploads/'.hash_file('sha256',$temp).'.'.$extensions[$mime];$image=$path;}
  if($id<1||$name===''||$model===''||$description===''||!in_array($condition,['nuevo','usado','reacondicionado'],true)||$price===false||$price<0||$warranty===false||$warranty<0||strlen($name)>180||strlen($model)>120){$_SESSION['flash']='Revisá los datos obligatorios del producto.';header('Location: ?action=admin#productos-existentes');return;}
  try{$db->beginTransaction();$check=$db->prepare('SELECT p.id_producto,p.condicion,i.cantidad_disponible FROM productos p JOIN inventario i USING(id_producto) WHERE p.id_producto=? FOR UPDATE');$check->execute([$id]);$existing=$check->fetch();if(!$existing)throw new RuntimeException('El producto ya no existe.');$categoryCheck=$db->prepare('SELECT 1 FROM categorias c JOIN marcas m ON m.id_marca=? WHERE c.id_categoria=? AND c.estado=1 AND m.estado=1');$categoryCheck->execute([$brand,$category]);if(!$categoryCheck->fetchColumn())throw new RuntimeException('Elegí una categoría y una marca activas.');if($image!==''&&$temp!==null){$duplicate=$db->prepare('SELECT 1 FROM imagenes_producto WHERE ruta_imagen=? AND id_producto<>? LIMIT 1');$duplicate->execute([$image,$id]);if($duplicate->fetchColumn())throw new RuntimeException('Esta imagen ya está asignada a otro producto.');$created=ROOT.'/public/'.$path;$dir=dirname($created);if(!is_dir($dir)&&!mkdir($dir,0775,true)&&!is_dir($dir))throw new RuntimeException('No se pudo preparar el almacenamiento de imágenes.');if(!move_uploaded_file($temp,$created))throw new RuntimeException('No se pudo guardar la imagen.');}
   $slugSource=function_exists('iconv')?iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$name):$name;$base=substr(trim(preg_replace('/[^a-z0-9]+/','-',strtolower((string)$slugSource)),'-'),0,180)?:'producto';$slug=$base;$n=2;$slugCheck=$db->prepare('SELECT id_producto FROM productos WHERE slug=? AND id_producto<>?');while(true){$slugCheck->execute([$slug,$id]);if(!$slugCheck->fetchColumn())break;$slug=$base.'-'.($n++);}
   if($existing['condicion']!==$condition){$qty=(int)$existing['cantidad_disponible'];if($existing['condicion']==='nuevo'&&$condition!=='nuevo'&&$qty>0){$unit=$db->prepare(Database::insertReturning('INSERT INTO unidades_usadas(id_producto,codigo_unidad,estado_conservacion,descripcion_estado,precio_especifico,garantia_meses) VALUES(?,?,?,?,?,?)','id_unidad_usada'));$move=$db->prepare("INSERT INTO movimientos_inventario(id_producto,id_unidad_usada,tipo_movimiento,cantidad,motivo,id_usuario_responsable) VALUES(?,?,'ajuste',1,'Cambio de condición del producto',?)");for($i=1;$i<=$qty;$i++){$unit->execute([$id,'KRU-'.$id.'-'.strtoupper(bin2hex(random_bytes(4))),$condition,$description,$price,$warranty]);$unitId=Database::insertedId($db,$unit);$move->execute([$id,$unitId,Auth::id()]);}}elseif($existing['condicion']!=='nuevo'&&$condition==='nuevo'){$units=$db->prepare("SELECT id_unidad_usada FROM unidades_usadas WHERE id_producto=? AND estado_disponibilidad='disponible' FOR UPDATE");$units->execute([$id]);$retire=$db->prepare("UPDATE unidades_usadas SET estado_disponibilidad='retirada' WHERE id_unidad_usada=?");$move=$db->prepare("INSERT INTO movimientos_inventario(id_producto,id_unidad_usada,tipo_movimiento,cantidad,motivo,id_usuario_responsable) VALUES(?,?,'ajuste',1,'Cambio de condición del producto',?)");foreach($units->fetchAll(PDO::FETCH_COLUMN) as $unitId){$retire->execute([$unitId]);$move->execute([$id,(int)$unitId,Auth::id()]);}}else{$db->prepare("UPDATE unidades_usadas SET estado_conservacion=?,descripcion_estado=?,precio_especifico=?,garantia_meses=? WHERE id_producto=? AND estado_disponibilidad='disponible'")->execute([$condition,$description,$price,$warranty,$id]);}}
   $s=$db->prepare('UPDATE productos SET id_categoria=?,id_marca=?,nombre=?,slug=?,modelo=?,descripcion=?,tipo_producto=?,condicion=?,precio=?,garantia_meses=?,publicado=? WHERE id_producto=?');$s->execute([$category,$brand,$name,$slug,$model,$description,$condition,$condition,$price,$warranty,isset($_POST['publicado'])?1:0,$id]);$db->prepare('DELETE FROM producto_categoria WHERE id_producto=?')->execute([$id]);$db->prepare('INSERT INTO producto_categoria(id_producto,id_categoria) VALUES(?,?)')->execute([$id,$category]);$current=$db->prepare('SELECT ruta_imagen FROM imagenes_producto WHERE id_producto=? ORDER BY es_principal DESC,id_imagen LIMIT 1');$current->execute([$id]);$oldImage=$current->fetchColumn();if($image!==''){$db->prepare('DELETE FROM imagenes_producto WHERE id_producto=?')->execute([$id]);$db->prepare('INSERT INTO imagenes_producto(id_producto,ruta_imagen,texto_alternativo,es_principal) VALUES(?,?,?,1)')->execute([$id,$image,$name]);}
   $db->prepare('DELETE FROM especificaciones_producto WHERE id_producto=?')->execute([$id]);$spec=$db->prepare('INSERT INTO especificaciones_producto(id_producto,atributo,valor) VALUES(?,?,?)');foreach(preg_split('/\\r?\\n/',trim($_POST['especificaciones']??'')) as $line){if(str_contains($line,':')){[$key,$value]=array_map('trim',explode(':',$line,2));if($key!==''&&$value!=='')$spec->execute([$id,mb_substr($key,0,100),mb_substr($value,0,255)]);}}
   $audit=$db->prepare("INSERT INTO auditoria(id_usuario,accion,entidad,id_registro_afectado,descripcion) VALUES(?,'actualizar','productos',?,?)");$audit->execute([Auth::id(),$id,'Producto actualizado: '.$name]);$db->commit();if($created&&$oldImage&&str_starts_with((string)$oldImage,'assets/images/uploads/')){$oldFile=ROOT.'/public/'.$oldImage;if(is_file($oldFile)&&$oldFile!==$created)@unlink($oldFile);}$_SESSION['flash']='Producto actualizado correctamente.';
  }catch(Throwable $e){if($db->inTransaction())$db->rollBack();if($created&&is_file($created))@unlink($created);@file_put_contents(ROOT.'/storage/app.log','['.date('c').'] Product update failed: '.$e.PHP_EOL,FILE_APPEND|LOCK_EX);$_SESSION['flash']=$e instanceof RuntimeException?$e->getMessage():'No se pudo actualizar el producto. El error quedó registrado para poder corregirlo.';}header('Location: ?action=admin#productos-existentes');
 }
 private function deleteProduct(PDO $db,int $id):void{try{$db->beginTransaction();$s=$db->prepare('SELECT nombre FROM productos WHERE id_producto=? FOR UPDATE');$s->execute([$id]);$name=$s->fetchColumn();if(!$name)throw new RuntimeException('El producto no existe.');$db->prepare('UPDATE productos SET estado=0,publicado=0 WHERE id_producto=?')->execute([$id]);$audit=$db->prepare("INSERT INTO auditoria(id_usuario,accion,entidad,id_registro_afectado,descripcion) VALUES(?,'eliminar','productos',?,?)");$audit->execute([Auth::id(),$id,'Producto retirado del catálogo: '.$name]);$db->commit();$_SESSION['flash']='Producto retirado del catálogo. Se conserva el historial de pedidos e inventario.';}catch(Throwable $e){if($db->inTransaction())$db->rollBack();$_SESSION['flash']=$e instanceof RuntimeException?$e->getMessage():'No se pudo retirar el producto.';}header('Location: ?action=admin#productos-existentes');}
 private function adjustStock(PDO $db,int $id,int $stock,string $reason):void{if($stock<0||$reason===''||mb_strlen($reason)>255){$_SESSION['flash']='Indicá una cantidad válida y el motivo del ajuste.';header('Location: ?action=admin#productos-existentes');return;}try{$db->beginTransaction();$s=$db->prepare('SELECT p.nombre,p.condicion,i.cantidad_disponible FROM productos p JOIN inventario i USING(id_producto) WHERE p.id_producto=? FOR UPDATE');$s->execute([$id]);$product=$s->fetch();if(!$product)throw new RuntimeException('Producto o inventario inexistente.');$old=(int)$product['cantidad_disponible'];if($stock===$old)throw new RuntimeException('La cantidad no cambió.');if($product['condicion']!=='nuevo'){$available=$db->prepare("SELECT id_unidad_usada FROM unidades_usadas WHERE id_producto=? AND estado_disponibilidad='disponible' ORDER BY id_unidad_usada FOR UPDATE");$available->execute([$id]);$units=$available->fetchAll(PDO::FETCH_COLUMN);if($stock>$old){$unit=$db->prepare(Database::insertReturning('INSERT INTO unidades_usadas(id_producto,codigo_unidad,estado_conservacion,descripcion_estado,precio_especifico,garantia_meses) VALUES(?,?,?,?,?,?)','id_unidad_usada'));$moveUnit=$db->prepare("INSERT INTO movimientos_inventario(id_producto,id_unidad_usada,tipo_movimiento,cantidad,motivo,id_usuario_responsable) VALUES(?,?,'ajuste',1,?,?)");for($i=$old+1;$i<=$stock;$i++){$unit->execute([$id,'KRU-'.$id.'-'.strtoupper(bin2hex(random_bytes(4))),$product['condicion'],$product['nombre'],null,0]);$unitId=Database::insertedId($db,$unit);$moveUnit->execute([$id,$unitId,$reason,Auth::id()]);}}else{$retire=array_slice($units,0,$old-$stock);$retireUnit=$db->prepare("UPDATE unidades_usadas SET estado_disponibilidad='retirada' WHERE id_unidad_usada=?");$moveUnit=$db->prepare("INSERT INTO movimientos_inventario(id_producto,id_unidad_usada,tipo_movimiento,cantidad,motivo,id_usuario_responsable) VALUES(?,?,'ajuste',1,?,?)");foreach($retire as $unitId){$retireUnit->execute([$unitId]);$moveUnit->execute([$id,$unitId,$reason,Auth::id()]);}}}else{$move=$db->prepare("INSERT INTO movimientos_inventario(id_producto,tipo_movimiento,cantidad,motivo,id_usuario_responsable) VALUES(?,'ajuste',?,?,?)");$move->execute([$id,abs($stock-$old),$reason,Auth::id()]);}$db->prepare('UPDATE inventario SET cantidad_disponible=? WHERE id_producto=?')->execute([$stock,$id]);$db->commit();$_SESSION['flash']='Stock actualizado correctamente.';}catch(Throwable $e){if($db->inTransaction())$db->rollBack();$_SESSION['flash']=$e instanceof RuntimeException?$e->getMessage():'No se pudo actualizar el stock.';}header('Location: ?action=admin#productos-existentes');}
 private function updateOrder(PDO $db,int $id,string $next):void{$db->beginTransaction();try{$s=$db->prepare('SELECT estado FROM pedidos WHERE id_pedido=? FOR UPDATE');$s->execute([$id]);$old=$s->fetchColumn();$nextStates=['confirmado'=>['preparando'],'preparando'=>['listo_retiro','despachado'],'listo_retiro'=>['entregado'],'despachado'=>['entregado']];if(!$old||!in_array($next,$nextStates[$old]??[],true))throw new RuntimeException('Ese cambio de estado no está permitido.');$s=$db->prepare('UPDATE pedidos SET estado=? WHERE id_pedido=?');$s->execute([$next,$id]);$s=$db->prepare('INSERT INTO historial_pedidos(id_pedido,id_usuario_responsable,estado_anterior,estado_nuevo,observacion) VALUES(?,?,?,?,?)');$s->execute([$id,Auth::id(),$old,$next,'Actualización operativa']);$db->commit();$_SESSION['flash']='Estado del pedido actualizado.';}catch(Throwable $e){if($db->inTransaction())$db->rollBack();$_SESSION['flash']=$e instanceof RuntimeException?$e->getMessage():'No se pudo actualizar el pedido.';}header('Location: ?action=admin#pedidos');}
 private function login():void{if(Auth::id()){header('Location: '.((Auth::user()['rol']??'')==='administrador'?'?action=admin':'?'));return;}if($_SERVER['REQUEST_METHOD']==='POST'){$this->csrf();$s=Database::connection()->prepare('SELECT u.*,r.nombre rol FROM usuarios u JOIN roles r USING(id_rol) WHERE u.email=? AND u.estado=1');$s->execute([strtolower(trim($_POST['email']??''))]);$u=$s->fetch();if($u&&password_verify($_POST['password']??'',$u['password_hash'])){if(Auth::login($u)){header('Location: '.($u['rol']==='administrador'?'?action=admin':'?'));return;}$_SESSION['flash']='Esta cuenta ya tiene una sesión activa. Cerrala antes de volver a iniciar sesión.';}else $_SESSION['flash']='Correo o contraseña incorrectos.';}$this->page('login');}
 private function register():void{if($_SERVER['REQUEST_METHOD']==='POST'){$this->csrf();$name=trim($_POST['nombre']??'');$last=trim($_POST['apellido']??'');$email=strtolower(trim($_POST['email']??''));$pw=$_POST['password']??'';if($name===''||$last===''||strlen($name)>100||strlen($last)>100||strlen($email)>190||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($pw)<10)$_SESSION['flash']='Completá nombre, apellido, correo y una contraseña de al menos 10 caracteres.';else{try{$s=Database::connection()->prepare("INSERT INTO usuarios(id_rol,nombre,apellido,email,password_hash,estado) SELECT id_rol,?,?,?,?,1 FROM roles WHERE nombre='cliente'");$s->execute([$name,$last,$email,password_hash($pw,PASSWORD_DEFAULT)]);$_SESSION['flash']='Cuenta creada. Ya podés iniciar sesión.';header('Location: ?action=login');return;}catch(PDOException){$_SESSION['flash']='No se pudo crear la cuenta. Verificá si el correo ya existe.';}}}$this->page('register');}
 private function checkout():void{
  $token=(string)($_POST['_checkout_token']??'');
  $expected=(string)($_SESSION['_checkout_token']??'');
  if($token===''||$expected===''||!hash_equals($expected,$token)){
   $_SESSION['flash']='Este checkout ya se procesó o venció. Revisá el carrito e intentá nuevamente.';
   header('Location: ?action=checkout');return;
  }
  unset($_SESSION['_checkout_token']);
  $paymentOutcome=(string)($_POST['pago']??'');
  if(!in_array($paymentOutcome,['aprobado','rechazado'],true)){
   $_SESSION['flash']='Elegí un resultado para el pago de demostración.';
   header('Location: ?action=checkout');return;
  }
  $dni=str_replace(['.',' ','-'],'',trim((string)($_POST['dni']??'')));
  $buyerName=trim((string)($_POST['nombre_completo']??''));
  $city=trim((string)($_POST['ciudad']??''));
  $postal=trim((string)($_POST['codigo_postal']??''));
  if(!preg_match('/^\d{7,8}$/',$dni)){
   $_SESSION['flash']='Ingresá un DNI argentino válido: 7 u 8 números, con o sin puntos.';
   header('Location: ?action=checkout');return;
  }
  if($buyerName===''||mb_strlen($buyerName)>160||$city===''||mb_strlen($city)>100||!preg_match('/^[A-Za-z0-9 -]{3,10}$/',$postal)){
   $_SESSION['flash']='Completá el nombre del comprador, la ciudad y un código postal válido.';
   header('Location: ?action=checkout');return;
  }
  $mode=(string)($_POST['modalidad']??'');
  if(!in_array($mode,['retiro','envio'],true)){
   $_SESSION['flash']='Elegí una modalidad de entrega válida.';
   header('Location: ?action=checkout');return;
  }
  $address=$mode==='envio'?trim((string)($_POST['direccion']??'')):null;
  if($mode==='envio'&&($address===''||mb_strlen($address)>2000)){
   $_SESSION['flash']='Ingresá una dirección de entrega válida.';
   header('Location: ?action=checkout');return;
  }

  $db=Database::connection();
  try{
   $db->beginTransaction();
   $userId=Auth::id();
   if($userId===null)throw new RuntimeException('Iniciá sesión nuevamente para confirmar la compra.');
   $cartId=Cart::lockActive($db,$userId);
   if($cartId===null)throw new RuntimeException('Tu carrito está vacío.');
   $cart=Cart::lockItems($db,$cartId);
   if($cart===[])throw new RuntimeException('Tu carrito está vacío.');
   ksort($cart,SORT_NUMERIC);

   $items=[];
   $subtotalCents=0;
   foreach($cart as $id=>$quantity){
    $id=(int)$id;$quantity=(int)$quantity;
    if($id<1||$quantity<1)throw new RuntimeException('El carrito contiene una cantidad inválida.');
    $query=$db->prepare('SELECT p.id_producto,p.nombre,p.modelo,p.condicion,COALESCE((SELECT pr.precio_promocional FROM promociones pr WHERE pr.id_producto=p.id_producto AND pr.estado=1 AND NOW() BETWEEN pr.fecha_inicio AND pr.fecha_fin ORDER BY pr.precio_promocional LIMIT 1),p.precio) precio,i.cantidad_disponible FROM productos p JOIN inventario i USING(id_producto) WHERE p.id_producto=? AND p.publicado=1 AND p.estado=1 FOR UPDATE');
    $query->execute([$id]);
    $product=$query->fetch();
    if(!$product){
     throw new RuntimeException('No se pudo realizar el pedido porque uno de los productos ya no está disponible. No se realizó ningún cobro y tu carrito se conservó.');
    }
    if((int)$product['cantidad_disponible']<$quantity){
     $available=(int)$product['cantidad_disponible'];
     $message=$available<1
      ? 'No se pudo realizar el pedido: ya no hay stock disponible de «'.$product['nombre'].'».'
      : 'No se pudo realizar el pedido: el stock de «'.$product['nombre'].'» no alcanza; quedan '.$available.' '.($available===1?'unidad':'unidades').' y solicitaste '.$quantity.'.';
     throw new RuntimeException($message.' No se realizó ningún cobro y tu carrito se conservó.');
    }
    $units=[];
    if($paymentOutcome==='aprobado'&&$product['condicion']!=='nuevo'){
     $unitQuery=$db->prepare("SELECT id_unidad_usada FROM unidades_usadas WHERE id_producto=? AND estado_disponibilidad='disponible' ORDER BY id_unidad_usada LIMIT $quantity FOR UPDATE");
     $unitQuery->execute([$id]);
     $units=array_map('intval',$unitQuery->fetchAll(PDO::FETCH_COLUMN));
     if(count($units)!==$quantity)throw new RuntimeException('No se pudo realizar el pedido: ya no hay stock disponible de «'.$product['nombre'].'». No se realizó ningún cobro y tu carrito se conservó.');
    }
    $product['id_producto']=$id;
    $items[]=$product+['cantidad'=>$quantity,'unidades'=>$units,'stock_antes'=>(int)$product['cantidad_disponible']];
    $unitCents=(int)round((float)$product['precio']*100);
    $subtotalCents+=$unitCents*$quantity;
   }

   $feeQuery=$db->prepare('SELECT costo FROM configuracion_envio WHERE modalidad=? AND activo=1 ORDER BY id_configuracion DESC LIMIT 1');
   $feeQuery->execute([$mode]);
   $feeValue=$feeQuery->fetchColumn();
   if($feeValue===false)throw new RuntimeException('La modalidad de entrega seleccionada no está disponible.');
   $shippingCents=(int)round((float)$feeValue*100);
   $totalCents=$subtotalCents+$shippingCents;
   $subtotal=$subtotalCents/100;
   $shipping=$shippingCents/100;
   $total=$totalCents/100;
   $number='KR-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(8)));
   $approved=$paymentOutcome==='aprobado';
   $status=$approved?'confirmado':'cancelado';

   $order=$db->prepare(Database::insertReturning('INSERT INTO pedidos(numero_pedido,id_usuario,estado,subtotal,descuento_total,costo_envio,total,modalidad_entrega,datos_destinatario_historicos,dni_destinatario_historico,ciudad_destinatario_historica,codigo_postal_destinatario_historico,direccion_entrega_historica) VALUES(?,?,?,?,0,?,?,?,?,?,?,?,?)','id_pedido'));
   $order->execute([$number,$userId,$status,$subtotal,$shipping,$total,$mode,$buyerName,$dni,$city,$postal,$address]);
   $orderId=Database::insertedId($db,$order);
   $detail=$db->prepare('INSERT INTO detalle_pedido(id_pedido,id_producto,id_unidad_usada,nombre_producto_historico,modelo_historico,precio_unitario,cantidad,descuento_unitario,subtotal_linea) VALUES(?,?,?,?,?,?,?,0,?)');
   $inventoryUpdate=$db->prepare('UPDATE inventario SET cantidad_disponible=cantidad_disponible-? WHERE id_producto=? AND cantidad_disponible>=?');
   $unitUpdate=$db->prepare("UPDATE unidades_usadas SET estado_disponibilidad='vendida' WHERE id_unidad_usada=? AND id_producto=? AND estado_disponibilidad='disponible'");
   $movement=$db->prepare("INSERT INTO movimientos_inventario(id_producto,id_unidad_usada,tipo_movimiento,cantidad,motivo,id_usuario_responsable,id_pedido) VALUES(?,?,'salida',?,?,?,?)");
   foreach($items as $item){
    $unitCents=(int)round((float)$item['precio']*100);
    if($approved){
     $inventoryUpdate->execute([$item['cantidad'],$item['id_producto'],$item['cantidad']]);
     if($inventoryUpdate->rowCount()!==1)throw new RuntimeException('No se pudo realizar el pedido porque el stock cambió durante la confirmación. No se realizó ningún cobro y tu carrito se conservó.');
     if($item['unidades']!==[]){
      foreach($item['unidades'] as $unitId){
       $detail->execute([$orderId,$item['id_producto'],$unitId,$item['nombre'],$item['modelo'],$unitCents/100,1,$unitCents/100]);
       $unitUpdate->execute([$unitId,$item['id_producto']]);
       if($unitUpdate->rowCount()!==1)throw new RuntimeException('No se pudo realizar el pedido porque una unidad dejó de estar disponible. No se realizó ningún cobro y tu carrito se conservó.');
       $movement->execute([$item['id_producto'],$unitId,1,'Compra confirmada',Auth::id(),$orderId]);
      }
     }else{
      $lineCents=$unitCents*$item['cantidad'];
      $detail->execute([$orderId,$item['id_producto'],null,$item['nombre'],$item['modelo'],$unitCents/100,$item['cantidad'],$lineCents/100]);
      $movement->execute([$item['id_producto'],null,$item['cantidad'],'Compra confirmada',Auth::id(),$orderId]);
     }
     if($item['stock_antes']===$item['cantidad']){
      $notice=$db->prepare('INSERT INTO notificaciones_administrador(id_producto,id_pedido,titulo,mensaje) VALUES(?,?,?,?)');
      $notice->execute([$item['id_producto'],$orderId,'Producto sin stock',$item['nombre'].' ('.$item['modelo'].') quedó sin unidades disponibles tras la compra '.$number.'.']);
     }
    }else{
     $lineCents=$unitCents*$item['cantidad'];
     $detail->execute([$orderId,$item['id_producto'],null,$item['nombre'],$item['modelo'],$unitCents/100,$item['cantidad'],$lineCents/100]);
    }
   }

   $payment=$db->prepare('INSERT INTO pagos(id_pedido,metodo_pago,importe,estado_pago,referencia_operacion,fecha_procesamiento) VALUES(?,?,?,?,?,NOW())');
   $payment->execute([$orderId,'simulacion',$total,$approved?'aprobado':'rechazado','DEMO-'.$number]);
   $history=$db->prepare('INSERT INTO historial_pedidos(id_pedido,id_usuario_responsable,estado_anterior,estado_nuevo,observacion) VALUES(?,NULL,NULL,?,?)');
   $history->execute([$orderId,$status,$approved?'Compra de demostración confirmada':'Pago de demostración rechazado; no se descontó stock']);
   if($approved)Cart::markConverted($db,$cartId);
   $db->commit();
   if($approved){unset($_SESSION['cart']);Cart::invalidate();}
   header('Location: ?action=order&n='.$number);return;
  }catch(Throwable $error){
   if($db->inTransaction())$db->rollBack();
   if($error instanceof PDOException){error_log((string)$error);$_SESSION['flash']='No se pudo procesar la compra. No se descontó stock y el carrito se conservó.';}
   else{$_SESSION['flash']=$error instanceof RuntimeException?$error->getMessage():'No se pudo procesar la compra. No se descontó stock y el carrito se conservó.';}
   header('Location: ?action=checkout');
  }
 }
 private function page(string $name,array $data=[]):void{extract($data,EXTR_SKIP);$view=ROOT.'/app/Views/'.$name.'.php';require ROOT.'/app/Views/layout.php';}
}
