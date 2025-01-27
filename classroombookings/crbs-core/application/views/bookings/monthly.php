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



<!-- Room Selector -->
<div class="room-selector">
    <?= form_open('bookings/monthly', ['method' => 'get'], ['year' => $year, 'month' => $month]) ?>
    <label for="room_id">Select Room:</label>
    <?= form_dropdown('room', $rooms, $room_id, ['id' => 'room_id', 'onchange' => 'this.form.submit()']) ?>
    <?= form_close() ?>
</div>


<!-- Bookings List -->
<div class="bookings-list">
    <h2>Bookings for <?= date('F Y', strtotime("$year-$month-01")) ?></h2>
    <?php if (!empty($grouped_bookings)): ?>
        <?php foreach ($grouped_bookings as $date => $daily_bookings): ?>
            <div class="daily-bookings">
                <h3><?= date('l, F j, Y', strtotime($date)) ?></h3>
                <ul>
                    <?php foreach ($daily_bookings as $booking): ?>
                        <li>
                            <span><?= !empty($booking->period_time_start) && !empty($booking->period_time_end)
                                ? html_escape($booking->period_time_start) . ' - ' . html_escape($booking->period_time_end)
                                : 'N/A' ?></span>
                            <span><strong><?= !empty($booking->user_name) ? html_escape($booking->user_name) : 'No User' ?></strong></span>
                            <span><?= !empty($booking->room_name) ? html_escape($booking->room_name) : 'N/A' ?></span>

                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No bookings available for this month.</p>
    <?php endif; ?>
</div>

<!-- <div class="calendar-container">
    <?= $calendar_html ?>
</div>
-->
</body>
</html>