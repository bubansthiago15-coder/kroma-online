<?php
declare(strict_types=1);
final class Store {
 private function filters(array $f,array &$args):string {
  $where=' WHERE p.publicado=1 AND p.estado=1';
  if(!empty($f['q'])){$where.=' AND (p.nombre LIKE ? OR p.modelo LIKE ? OR m.nombre LIKE ?)';$v='%'.$f['q'].'%';array_push($args,$v,$v,$v);}
  if(!empty($f['categoria'])){$where.=' AND EXISTS(SELECT 1 FROM producto_categoria pc JOIN categorias cx USING(id_categoria) WHERE pc.id_producto=p.id_producto AND cx.slug=?)';$args[]=$f['categoria'];}
  if(!empty($f['condicion'])){$where.=' AND p.condicion=?';$args[]=$f['condicion'];}
  if(!empty($f['solo_ofertas']))$where.=' AND EXISTS(SELECT 1 FROM promociones pr WHERE pr.id_producto=p.id_producto AND pr.estado=1 AND NOW() BETWEEN pr.fecha_inicio AND pr.fecha_fin)';
  if(!empty($f['solo_novedades']))$where.=(Database::driver()==='pgsql'?" AND p.fecha_publicacion >= CURRENT_TIMESTAMP - INTERVAL '45 days'":" AND p.fecha_publicacion>=DATE_SUB(NOW(),INTERVAL 45 DAY)");
  return $where;
 }
 public function products(array $f=[]):array {
  $args=[];$where=$this->filters($f,$args);$order=match($f['orden']??''){ 'precio'=>'precio ASC','precio_desc'=>'precio DESC',default=>'p.fecha_publicacion DESC'};$page=max(1,(int)($f['pagina']??1));$offset=($page-1)*12;
  $sql='SELECT p.*,CASE WHEN EXISTS(SELECT 1 FROM promociones px WHERE px.id_producto=p.id_producto AND px.estado=1 AND NOW() BETWEEN px.fecha_inicio AND px.fecha_fin) THEN p.precio ELSE p.precio_anterior END precio_anterior,COALESCE((SELECT pr.precio_promocional FROM promociones pr WHERE pr.id_producto=p.id_producto AND pr.estado=1 AND NOW() BETWEEN pr.fecha_inicio AND pr.fecha_fin ORDER BY pr.precio_promocional LIMIT 1),p.precio) precio,c.nombre categoria,m.nombre marca,(SELECT ip.ruta_imagen FROM imagenes_producto ip WHERE ip.id_producto=p.id_producto ORDER BY ip.es_principal DESC,ip.orden,ip.id_imagen LIMIT 1) imagen,(SELECT fs.url_fuente FROM producto_fuente_imagen fs WHERE fs.id_producto=p.id_producto LIMIT 1) imagen_fuente,i.cantidad_disponible stock FROM productos p JOIN categorias c USING(id_categoria) JOIN marcas m USING(id_marca) LEFT JOIN inventario i USING(id_producto)'.$where.' ORDER BY '.$order.' LIMIT 12 OFFSET '.(int)$offset;
  $s=Database::connection()->prepare($sql);$s->execute($args);return $s->fetchAll();
 }
 public function productCount(array $f=[]):int{$args=[];$where=$this->filters($f,$args);$s=Database::connection()->prepare('SELECT COUNT(*) FROM productos p JOIN marcas m USING(id_marca)'.$where);$s->execute($args);return (int)$s->fetchColumn();}
 public function categories():array{return Database::connection()->query('SELECT nombre,slug FROM categorias WHERE estado=1 ORDER BY orden_visualizacion,nombre')->fetchAll();}
 public function product(int $id):?array{$s=Database::connection()->prepare('SELECT p.*,CASE WHEN EXISTS(SELECT 1 FROM promociones px WHERE px.id_producto=p.id_producto AND px.estado=1 AND NOW() BETWEEN px.fecha_inicio AND px.fecha_fin) THEN p.precio ELSE p.precio_anterior END precio_anterior,COALESCE((SELECT pr.precio_promocional FROM promociones pr WHERE pr.id_producto=p.id_producto AND pr.estado=1 AND NOW() BETWEEN pr.fecha_inicio AND pr.fecha_fin ORDER BY pr.precio_promocional LIMIT 1),p.precio) precio,c.nombre categoria,m.nombre marca,(SELECT ip.ruta_imagen FROM imagenes_producto ip WHERE ip.id_producto=p.id_producto ORDER BY ip.es_principal DESC,ip.orden,ip.id_imagen LIMIT 1) imagen,(SELECT fs.url_fuente FROM producto_fuente_imagen fs WHERE fs.id_producto=p.id_producto LIMIT 1) imagen_fuente,COALESCE(i.cantidad_disponible,0) stock FROM productos p JOIN categorias c USING(id_categoria) JOIN marcas m USING(id_marca) LEFT JOIN inventario i USING(id_producto) WHERE p.id_producto=? AND p.publicado=1');$s->execute([$id]);$p=$s->fetch();if(!$p)return null;$s=Database::connection()->prepare('SELECT atributo,valor,unidad FROM especificaciones_producto WHERE id_producto=?');$s->execute([$id]);$p['specs']=$s->fetchAll();return $p;}
 public function cartCount():int{return Cart::count();}
}
