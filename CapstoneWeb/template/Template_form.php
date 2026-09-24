<?php
require_once __DIR__ . '/layout.php';
showHeader('Services', 'services');
?>

<!-- Reuse the section heading structure -->
  <section id="form-template" class="form-section" aria-labelledby="form-heading">
  <!-- Reuse the section heading structure -->
  <div class="section-header">
    <span class="section-badge">Form Template</span>
    <h1 id="form-heading">Fill in the form</h1>
    <p>Enter the requested information, then submit the form.</p>
  </div>

  <!-- Form table container -->
  <form class="form-table-container" novalidate>
    <table class="form-table">
      <thead>
        <tr>
          <th scope="col">Field</th>
          <th scope="col">Response</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><label for="form-field-one">Title 1</label></td>
          <td><input id="form-field-one" name="fieldOne" type="text" required></td>
        </tr>
        <tr>
          <td><label for="form-field-two">Title 2</label></td>
          <td><input id="form-field-two" name="fieldTwo" type="text" required></td>
        </tr>
      </tbody>
    </table>
    <hr>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
  
</section>

<?php showFooter(); ?>