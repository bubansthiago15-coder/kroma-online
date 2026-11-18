-- Reparaciones para el esquema Kroma ya creado en Supabase (PostgreSQL).
-- Conserva tablas y datos existentes. Ejecutar desde Supabase SQL Editor.
-- Requiere las 26 tablas públicas creadas previamente.

BEGIN;

-- Comprobar datos repetidos antes de crear las restricciones necesarias
-- para que los INSERT ... ON CONFLICT del backend sean seguros.
DO $$
BEGIN
    IF EXISTS (SELECT 1 FROM public.usuarios GROUP BY email HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay correos duplicados en usuarios; corrígelos antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.usuarios WHERE dni IS NOT NULL GROUP BY dni HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay DNI duplicados en usuarios; corrígelos antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.roles GROUP BY nombre HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay nombres duplicados en roles; corrígelos antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.categorias GROUP BY slug HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay slugs duplicados en categorias; corrígelos antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.marcas GROUP BY nombre HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay nombres duplicados en marcas; corrígelos antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.marcas GROUP BY slug HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay slugs duplicados en marcas; corrígelos antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.productos GROUP BY slug HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay slugs duplicados en productos; corrígelos antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.inventario GROUP BY id_producto HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay más de un inventario por producto; corrígelo antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.unidades_usadas GROUP BY codigo_unidad HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay códigos de unidad usada duplicados; corrígelos antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.detalle_carrito GROUP BY id_carrito,id_producto HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay productos repetidos dentro de un carrito; consolida sus cantidades primero.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.pedidos GROUP BY numero_pedido HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay números de pedido duplicados; corrígelos antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.detalle_pedido WHERE id_unidad_usada IS NOT NULL GROUP BY id_unidad_usada HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Una unidad usada figura en más de un detalle; corrígelo antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.pagos GROUP BY id_pedido HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay varios pagos por pedido; revísalos antes de continuar.';
    END IF;
    IF EXISTS (SELECT 1 FROM public.favoritos GROUP BY id_usuario,id_producto HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Hay favoritos duplicados; corrígelos antes de continuar.';
    END IF;
END;
$$;

-- Claves únicas requeridas por la lógica de cuentas, catálogo, carrito y compra.
CREATE UNIQUE INDEX IF NOT EXISTS uq_usuarios_email ON public.usuarios(email);
CREATE UNIQUE INDEX IF NOT EXISTS uq_usuarios_dni ON public.usuarios(dni) WHERE dni IS NOT NULL;
CREATE UNIQUE INDEX IF NOT EXISTS uq_roles_nombre ON public.roles(nombre);
CREATE UNIQUE INDEX IF NOT EXISTS uq_categorias_slug ON public.categorias(slug);
CREATE UNIQUE INDEX IF NOT EXISTS uq_marcas_nombre ON public.marcas(nombre);
CREATE UNIQUE INDEX IF NOT EXISTS uq_marcas_slug ON public.marcas(slug);
CREATE UNIQUE INDEX IF NOT EXISTS uq_productos_slug ON public.productos(slug);
CREATE UNIQUE INDEX IF NOT EXISTS uq_inventario_producto ON public.inventario(id_producto);
CREATE UNIQUE INDEX IF NOT EXISTS uq_unidades_usadas_codigo ON public.unidades_usadas(codigo_unidad);
CREATE UNIQUE INDEX IF NOT EXISTS uq_carrito_producto ON public.detalle_carrito(id_carrito,id_producto);
CREATE UNIQUE INDEX IF NOT EXISTS uq_pedidos_numero ON public.pedidos(numero_pedido);
CREATE UNIQUE INDEX IF NOT EXISTS uq_detalle_unidad_usada ON public.detalle_pedido(id_unidad_usada) WHERE id_unidad_usada IS NOT NULL;
CREATE UNIQUE INDEX IF NOT EXISTS uq_pago_pedido ON public.pagos(id_pedido);
CREATE UNIQUE INDEX IF NOT EXISTS uq_favorito_usuario_producto ON public.favoritos(id_usuario,id_producto);

-- Si se importaron filas con IDs explícitos, alinear las secuencias identity
-- para que las próximas altas no intenten reutilizar IDs ya ocupados.
DO $$
DECLARE
    item record;
    secuencia text;
BEGIN
    FOR item IN
        SELECT * FROM (VALUES
            ('auditoria','id_auditoria'),('carritos','id_carrito'),('categorias','id_categoria'),
            ('configuracion_envio','id_configuracion'),('consultas_soporte','id_consulta'),
            ('detalle_carrito','id_detalle_carrito'),('detalle_pedido','id_detalle_pedido'),
            ('direcciones','id_direccion'),('especificaciones_producto','id_especificacion'),
            ('favoritos','id_favorito'),('historial_pedidos','id_historial'),('imagenes_producto','id_imagen'),
            ('inventario','id_inventario'),('marcas','id_marca'),('movimientos_inventario','id_movimiento'),
            ('notificaciones_administrador','id_notificacion'),('pagos','id_pago'),('pedidos','id_pedido'),
            ('productos','id_producto'),('roles','id_rol'),('sucursales','id_sucursal'),
            ('unidades_usadas','id_unidad_usada'),('usuarios','id_usuario')
        ) AS ids(nombre_tabla,nombre_columna)
    LOOP
        secuencia := pg_get_serial_sequence(
            format('public.%I', item.nombre_tabla), item.nombre_columna
        );
        IF secuencia IS NOT NULL THEN
            EXECUTE format(
                'SELECT setval(%L::regclass, COALESCE(MAX(%I), 1), COUNT(*) > 0) FROM public.%I',
                secuencia,item.nombre_columna,item.nombre_tabla
            );
        END IF;
    END LOOP;
END;
$$;

-- Índices de consulta y de relaciones que PostgreSQL no crea automáticamente.
CREATE INDEX IF NOT EXISTS idx_soporte_estado_fecha ON public.consultas_soporte(estado,fecha_creacion);
CREATE INDEX IF NOT EXISTS idx_soporte_email ON public.consultas_soporte(email);
CREATE INDEX IF NOT EXISTS idx_producto_categoria ON public.productos(id_categoria);
CREATE INDEX IF NOT EXISTS idx_producto_marca ON public.productos(id_marca);
CREATE INDEX IF NOT EXISTS idx_producto_estado ON public.productos(estado,publicado);
CREATE INDEX IF NOT EXISTS idx_promocion_producto_estado_fechas ON public.promociones(id_producto,estado,fecha_inicio,fecha_fin);
CREATE INDEX IF NOT EXISTS idx_unidad_producto_estado ON public.unidades_usadas(id_producto,estado_disponibilidad);
CREATE INDEX IF NOT EXISTS idx_carrito_usuario_estado ON public.carritos(id_usuario,estado,id_carrito);
CREATE INDEX IF NOT EXISTS idx_pedido_usuario_fecha ON public.pedidos(id_usuario,fecha_pedido DESC);
CREATE INDEX IF NOT EXISTS idx_pedido_estado_fecha ON public.pedidos(estado,fecha_pedido);
CREATE INDEX IF NOT EXISTS idx_notificacion_leida_fecha ON public.notificaciones_administrador(leida,fecha_creacion DESC);
CREATE INDEX IF NOT EXISTS idx_movimiento_producto_fecha ON public.movimientos_inventario(id_producto,fecha_movimiento DESC);

-- PostgreSQL no implementa automáticamente el equivalente de
-- ON UPDATE CURRENT_TIMESTAMP de MySQL.
CREATE OR REPLACE FUNCTION public.kroma_actualizar_fecha()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
    NEW.fecha_actualizacion := CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;

DO $$
DECLARE tabla text;
BEGIN
    FOREACH tabla IN ARRAY ARRAY[
        'usuarios','consultas_soporte','productos','inventario',
        'carritos','configuracion_envio','pedidos'
    ] LOOP
        EXECUTE format('DROP TRIGGER IF EXISTS trg_kroma_fecha_actualizacion ON public.%I', tabla);
        EXECUTE format(
            'CREATE TRIGGER trg_kroma_fecha_actualizacion BEFORE UPDATE ON public.%I FOR EACH ROW EXECUTE FUNCTION public.kroma_actualizar_fecha()',
            tabla
        );
    END LOOP;
END;
$$;

-- Auditoría transaccional de movimientos de inventario.
CREATE OR REPLACE FUNCTION public.kroma_auditar_movimiento()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
    INSERT INTO public.auditoria
        (id_usuario,accion,entidad,id_registro_afectado,descripcion)
    VALUES
        (NEW.id_usuario_responsable,'movimiento_inventario','movimientos_inventario',NEW.id_movimiento,
         NEW.tipo_movimiento || ' producto ' || NEW.id_producto || ', cantidad ' || NEW.cantidad || ': ' || NEW.motivo);
    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS trg_movimiento_auditoria ON public.movimientos_inventario;
CREATE TRIGGER trg_movimiento_auditoria
AFTER INSERT ON public.movimientos_inventario
FOR EACH ROW EXECUTE FUNCTION public.kroma_auditar_movimiento();

COMMIT;

-- IMPORTANTE:
-- Este archivo repara estructura, índices y triggers; no importa productos,
-- cuentas ni demás registros desde MySQL. Tampoco convierte el backend PHP.
-- GitHub Pages no ejecuta PHP. Para una tienda funcional se necesita un host
-- de PHP que conecte a Supabase por PDO_PGSQL y use SSL. No pongas credenciales
-- de Supabase ni claves secretas en el navegador, GitHub Pages o Git.
