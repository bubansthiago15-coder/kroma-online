<section class="content">
 <h1>Mi cuenta</h1>
 <p><?=h($profile['email'])?> · <?=h($_SESSION['user']['rol'])?></p>
 <?php if(($_SESSION['user']['rol']??'')!=='administrador'):?>
  <div class="cats"><a class="pill" href="?action=orders">Mis pedidos</a><a class="pill" href="?action=favorites">Favoritos</a></div>
 <?php endif?>
 <form class="form" method="post" action="?action=profile_save">
  <?=csrf_field()?>
  <h2>Mis datos</h2>
  <label for="profile-name">Nombre</label><input id="profile-name" name="nombre" value="<?=h($profile['nombre'])?>" required maxlength="100" autocomplete="given-name">
  <label for="profile-last-name">Apellido</label><input id="profile-last-name" name="apellido" value="<?=h($profile['apellido'])?>" maxlength="100" autocomplete="family-name">
  <label for="profile-email">Correo electrónico</label><input id="profile-email" name="email" type="email" value="<?=h($profile['email'])?>" required maxlength="190" autocomplete="email">
  <label for="profile-phone">Número de teléfono</label><input id="profile-phone" name="telefono" type="tel" value="<?=h($profile['telefono']??'')?>" maxlength="30" autocomplete="tel">
  <label for="profile-dni">DNI (opcional)</label><input id="profile-dni" name="dni" type="text" value="<?=h($profile['dni']??'')?>" inputmode="numeric" maxlength="10" pattern="[0-9]{7,8}|[0-9]{1,2}\.[0-9]{3}\.[0-9]{3}" autocomplete="off" aria-describedby="profile-dni-help">
  <small id="profile-dni-help">Se guarda para completar más rápido tus pedidos. Podés escribirlo con puntos o sin ellos.</small>
  <button class="btn">Guardar cambios</button>
 </form>
</section>
