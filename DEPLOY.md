# ڕێبەری بڵاوکردنەوە و بەڕێوەبردنی سیستەم (DEPLOY.md)

ئەم فایلە ڕێبەری تەواوی کارکردنی ڕۆژانە، بڵاوکردنەوەی نوێکارییەکان (`Deploy`)، گەڕاندنەوەی بەکەپ (`Rollback`)، و ڕێکخستنی Cron Job لەسەر سێرڤەری cPanel لەخۆدەگرێت.

---

## ١. پێکهاتەی سکریپتەکانی بڵاوکردنەوە (`/scripts`)

هەموو سکریپتەکان زانیاریی پەیوەندیی SSH و داتابەیس ڕاستەوخۆ لە فایلی شاراوەی [.deploy.env](file:///Users/rebinmaarouf/Documents/SarwaryMukiryanCo/.deploy.env) دەخوێننەوە (کە لە [.gitignore](file:///Users/rebinmaarouf/Documents/SarwaryMukiryanCo/.gitignore) پارێزراوە و هەرگیز ناچێتە سەر GitHub):

1. **`./scripts/deploy-dry.sh` (`deploy:dry`) — تاقیکردنەوەی بێ-ئەپلۆد (Dry-Run):**
   * پیشانی دەدات کام فایلانەی Backend و کام فایلانەی Frontend Build لەنێوان کۆمپیوتەرەکەت و سێرڤەری زیندوو جیاوازن (`rsync --dry-run`) **بەبێ ئەوەی هیچ فایلێک لەسەر سێرڤەر بگۆڕێت**.
   * بەکارهێنان:
     ```bash
     ./scripts/deploy-dry.sh
     ```

2. **`./scripts/deploy.sh` (`deploy`) — بڵاوکردنەوەی فەرمی و سەلامەت:**
   * **(a)** پشکنینی پاکیی `git status` دەکات تا دڵنیابێت هەموو گۆڕانکارییەکان Commit کراون.
   * **(b)** لەناو فۆڵدەری `frontend/` فەرمانی `npm ci && npm run build` جێبەجێ دەکات و فایلە نوێیەکان دەخاتە ناو `backend/public/build/`.
   * **(c)** **بەکەپی ئۆتۆماتیکی پێش Deploy:** پێش دەستکاریکردنی هەر فایلێک، بەکەپی تەواوی داتابەیسی MySQL (`prod-db-TIMESTAMP.sql.gz`) و فایلەکانی سێرڤەر (`prod-files-TIMESTAMP.tar.gz`) وەردەگرێت و دەیانخاتە ناو فۆڵدەری `/backups`.
   * **(d)** تەنها فایلە گۆڕاوەکان بە `rsync -rcz` دەنێرێت بۆ سێرڤەر:
     * فایلەکانی ناوکی لاراڤێڵ (`app/`, `routes/`, `resources/`, `config/`, `database/`, `artisan`, `composer.json`, `composer.lock`) دەچنە ناو `/home/sarwarymukiryan/public_html/SarwarCo/`.
     * فایلەکانی بیڵدی Vue (`backend/public/build/`) دەچنە ناو `/home/sarwarymukiryan/public_html/build/`.
     * **تایبەتمەندیی پاراستن:** هەرگیز فەرمانی `--delete` لەسەر ڕووتی `public_html` یان `SarwarCo` بەکارناهێنێت، و هەرگیز دەستکاریی `.env`، `storage/`، `public_html/.htaccess`، و `public_html/SarwarCo/.htaccess` ناکات.
   * **(e)** ئەگەر `composer.json` یان `composer.lock` گۆڕابێت، خۆکارانە لەسەر سێرڤەر بە PHP 8.3 فەرمانی `composer install --no-dev --optimize-autoloader` جێبەجێ دەکات.
   * **(f)** لەسەر سێرڤەر بە PHP 8.3 (`/opt/cpanel/ea-php83/root/usr/bin/php`) ئەم فەرمانانە جێبەجێ دەکات:
     * `php artisan migrate --force`
     * `php artisan optimize:clear`
     * `php artisan config:cache && php artisan route:cache && php artisan view:cache`
   * **(g)** دەسەڵاتی فۆڵدەرەکانی `storage` و `bootstrap/cache` (`775`) و قفڵەکانی `.htaccess` (`644`) ڕێکدەخاتەوە.
   * **(h)** **پشکنینی کۆتایی (Verification):**
     * دڵنیا دەبێتەوە کە `https://sarwary-mukiryan.com/SarwarCo/.env` و `laravel.log` کۆدی **`HTTP 403`** دەگەڕێننەوە (داخراون).
     * دڵنیا دەبێتەوە کە سایتی سەرەکی `/` و `/up` کۆدی **`HTTP 200`** دەگەڕێننەوە.
   * بەکارهێنان:
     ```bash
     ./scripts/deploy.sh
     ```

3. **`./scripts/rollback.sh` (`rollback`) — گەڕاندنەوەی خێرای دوایین بەکەپ:**
   * ئەگەر هەر کێشەیەک دوای Deploy دروست بوو، بە یەک فەرمان دوایین بەکەپی فایلەکانی ناو `/backups/prod-files-*.tar.gz` دەگەڕێنێتەوە سەر سێرڤەر (بە پاراستنی `.env` و `storage/` و قفڵەکانی `.htaccess`) و Cacheـەکان پاک دەکاتەوە.
   * بەکارهێنان:
     ```bash
     ./scripts/rollback.sh
     ```

---

## ٢. چۆنیەتی زیادکردنی Cron Job لەسەر cPanel (بۆ ڕاپۆرتی کۆتایی ڕۆژ `finance:eod-closeout`)

لە فایلی [backend/routes/console.php](file:///Users/rebinmaarouf/Documents/SarwaryMukiryanCo/backend/routes/console.php#L12) فەرمانی ناردنی ئیمەیڵی باڵانسی کۆتایی ڕۆژ (`finance:eod-closeout`) دانراوە کە هەموو شەوێک کاتژمێر `23:59` کار بکات. لەبەر ئەوەی لە ئێستادا هیچ Cron Jobـێک لەسەر cPanel دانەنراوە، بەم هەنگاوانە زیادی بکە:

### ڕێگای یەکەم: لەناو داشبۆردی cPanel
1. بچۆ ناو **cPanel** ← لە بەشی **Advanced** کرتە لە **Cron Jobs** بکە.
2. لە بەشی **Add New Cron Job**:
   * لە لیستە ئامادەکراوەکە (**Common Settings**) هەڵبژێرە: **Once Per Minute (`* * * * *`)**.
   * لە خانەی **Command** ئەم فەرمانە بە وردی دابنێ (بە PHP 8.3):
     ```bash
     /opt/cpanel/ea-php83/root/usr/bin/php /home/sarwarymukiryan/public_html/SarwarCo/artisan schedule:run >> /dev/null 2>&1
     ```
3. کرتە لە **Add New Cron Job** بکە.

### ڕێگای دووەم: ڕاستەوخۆ بە یەک فەرمانی SSH
دەتوانین بە یەک فەرمان لە ڕێگەی SSH زیادی بکەینە ناو `crontab`ی سێرڤەرەکە:
```bash
ssh -p 22 -i ~/.ssh/id_ed25519 sarwarymukiryan@162.240.238.217 '(crontab -l 2>/dev/null; echo "* * * * * /opt/cpanel/ea-php83/root/usr/bin/php /home/sarwarymukiryan/public_html/SarwarCo/artisan schedule:run >> /dev/null 2>&1") | crontab -'
```
