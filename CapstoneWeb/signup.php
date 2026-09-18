<?php
require_once __DIR__ . '/layout.php';
showHeader('Create an account');
?>

<section id="signup-form" class="form-section" aria-labelledby="signup-heading">
  <?php showBreadcrumb([
    'Create an account' => null,
  ]); ?>

  <div class="section-header">
    <span class="section-badge">Sign up</span>
    <h1 id="signup-heading">Create your CityLink account</h1>
    <p>Enter your essential details below to access CityLink online services.</p>
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
          <th scope="row"><label for="full-name">Full name</label></th>
          <td>
            <input type="text" id="full-name" name="full_name" autocomplete="name" required>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="email">Email address</label></th>
          <td>
            <input type="email" id="email" name="email" autocomplete="email" required>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="password">Password</label></th>
          <td>
            <input type="password" id="password" name="password" autocomplete="new-password" minlength="8" aria-describedby="password-help" required>
            <p id="password-help">Use at least 8 characters.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="confirm-password">Confirm password</label></th>
          <td>
            <input type="password" id="confirm-password" name="confirm_password" autocomplete="new-password" minlength="8" required>
          </td>
        </tr>
        <tr>
          <th scope="row">Privacy</th>
          <td>
            <label for="accept-privacy">
              <input type="checkbox" id="accept-privacy" name="accept_privacy" required>
              I have read and agree to the <a href="./privacy.php">Privacy Policy</a>.
            </label>
          </td>
        </tr>
      </tbody>
    </table>

    <hr>
    <button type="submit" class="btn btn-primary">Create account</button>
    <p>Already have an account? <a href="./login.php">Log in</a>.</p>
  </form>
</section>

<?php showFooter(); ?>
