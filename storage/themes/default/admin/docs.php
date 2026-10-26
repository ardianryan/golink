<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold"><?php ee('System Documentation & Operator Manual') ?></h1>
        <p class="text-muted mb-0"><?php ee('Comprehensive offline guide for managing, configuring, and extending your URL Shortener platform.') ?></p>
    </div>
    <div>
        <a href="<?php echo route('apidocs') ?>" class="btn btn-outline-primary rounded-3 me-2" target="_blank">
            <i class="align-middle me-1" data-feather="code"></i> <?php ee('REST API Docs') ?>
        </a>
        <a href="<?php echo route('admin.about') ?>" class="btn btn-secondary rounded-3">
            <i class="align-middle me-1" data-feather="info"></i> <?php ee('System Info') ?>
        </a>
    </div>
</div>

<div class="row">
    <!-- Sticky Table of Contents -->
    <div class="col-lg-3 mb-4">
        <div class="card rounded-4 shadow-sm position-sticky" style="top: 20px; z-index: 10;">
            <div class="card-header fw-bold bg-transparent border-bottom">
                <i class="align-middle me-2 text-primary" data-feather="book-open"></i><?php ee('Documentation Index') ?>
            </div>
            <div class="card-body p-2">
                <div class="list-group list-group-flush small" id="docNav">
                    <a href="#overview" class="list-group-item list-group-item-action border-0 rounded-2 py-2">1. <?php ee('Overview & Architecture') ?></a>
                    <a href="#server-setup" class="list-group-item list-group-item-action border-0 rounded-2 py-2">2. <?php ee('Server & Nginx/Apache Setup') ?></a>
                    <a href="#cron-jobs" class="list-group-item list-group-item-action border-0 rounded-2 py-2">3. <?php ee('Cron Jobs & Automation') ?></a>
                    <a href="#url-shortener" class="list-group-item list-group-item-action border-0 rounded-2 py-2">4. <?php ee('URL Shortening & Targeting') ?></a>
                    <a href="#bio-qr" class="list-group-item list-group-item-action border-0 rounded-2 py-2">5. <?php ee('Bio Pages & QR Studio') ?></a>
                    <a href="#billing-gateways" class="list-group-item list-group-item-action border-0 rounded-2 py-2">6. <?php ee('Billing & Payment Gateways') ?></a>
                    <a href="#custom-domains" class="list-group-item list-group-item-action border-0 rounded-2 py-2">7. <?php ee('Custom Domains & DNS') ?></a>
                    <a href="#security-abuse" class="list-group-item list-group-item-action border-0 rounded-2 py-2">8. <?php ee('Anti-Abuse & Security') ?></a>
                    <a href="#plugins-themes" class="list-group-item list-group-item-action border-0 rounded-2 py-2">9. <?php ee('Plugins & Themes Customization') ?></a>
                    <a href="#maintenance" class="list-group-item list-group-item-action border-0 rounded-2 py-2">10. <?php ee('Maintenance & Troubleshooting') ?></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">
        <!-- 1. Overview & Architecture -->
        <div class="card rounded-4 shadow-sm mb-4" id="overview">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <span class="badge bg-primary me-2">1</span>
                <h5 class="card-title mb-0 fw-bold"><?php ee('Overview & Architecture') ?></h5>
            </div>
            <div class="card-body">
                <p><?php ee('This platform is an enterprise-grade URL Management, Link-in-Bio, and QR Code generation system built using GemFramework (PHP 8.2+). It operates completely standalone with zero external licensing phone-home dependencies.') ?></p>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <strong class="d-block mb-1 text-primary"><i class="align-middle me-1" data-feather="folder"></i> /app</strong>
                            <small class="text-muted"><?php ee('Core application logic: Controllers, Models, Middleware, Helpers, and Payment drivers.') ?></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <strong class="d-block mb-1 text-primary"><i class="align-middle me-1" data-feather="layers"></i> /storage</strong>
                            <small class="text-muted"><?php ee('Application storage: Themes, Plugins, Translations, Cache, and User uploads.') ?></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <strong class="d-block mb-1 text-primary"><i class="align-middle me-1" data-feather="globe"></i> /public</strong>
                            <small class="text-muted"><?php ee('Web server document root containing entry point index.php, assets, and media.') ?></small>
                        </div>
                    </div>
                </div>
                <div class="alert alert-success mt-3 mb-0 d-flex align-items-center">
                    <i class="me-2" data-feather="check-circle"></i>
                    <div><strong><?php ee('Standalone Status') ?>:</strong> <?php ee('All Pro and Extended modules (Vouchers, Tax, Bio Widgets, Subscriptions, Teams) are permanently active without license validation.') ?></div>
                </div>
            </div>
        </div>

        <!-- 2. Server & Webserver Setup -->
        <div class="card rounded-4 shadow-sm mb-4" id="server-setup">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <span class="badge bg-primary me-2">2</span>
                <h5 class="card-title mb-0 fw-bold"><?php ee('Server & Nginx / Apache Configuration') ?></h5>
            </div>
            <div class="card-body">
                <h6><?php ee('Nginx Virtual Host Configuration') ?></h6>
                <p class="text-muted small"><?php ee('Point the root directive to the <code>public</code> directory for optimal security.') ?></p>
                <pre class="bg-dark text-white p-3 rounded-3 small"><code>server {
    listen 80;
    server_name yourdomain.com *.yourdomain.com;
    root /var/www/shorturl/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}</code></pre>

                <h6 class="mt-4"><?php ee('Directory Permissions') ?></h6>
                <p class="text-muted small"><?php ee('Ensure storage and upload directories are writable by the web server user (<code>www-data</code>):') ?></p>
                <pre class="bg-dark text-white p-3 rounded-3 small"><code>chmod -R 775 storage/ public/content/
chown -R www-data:www-data storage/ public/content/</code></pre>
            </div>
        </div>

        <!-- 3. Cron Jobs & Automation -->
        <div class="card rounded-4 shadow-sm mb-4" id="cron-jobs">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <span class="badge bg-primary me-2">3</span>
                <h5 class="card-title mb-0 fw-bold"><?php ee('Cron Jobs & Automated Tasks') ?></h5>
            </div>
            <div class="card-body">
                <p><?php ee('The platform relies on scheduled cron tasks to handle subscription expirations, invoice generation, link health checks, and data aggregation.') ?></p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th><?php ee('Task') ?></th>
                                <th><?php ee('Frequency') ?></th>
                                <th><?php ee('Crontab Command') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Cron.php</strong><br><small class="text-muted">Subscription renewal, expiration, reminder emails</small></td>
                                <td>Every hour<br><code>0 * * * *</code></td>
                                <td><code>wget -q -O - <?php echo url('cron') ?> >/dev/null 2>&1</code></td>
                            </tr>
                            <tr>
                                <td><strong>Data Aggregation</strong><br><small class="text-muted">Purges stale click logs and aggregates daily stats</small></td>
                                <td>Daily at midnight<br><code>0 0 * * *</code></td>
                                <td><code>php <?php echo ROOT ?>/storage/app/cron.php >/dev/null 2>&1</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. URL Shortening & Targeting -->
        <div class="card rounded-4 shadow-sm mb-4" id="url-shortener">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <span class="badge bg-primary me-2">4</span>
                <h5 class="card-title mb-0 fw-bold"><?php ee('URL Shortening, Redirections & Targeting') ?></h5>
            </div>
            <div class="card-body">
                <p><?php ee('Advanced redirection engine capabilities available to users and administrators:') ?></p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3">
                            <h6 class="fw-bold"><i class="align-middle me-1 text-primary" data-feather="compass"></i> Geotargeting</h6>
                            <p class="small text-muted mb-0"><?php ee('Redirect visitors to different destination URLs based on their country of origin using local MaxMind GeoIP database.') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3">
                            <h6 class="fw-bold"><i class="align-middle me-1 text-primary" data-feather="smartphone"></i> Device & OS Targeting</h6>
                            <p class="small text-muted mb-0"><?php ee('Send iOS, Android, macOS, or Windows users to custom App Store or landing page links.') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3">
                            <h6 class="fw-bold"><i class="align-middle me-1 text-primary" data-feather="target"></i> Pixels & Retargeting</h6>
                            <p class="small text-muted mb-0"><?php ee('Inject Meta (Facebook) Pixel, Google Tag Manager, TikTok Pixel, LinkedIn Insight, or Twitter Tag on redirect intermediate pages.') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3">
                            <h6 class="fw-bold"><i class="align-middle me-1 text-primary" data-feather="shield"></i> Protection & Expiration</h6>
                            <p class="small text-muted mb-0"><?php ee('Password protect links, set expiration dates, or set maximum total allowed clicks.') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Bio Pages & QR Studio -->
        <div class="card rounded-4 shadow-sm mb-4" id="bio-qr">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <span class="badge bg-primary me-2">5</span>
                <h5 class="card-title mb-0 fw-bold"><?php ee('Bio Pages (Link-in-Bio) & QR Code Studio') ?></h5>
            </div>
            <div class="card-body">
                <h6><?php ee('Bio Pages Builder') ?></h6>
                <p><?php ee('Allows users to create customizable mobile landing pages with drag-and-drop widgets:') ?></p>
                <ul>
                    <li><strong>Content Widgets:</strong> <?php ee('Buttons, Headings, Text blocks, Images, Dividers, and HTML embeds.') ?></li>
                    <li><strong>Social & Media Widgets:</strong> <?php ee('YouTube player, Spotify playlist, TikTok embed, SoundCloud, and Social icons.') ?></li>
                    <li><strong>Interactive Widgets:</strong> <?php ee('Newsletter capture, Contact form, Phone / WhatsApp direct call, PayPal donation button, and vCard.') ?></li>
                </ul>

                <h6 class="mt-4"><?php ee('QR Code Generator Studio') ?></h6>
                <p><?php ee('Generate rich vector QR codes with customized styles:') ?></p>
                <ul>
                    <li><strong>Shapes & Gradients:</strong> <?php ee('Square, Round, Dots, Heart, Diamond with linear or radial gradients.') ?></li>
                    <li><strong>Custom Branding:</strong> <?php ee('Upload company logo or select preset brand icons embedded in the center of the QR code.') ?></li>
                    <li><strong>Frames & Calls to Action:</strong> <?php ee('Predefined frames with text headers such as "SCAN ME".') ?></li>
                    <li><strong>Export Formats:</strong> <?php ee('High-resolution PNG, SVG vector, WEBP, and PDF.') ?></li>
                </ul>
            </div>
        </div>

        <!-- 6. Billing & Payment Gateways -->
        <div class="card rounded-4 shadow-sm mb-4" id="billing-gateways">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <span class="badge bg-primary me-2">6</span>
                <h5 class="card-title mb-0 fw-bold"><?php ee('Plans, Subscriptions & Payment Gateways') ?></h5>
            </div>
            <div class="card-body">
                <h6><?php ee('Configuring Payment Gateways') ?></h6>
                <p><?php ee('Configure API keys in <code>Admin Panel -> Settings -> Payment Gateways</code>:') ?></p>
                <div class="accordion" id="accordionGateways">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingStripe">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStripe">
                                Stripe Subscriptions & One-time
                            </button>
                        </h2>
                        <div id="collapseStripe" class="accordion-collapse collapse" data-bs-parent="#accordionGateways">
                            <div class="accordion-body small">
                                <p><?php ee('Requires <code>Publishable Key</code>, <code>Secret Key</code>, and <code>Webhook Secret</code>.') ?></p>
                                <p><?php ee('Add webhook endpoint in Stripe Dashboard:') ?> <code><?php echo url('webhook/stripe') ?></code></p>
                                <p><?php ee('Listen to events:') ?> <code>checkout.session.completed</code>, <code>invoice.paid</code>, <code>customer.subscription.deleted</code>.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPaypal">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePaypal">
                                PayPal REST API
                            </button>
                        </h2>
                        <div id="collapsePaypal" class="accordion-collapse collapse" data-bs-parent="#accordionGateways">
                            <div class="accordion-body small">
                                <p><?php ee('Configure <code>Client ID</code> and <code>Client Secret</code> from developer.paypal.com.') ?></p>
                                <p><?php ee('Webhook URL:') ?> <code><?php echo url('webhook/paypal') ?></code></p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBank">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBank">
                                Bank Transfer / Offline Payment
                            </button>
                        </h2>
                        <div id="collapseBank" class="accordion-collapse collapse" data-bs-parent="#accordionGateways">
                            <div class="accordion-body small">
                                <p><?php ee('Users upload transfer proof / receipt. The administrator approves transactions under <code>Admin -> Subscriptions -> Invoices</code>.') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Custom Domains & DNS -->
        <div class="card rounded-4 shadow-sm mb-4" id="custom-domains">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <span class="badge bg-primary me-2">7</span>
                <h5 class="card-title mb-0 fw-bold"><?php ee('Custom Domains & DNS Configuration') ?></h5>
            </div>
            <div class="card-body">
                <p><?php ee('Allowing users or admins to connect their own brand domains (e.g. <code>link.mybrand.com</code>):') ?></p>
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <h6 class="fw-bold mb-2"><?php ee('DNS Records for User Domains') ?></h6>
                    <ul class="mb-0 small">
                        <li><strong>For Root Domains (mybrand.com):</strong> Create an <code>A Record</code> pointing to your server's IP address.</li>
                        <li><strong>For Subdomains (link.mybrand.com):</strong> Create a <code>CNAME Record</code> pointing to your main platform domain (e.g. <code><?php echo $_SERVER['HTTP_HOST'] ?? 'yourdomain.com' ?></code>).</li>
                    </ul>
                </div>
                <p class="small text-muted mb-0"><?php ee('Ensure your web server (or Cloudflare / reverse proxy) has a wildcard SSL certificate or automated Let\'s Encrypt SSL provisioned for incoming custom domains.') ?></p>
            </div>
        </div>

        <!-- 8. Anti-Abuse & Security -->
        <div class="card rounded-4 shadow-sm mb-4" id="security-abuse">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <span class="badge bg-primary me-2">8</span>
                <h5 class="card-title mb-0 fw-bold"><?php ee('Anti-Abuse & Security Protection') ?></h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 h-100">
                            <strong class="d-block mb-1"><i class="align-middle me-1 text-danger" data-feather="slash"></i> Keyword & Domain Blacklist</strong>
                            <p class="small text-muted mb-0"><?php ee('Block prohibited TLDs (.xyz, .top), spam keywords, or target domains directly in <code>Admin -> Settings -> Security</code> or via <code>storage/app/domains.txt</code>.') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 h-100">
                            <strong class="d-block mb-1"><i class="align-middle me-1 text-primary" data-feather="shield"></i> Google Web Risk API</strong>
                            <p class="small text-muted mb-0"><?php ee('Enable real-time scanning against malware, social engineering, and phishing URLs using Google Cloud Web Risk.') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 h-100">
                            <strong class="d-block mb-1"><i class="align-middle me-1 text-warning" data-feather="lock"></i> Rate Limiting & Throttle</strong>
                            <p class="small text-muted mb-0"><?php ee('Built-in IP rate limiter protects login, registration, and shortening endpoints from brute force and automated bot spam.') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 h-100">
                            <strong class="d-block mb-1"><i class="align-middle me-1 text-success" data-feather="check-square"></i> Bot Captcha Verification</strong>
                            <p class="small text-muted mb-0"><?php ee('Support for Cloudflare Turnstile, reCAPTCHA v2/v3, and Altcha privacy-friendly proof-of-work.') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 9. Plugins & Themes Customization -->
        <div class="card rounded-4 shadow-sm mb-4" id="plugins-themes">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <span class="badge bg-primary me-2">9</span>
                <h5 class="card-title mb-0 fw-bold"><?php ee('Plugins & Theme Customization') ?></h5>
            </div>
            <div class="card-body">
                <h6><?php ee('Plugin Architecture') ?></h6>
                <p><?php ee('Plugins are stored in <code>storage/plugins/{plugin-name}/</code>. A valid plugin contains:') ?></p>
                <ul>
                    <li><code>config.json</code>: <?php ee('Metadata (name, description, author, version, and main bootstrap class).') ?></li>
                    <li><code>plugin.php</code>: <?php ee('Main execution entry registering hooks via <code>\Core\Plugin::add()</code>.') ?></li>
                </ul>

                <h6 class="mt-4"><?php ee('Available Plugin Hook Events') ?></h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered small">
                        <thead class="table-light">
                            <tr>
                                <th><?php ee('Event Hook') ?></th>
                                <th><?php ee('Description') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>shorten.start</code></td>
                                <td><?php ee('Executed before a link is shortened; allows modifying destination URL or parameters.') ?></td>
                            </tr>
                            <tr>
                                <td><code>shorten.complete</code></td>
                                <td><?php ee('Triggered when shortening succeeds; ideal for custom notifications or webhooks.') ?></td>
                            </tr>
                            <tr>
                                <td><code>redirect.start</code></td>
                                <td><?php ee('Executed on every click redirection before rendering target or splash page.') ?></td>
                            </tr>
                            <tr>
                                <td><code>apidocs.extend</code></td>
                                <td><?php ee('Appends custom endpoints to the Developer API documentation page.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 10. Maintenance & Troubleshooting -->
        <div class="card rounded-4 shadow-sm mb-4" id="maintenance">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center">
                <span class="badge bg-primary me-2">10</span>
                <h5 class="card-title mb-0 fw-bold"><?php ee('Maintenance & Troubleshooting') ?></h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0">
                        <h6 class="fw-bold mb-1"><?php ee('Clearing Application Cache') ?></h6>
                        <p class="small text-muted mb-2"><?php ee('If setting changes or translations do not immediately update, clear the files inside the cache directory:') ?></p>
                        <pre class="bg-dark text-white p-2 rounded-2 small mb-0"><code>rm -rf storage/cache/*</code></pre>
                    </div>
                    <div class="list-group-item px-0">
                        <h6 class="fw-bold mb-1"><?php ee('Viewing Error Logs') ?></h6>
                        <p class="small text-muted mb-2"><?php ee('Detailed PHP and application error traces are written to:') ?></p>
                        <pre class="bg-dark text-white p-2 rounded-2 small mb-0"><code>tail -f storage/logs/*.log</code></pre>
                    </div>
                    <div class="list-group-item px-0">
                        <h6 class="fw-bold mb-1"><?php ee('Database Backups') ?></h6>
                        <p class="small text-muted mb-2"><?php ee('Regularly back up your MySQL database using mysqldump:') ?></p>
                        <pre class="bg-dark text-white p-2 rounded-2 small mb-0"><code>mysqldump -u username -p database_name > backup_$(date +%F).sql</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
