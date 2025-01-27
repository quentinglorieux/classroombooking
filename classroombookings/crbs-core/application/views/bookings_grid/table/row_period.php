<th class="bookings-grid-header-cell bookings-grid-header-cell-period">
    <strong>
        <?php 
        // echo html_escape($period->name); 
        ?>
    </strong>
    <br />
    <?php if (!empty($room) && !empty($room->name)): ?>
        <span style='font-size: 110%; font-weight: bold; color: #555;'>
            <?php echo html_escape($room->name); ?>
        </span>
        <br />
    <?php endif; ?>
    <span style="font-size: 110%">
        <?php
        $time_fmt = setting('time_format_period');
        if (!empty($time_fmt)) {
            $start = date($time_fmt, strtotime($period->time_start));
            $end = date($time_fmt, strtotime($period->time_end));
            echo sprintf('%s - %s', $start, $end);
        }
        ?>
    </span>
</th>