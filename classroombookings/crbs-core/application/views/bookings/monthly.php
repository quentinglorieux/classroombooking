<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="<?= base_url('assets/css/monthly_calendar.css') ?>">
    <title>Monthly Calendar</title>
</head>
<body>
<div class="calendar-navigation">
    <a href="<?= site_url("bookings/monthly?year=" . ($month == 1 ? $year - 1 : $year) . "&month=" . ($month == 1 ? 12 : $month - 1)) ?>">
        Previous
    </a>
    <span><?= date('F Y', strtotime("$year-$month-01")) ?></span>
    <a href="<?= site_url("bookings/monthly?year=" . ($month == 12 ? $year + 1 : $year) . "&month=" . ($month == 12 ? 1 : $month + 1)) ?>">
        Next
    </a>
</div>

<div class="calendar-container">
    <?= $calendar_html ?>
</div>

<!-- Optional: Show bookings or events for the month -->
<div class="bookings-list">
    <h2>Bookings for <?= date('F Y', strtotime("$year-$month-01")) ?></h2>
    <ul>
        <?php if (!empty($bookings)): ?>
            <?php foreach ($bookings as $booking): ?>
                <li>
                    <strong><?= !empty($booking->user_name) ? html_escape($booking->user_name) : 'No User' ?></strong><br>
                    Room: <?= !empty($booking->room_name) ? html_escape($booking->room_name) : 'N/A' ?><br>
                    Period: <?= !empty($booking->period_name) ? html_escape($booking->period_name) : 'N/A' ?><br>
                    Date: <?= html_escape($booking->date) ?><br>
                    Time: <?= !empty($booking->period_time_start) && !empty($booking->period_time_end)
                        ? html_escape($booking->period_time_start) . ' - ' . html_escape($booking->period_time_end)
                        : 'N/A' ?>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No bookings available for this month.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>