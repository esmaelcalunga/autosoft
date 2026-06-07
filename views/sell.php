<?php /* Vender viatura */ $navActive = 'sell'; ?>
<section class="container section narrow">
  <span class="eyebrow">Venda por consignação</span>
  <h1>Vender a minha viatura</h1>
  <p class="lead">Avaliação grátis em 24h, exposição nos nossos stands e toda a documentação tratada por nós.
     Deixe os seus dados e um consultor entra em contacto.</p>

  <?php if (!empty($_GET['ok'])): ?>
    <div class="flash flash-success">Pedido recebido! Entraremos em contacto em breve.</div>
  <?php endif; ?>

  <form class="form-card" method="post" action="<?= url('/vender') ?>">
    <?= csrf_field() ?>
    <div class="form-row">
      <label>Nome<input type="text" name="name" required></label>
      <label>Telefone<input type="text" name="phone" required></label>
    </div>
    <div class="form-row">
      <label>Marca<input type="text" name="brand" placeholder="Ex.: Toyota"></label>
      <label>Modelo<input type="text" name="model" placeholder="Ex.: Hilux"></label>
    </div>
    <div class="form-row">
      <label>Ano<input type="text" name="year" placeholder="2022"></label>
      <label>Quilometragem<input type="text" name="km" placeholder="45000"></label>
    </div>
    <label>Mensagem<textarea name="message" rows="4" placeholder="Conte-nos sobre a viatura"></textarea></label>
    <button class="btn btn-lg btn-primary" type="submit">Pedir avaliação</button>
  </form>
</section>
