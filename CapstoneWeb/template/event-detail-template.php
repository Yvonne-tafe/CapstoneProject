<?php
require_once __DIR__ . '/layout.php';
// 接上資料後，將頁面標題改為 Service.service_title。
showHeader('Event details', 'events');
?>

<!--
  資料對應規則（這份空白版型尚未查詢資料庫）：
  data-source：來源資料表；data-field：要填入的欄位。
  data-content-key：對應 ServiceWebContent.content_key。
  data-content-type：對應 paragraph / list / image。
  data-attribute：將欄位值填入指定 HTML 屬性；未指定時填入文字內容。
  data-format：日期、時間或費用的呈現方式，須由後續 PHP 處理。
  這些 data-* 是配對標記，不會自行載入或顯示資料。

  目前 JSON 已有 introduction、what_to_expect、who_can_join、getting_there。
  feature_image、accessibility、what_to_bring、weather_updates 為可選內容。
  可選區塊填入資料後才移除 hidden；沒有資料則保持隱藏。
  文字輸出須用 htmlspecialchars；連結與圖片網址須先驗證。
  content_items 是 JSON 字串，須 json_decode 後迴圈輸出 li。
-->
<article class="announcement-detail" aria-labelledby="event-detail-heading" data-source="Service" data-service-id="">
  <?php showBreadcrumb([
    'Events' => './events.php'
  ]); ?>
  <!-- 接上資料後，將 Service.service_title 加為 showBreadcrumb 的最後一項（連結為 null）。 -->

  <header class="announcement-detail-header">
    <span class="section-badge" data-field="service_quick_desc"></span>
    <h1 id="event-detail-heading" data-field="service_title"></h1>
    <p class="announcement-lead" data-field="service_short_desc"></p>
    <!-- 日期／時間來自選定的 ServiceSession；沒有場次時保持空白，不虛構日期。 -->
    <dl class="announcement-meta">
      <div><dt>Date</dt><dd data-source="ServiceSession"><time data-field="session_start_at" data-format="date" data-attribute="datetime"></time></dd></div>
      <div><dt>Time</dt><dd data-source="ServiceSession" data-field="session_start_at,session_end_at" data-format="time-range"></dd></div>
      <div><dt>Location</dt><dd data-field="service_location"></dd></div>
      <div><dt>Cost</dt><dd data-field="service_fee,service_fee_basis" data-format="fee"></dd></div>
    </dl>
  </header>

  <figure class="announcement-feature-image" data-source="ServiceWebContent" data-content-key="feature_image" data-content-type="image" hidden>
    <!-- 載入 image_url 後設定 src；沒有圖片時不要輸出空的 src。 -->
    <img alt="" data-field="image_url" data-attribute="src" data-alt-field="image_alt_text">
    <figcaption data-field="image_caption"></figcaption>
  </figure>

  <div class="announcement-layout">
    <div class="announcement-body" data-source="ServiceWebContent">
      <p data-content-key="introduction" data-content-type="paragraph" data-field="content_body"></p>

      <h2 data-content-key="what_to_expect" data-field="content_heading"></h2>
      <ul data-content-key="what_to_expect" data-content-type="list" data-field="content_items">
        <!-- 將 content_items 解碼成陣列後，每個項目產生一個 li；取代此隱藏佔位元素。 -->
        <li data-list-item hidden></li>
      </ul>

      <aside class="announcement-notice" aria-labelledby="event-access-heading" data-content-key="accessibility" data-content-type="paragraph" hidden>
        <h2 id="event-access-heading" data-field="content_heading"></h2>
        <p data-field="content_body"></p>
      </aside>

      <!-- 補上目前 JSON 已有的 who_can_join 對應位置，沿用原本 h2 + p 的結構。 -->
      <h2 data-content-key="who_can_join" data-field="content_heading"></h2>
      <p data-content-key="who_can_join" data-content-type="paragraph" data-field="content_body"></p>

      <h2 data-content-key="getting_there" data-field="content_heading"></h2>
      <p data-content-key="getting_there" data-content-type="paragraph" data-field="content_body"></p>

      <h2 data-content-key="what_to_bring" data-field="content_heading" hidden></h2>
      <p data-content-key="what_to_bring" data-content-type="paragraph" data-field="content_body" hidden></p>

      <h2 data-content-key="weather_updates" data-field="content_heading" hidden></h2>
      <p data-content-key="weather_updates" data-content-type="paragraph" data-field="content_body" hidden></p>
    </div>

    <aside class="announcement-sidebar" aria-label="Event booking and contact information" data-source="Service">
      <!-- 只有 booking_required 為真，且有可預約場次時，才顯示此卡片並設定預約連結。 -->
      <div class="announcement-contact-card" data-visible-field="booking_required" hidden>
        <h2>Booking</h2>
        <p data-field="service_booking_requirements"></p>
        <a class="btn btn-primary" data-booking-link data-source="ServiceSession" data-field="service_session_id">Book this event</a>
      </div>
      <div class="announcement-update-card">
        <h2>Event contact</h2>
        <p data-field="service_contact_name"></p>
        <!-- 電話／信箱為空時隱藏對應 p；有值時設定文字與 tel:／mailto: href。 -->
        <p><strong>Phone</strong><br><a data-field="service_contact_phone" data-attribute="href" data-link-prefix="tel:"></a></p>
        <p><strong>Email</strong><br><a data-field="service_contact_email" data-attribute="href" data-link-prefix="mailto:"></a></p>
      </div>
    </aside>
  </div>

  <footer class="announcement-detail-footer">
    <a href="./events.php" class="card-link">&larr; Back to all events</a>
  </footer>
</article>

<?php
// 沿用共用版型：CSS 由 showHeader 載入，JavaScript 由 showFooter 載入。
showFooter();
?>
