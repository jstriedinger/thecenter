<?php  ?>
<div class="columns is-variable in-desktop is-8 is-vcentered is-multiline has-medium-padding-top">
  <div class="column is-half">
    <h3 class="title is-2">Lleva este programa tu <span class="is-blue">empresa</span> o <span class="is-blue">entidad pública</span></h3>
    <p class="subtitle is-5">Entrena a tu equipo y compañeros en liderazgo e innovación empresarial.</p>
    <a class="button alt alt-font">
      <span class="icon">
        <i class="far fa-envelope"></i>
      </span>
      <span>Info@thecenterforcompetitiveness.org</span>
    </a>
  </div>
  <div class="column is-half">
    <div class="box">
      <h3 class="title is-3">programa especializado</h3>
      <p class="subtitle is-6">Dejanos tus datos y te contactaremos para crearte un programa especializado</p>
      <?php  echo do_shortcode( '[wpforms id="'.get_field('wp_form_id').'"]' ); ?>
    </div>
  </div>
</div>
