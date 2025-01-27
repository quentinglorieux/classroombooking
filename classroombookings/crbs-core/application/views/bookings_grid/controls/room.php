<?php

if (feature('room_groups')) {

    $params = [
        'room' => $room ? $room->room_id : null,
        'date' => $datetime->format('Y-m-d'),
    ];

    // Date button
    $date_url = site_url('bookings/filter/date') . '?' . http_build_query($params);
    $long_date = $datetime->format(setting('date_format_long'));
    $date_img = img('assets/images/ui/cal_day.png');
    $date_text = $long_date;
    $date_label = "<span>{$date_img} {$date_text}</span> <span>&#x25BC;</span>";
    $date_button = "<button
        type='button'
        class='filter-button'
        up-layer='new popup'
        up-size='medium'
        up-href='$date_url'
        up-history='false'
        up-target='.bookings-filter'
        up-preload=''
    >{$date_label}</button>";

    // Room button
    $room_button = '';
    if ($room) {
        $rooms_url = site_url('bookings/filter/room') . '?' . http_build_query($params);
        $rooms_img = img('assets/images/ui/school_manage_rooms.png');
        $current_room = $room ? html_escape($room->name) : '';
        $rooms_text = $current_room;
        $rooms_label = "<span>{$rooms_img} {$rooms_text}</span> <span>&#x25BC;</span>";
        $room_button = "<button
            type='button'
            class='filter-button'
            up-layer='new popup'
            up-size='max'
            up-href='$rooms_url'
            up-history='false'
            up-target='.bookings-filter'
            up-preload=''
        >{$rooms_label}</button>";
    }

    // Info link
    $info_link = '';
    if ($room) {
        $info_url = "rooms/info/{$room->room_id}";
        $icon = img('assets/images/ui/school_manage_details.png');
        $info_link = anchor($info_url, $icon, [
            'up-layer' => 'new drawer',
            'up-position' => 'left',
            'up-target' => '.room-info',
            'up-preload' => '',
            'style' => 'padding: 4px; margin: 0 24px 0 8px',
        ]);
    }

    // Monthly View button
    $monthly_url = site_url('bookings/monthly');
    $monthly_button = "<a href='{$monthly_url}' class='btn-monthly-view'>" . lang('go_to_monthly_view') . "</a>";
}

?>

<?php if (isset($room_button) && isset($date_button)): ?>
    <div class="controls">
        <?= $room_button . $info_link . $date_button . $monthly_button ?>
    </div>
<?php else: ?>
    <?= form_open($form_action, ['method' => 'get', 'id' => 'bookings_controls_room'], $query_params) ?>
    <table>
        <tr>
            <td valign="middle">
                <label>
                    <?php
                    if ($room) {
                        $url = "rooms/info/{$room->room_id}";
                        $name = 'Room:';
                        $link = anchor($url, $name, [
                            'up-layer' => 'new drawer',
                            'up-position' => 'left',
                            'up-target' => '.room-info',
                            'up-preload',
                        ]);
                        echo "<strong>{$link}</strong>";
                    }
                    ?>
                </label>
            </td>
            <?php if (!empty($rooms)): ?>
                <td valign="middle">
                    <?php
                    echo form_dropdown([
                        'name' => 'room',
                        'id' => 'room_id',
                        'options' => $rooms,
                        'selected' => ($room) ? $room->room_id : '',
                    ]);
                    ?>
                </td>
                <td> &nbsp; <input type="submit" value=" Load " /></td>
            <?php endif; ?>
            <td> &nbsp; <?= $monthly_button ?></td>
        </tr>
    </table>
    <?= form_close() ?>
    <br>
<?php endif; ?>

<!-- Add the styles for the button directly in the file -->
<style>
.controls .btn-monthly-view,
table .btn-monthly-view {
    display: inline-block;
    margin-left: 20px;
    padding: 6px 12px;
    font-size: 14px;
    font-weight: bold;
    color: #fff;
    background-color: #007bff;
    text-decoration: none;
    border-radius: 4px;
    border: 1px solid #0056b3;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.controls .btn-monthly-view:hover,
table .btn-monthly-view:hover {
    background-color: #0056b3;
    border-color: #003f7f;
	color: white;
}

</style>