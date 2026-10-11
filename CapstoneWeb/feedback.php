<?php
require_once __DIR__ . '/config/feedback_data.php';
$feedbackData=feedbackPageData();
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

  <?php authErrors($feedbackData['errors']); ?>
  <?php if ($feedbackData['success']): ?><div class="booking-notice" role="status"><h2>Your feedback has been received</h2><p>Reference: <strong><?= serviceEscape($feedbackData['success']) ?></strong> · Status: New</p></div><?php endif; ?>
  <?php if (!$feedbackData['success']): ?>
  <form id="member-feedback" class="form-table-container" method="post" action="./feedback.php">
    <input type="hidden" name="csrf_token" value="<?= serviceEscape(authToken()) ?>">
    <input type="hidden" name="form_token" value="<?= serviceEscape($feedbackData['form_token']) ?>">
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
              <?php foreach ($feedbackData['categories'] as $category): ?><option value="<?= (int)$category['feedback_category_id'] ?>"<?= $feedbackData['values']['feedback_type']===(string)$category['feedback_category_id']?' selected':'' ?>><?= serviceEscape($category['feedback_category_name']) ?></option><?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="subject">Subject (required)</label></th>
          <td>
            <input type="text" id="subject" name="subject" maxlength="150" value="<?= serviceEscape($feedbackData['values']['subject']) ?>" required>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="message">Your feedback (required)</label></th>
          <td>
            <textarea id="message" name="message" rows="7" maxlength="2000" aria-describedby="message-help" required><?= serviceEscape($feedbackData['values']['message']) ?></textarea>
            <p id="message-help">Describe what happened, the service involved and what you would like us to consider. Do not include passwords or payment details.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="full-name">Your name (optional)</label></th>
          <td>
            <input type="text" id="full-name" name="full_name" maxlength="150" value="<?= serviceEscape($feedbackData['values']['full_name']) ?>" autocomplete="name">
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="email">Email address (required when requesting a response)</label></th>
          <td>
            <input type="email" id="email" name="email" <?= $feedbackData['values']['response_requested']==='yes'?'required':'' ?> maxlength="254" value="<?= serviceEscape($feedbackData['values']['email']) ?>" autocomplete="email">
            <p>Provide an email address if you would like CityLink to respond.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Response requested</th>
          <td>
            <label for="response-requested">
              <input type="checkbox" id="response-requested" name="response_requested" value="yes" <?= $feedbackData['values']['response_requested']==='yes'?'checked':'' ?>>
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
  <?php else: ?>
    <p><a class="btn btn-primary" href="./feedback.php?new=1">Submit more feedback</a></p>
    <p><a class="card-link" href="./account.php">View my account</a> · <a class="card-link" href="./index.php">Return to home</a></p>
  <?php endif; ?>

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
