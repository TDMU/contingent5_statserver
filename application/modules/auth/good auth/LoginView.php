<div id="login">
<?php if ($this->login) :?>
    <h2>Username</h2>
    You are logged in as <strong class="username"><?php echo $this->username ?></strong>
<?php else : ?>
    <h2>Login</h2>
    <!-- выводим ошибку -->
    <?php if(!empty($this->error)) :?>
        <div id="error"><?php echo $this->escape($this->error);?></div>
    <?php endif; ?>
    <?php echo $this->form; ?>
<?php endif; ?>
</div>
