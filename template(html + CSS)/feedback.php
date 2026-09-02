<?php
require_once __DIR__ . '/layout.php';
showHeader('Feedback', 'feedback');
?>

<section id="feedback-form" class="form-section" aria-labelledby="feedback-heading">
  <?php showBreadcrumb([
    'Feedback' => null,
  ]); ?>

  <div class="section-header">
    <span class="section-badge">Your feedback</span>
    <h1 id="feedback-heading">Tell us about your experience</h1>
    <p>Your comments help CityLink improve its services and website. Fields marked required must be completed.</p>
  </div>

  <form class="form-table-container" method="post" action="">
    <table class="form-table">
      <thead>
        <tr>
          <th scope="col">Field</th>
          <th scope="col">Your response</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row"><label for="feedback-type">Feedback type (required)</label></th>
          <td>
            <select id="feedback-type" name="feedback_type" required>
              <option value="">Select an option</option>
              <option value="compliment">Compliment</option>
              <option value="suggestion">Suggestion</option>
              <option value="complaint">Complaint</option>
              <option value="website">Website issue</option>
              <option value="other">Other</option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="subject">Subject (required)</label></th>
          <td>
            <input type="text" id="subject" name="subject" required>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="message">Your feedback (required)</label></th>
          <td>
            <textarea id="message" name="message" rows="7" maxlength="2000" aria-describedby="message-help" required></textarea>
            <p id="message-help">Describe what happened, the service involved and what you would like us to consider. Do not include passwords or payment details.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="full-name">Your name (optional)</label></th>
          <td>
            <input type="text" id="full-name" name="full_name" autocomplete="name">
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="email">Email address (optional)</label></th>
          <td>
            <input type="email" id="email" name="email" autocomplete="email">
            <p>Provide an email address if you would like CityLink to respond.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Response requested</th>
          <td>
            <label for="response-requested">
              <input type="checkbox" id="response-requested" name="response_requested" value="yes">
              I would like CityLink to contact me about this feedback.
            </label>
          </td>
        </tr>
      </tbody>
    </table>

    <hr>
    <button type="submit" class="btn btn-primary">Submit feedback</button>
    <p>Personal information included with your feedback will be handled according to our <a href="./privacy.php">Privacy Policy</a>.</p>
  </form>

  <div class="section-header">
    <span class="section-badge">Other ways to respond</span>
    <h2>Contact CityLink another way</h2>
    <p>You can also provide feedback by email, post or phone. Include enough detail for us to understand your feedback, but do not send passwords or payment information.</p>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <h2 class="card-title">Email</h2>
      <p class="card-desc">Send your comments to our customer service team.</p>
      <a href="mailto:contact@citylink.example" class="card-link">contact@citylink.example</a>
    </article>

    <article class="service-card">
      <h2 class="card-title">Write to us</h2>
      <p class="card-desc">Feedback Officer<br>CityLink Community Hub<br>25 Civic Square<br>CityLink WA 6000</p>
    </article>

    <article class="service-card">
      <h2 class="card-title">Call us</h2>
      <p class="card-desc">Call Monday to Friday, 8:30 am–5 pm, excluding public holidays.</p>
      <a href="tel:+61890001234" class="card-link">(08) 9000 1234</a>
    </article>
  </div>
</section>

<?php showFooter(); ?>
