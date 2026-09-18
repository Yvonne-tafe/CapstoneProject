<?php
require_once __DIR__ . '/layout.php';
showHeader('Log in');
?>

<section id="login-form" class="form-section" aria-labelledby="login-heading">
  <?php showBreadcrumb([
    'Log in' => null,
  ]); ?>

  <div class="section-header">
    <span class="section-badge">Account access</span>
    <h1 id="login-heading">Log in to CityLink</h1>
    <p>Enter the email address and password associated with your account.</p>
  </div>

  <form class="form-table-container" method="post" action="">
    <table class="form-table">
      <thead>
        <tr>
          <th scope="col">Field</th>
          <th scope="col">Your details</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row"><label for="email">Email address</label></th>
          <td>
            <input type="email" id="email" name="email" autocomplete="email" required>
            <p>Use the email address you entered when creating your account.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="password">Password</label></th>
          <td>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
            <p>Passwords are case-sensitive.</p>
          </td>
        </tr>
      </tbody>
    </table>

    <hr>
    <button type="submit" class="btn btn-primary">Log in</button>
    <p><a href="./contact.php">Forgot your password?</a></p>
    <p>Do not have an account? <a href="./signup.php">Create an account</a>.</p>
  </form>
</section>

<?php showFooter(); ?>
