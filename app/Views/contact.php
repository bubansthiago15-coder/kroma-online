<?php $currentUser=Auth::user()??[];?>
<section class="content support-page support-page--hero">
 <div class="support-decor support-decor--one" aria-hidden="true"></div><div class="support-decor support-decor--two" aria-hidden="true"></div>
 <div class="support-intro"><div><p class="admin-eyebrow">CONTACTANOS</p><h1>Estamos para ayudarte</h1><p>Completá el formulario y te responderemos lo antes posible.</p></div></div>
 <form class="form support-form" method="post" action="?action=contact"><?=csrf_field()?>
  <label for="support-name"><span><?=kroma_icon('user')?></span>Nombre</label><input id="support-name" name="nombre" value="<?=h(trim(($currentUser['nombre']??'').' '.($currentUser['apellido']??'')))?>" maxlength="120" autocomplete="name" required>
  <label for="support-email"><span><?=kroma_icon('mail')?></span>Correo electrónico</label><input id="support-email" name="email" type="email" value="<?=h($currentUser['email']??'')?>" maxlength="190" autocomplete="email" required>
  <label for="support-message"><span><?=kroma_icon('message')?></span>Mensaje</label><textarea id="support-message" name="mensaje" rows="6" maxlength="5000" placeholder="Describí el problema o la consulta con el mayor detalle posible…" required></textarea>
  <p class="muted">La consulta quedará guardada para que el equipo de administración pueda darle seguimiento.</p><button class="btn" type="submit"><?=kroma_icon('send')?>Enviar consulta</button>
 </form>
</section>
